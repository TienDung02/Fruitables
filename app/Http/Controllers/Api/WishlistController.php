<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
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
        $products = \App\Models\Product::with('media')->whereIn('id', $productIds)->get();
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
}
