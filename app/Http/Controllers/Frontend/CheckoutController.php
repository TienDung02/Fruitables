<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request, $checkoutId)
    {
        // Lấy dữ liệu giỏ hàng từ session
        $selectedItems = session()->get("checkout_data_{$checkoutId}");

        if (!$selectedItems) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy thông tin giỏ hàng.');
        }

        return Inertia::render('Frontend/Checkout/Index', [
            'auth' => [
                'user' => auth()->user(),
            ],
            'csrf_token' => csrf_token(),
            'selectedItems' => $selectedItems,
            'checkoutId' => $checkoutId
        ]);
    }
}
