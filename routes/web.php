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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

require __DIR__.'/auth.php';
// Home page
Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::post('/test-validation', function() {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'test' => 'This is a test error',
    ]);
});
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/detail/{id}', [ProductDetailController::class, 'index'])->name('detail.index');
Route::get('/checkout/{checkoutId}', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/buyNow/{buyNow}', [CheckoutController::class, 'buyNow'])->name('checkout.buy-now');

//Route::get('/login', function () {
//    return Inertia::render('Auth/Login');
//})->name('login');
//
//Route::get('/register', function () {
//    return Inertia::render('Auth/Register');
//})->name('register');

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

// Test QR code endpoint
Route::get('/test-qr', function() {
    try {
        $vietQRService = new App\Services\VietQRService();

        // Test data
        $qrContent = $vietQRService->generateVietQRString(
            '1234567890',    // account number
            '970415',        // bank code
            'CONG TY FRUITABLES', // account name
            50000,           // amount
            'Test payment',  // description
            'TEST001'        // order code
        );

        $qrFormats = $vietQRService->generateMultipleQRFormats($qrContent);

        return response()->json([
            'success' => true,
            'qr_content' => $qrContent,
            'qr_formats' => $qrFormats,
            'test_info' => [
                'content_length' => strlen($qrContent),
                'starts_with_vietqr' => str_starts_with($qrContent, '000201'),
                'has_crc' => str_contains($qrContent, '63')
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});



// Route 404 - PHẢI đặt cuối cùng để bắt tất cả routes không tồn tại
Route::fallback(function () {
    return Inertia::render('Frontend/404/Index', [], 404);
});
