<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale');

        // Validate locale
        $supportedLocales = ['en', 'vi'];
        if (in_array($locale, $supportedLocales)) {
            Session::put('locale', $locale);
        }

        return Redirect::back();
    }
}
