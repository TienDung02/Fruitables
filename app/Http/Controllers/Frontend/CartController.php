<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Http;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Gọi API lấy dữ liệu giỏ hàng
//        $response = Http::withToken(auth()->user()->api_token ?? null)
//            ->get(route('api.cart.index'));
//        $cartData = $response->json('data');

        return Inertia::render('Frontend/Cart/Index', [
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }
}
