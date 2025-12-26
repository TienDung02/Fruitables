<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Ward;
use App\Services\OrderService;
use App\Services\SepayPaymentService;
use App\Services\VietQRService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(
        OrderService $orderService,
    ) {
        $this->orderService = $orderService;
    }
    /**
     * Display a listing of user's orders.
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $orders = Order::with(['orderItems.Products.media'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        Log::info('Order store method called', $request->all());
        $checkoutId = $request->input('checkoutId');
        Log::info('checkoutId: ' . $checkoutId);
        $user = Auth::user();
        $session_key_data = 'checkout_data_' . $checkoutId;
        $session_key_type = 'checkout_type_' . $checkoutId;
        $type = session($session_key_type, []);
        $Cart = session($session_key_data, []);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }
        Log::info('sau check user', ['user_id' => $user->id]);
//        $request->merge($request);
        $validated = $request->validate([
            'items' => 'required|array',
            'shipping_type' => 'required|string|in:free,standard,fast',
            'payment_method' => 'required|string',
            'customer_info.name' => 'required|string',
            'customer_info.email' => 'required|email',
            'customer_info.phone' => 'required|string',
            'customer_info.address' => 'required|string',
            'customer_info.ward_id' => 'int',
        ]);
        if ($validated['customer_info']['ward_id'] == null) {
            $detail_adress = $validated['customer_info']['address'];
        }else {
            $ward_id = $validated['customer_info']['ward_id'];
            $ward = Ward::find($ward_id);
            $detail_adress = $validated['customer_info']['address'] . ', ' . $ward->name . ', ' . $ward->district->name . ', ' . $ward->district->province->name;
        }
        $validated['customer_info']['detail_address'] = $detail_adress;
        Log::info('sau check validate');
        if (Auth::check()) {
            Log::info('check user', ['user_id' => Auth::id()]);
            $order = $this->orderService->getOrCreatePendingOrder(
                $validated['items'],
                $validated['shipping_type'],
                $validated['customer_info'],
                $validated['payment_method']
            );
        }
        if ($order->user_id) {

            $userCart = Cart::where('user_id', $order->user_id)->first();

            if (!$userCart) {
                Log::warning('User cart not found', [
                    'user_id' => $order->user_id
                ]);
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


        return response()->json([
            'success' => true,
            'message' => 'Đặt hàng thành công!',
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $order->load(['orderItems.Products.media']);

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * Update order status (admin only).
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
        ]);

        $order->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $order
        ]);
    }

    /**
     * Cancel an order (user can only cancel pending orders).
     */
    public function cancel(Order $order): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be cancelled'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Restore Products stock
            foreach ($order->orderItems as $orderItem) {
                $orderItem->product->increment('stock_quantity', $orderItem->quantity);
            }

            // Update order status
            $order->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully',
                'data' => $order
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }

    /**
     * Get all orders (admin only).
     */
    public function adminIndex(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $orders = Order::with(['user', 'orderItems.Products'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
