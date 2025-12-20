<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ProductDetailController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Api\MoMoPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home page
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');


// MoMo return URLs
Route::get('/payment/momo/return', [MoMoPaymentController::class, 'handleReturn'])->name('payment.momo.return');


Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/detail/{id}', [ProductDetailController::class, 'index'])->name('detail.index');
Route::get('/checkout/{checkoutId}', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/buyNow/{buyNow}', [CheckoutController::class, 'buyNow'])->name('checkout.buy-now');

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});

// Test translation route
Route::get('/test-translation', function () {
    $locale = App::getLocale();
    $translationPath = base_path("lang/{$locale}");
    $translations = [];

    if (file_exists($translationPath)) {
        $files = glob($translationPath . '/*.php');
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $translations[$key] = require $file;
        }
    }

    return response()->json([
        'locale' => $locale,
        'translations' => $translations,
        'session_locale' => Session::get('locale'),
        'config_locale' => config('app.locale')
    ]);
});

// Language switching routes
Route::get('/language/switch', [LanguageController::class, 'switch'])->name('language.switch.get');
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

require __DIR__.'/auth.php';

// Route 404 - PHẢI đặt cuối cùng để bắt tất cả routes không tồn tại
Route::fallback(function () {
    return Inertia::render('Frontend/404/Index', [], 404);
});
