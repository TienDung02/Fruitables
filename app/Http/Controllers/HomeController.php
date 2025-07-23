<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index(): Response
    {
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('category')
            ->limit(8)
            ->get();

        $categories = Category::where('is_active', true)
            ->withCount('products')
            ->limit(6)
            ->get();

        return Inertia::render('Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }

    /**
     * Display the shop page.
     */
    public function shop(Request $request): Response
    {
        $query = Product::where('is_active', true)->with('category');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort by
        $sortBy = $request->get('sort', 'name');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return Inertia::render('Shop', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only(['category', 'search', 'min_price', 'max_price', 'sort'])
        ]);
    }

    /**
     * Display the contact page.
     */
    public function contact(): Response
    {
        return Inertia::render('Contact');
    }

    /**
     * Display the about page.
     */
    public function about(): Response
    {
        return Inertia::render('About');
    }
}
