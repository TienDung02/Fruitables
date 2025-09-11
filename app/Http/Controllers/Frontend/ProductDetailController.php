<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class ProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $query = Product::with(['category', 'media', 'variants'])
            ->where('is_active', true);



        $products = $query->where('id', $id)->firstOrFail();
        return Inertia::render('Frontend/Details/Index', [
            'data' => $products,
            'auth' => [
                'user' => auth()->user(),
            ],
            'csrf_token' => csrf_token(),
        ]);
    }
}
