<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\MoMoPaymentController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LocationController;
//use App\Http\Controllers\Api\UserProfileController;
//use App\Http\Controllers\Api\UserNotificationController;
//use App\Http\Controllers\Api\UserOrderController;
//use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Dashboard routes
Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('dashboard/data', [DashboardController::class, 'getDashboardData']);
Route::get('dashboard/products/all', [DashboardController::class, 'allProducts']);
Route::get('dashboard/products/featured', [DashboardController::class, 'featuredProducts']);
Route::get('dashboard/products/bestsellers', [DashboardController::class, 'bestsellingProducts']);

Route::get('products/bestsellers', [ProductController::class, 'bestsellers']);
Route::get('products/featured', [ProductController::class, 'featured']);

// Public routes
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);

// MoMo Payment routes (public - không cần auth)
Route::prefix('payment/momo')->group(function () {
    Route::post('create', [MoMoPaymentController::class, 'createPayment']);
    Route::post('notify', [MoMoPaymentController::class, 'handleCallback']);
    Route::get('check-status', [MoMoPaymentController::class, 'checkStatus']);
    Route::post('regenerate-qr', [MoMoPaymentController::class, 'regenerateQR']);
});

// Session-based routes (for non-authenticated users)
Route::prefix('session')->group(function () {
    Route::get('cart', [SessionController::class, 'getSessionCart']);
    Route::post('cart', [SessionController::class, 'addToSessionCart']);
    Route::put('cart', [SessionController::class, 'updateSessionCart']);
    Route::delete('cart', [SessionController::class, 'removeFromSessionCart']);
    Route::get('cart/count', [SessionController::class, 'getSessionCartCount']);
    Route::delete('cart/clear', [SessionController::class, 'clearSessionCart']);
    Route::get('cart/checkout', [SessionController::class, 'checkoutInfo']);
    Route::post('cart/checkout', [SessionController::class, 'checkout']);
    Route::post('cart/draft', [SessionController::class, 'cartDraft']);

    Route::get('wishlist', [SessionController::class, 'getSessionWishlist']);
    Route::post('wishlist', [SessionController::class, 'addToSessionWishlist']);
    Route::post('wishlist/selected', [SessionController::class, 'updateSessionWishlistSelected']);
    Route::delete('wishlist', [SessionController::class, 'removeFromSessionWishlist']);
    Route::delete('wishlist/clear', [SessionController::class, 'clearSessionWishlist']);
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Cart management
    Route::get('cart', [CartController::class, 'index']);
    Route::put('cart/{cartItem}', [CartController::class, 'update']);
    Route::post('cart', [CartController::class, 'store']);
    Route::get('cart/checkout', [CartController::class, 'checkoutInfo']); // Add GET method for info
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
//    Route::post('wishlist/sync', [WishlistController::class, 'syncWishlist']);
    Route::get('wishlist/ids', [WishlistController::class, 'getWishlistIds']);

    // Profile API routes
    Route::get('profile', [ProfileController::class, 'show']);
    Route::post('profile/update', [ProfileController::class, 'update']);
    Route::post('profile/update-phone', [ProfileController::class, 'updatePhone']);
    Route::patch('profile/password', [ProfileController::class, 'changePassword']);

    // Profile Notifications
    Route::prefix('profile/notifications')->group(function () {
        Route::post('{id}/read', [ProfileController::class, 'markNotificationAsRead']);
        Route::post('mark-all-as-read', [ProfileController::class, 'markAllNotificationsAsRead']);
    });

    // User Addresses
    Route::prefix('profile/addresses')->group(function () {
        Route::get('/', [UserAddressController::class, 'index']);
        Route::post('/', [UserAddressController::class, 'store']);
        Route::get('{id}', [UserAddressController::class, 'show']);
        Route::put('{id}', [UserAddressController::class, 'update']);
        Route::delete('{id}', [UserAddressController::class, 'destroy']);
        Route::post('{id}/set-default', [UserAddressController::class, 'setDefault']);
    });

    // Location data routes for address forms
    Route::prefix('addresses')->group(function () {
        Route::get('location-data', [UserAddressController::class, 'getLocationData']);
        Route::get('districts/{provinceId}', [UserAddressController::class, 'getDistricts']);
        Route::get('wards/{districtId}', [UserAddressController::class, 'getWards']);
    });

//    // User Profile
//    Route::prefix('user')->group(function () {
//        Route::get('profile', [UserProfileController::class, 'show']);
//        Route::put('profile', [UserProfileController::class, 'update']);
//        Route::post('change-password', [UserProfileController::class, 'changePassword']);
//    });
//
//    // User Notifications
//    Route::prefix('user/notifications')->group(function () {
//        Route::get('/', [UserNotificationController::class, 'index']);
//        Route::get('stats', [UserNotificationController::class, 'getStats']);
//        Route::post('{id}/read', [UserNotificationController::class, 'markAsRead']);
//        Route::post('mark-all-read', [UserNotificationController::class, 'markAllAsRead']);
//        Route::delete('{id}', [UserNotificationController::class, 'destroy']);
//        Route::delete('read/all', [UserNotificationController::class, 'deleteAllRead']);
//    });
//
//    // User Orders
//    Route::prefix('user/orders')->group(function () {
//        Route::get('/', [UserOrderController::class, 'index']);
//        Route::get('stats', [UserOrderController::class, 'getStats']);
//        Route::get('{id}', [UserOrderController::class, 'show']);
//        Route::post('{id}/cancel', [UserOrderController::class, 'cancel']);
//        Route::post('{id}/reorder', [UserOrderController::class, 'reorder']);
//    });
});
// Location routes (public - không cần auth)
Route::prefix('locations')->group(function () {
    Route::get('provinces', [LocationController::class, 'getProvinces']);
    Route::get('districts/{provinceId}', [LocationController::class, 'getDistricts']);
    Route::get('wards/{districtId}', [LocationController::class, 'getWards']);
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

// Location routes
Route::prefix('locations')->group(function () {
    Route::get('/', [LocationController::class, 'index']);
    Route::get('/{id}', [LocationController::class, 'show']);
    Route::post('/', [LocationController::class, 'store']);
    Route::put('/{id}', [LocationController::class, 'update']);
    Route::delete('/{id}', [LocationController::class, 'destroy']);
});
