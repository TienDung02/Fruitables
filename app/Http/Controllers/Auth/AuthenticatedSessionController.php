<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Wishlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'login_error' => session('login_error'),



        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // Tự động sync cart và wishlist sau khi đăng nhập thành công
            $this->syncDataAfterLogin($request);

            return redirect()->intended(route('dashboard', absolute: false))->with('success', 'Đăng nhập thành công! Chào mừng bạn quay trở lại.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // VULNERABILITY: User Enumeration Attack
            // Kiểm tra email có tồn tại trong hệ thống không


//            $user = \App\Models\User::where('email', $request->email)->first();
//            if (!$user) {
//                // Email không tồn tại
//                return back()->withErrors([
//                    'email' => 'Email does not exist in system.',
//                ])->withInput($request->only('email', 'remember'))->with('login_error', 'Email does not exist in system!');
//            } else {
//                // Email tồn tại nhưng password sai
//                return back()->withErrors([
//                    'password' => 'Incorrect password.',
//                ])->withInput($request->only('email', 'remember'))->with('login_error', 'Incorrect password!');
//            }


            return back()->withErrors([
                'email' => 'Incorrect email or password.',
            ])->withInput($request->only('email', 'remember'))->with('login_error', 'Incorrect email or password!');


        }
    }

    /**
     * Sync cart and wishlist from session to database after login
     */
    private function syncDataAfterLogin(Request $request): void
    {
        try {
            // Sync Cart từ CartController
            $cartController = new \App\Http\Controllers\Api\CartController();
            $cartController->syncCart($request);
            // Sync Wishlist từ WishlistController
            $wishlistController = new \App\Http\Controllers\Api\WishlistController();
            $wishlistController->syncWishlist($request);
            // Sync Wishlist từ WishlistController
            $checkoutSessionService = new \App\Services\CheckoutSessionService();
            $checkoutSessionService->syncFromDatabase();
            $wishlist = $request->session()->get('wishlist', []);
            Log::info('Wishlist data after sync:', is_array($wishlist) ? $wishlist : ['data' => $wishlist]);
        } catch (\Exception $e) {
            Log::error('Error syncing data after login: ' . $e->getMessage());
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Lưu cart, wishlist và shipping info từ session trước khi logout
        $wishlist = $request->session()->get('wishlist', []);
        $cart = $request->session()->get('cart', []);
        $shippingInfo = $request->session()->get('checkout_shipping_info', []);

        Log::info('wishlist before logout:', is_array($wishlist) ? $wishlist : ['data' => $wishlist]);
        Log::info('cart before logout:', is_array($cart) ? $cart : ['data' => $cart]);
        Log::info('shipping info before logout:', is_array($shippingInfo) ? $shippingInfo : ['data' => $shippingInfo]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Lưu lại cart, wishlist và shipping info vào session mới
        $request->session()->put('cart', $cart);
        $request->session()->put('wishlist', $wishlist);
//        $request->session()->put('checkout_shipping_info', $shippingInfo);

        Log::info('Session data restored after logout');

        // Redirect với flash message để trigger menu reload
        return redirect()->route('dashboard')->with([
            'logout_success' => true,
            'reload_menu' => true
        ]);
    }
}
