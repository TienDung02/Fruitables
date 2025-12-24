<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
class OrderController extends Controller
{
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
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validated = $request->validate([
            'shipping_address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Get cart items
        $cartItems = Cart::with('Products')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Calculate totals
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->effective_price;
        });
        $tax = $subtotal * 0.1; // 10% tax
        $total = $subtotal + $tax;

        DB::beginTransaction();

        try {
            // Create order
            $order = Order::create([
                'id' => 'ORD_' . time() . '_' . \Illuminate\Support\Str::random(8), // Generate unique ID
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time() . '-' . $user->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'shipping_address' => $validated['shipping_address'],
                'phone' => $validated['phone'],
                'notes' => $validated['notes'] ?? null
            ]);

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->effective_price
                ]);

                // Update Products stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'data' => $order->load(['orderItems.Products.media'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Failed to place order'
            ], 500);
        }
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
