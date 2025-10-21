<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\ProductDetailController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Api\MoMoPaymentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// MoMo return URLs
Route::get('/payment/momo/return', [MoMoPaymentController::class, 'handleReturn'])->name('payment.momo.return');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/detail/{id}', [ProductDetailController::class, 'index'])->name('detail.index');
Route::get('/checkout/{checkoutId}', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/buyNow/{buyNow}', [CheckoutController::class, 'buyNow'])->name('checkout.buy-now');
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

    // Các routes profile đã được chuyển sang API
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/register');
})->name('register');

require __DIR__.'/auth.php';
