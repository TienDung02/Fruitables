<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckAuth;
use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);
        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
            HandleAppearance::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'check.auth' => CheckAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {


        $exceptions->render(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Not found.'], 404);
            }

            return Inertia::render('Frontend/404/Index')
                ->toResponse($request)
                ->setStatusCode(404);
        });
    })->create();
