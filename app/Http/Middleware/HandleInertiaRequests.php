<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = App::getLocale();
        $translations = [];

        // Load translation files đồng bộ
        $translationPath = base_path("lang/{$locale}");

        if (file_exists($translationPath)) {
            $files = glob($translationPath . '/*.php');

            foreach ($files as $file) {
                $key = basename($file, '.php');
                $fileContents = require $file;
                $translations[$key] = $fileContents;
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'csrf_token' => csrf_token(),
            'flash' => [
                'message' => fn () => $request->session()->get('message')
            ],
            'locale' => $locale,
            'translations' => $translations,
        ];
    }
}
