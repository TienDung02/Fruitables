<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use App\Mail\ResetPassword;
use App\Models\PendingRegistration;
use App\Mail\CompleteActionMail;
use App\Mail\AccountStatusMail;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email_sent' => session('email_sent'),
            'email' => session('email'),
            'error' => session('error'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function submitEmail(Request $request)
    {
        Log::info('submitEmail called for password reset', $request->all());
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = $request->email;
        $locale = App::getLocale();
        $user = User::where('emails', $email)->first();

        try {
            if (! $user) {
                Mail::to($email)->send(
                    new AccountStatusMail(
                        linkUrl: route('register'),
                        type: 'not_exists',
                        locale: $locale
                    )
                );
                Log::info("Account not exists email sent to {$email} in {$locale}");

                return redirect()->route('password.reset')
                    ->with([
                        'email_sent' => true,
                        'email' => $request->email,
                    ]);
            }else{
                $token = Str::uuid();

                PendingRegistration::updateOrCreate(
                    [
                        'email' => $email,
                        'type' => 'reset_password',
                    ],
                    [
                        'token' => $token,
                        'step' => 'email',
                        'expires_at' => now()->addMinutes(15),
                        'used' => false,
                    ]
                );

                Mail::to($email)->send(
                    new CompleteActionMail($token, 'password_reset', $locale)
                );

                Log::info("Reset password email sent", [
                    'email' => $email,
                    'token' => $token,
                ]);

                return redirect()->route('password.reset')->with([
                    'email_sent' => true,
                    'email' => $request->email,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send reset password email", [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('password.reset')->withErrors([
                'email' => 'Không thể gửi email reset password.',
            ]);
        }
    }

    /**
     * Step 2: Show retrieve password page (có nút)
     */
    public function showRetrievePage(string $token)
    {
        $pending = PendingRegistration::where('token', $token)
            ->where('type', 'reset_password')
            ->where('expires_at', '>', now())
            ->where('used', false)
            ->firstOrFail();

        return Inertia::render('Auth/RetrievePassword', [
            'token' => $token,
//            'email' => $pending->email,
        ]);
    }

    /**
     * Step 3: Retrieve password (generate & show)
     */
    public function retrievePassword(string $token)
    {
        try {
            $pending = PendingRegistration::where('token', $token)
                ->where('type', 'reset_password')
                ->where('expires_at', '>', now())
                ->where('used', false)
                ->firstOrFail();

            $user = User::where('emails', $pending->email)->firstOrFail();

            // ✅ Tạo password mới
            $plainPassword = Str::password(16, true, true, true);

            $user->password = Hash::make($plainPassword);

            // 🔥 DÒNG QUAN TRỌNG NHẤT
            $user->setRememberToken(Str::random(60));

            $user->save();
            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();

            // ✅ Đánh dấu token đã dùng
            $pending->update([
                'step' => 'completed',
                'used' => true,
            ]);

            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();


            Log::info("Password reset successfully", [
                'user_id' => $user->id,
                'email' => $user->emails,
            ]);

            return Inertia::render('Auth/ShowPassword', [
                'password' => $plainPassword,
                'username' => $user->username,
                'email' => $user->emails,
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to reset password", [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            abort(404, 'Token không hợp lệ hoặc đã hết hạn.');
        }
    }
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'emails' => 'required|emails',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('emails', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'emails' => [trans($status)],
        ]);
    }
}
