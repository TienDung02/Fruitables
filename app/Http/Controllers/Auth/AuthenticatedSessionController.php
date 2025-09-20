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
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Tự động sync cart và wishlist sau khi đăng nhập thành công
        $this->syncDataAfterLogin($request);

        return redirect()->intended(route('dashboard', absolute: false));
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
            Log::info('Cart synced successfully after login');
            Log::info('1');
            // Sync Wishlist từ WishlistController
            $wishlistController = new \App\Http\Controllers\Api\WishlistController();
            $wishlistController->syncWishlist($request);
            Log::info('Wishlist synced successfully after login');

        } catch (\Exception $e) {
            Log::error('Error syncing data after login: ' . $e->getMessage());
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Lưu cart và wishlist từ session trước khi logout
        $wishlist = $request->session()->get('wishlist', []);
        $cart = $request->session()->get('cart', []);

        Log::info('Session data before logout:', [
            'wishlist' => is_array($wishlist) ? $wishlist : ['data' => $wishlist],
            'cart' => is_array($cart) ? $cart : ['data' => $cart]
        ]);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Lưu lại cart và wishlist vào session mới
        $request->session()->put('cart', $cart);
        $request->session()->put('wishlist', $wishlist);

        Log::info('Session data restored after logout');

        // Redirect với flash message để trigger menu reload
        return redirect()->route('dashboard')->with([
            'logout_success' => true,
            'reload_menu' => true
        ]);
    }
}
