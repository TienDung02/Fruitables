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

    public function buyNow(Request $request)
    {
        Log::info('Buy Now request received', $request->all());

        $validated = $request->validate([
            'buy_now' => 'required|boolean',
            'items' => 'required|array',
            'items.*.productVariant_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.sale_price' => 'required|numeric',
            'type' => 'required|string'
        ]);

        // Lưu dữ liệu vào session với key đặc biệt cho buy_now
        $checkoutId = uniqid('buy_now_');
        session()->put("checkout_data_{$checkoutId}", $validated['items']);

        Log::info('Buy Now data stored in session', [
            'checkout_id' => $checkoutId,
            'items' => $validated['items']
        ]);

        // Render trang checkout với dữ liệu buy_now
        return Inertia::render('Frontend/Checkout/Index', [
            'auth' => [
                'user' => auth()->user(),
            ],
            'csrf_token' => csrf_token(),
            'selectedItems' => $validated['items'],
            'checkoutId' => $checkoutId,
            'buyNow' => true // Flag để phân biệt với checkout thông thường
        ]);
    }
}
