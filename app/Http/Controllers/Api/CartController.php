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
class CartController extends Controller
{
    /**
     * Display the cart page.
     */
    public function index(): JsonResponse
    {
        $cartItems = Cart::with('Products', 'Products.Categories', 'Products.media')
            ->where('user_id', auth()->id())
            ->get();

        // Tính tổng tiền: dùng sale_price nếu có, ngược lại dùng price
        $total = $cartItems->reduce(function ($carry, $item) {
            $product = $item->Products;
            $price = $product->sale_price ?? $product->price;
            return $carry + ($price * $item->quantity);
        }, 0);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'count' => $cartItems->sum('quantity')
            ]
        ]);
    }

    /**
     * Add Products to cart.
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Bạn cần đăng nhập để thêm sản phẩm vào giỏ hàng!'], 401);
        }

        // 2. Tìm kiếm hoặc tạo mới item trong giỏ hàng
        $cartItem = Cart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Nếu sản phẩm đã tồn tại, tăng số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Nếu sản phẩm chưa có, tạo mới
            Cart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        // 3. Trả về phản hồi thành công
        return response()->json(['message' => 'Đã thêm sản phẩm vào giỏ hàng thành công!'], 200);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request, Cart $cart): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Ensure user owns this cart item
        if ($cart->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cart->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully!',
            'total' => $cart->total
        ]);
    }

    /**
     * Remove cart item.
     */
    public function destroy(Cart $cart): JsonResponse
    {
        // Ensure user owns this cart item
        if ($cart->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cart->delete();

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
        $count = Cart::where('user_id', auth()->id())->sum('quantity');

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
