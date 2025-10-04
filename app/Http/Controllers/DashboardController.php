<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with all product types.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return $this->getDashboardData($request);
        }

        // Return view for web interface
        return view('dashboard.index');
    }

    /**
     * Get dashboard data via API.
     */
    public function getDashboardData(Request $request): JsonResponse
    {
        try {
            $data = [
                'categories' => $this->getParentCategories(),
                'products_by_category' => $this->getProductsByCategory($request),
                'statistics' => $this->getStatistics()
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent categories (categories with parent_id = null).
     */
    private function getParentCategories()
    {
        return \App\Models\Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    /**
     * Get products grouped by category.
     */
    private function getProductsByCategory(Request $request)
    {
        $limit = $request->get('products_per_category', 8);
        $parentCategories = $this->getParentCategories();

        $productsByCategory = [];

        // Thêm tab "All Products"
        $productsByCategory['all'] = Product::with(['category', 'media', 'variants'])
            ->where('is_active', true)
            ->withCount('approvedReviews as reviews_count')
            ->withAvg('approvedReviews as average_rating', 'rating')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->transform(function ($product) {
                $product->average_rating = round($product->average_rating ?? 0, 1);
                return $product;
            });

        // Lấy sản phẩm cho từng category (bao gồm cả category con)
        foreach ($parentCategories as $category) {
            // Lấy danh sách ID của category cha và tất cả category con
            $categoryIds = $this->getCategoryAndChildrenIds($category->id);

            $productsByCategory[$category->slug] = Product::with(['category', 'media', 'variants'])
                ->whereIn('category_id', $categoryIds)
                ->where('is_active', true)
                ->withCount('approvedReviews as reviews_count')
                ->withAvg('approvedReviews as average_rating', 'rating')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->transform(function ($product) {
                    $product->average_rating = round($product->average_rating ?? 0, 1);
                    return $product;
                });
            Log::info('Fetched ' . count($productsByCategory[$category->slug]) . ' products for category ' . $category->name . ' and its children (IDs: ' . implode(',', $categoryIds) . ')');
        }

        return $productsByCategory;
    }

    /**
     * Get category ID and all its children IDs recursively.
     */
    private function getCategoryAndChildrenIds($categoryId)
    {
        $categoryIds = [$categoryId]; // Bắt đầu với ID của category cha

        // Lấy tất cả category con trực tiếp
        $childCategories = \App\Models\Category::where('parent_id', $categoryId)
            ->where('is_active', true)
            ->get(['id']);

        // Đệ quy để lấy category con của category con (nếu có)
        foreach ($childCategories as $childCategory) {
            $childIds = $this->getCategoryAndChildrenIds($childCategory->id);
            $categoryIds = array_merge($categoryIds, $childIds);
        }

        return array_unique($categoryIds);
    }

    /**
     * Get dashboard statistics.
     */
    private function getStatistics()
    {
        return [
            'total_products' => Product::where('is_active', true)->count(),
            'out_of_stock_count' => Product::whereHas('variants', function($query) {
                $query->where('stock_quantity', '<=', 0);
            })->where('is_active', true)->count()
        ];
    }

    /**
     * Get all products endpoint.
     */
    public function allProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->getAllProducts($request);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch all products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get featured products endpoint.
     */
    public function featuredProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->getFeaturedProducts($request);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured products',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bestselling products endpoint.
     */
    public function bestsellingProducts(Request $request): JsonResponse
    {
        try {
            $products = $this->getBestsellingProducts($request);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch bestselling products',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
