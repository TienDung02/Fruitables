<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\App;
use App\Mail\CompleteActionMail;
use App\Mail\AccountStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\PendingRegistration;
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register', [
            'email_sent' => session('email_sent'),
            'email' => session('email'),
            'error' => session('error'),
        ]);
    }
    public function submitEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $userExists = User::where('emails', $email)->exists();
        $locale = App::getLocale();

        Log::info("Processing registration for {$email} with locale: {$locale}");

        try {
            if ($userExists) {
                Mail::to($email)->send(
                    new AccountStatusMail(
                        linkUrl: route('login'),
                        locale: $locale,
                        type: 'exists',
                    )
                );
                return redirect()->route('register')
                    ->with([
                        'email_sent' => true,
//                        'email' => $request->email,
                ]);
            } else {
                $token = Str::uuid();

                PendingRegistration::updateOrCreate(
                    [
                        'email' => $email,
                        'type' => 'register',
                    ],

                    [
                        'token' => $token,
                        'step' => 'email',
                        'expires_at' => now()->addMinutes(15),
                        'used' => false,
                    ]
                );

                Mail::to($email)->send(
                    new CompleteActionMail(
                        token: $token,
                        type: 'register',
                        locale: $locale
                    )
                );
                Log::info("Complete registration email sent to {$email}");
                return redirect()->route('register')
                    ->with([
                        'email_sent' => true,
//                        'email' => $request->email,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send registration email to {$request->email}: ".$e->getMessage());

            return redirect()->route('register')
                ->with([
                    'email_sent' => false,
                    'error' => 'Gửi email thất bại. Vui lòng thử lại sau.',
                ]);
        }
    }
    public function showUsernameForm(string $token)
    {
        $pending = PendingRegistration::where('token', $token)
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->firstOrFail();

        return Inertia::render('Auth/SubmitUsername', [
            'token' => $token,
            'email' => $pending->email,
        ]);
    }
    public function submitUsername(Request $request, string $token)
    {
        Log::info('submitUsername had called');
        $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username',
        ]);

        try {
            $pending = PendingRegistration::where('token', $token)
                ->where('expires_at', '>', now())
                ->where('used', false) // ✅ Double check
                ->firstOrFail();

            $pending->update([
                'username' => $request->username,
                'step' => 'username',
            ]);

            Log::info("Pending registration username updated successfully", [
                'token' => $token,
                'username' => $request->username,
            ]);

            return redirect()->route('register.password', $token);

        } catch (\Throwable $e) {
            Log::error("Failed to submit username", [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors('Token không hợp lệ hoặc đã hết hạn.');
        }
    }

    // Bước 3: show password
    public function showPasswordPage(string $token)
    {

        try {
            $pending = PendingRegistration::where('token', $token)
                ->where('expires_at', '>', now())
                ->where('used', false)
                ->where('step', 'username')
                ->firstOrFail();

            // ✅ TẠO USER + PASSWORD CHỈ 1 LẦN
            $plainPassword = Str::password(16, true, true, true);

            $user = User::create([
                'username' => $pending->username,
                'emails' => $pending->email,
                'password' => Hash::make($plainPassword),
            ]);

            $pending->update([
                'user_id' => $user->id,
                'step' => 'completed',
                'used' => true,
            ]);

            Log::info("User created successfully", [
                'user_id' => $user->id,
                'username' => $user->username,
            ]);

            // ✅ TRUYỀN TRỰC TIẾP VÀO VIEW - KHÔNG QUA SESSION
            return Inertia::render('Auth/ShowPassword', [
                'password' => $plainPassword,  // Chỉ tồn tại trong response này
                'username' => $pending->username,
                'email' => $pending->email,
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to create user", [
                'token' => $token,
                'error' => $e->getMessage()
            ]);

            abort(404, 'Token không hợp lệ hoặc đã được sử dụng.');
        }
    }



    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'emails' => 'required|string|lowercase|emails|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'emails' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
