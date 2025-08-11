<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('products/featured', [ProductController::class, 'featured']);

// Public routes
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

Route::get('products/on-sale', [ProductController::class, 'onSale']);

// Protected routes (requires authentication)
Route::middleware('Auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart management
    Route::get('cart', [CartController::class, 'index']);
    Route::post('cart', [CartController::class, 'store']);
    Route::put('cart/{cart}', [CartController::class, 'update']);
    Route::delete('cart/{cart}', [CartController::class, 'destroy']);
    Route::delete('cart', [CartController::class, 'clear']);
    Route::get('cart/count', [CartController::class, 'count']);

    // Order management
    Route::get('orders', [OrderController::class, 'index']);
    Route::post('orders', [OrderController::class, 'store']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::put('orders/{order}/cancel', [OrderController::class, 'cancel']);
});

// Admin routes (requires admin role)
Route::middleware(['Auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Categories management
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

    // Products management
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);

    // Orders management
    Route::get('orders', [OrderController::class, 'adminIndex']);
    Route::put('orders/{order}/status', [OrderController::class, 'updateStatus']);
});
