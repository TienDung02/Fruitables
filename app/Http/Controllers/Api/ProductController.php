<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::with(['category', 'media'])
            ->withCount('approvedReviews as reviews_count')
            ->withAvg('approvedReviews as average_rating', 'rating')
            ->paginate(9);

        // Format the rating data
        $products->getCollection()->transform(function ($product) {
            $product->average_rating = round($product->average_rating ?? 0, 1);
            return $product;
        });

        return $products;
    }

    public function featured()
    {
        $topRatedProducts = Product::with(['category', 'media'])
            ->withAvg('approvedReviews', 'rating')
            ->withCount('approvedReviews')
            ->whereHas('approvedReviews') // Chỉ lấy products có ít nhất 1 review
            ->where('is_active', true)
            ->orderBy('approved_reviews_avg_rating', 'desc')
            ->orderBy('approved_reviews_count', 'desc') // Sắp xếp thứ 2 theo số lượng reviews
            ->limit(4)
            ->get();
        return $topRatedProducts;
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products',
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product->load(['Categories', 'media'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'media', 'approvedReviews']);

        // Calculate rating stats
        $product->loadCount('approvedReviews as reviews_count');
        $product->loadAvg('approvedReviews as average_rating', 'rating');
        $product->average_rating = round($product->average_rating ?? 0, 1);

        // Get related products from same category
        $relatedProducts = Product::with(['category', 'media'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->withCount('approvedReviews as reviews_count')
            ->withAvg('approvedReviews as average_rating', 'rating')
            ->limit(4)
            ->get();

        // Format rating for related products
        $relatedProducts->transform(function ($item) {
            $item->average_rating = round($item->average_rating ?? 0, 1);
            return $item;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $product,
                'related_products' => $relatedProducts
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug,' . $product->id,
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'stock_quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product->load(['Categories', 'media'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return  response()->noContent();
//        return response()->json([
//            'success' => true,
//            'message' => 'Product deleted successfully'
//        ]);
    }

    /**
     * Get featured products.
     */
//    public function featured(): JsonResponse
//    {
//        $products = Product::where('is_featured', true)
//            ->where('is_active', true)
//            ->with(['Categories', 'media'])
//            ->limit(8)
//            ->get();
//
//        return response()->json([
//            'success' => true,
//            'data' => $products
//        ]);
//    }

    /**
     * Get products on sale.
     */
    public function onSale(): JsonResponse
    {
        $products = Product::whereNotNull('sale_price')
            ->where('is_active', true)
            ->with(['Categories', 'media'])
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
