<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Inertia::share('csrf_token', csrf_token());
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(15, 3)
                ->by($request->input('emails') ?? $request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = $headers['Retry-After'] ?? 60;

                    return back()->withErrors([
                        'emails' => [
                            [
                                'key' => 'rate_limit.login_attempts',
                                'params' => [
                                    'seconds' => $retryAfter,
                                    'minutes' => ceil($retryAfter / 60)
                                ]
                            ]
                        ]
                    ])->withInput($request->except('password'));
                });
        });

        // ✅ Rate limiter cho register email
        RateLimiter::for('register-email', function (Request $request) {
            return Limit::perMinutes(15,3)
                ->by($request->input('email') ?? $request->ip())
                ->response(function (Request $request, array $headers) {
                    $retryAfter = $headers['Retry-After'] ?? 60;

                    return back()->withErrors([
                        'emails' => [
                            [
                                'key' => 'rate_limit.register_attempts',
                                'params' => [
                                    'seconds' => $retryAfter,
                                    'minutes' => ceil($retryAfter / 60)
                                ]
                            ]
                        ]
                    ])->withInput();
                });
        });

        // ✅ Rate limiter cho password reset
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)
                ->by($request->input('email'))
                ->response(function (Request $request, array $headers) {
                    $retryAfter = $headers['Retry-After'] ?? 3600;

                    return back()->withErrors([
                        'emails' => [
                            [
                                'key' => 'rate_limit.password_reset',
                                'params' => [
                                    'minutes' => ceil($retryAfter / 60)
                                ]
                            ]
                        ]
                    ]);
                });
        });

        // ✅ Rate limiter cho username submission
        RateLimiter::for('register-username', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip());
        });
    }
}
