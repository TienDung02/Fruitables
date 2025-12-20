<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Lấy ngôn ngữ từ session, URL parameter, hoặc mặc định
        $locale = $request->get('locale') ?? Session::get('locale') ?? config('app.locale');

        // Kiểm tra ngôn ngữ có được hỗ trợ không
        $supportedLocales = ['en', 'vi'];
        if (!in_array($locale, $supportedLocales)) {
            $locale = config('app.locale');
        }

        // Set locale cho ứng dụng
        App::setLocale($locale);

        // Lưu locale vào session
        Session::put('locale', $locale);

        return $next($request);
    }
}
