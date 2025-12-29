<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // STEP 1: Email
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'submitEmail'])
//        ->middleware('throttle:register-email')
        ->name('register.emails.submit');

    // STEP 2: Username (qua email)
    Route::get('/complete/{token}', [RegisteredUserController::class, 'showUsernameForm'])
        ->name('register.username');

    Route::post('/complete/{token}', [RegisteredUserController::class, 'submitUsername'])
        ->name('register.username.submit');

    // STEP 3: Hiển thị password cho user
    Route::get('/password/{token}', [RegisteredUserController::class, 'showPasswordPage'])
        ->name('register.password');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:login');

//    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
//        ->name('password.request');
//
//    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
//        ->name('password.emails');


    Route::get('reset-password', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'submitEmail'])
//        ->middleware('throttle:password-reset')
        ->name('password.store');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'showRetrievePage'])
        ->name('password.retrieve');

    Route::post('/reset-password/{token}', [NewPasswordController::class, 'retrievePassword'])
        ->name('password.retrieve.submit');

    // STEP 3: Hiển thị password cho user
    Route::get('/show-reset-password/{token}', [NewPasswordController::class, 'showPasswordPage'])
        ->name('password.retrieve.show');

//    Route::prefix('reset-password')->group(function () {
//        Route::post('/', [RegisteredUserController::class, 'submitEmail'])
//            ->name('password.email');
//
//        Route::get('{token}', [RegisteredUserController::class, 'showRetrievePage'])
//            ->name('password.retrieve');
//
//        Route::post('{token}', [RegisteredUserController::class, 'retrievePassword'])
//            ->name('password.retrieve.submit');
//    });
});

Route::middleware('auth')->group(function () {
    Route::get('verify-emails', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-emails/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('emails/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
