<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SessionController extends Controller
{
    /**
     * Get session cart with product details
     */
    public function getSessionCart(): JsonResponse
    {
        $sessionCart = session('cart', []);
        Log::info('sessionCart', $sessionCart);
        $cartItems = [];

        if (!empty($sessionCart)) {
            foreach ($sessionCart as $item) {
                $productVariant = ProductVariant::with(['product.media', 'product.category'])
                    ->find($item['productVariant_id']);

                if ($productVariant) {
                    $cartItems[] = [
                        'productVariant_id' => $item['productVariant_id'],
                        'quantity' => $item['quantity'],
                        'product_variant' => $productVariant,
                        'selected' => array_key_exists('selected', $item) ? $item['selected'] : 1,
                    ];
                }
            }
        }

        $total = collect($cartItems)->reduce(function ($carry, $item) {
            $price = $item['product_variant']->sale_price ?? $item['product_variant']->price;
            return $carry + ($price * $item['quantity']);
        }, 0);

        $count = collect($cartItems)->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'count' => $count
            ]
        ]);
    }

    /**
     * Add item to session cart
     */
    public function addToSessionCart(Request $request): JsonResponse
    {
        $request->validate([
            'productVariant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionCart = session('cart', []);
        $productVariantId = $request->productVariant_id;
        $quantity = $request->quantity;
        $selected = $request->has('selected') ? $request->selected : 1;

        // Find existing item in cart
        $existingIndex = collect($sessionCart)->search(function ($item) use ($productVariantId) {
            return $item['productVariant_id'] == $productVariantId;
        });

        if ($existingIndex !== false) {
            // Update existing item
            $sessionCart[$existingIndex]['quantity'] += $quantity;
            $sessionCart[$existingIndex]['selected'] = $selected;
        } else {
            // Add new item
            $sessionCart[] = [
                'productVariant_id' => $productVariantId,
                'quantity' => $quantity,
                'selected' => $selected,
            ];
        }

        session(['cart' => $sessionCart]);

        return response()->json([
            'success' => true,
            'message' => 'Item added to cart successfully',
            'cart_count' => collect($sessionCart)->sum('quantity')
        ]);
    }

    /**
     * Update session cart item quantity
     */
    public function updateSessionCart(Request $request): JsonResponse
    {
        $request->validate([
            'productVariant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionCart = session('cart', []);
        $productVariantId = $request->productVariant_id;
        $quantity = $request->quantity;
        $selected = $request->has('selected') ? $request->selected : null;

        $existingIndex = collect($sessionCart)->search(function ($item) use ($productVariantId) {
            return $item['productVariant_id'] == $productVariantId;
        });

        if ($existingIndex !== false) {
            $sessionCart[$existingIndex]['quantity'] = $quantity;
            if ($selected !== null) {
                $sessionCart[$existingIndex]['selected'] = $selected;
            }
            session(['cart' => $sessionCart]);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_count' => collect($sessionCart)->sum('quantity')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    /**
     * Remove item from session cart
     */
    public function removeFromSessionCart(Request $request): JsonResponse
    {
        $request->validate([
            'productVariant_id' => 'required|exists:product_variants,id',
        ]);

        $sessionCart = session('cart', []);
        $productVariantId = $request->productVariant_id;

        $sessionCart = collect($sessionCart)->filter(function ($item) use ($productVariantId) {
            return $item['productVariant_id'] != $productVariantId;
        })->values()->toArray();

        session(['cart' => $sessionCart]);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'cart_count' => collect($sessionCart)->sum('quantity')
        ]);
    }

    /**
     * Update selected status in session wishlist
     */
    public function updateSessionWishlistSelected(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'selected' => 'required|boolean',
        ]);

        $sessionWishlist = session('wishlist', []);
        $productId = $request->product_id;
        $selected = $request->selected;

        $existsIndex = collect($sessionWishlist)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existsIndex !== false) {
            $sessionWishlist[$existsIndex]['selected'] = $selected;
            session(['wishlist' => $sessionWishlist]);
            return response()->json([
                'success' => true,
                'message' => 'Wishlist selected updated'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in wishlist'
        ], 404);
    }

    /**
     * Add item to session wishlist
     */
    public function addToSessionWishlist(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $sessionWishlist = session('wishlist', []);
        $productId = $request->product_id;
        $selected = $request->has('selected') ? $request->selected : 1;

        // Check if already exists
        $existsIndex = collect($sessionWishlist)->search(function ($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existsIndex === false) {
            $sessionWishlist[] = ['product_id' => $productId, 'selected' => $selected];
            session(['wishlist' => $sessionWishlist]);
        } else {
            // Nếu đã tồn tại thì cập nhật selected
            $sessionWishlist[$existsIndex]['selected'] = $selected;
            session(['wishlist' => $sessionWishlist]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item added to wishlist'
        ]);
    }

    /**
     * Get session wishlist with product details
     */
    public function getSessionWishlist(): JsonResponse
    {
        $sessionWishlist = session('wishlist', []);
        Log::info('sessionWishlist', $sessionWishlist);
        $wishlistItems = [];

        if (!empty($sessionWishlist)) {
            $productIds = collect($sessionWishlist)->pluck('product_id');
            $products = Product::with(['media', 'category', 'variants'])
                ->whereIn('id', $productIds)
                ->get();

            // Gắn trạng thái selected vào từng sản phẩm
            foreach ($products as $product) {
                $item = collect($sessionWishlist)->firstWhere('product_id', $product->id);
                $product->selected = $item['selected'] ?? 1;
                $wishlistItems[] = $product;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $wishlistItems
        ]);
    }

    /**
     * Clear session cart
     */
    public function clearSessionCart(): JsonResponse
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared'
        ]);
    }

    /**
     * Clear session wishlist
     */
    public function clearSessionWishlist(): JsonResponse
    {
        session()->forget('wishlist');

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared'
        ]);
    }

    /**
     * Get session cart count
     */
    public function getSessionCartCount(): JsonResponse
    {
        $sessionCart = session('cart', []);
        $count = collect($sessionCart)->sum('quantity');

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
