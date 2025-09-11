<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        $sessionId = session()->getId();
        $cart = null;
        if ($user) {
            $cart = \App\Models\Cart::where('user_id', $user->id)->where('status', 'active')->first();
        } else {
            $cart = \App\Models\Cart::where('session_id', $sessionId)->where('status', 'active')->first();
        }
        $items = [];
        $total = 0;
        $count = 0;
        if ($cart) {
            $items = \App\Models\CartItem::with('productVariant.product.media')
                ->where('cart_id', $cart->id)
                ->get();
            $total = $items->reduce(function ($carry, $item) {
                $price = $item->price;
                return $carry + ($price * $item->quantity);
            }, 0);
            $count = $items->sum('quantity');
        }
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $count
            ]
        ]);
    }

    /**
     * Add Products to cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'productVariant_id' => 'required|exists:product_variants,id', // Sửa 'products' thành 'product_variants'
            'quantity' => 'required|integer|min:1',
        ]);
        $user = auth()->user();
        $sessionId = session()->getId();
        if (!$user && !$sessionId) {
            return response()->json(['message' => 'Bạn cần đăng nhập hoặc có session để thêm sản phẩm vào giỏ hàng!'], 401);
        }
        Log::info('id', $request->all());
        // Tìm hoặc tạo cart
        $cart = \App\Models\Cart::firstOrCreate([
            'user_id' => $user ? $user->id : null,
            'session_id' => $user ? null : $sessionId,
            'status' => 'active',
        ]);
        // Tìm hoặc tạo cart_item
        $cartItem = \App\Models\CartItem::where('cart_id', $cart->id)
            ->where('productVariant_id', $request->productVariant_id)
            ->first();
//        Log::info($cartItem->id);
        $product = \App\Models\ProductVariant::find($request->productVariant_id);
        $price = $product->sale_price ?? $product->price;
        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            Log::info($cartItem->quantity);
            $cartItem->price = $price;
            $cartItem->save();
        } else {
            \App\Models\CartItem::create([
                'cart_id' => $cart->id,
                'productVariant_id' => $request->productVariant_id,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }
        // Tính lại tổng số lượng và tổng giá trị giỏ hàng
        $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)->get();
        $totalQuantity = $cartItems->sum('quantity');
        $totalPrice = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->quantity * $item->price);
        }, 0);
        $cart->update([
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ]);
        return response()->json(['message' => 'Đã thêm sản phẩm vào giỏ hàng thành công!'], 200);
    }

    /**
     * Update cart item quantity.
     */
    public function update(\App\Models\CartItem $cartItem, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Kiểm tra quyền sở hữu
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated successfully!',
            'item' => $cartItem
        ]);
    }

    /**
     * Remove cart item.
     */
    public function destroy(\App\Models\CartItem $cartItem): JsonResponse
    {
        // Kiểm tra quyền sở hữu
        if ($cartItem->cart->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();

        // Cập nhật lại tổng số lượng và tổng giá trị giỏ hàng
        $cartItems = \App\Models\CartItem::where('cart_id', $cart->id)->get();
        $totalQuantity = $cartItems->sum('quantity');
        $totalPrice = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->quantity * $item->price);
        }, 0);
        $cart->update([
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!'
        ]);
    }

    /**
     * Get cart count for navbar.
     */
    public function count(): JsonResponse
    {
//        dd(auth()->id());
        $count = Cart::where('user_id', auth()->id())->sum('total_quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Clear all cart items.
     */
    public function clear(): JsonResponse
    {
        Cart::where('user_id', auth()->id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!'
        ]);
    }
}
