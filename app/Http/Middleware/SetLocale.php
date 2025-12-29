<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ✅ Lấy locale từ header (do axios interceptor gửi)
        $locale = $request->header('X-Locale', 'vi');

        // Validate locale
        $supportedLocales = ['en', 'vi', 'ru', 'jp'];

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
