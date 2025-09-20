<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request, $checkoutId)
    {
        Log::info('method checkout in CheckoutController called');
        // Lấy dữ liệu giỏ hàng từ session
        $selectedItems = session()->get("checkout_data_{$checkoutId}");
        Log::info('Selected Items: ', ['items' => $selectedItems]);

        if (!$selectedItems) {
            return redirect()->route('cart.index')->with('error', 'Không tìm thấy thông tin giỏ hàng.');
        }

        // Đảm bảo selectedItems là array
        if (!is_array($selectedItems)) {
            $selectedItems = [];
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
