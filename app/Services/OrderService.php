<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Tạo hoặc lấy order pending hiện tại cho user
     */
    public function getOrCreatePendingOrder($cartItems, $shippingType, $customerInfo)
    {
        try {
            DB::beginTransaction();

            $userId = Auth::id();

            // Kiểm tra xem user đã có order pending chưa
            $existingOrder = Order::where('user_id', $userId)
                ->where('status', Order::STATUS_PENDING)
                ->where('payment_status', Order::PAYMENT_STATUS_PENDING)
                ->first();

            if ($existingOrder) {
                // Cập nhật order existing với thông tin mới
                Log::info('Updating existing pending order');
                $orderData = $this->calculateOrderData($cartItems, $shippingType, $customerInfo);

                $existingOrder->update($orderData);

                // Xóa order items cũ và tạo mới
                $existingOrder->orderItems()->delete();
                $this->createOrderItems($existingOrder, $cartItems);

                DB::commit();

                Log::info('Updated existing pending order', [
                    'order_id' => $existingOrder->id,
                    'user_id' => $userId,
                    'subtotal' => $existingOrder->subtotal,
                    'total' => $existingOrder->total
                ]);

                return $existingOrder->fresh(['orderItems.productVariant.product']);
            }

            // Tạo order mới
            Log::info('Creating new pending order');
            $orderData = $this->calculateOrderData($cartItems, $shippingType, $customerInfo);

            Log::info('Order calculation result', [
                'subtotal' => $orderData['subtotal'],
                'shipping_cost' => $orderData['shipping_cost'],
                'total' => $orderData['total']
            ]);

            $orderData['id'] = $this->generateOrderId();
            $orderData['customer_name'] = $customerInfo['name'];
            $orderData['customer_email'] = $customerInfo['email'];
            $orderData['customer_phone'] = $customerInfo['phone'];
            $orderData['customer_address'] = $customerInfo['detail_address'];
            $orderData['user_id'] = $userId;
            $orderData['order_number'] = Order::generateOrderNumber();
            $orderData['status'] = Order::STATUS_PENDING;
            $orderData['payment_status'] = Order::PAYMENT_STATUS_PENDING;

            $order = Order::create($orderData);

            // Tạo order items
            $this->createOrderItems($order, $cartItems);

            DB::commit();

            Log::info('Created new pending order', [
                'order_id' => $order->id,
                'user_id' => $userId,
                'subtotal' => $order->subtotal,
                'total' => $order->total
            ]);

            return $order->fresh(['orderItems.productVariant.product']);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating pending order', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            throw $e;
        }
    }

    /**
     * Tính toán dữ liệu order
     */
    private function calculateOrderData($cartItems, $shippingType, $customerInfo)
    {
        $subtotal = 0;


        foreach ($cartItems as $item) {
            $price = $item['product_variant']['sale_price'] ?? $item['product_variant']['price'];
            $subtotal += $price * $item['quantity'];
        }

        $shippingCost = $this->calculateShippingCost($shippingType);
        $total = $subtotal + $shippingCost;

        return [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'amount' => $total, // Để tương thích với payment services
            'shipping_method' => $shippingType,
            'customer_info' => $customerInfo,
            'phone' => $customerInfo['phone'] ?? null,
            'shipping_address' => $this->formatShippingAddress($customerInfo),
        ];
    }

    /**
     * Tạo order items
     */
    private function createOrderItems($order, $cartItems)
    {
        foreach ($cartItems as $item) {
            $price = $item['product_variant']['sale_price'] ?? $item['product_variant']['price'];

            OrderItem::create([
                'order_id' => $order->id,
                'productVariant_id' => $item['productVariant_id'],
                'quantity' => $item['quantity'],
                'price' => $price,
                'total' => $price * $item['quantity']
            ]);
        }
    }

    /**
     * Tính phí ship
     */
    private function calculateShippingCost($shippingType)
    {
        $costs = [
            'free' => 0,
            'standard' => 3,
            'fast' => 6
        ];

        return $costs[$shippingType] ?? 0;
    }

    /**
     * Format địa chỉ giao hàng
     */
    private function formatShippingAddress($customerInfo)
    {
        $parts = [];

        if (!empty($customerInfo['address'])) {
            $parts[] = $customerInfo['address'];
        }

        if (!empty($customerInfo['country'])) {
            $parts[] = $customerInfo['country'];
        }

        if (!empty($customerInfo['city'])) {
            $parts[] = $customerInfo['city'];
        }

        return implode(', ', $parts);
    }

    /**
     * Generate unique order ID
     */
    private function generateOrderId()
    {
        do {
            $id = 'ORD_' . time() . '_' . Str::random(8);
        } while (Order::where('id', $id)->exists());

        return $id;
    }

    /**
     * Confirm order sau khi thanh toán thành công
     */
    public function confirmOrder($orderId, $paymentData)
    {
        try {
            DB::beginTransaction();

            $order = Order::find($orderId);
            Log::info('$order', ['order' => $order]);

            if (!$order) {
                throw new \Exception('Order not found');
            }

            $order->update([
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'payment_method' => $paymentData['payment_method'] ?? null,
                'payment_transaction_id' => $paymentData['transaction_id'] ?? null,
                'payment_data' => $paymentData,
                'paid_at' => now()
            ]);

            // Xóa chỉ các sản phẩm đã thanh toán khỏi giỏ hàng
            if ($order->user_id) {

                $userCart = Cart::where('user_id', $order->user_id)->first();

                if (!$userCart) {
                    Log::warning('User cart not found', [
                        'user_id' => $order->user_id
                    ]);
                    return;
                }

                // Lấy productVariant_id đã thanh toán
                $paidProductVariantIds = $order->orderItems()
                    ->pluck('productVariant_id')
                    ->toArray();

                Log::info('Paid product variants', [
                    'product_variant_ids' => $paidProductVariantIds
                ]);

                // Lấy cart_item ID cần xóa
                $targetCartItemIds = CartItem::where('cart_id', $userCart->id)
                    ->whereIn('productVariant_id', $paidProductVariantIds)
                    ->pluck('id')
                    ->toArray();

                Log::info('Target cart items before delete', [
                    'cart_id' => $userCart->id,
                    'cart_item_ids' => $targetCartItemIds
                ]);

                // ===== SESSION CART TRƯỚC KHI XÓA =====
                $sessionCart = session('cart', []);
                Log::info('Session cart before delete', $sessionCart);

                // ===== XÓA CART_ITEM TRONG DB =====
                if (!empty($targetCartItemIds)) {

                    $deletedCount = CartItem::destroy($targetCartItemIds);

                    Log::info('Delete cart items result', [
                        'expected_delete' => count($targetCartItemIds),
                        'actual_deleted' => $deletedCount
                    ]);
                }

                // ===== XÓA CART_ITEM TRONG SESSION =====
                if (!empty($sessionCart)) {

                    $filteredSessionCart = array_values(array_filter(
                        $sessionCart,
                        function ($item) use ($paidProductVariantIds) {
                            return !in_array($item['productVariant_id'], $paidProductVariantIds);
                        }
                    ));

                    if (empty($filteredSessionCart)) {
                        session()->forget('cart');
                        Log::info('Session cart cleared');
                    } else {
                        session(['cart' => $filteredSessionCart]);
                        Log::info('Session cart after delete', $filteredSessionCart);
                    }
                }

                // ===== ĐẾM LẠI CART_ITEM TRONG DB =====
                $remainingItemsCount = CartItem::where('cart_id', $userCart->id)->count();

                Log::info('Remaining cart items', [
                    'cart_id' => $userCart->id,
                    'remaining_items_count' => $remainingItemsCount
                ]);

                // ===== NẾU CART RỖNG → XÓA CART =====
                if ($remainingItemsCount === 0) {
                    $userCart->delete();

                    Log::info('Deleted empty cart', [
                        'cart_id' => $userCart->id
                    ]);
                }
            }



            DB::commit();

            Log::info('Order confirmed successfully', [
                'order_id' => $orderId,
                'payment_method' => $paymentData['payment_method'] ?? null
            ]);

            return $order->fresh(['orderItems.productVariant.product']);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error confirming order', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Cancel order pending quá hạn
     */
    public function cancelExpiredPendingOrders($hoursOld = 24)
    {
        try {
            $expiredOrders = Order::where('status', Order::STATUS_PENDING)
                ->where('payment_status', Order::PAYMENT_STATUS_PENDING)
                ->where('created_at', '<', now()->subHours($hoursOld))
                ->get();

            $cancelledCount = 0;

            foreach ($expiredOrders as $order) {
                $order->update([
                    'status' => Order::STATUS_CANCELLED,
                    'payment_status' => Order::PAYMENT_STATUS_FAILED
                ]);
                $cancelledCount++;
            }

            Log::info('Cancelled expired pending orders', [
                'cancelled_count' => $cancelledCount,
                'hours_old' => $hoursOld
            ]);

            return $cancelledCount;

        } catch (\Exception $e) {
            Log::error('Error cancelling expired orders', [
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Lấy pending order của user hiện tại
     */
    public function getCurrentPendingOrder()
    {
        $userId = Auth::id();

        if (!$userId) {
            return null;
        }

        return Order::where('user_id', $userId)
            ->where('status', Order::STATUS_PENDING)
            ->where('payment_status', Order::PAYMENT_STATUS_PENDING)
            ->with(['orderItems.productVariant.product'])
            ->first();
    }

    /**
     * Kiểm tra và tạo order cho guest user (không đăng nhập)
     */
    public function createGuestOrder($cartItems, $shippingType, $customerInfo)
    {

        try {
            DB::beginTransaction();

            $orderData = $this->calculateOrderData($cartItems, $shippingType, $customerInfo);
            Log::info('Customer Info', $customerInfo);

            $orderData['id'] = $this->generateOrderId();
            $orderData['customer_name'] = $customerInfo['name'];
            $orderData['customer_email'] = $customerInfo['email'];
            $orderData['customer_phone'] = $customerInfo['phone'];
            $orderData['customer_address'] = $customerInfo['detail_address'];
            $orderData['user_id'] = null; // Guest order
            $orderData['order_number'] = Order::generateOrderNumber();
            $orderData['status'] = Order::STATUS_PENDING;
            $orderData['payment_status'] = Order::PAYMENT_STATUS_PENDING;

            $order = Order::create($orderData);

            // Tạo order items
            $this->createOrderItems($order, $cartItems);

            DB::commit();

            Log::info('Created guest order', [
                'order_id' => $order->id,
                'customer_email' => $customerInfo['email'] ?? 'no-email',
                'total' => $order->total
            ]);

            return $order->fresh(['orderItems.productVariant.product']);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating guest order', [
                'error' => $e->getMessage(),
                'customer_info' => $customerInfo
            ]);

            throw $e;
        }
    }
}
