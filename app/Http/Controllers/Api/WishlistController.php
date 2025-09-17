<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    // Lấy danh sách sản phẩm yêu thích của user
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $productIds = Wishlist::where('user_id', $user->id)->pluck('product_id');
        $products = Product::with('media', 'variants')->whereIn('id', $productIds)->get();
        return response()->json($products);
    }

    // Thêm sản phẩm vào wishlist
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        $exists = Wishlist::where('user_id', $user->id)
            ->where('product_id', $validated['product_id'])
            ->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Product already in wishlist'], 409);
        }
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $validated['product_id'],
        ]);
        return response()->json(['success' => true, 'message' => 'Added to wishlist', 'data' => $wishlist], 201);
    }

    // Xóa sản phẩm khỏi wishlist
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $deleted = Wishlist::where('user_id', $user->id)
            ->where('product_id', $id)
            ->delete();
        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Removed from wishlist']);
        } else {
            return response()->json(['success' => false, 'message' => 'Product not found in wishlist'], 404);
        }
    }

    // Sync wishlist from session to database when user logs in
    public function syncWishlist(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $sessionWishlist = session('wishlist', []);

        foreach ($sessionWishlist as $item) {
            \App\Models\Wishlist::firstOrCreate([
                'user_id' => $user->id,
                'product_id' => $item['product_id']
            ]);
        }

        // Sau khi sync, lấy lại wishlist từ database và lưu lại vào session
        $wishlistDb = \App\Models\Wishlist::where('user_id', $user->id)->get();
        $sessionWishlistNew = [];
        foreach ($wishlistDb as $wishlistItem) {
            $sessionWishlistNew[] = [
                'product_id' => $wishlistItem->product_id,
                // Nếu có selected trong session cũ thì giữ lại, không thì mặc định là 1
                'selected' => collect($sessionWishlist)->firstWhere('product_id', $wishlistItem->product_id)['selected'] ?? 1
            ];
        }
        session(['wishlist' => $sessionWishlistNew]);

        // Get updated wishlist
        $wishlistProducts = \App\Models\Product::with('media')
            ->whereIn('id', \App\Models\Wishlist::where('user_id', $user->id)->pluck('product_id'))
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist synced successfully',
            'data' => $wishlistProducts
        ]);
    }

    // Get wishlist IDs for frontend checking
    public function getWishlistIds()
    {
        $user = Auth::user();

        if ($user) {
            $wishlistIds = \App\Models\Wishlist::where('user_id', $user->id)->pluck('product_id');
        } else {
            $sessionWishlist = session('wishlist', []);
            $wishlistIds = collect($sessionWishlist)->pluck('product_id');
        }

        return response()->json([
            'success' => true,
            'data' => $wishlistIds
        ]);
    }
}
