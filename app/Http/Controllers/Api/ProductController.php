<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'media', 'variants'])
                ->where('is_active', true);

            //apply search filter
            $this->applySearchFilter($query, $request);

            //apply category filter
            $this->applyCategoryFilter($query, $request);

            //apply ordering
            $this->applyOrdering($query, $request);

            $products = $query->paginate(9);

            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function applySearchFilter($query, $request)
    {
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('short_description', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                        $categoryQuery->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }
    }

    private function applyCategoryFilter($query, $request)
    {
        if ($request->has('category_id') && $request->category_id) {
            $categoryId = $request->category_id;
            $category = Category::find($categoryId);

            if ($category && $category->children()->count() > 0) {
                $subcategoryIds = $category->children()->pluck('id')->toArray();
                $subcategoryIds[] = $categoryId;
                $query->whereIn('category_id', $subcategoryIds);
            } else {
                $query->where('category_id', $categoryId);
            }
        }
    }

    private function applyOrdering($query, $request)
    {
        $sortBy = $request->get('sort_by', 'newest');

        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'featured':
                $query->withAvg('approvedReviews', 'rating')
                      ->withCount('approvedReviews')
                      ->orderBy('approved_reviews_avg_rating', 'desc')
                      ->orderBy('approved_reviews_count', 'desc');
                break;
            case 'popularity':
                $query->withCount('approvedReviews')
                      ->orderBy('approved_reviews_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // If search is applied, add relevance ordering as secondary
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->orderByRaw("
                CASE
                    WHEN name LIKE ? THEN 1
                    WHEN short_description LIKE ? THEN 2
                    WHEN description LIKE ? THEN 3
                    ELSE 4
                END
            ", ["%{$searchTerm}%", "%{$searchTerm}%", "%{$searchTerm}%"]);
        }
    }

    public function featured()
    {
        $topRatedProducts = Product::with(['category', 'media', 'variants'])
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
