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

        $total = $cartItems->sum('total');

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
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check if item already exists in cart
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $validated['quantity'],
                'price' => $product->effective_price
            ]);
        }

        $cartCount = Cart::where('user_id', auth()->id())->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount
        ]);
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
