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
        Log::info('Cart items', $items->toArray());
        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items,
                'total' => $total,
                'count' => $count
            ]
        ]);
    }
    public function CartItem(){
        $user = auth()->user();
        return Cart::where('user_id', $user->id)->where('status', 'active')->first();
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
//        Log::info('total price', $totalPrice);
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

        // Cập nhật lại tổng số lượng và tổng giá trị giỏ hàng
        $cart = $cartItem->cart;
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
            'message' => 'Cart item updated successfully!',
            'item' => $cartItem
        ]);
    }

    public function checkout(Request $request)
    {
//        dd($request->all());
        Log::info($request->all());
        $selectedItems = $request->input('items');
        Log::info('selectedItems', $selectedItems);

        $checkoutId = uniqid(); // Tạo một ID duy nhất
        session()->put("checkout_data_{$checkoutId}", $selectedItems);

        return response()->json(['checkout_id' => $checkoutId]);
    }

    public function syncCart(Request $request)
    {
        $user = auth()->user();

        // Get session cart
        $sessionCart = session('cart', []);

        // Get or create user's cart
        $userCart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'status' => 'active'
        ]);

        // Sync session cart items with database
        foreach ($sessionCart as $sessionItem) {
            $dbItem = $userCart->items()->where('productVariant_id', $sessionItem['productVariant_id'])->first();

            if ($dbItem) {
                // If exists in both, take the maximum quantity
                $maxQty = max($dbItem->quantity, $sessionItem['quantity']);
                $dbItem->update(['quantity' => $maxQty]);
            } else {
                // If only in session, add to database
                $productVariant = \App\Models\ProductVariant::find($sessionItem['productVariant_id']);
                if ($productVariant) {
                    $price = $productVariant->sale_price ?? $productVariant->price;
                    $userCart->items()->create([
                        'productVariant_id' => $sessionItem['productVariant_id'],
                        'quantity' => $sessionItem['quantity'],
                        'price' => $price,
                    ]);
                }
            }
        }

        // Update cart totals
        $cartItems = $userCart->items()->get();
        $totalQuantity = $cartItems->sum('quantity');
        $totalPrice = $cartItems->reduce(function ($carry, $item) {
            return $carry + ($item->quantity * $item->price);
        }, 0);

        $userCart->update([
            'total_quantity' => $totalQuantity,
            'total_price' => $totalPrice,
        ]);

        // Sau khi sync, lấy lại cart từ database và lưu lại vào session
        $sessionCartNew = [];
        foreach ($cartItems as $cartItem) {
            $sessionCartNew[] = [
                'productVariant_id' => $cartItem->productVariant_id,
                'quantity' => $cartItem->quantity,
                'selected' => $cartItem->selected ?? 1 // Nếu có selected thì giữ lại, không thì mặc định là 1
            ];
        }
        session(['cart' => $sessionCartNew]);

        // Get updated cart items with relationships
        $updatedItems = \App\Models\CartItem::with('productVariant.product.media')
            ->where('cart_id', $userCart->id)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Cart synced successfully',
            'cart' => $updatedItems->toArray()
        ]);
    }

    public function syncSessionToDatabase()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        // Sync cart
        $this->syncCart(request());

        // Sync wishlist
        $sessionWishlist = session('wishlist', []);
        foreach ($sessionWishlist as $item) {
            \App\Models\Wishlist::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $item['product_id']
            ]);
        }

        // Clear session wishlist after sync
        session()->forget('wishlist');

        return response()->json([
            'success' => true,
            'message' => 'Session data synced to database successfully'
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
