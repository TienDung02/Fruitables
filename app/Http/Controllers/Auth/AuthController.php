<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle user login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'emails' => 'required|emails',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            flash()->success('Welcome back, ' . Auth::user()->name . '!');

            return Redirect::intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'emails' => __('auth.failed'),
        ]);
    }

    /**
     * Show registration form
     */
    public function showRegistration()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emails' => 'required|string|emails|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'emails' => $validated['emails'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        flash()->success('Welcome to our application! Your account has been created.');

        return Redirect::route('dashboard');
    }

    /**
     * Log the user out
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('login');
    }
}
