<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MoMoPaymentService;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MoMoPaymentController extends Controller
{
    private $momoService;

    public function __construct(MoMoPaymentService $momoService)
    {
        $this->momoService = $momoService;
    }

    /**
     * Tạo thanh toán MoMo
     */
    public function createPayment(Request $request)
    {
        Log::info('Create MoMo Payment Request received:', $request->all());
        $request->validate([
            'items' => 'required|array',
            'shipping_type' => 'required|string|in:free,standard,fast',
            'customer_info' => 'required|array',
            'customer_info.name' => 'required|string',
            'customer_info.email' => 'required|email',
            'customer_info.phone' => 'required|string',
            'customer_info.address' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Tính toán giá tiền
            $subtotal = 0;
            foreach ($request->items as $item) {
                $price = $item['product_variant']['sale_price'] ?? $item['product_variant']['price'];
                $subtotal += $price * $item['quantity'];
            }

            // Phí ship
            $shippingCosts = [
                'free' => 0,
                'standard' => 3,
                'fast' => 6
            ];
            $shippingCost = $shippingCosts[$request->shipping_type];
            $total = $subtotal + $shippingCost;

            // Tạo đơn hàng
            $orderId = 'ORD-' . strtoupper(Str::random(8));

            Log::info('Creating order with data:', [
                'id' => $orderId,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'shipping_method' => $request->shipping_type,
                'customer_info' => $request->customer_info,
            ]);

            $order = Order::create([
                'id' => $orderId,
                'user_id' => auth()->id(), // Có thể null nếu chưa đăng nhập
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => 'momo',
                'payment_status' => 'pending',
                'shipping_method' => $request->shipping_type,
                'customer_info' => $request->customer_info,
                'notes' => $request->notes ?? null,
            ]);

            Log::info('Order created:', ['order_id' => $order->id]);

            // Tạo order items
            foreach ($request->items as $item) {
                // Sử dụng productVariant_id thay vì product_variant_id để khớp với dữ liệu từ Vue
                $productVariantId = $item['productVariant_id'] ?? $item['product_variant_id'] ?? null;

                if (!$productVariantId) {
                    throw new \Exception('Missing product variant ID in item: ' . json_encode($item));
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $productVariantId,
                    'quantity' => $item['quantity'],
                    'price' => $item['product_variant']['sale_price'] ?? $item['product_variant']['price'],
                ]);

                Log::info('OrderItem created for variant:', ['product_variant_id' => $productVariantId]);
            }
            // Tạo thanh toán MoMo
            $orderInfo = "Thanh toán đơn hàng #{$order->id}";
            $usdToVndRate = 25000;
            $amount = (int) round($total * $usdToVndRate);
            $paymentResult = $this->momoService->createPayment(
                $order->id,
                $amount,
                $orderInfo
            );

            if ($paymentResult['success']) {
                // Lưu thông tin thanh toán
                $order->update([
                    'payment_request_id' => $paymentResult['requestId'],
                    'payment_data' => $paymentResult
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'payment_url' => $paymentResult['payUrl'],
                    'qr_code_url' => $paymentResult['qrCodeUrl'] ?? null,
                    'deeplink' => $paymentResult['deeplink'] ?? null,
                    'amount' => $amount,
                    'message' => 'Tạo thanh toán thành công'
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Create MoMo Payment Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xử lý callback từ MoMo (IPN)
     */
    public function handleCallback(Request $request)
    {
        Log::info('MoMo Callback Received', $request->all());

        try {
            // Xác minh chữ ký
            if (!$this->momoService->verifySignature($request->all())) {
                Log::error('MoMo Invalid Signature', $request->all());
                return response()->json(['message' => 'Invalid signature'], 400);
            }

            $orderId = $request->orderId;
            $resultCode = $request->resultCode;
            $transId = $request->transId;

            $order = Order::find($orderId);
            if (!$order) {
                Log::error('Order not found', ['orderId' => $orderId]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            if ($resultCode == 0) {
                // Thanh toán thành công
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'transaction_id' => $transId,
                    'paid_at' => now()
                ]);

                // Xóa giỏ hàng nếu user đã đăng nhập
                if ($order->user_id) {
                    \App\Models\Cart::where('user_id', $order->user_id)->delete();
                }

                Log::info('Payment Success', ['orderId' => $orderId, 'transId' => $transId]);
            } else {
                // Thanh toán thất bại
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);

                Log::info('Payment Failed', ['orderId' => $orderId, 'resultCode' => $resultCode]);
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('MoMo Callback Error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Trang xử lý sau khi thanh toán (return URL)
     */
    public function handleReturn(Request $request)
    {
        $orderId = $request->orderId;
        $resultCode = $request->resultCode;

        $order = Order::find($orderId);

        if ($resultCode == 0) {
            // Thanh toán thành công
            return redirect()->route('checkout.success', ['order' => $orderId])
                ->with('success', 'Thanh toán thành công!');
        } else {
            // Thanh toán thất bại
            return redirect()->route('checkout.failed', ['order' => $orderId])
                ->with('error', 'Thanh toán thất bại!');
        }
    }

    /**
     * Kiểm tra trạng thái thanh toán
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'orderId' => 'required|string'
        ]);

        try {
            $order = Order::find($request->orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'status' => $order->payment_status,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
                'transaction_id' => $order->transaction_id,
                'paid_at' => $order->paid_at
            ]);

        } catch (\Exception $e) {
            Log::error('Check Payment Status Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo lại QR code cho đơn hàng
     */
    public function regenerateQR(Request $request)
    {
        // Log request data để debug
        Log::info('Regenerate QR Request received:', [
            'all_data' => $request->all(),
            'order_id' => $request->order_id,
            'headers' => $request->headers->all()
        ]);

        $request->validate([
            'order_id' => 'required|string'
        ]);

        try {
            $order = Order::find($request->order_id);

            if (!$order) {
                Log::error('Order not found for regenerate QR:', ['order_id' => $request->order_id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            Log::info('Order found for regenerate QR:', [
                'order_id' => $order->id,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'status' => $order->status
            ]);

            // Kiểm tra xem đơn hàng có phải là MoMo payment không
            if ($order->payment_method !== 'momo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order is not using MoMo payment method'
                ], 400);
            }

            // Kiểm tra trạng thái đơn hàng
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order has already been paid'
                ], 400);
            }

            if ($order->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order has been cancelled'
                ], 400);
            }

            // Tạo orderId unique cho MoMo bằng cách thêm timestamp
            // Giữ nguyên orderId gốc trong database, chỉ thay đổi orderId gửi tới MoMo
            $uniqueOrderId = $order->id . '-R' . time(); // Ví dụ: ORD-OLYKZ4X6-R1758386310

            // Tạo lại payment request với MoMo với orderId unique
            $orderInfo = "Thanh toán đơn hàng #{$order->id} (Renewed)";
            $paymentResult = $this->momoService->createPayment(
                $uniqueOrderId, // Sử dụng orderId unique
                $order->total * 25000, // Convert USD to VND
                $orderInfo
            );

            Log::info('MoMo service result for regenerate QR:', [
                'success' => $paymentResult['success'] ?? false,
                'message' => $paymentResult['message'] ?? 'No message',
                'unique_order_id' => $uniqueOrderId
            ]);

            if ($paymentResult['success']) {
                // Cập nhật thông tin thanh toán mới
                $order->update([
                    'payment_request_id' => $paymentResult['requestId'],
                    'payment_data' => $paymentResult
                ]);

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id, // Trả về orderId gốc cho frontend
                    'payment_url' => $paymentResult['payUrl'],
                    'qr_code_url' => $paymentResult['qrCodeUrl'] ?? null,
                    'deeplink' => $paymentResult['deeplink'] ?? null,
                    'amount' => $order->total * 25000,
                    'message' => 'QR code regenerated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResult['message']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Regenerate QR Code Error', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage()
            ], 500);
        }
    }
}
