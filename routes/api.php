<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\SessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('products/featured', [ProductController::class, 'featured']);

// Public routes
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

// Session-based routes (for non-authenticated users)
Route::prefix('session')->group(function () {
    Route::get('cart', [SessionController::class, 'getSessionCart']);
    Route::post('cart', [SessionController::class, 'addToSessionCart']);
    Route::put('cart', [SessionController::class, 'updateSessionCart']);
    Route::delete('cart', [SessionController::class, 'removeFromSessionCart']);
    Route::get('cart/count', [SessionController::class, 'getSessionCartCount']);
    Route::delete('cart/clear', [SessionController::class, 'clearSessionCart']);

    Route::get('wishlist', [SessionController::class, 'getSessionWishlist']);
    Route::post('wishlist', [SessionController::class, 'addToSessionWishlist']);
    Route::post('wishlist/selected', [SessionController::class, 'updateSessionWishlistSelected']);
    Route::delete('wishlist', [SessionController::class, 'removeFromSessionWishlist']);
    Route::delete('wishlist/clear', [SessionController::class, 'clearSessionWishlist']);
});

Route::get('products/on-sale', [ProductController::class, 'onSale']);

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart management
    Route::get('cart', [CartController::class, 'index']);
    Route::put('cart/{cartItem}', [CartController::class, 'update']);
    Route::post('cart', [CartController::class, 'store']);
    Route::post('cart/checkout', [CartController::class, 'checkout']);
    Route::delete('cart/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('cart', [CartController::class, 'clear']);
    Route::get('cart/count', [CartController::class, 'count']);
    Route::post('/cart/sync', [CartController::class, 'syncCart']);
    Route::post('/sync-session', [CartController::class, 'syncSessionToDatabase']);

    // Order management
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}/cancel', [OrderController::class, 'cancel']);

    // Wishlist management
    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('wishlist', [WishlistController::class, 'store']);
    Route::delete('wishlist/{id}', [WishlistController::class, 'destroy']);
    Route::post('wishlist/sync', [WishlistController::class, 'syncWishlist']);
    Route::get('wishlist/ids', [WishlistController::class, 'getWishlistIds']);
});

// Admin routes (requires admin role)
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Categories management
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    // Products management
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);

    // Orders management
    Route::get('orders', [OrderController::class, 'adminIndex']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus']);
});
