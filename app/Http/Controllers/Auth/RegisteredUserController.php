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
use App\Mail\CompleteRegistrationMail;
use App\Mail\AccountAlreadyExistsMail;
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

        $token = Str::uuid();

        $email = $request->email;

        $userExists = User::where('emails', $email)->exists();

        PendingRegistration::updateOrCreate(
            ['email' => $request->email],
            [
                'token' => $token,
                'step' => 'email',
                'expires_at' => now()->addMinutes(15),
            ]
        );

        try {
            if ($userExists) {
//                $token = Str::uuid();
//                PendingRegistration::updateOrCreate(
//                    ['email' => $email],
//                    [
//                        'token' => $token,
//                        'step' => 'username',
//                        'expires_at' => now()->addMinutes(15),
//                    ]
//                );

                Mail::to($email)->send(
                    new AccountAlreadyExistsMail()
                );

            } else {
                // 👉 NHÁNH A: user mới
                $token = Str::uuid();

                PendingRegistration::updateOrCreate(
                    ['email' => $email],
                    [
                        'token' => $token,
                        'step' => 'email',
                        'expires_at' => now()->addMinutes(15),
                    ]
                );

                Mail::to($email)->send(new CompleteRegistrationMail($token));

                Log::info("Complete registration email sent to {$email}");
            }

            return redirect()->route('register')
                ->with([
                    'email_sent' => true,
                    'email' => $request->email,
                ]);


        } catch (\Throwable $e) {
            Log::error("Failed to send registration email to {$request->email}: ".$e->getMessage());
            // Trả về redirect với flash session lỗi
            return redirect()->route('register')
                ->with([
                    'email_sent' => false,
                    'error' => 'Gửi email thất bại. Vui lòng thử lại sau.',
                ]);
        }
    }
    public function showUsernameForm(string $token) {
        $pending = PendingRegistration::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();
        return Inertia::render('Auth/SubmitUsername', [
            'token' => $token,
            'email' => $pending->email,
        ]);
    }
    public function submitUsername(Request $request, string $token)
    {
        Log::info('Submit username started', ['token' => $token]);

        $request->validate([
            'username' => 'required|string|min:3|max:30|unique:users,username',
        ]);

        try {
            $pending = PendingRegistration::where('token', $token)
                ->where('expires_at', '>', now())
                ->firstOrFail();

            $pending->update([
                'username' => $request->username,
                'step' => 'username',
            ]);

            Log::info("Pending registration username updated successfully", [
                'token' => $token,
                'username' => $request->username,
                'email' => $pending->email
            ]);

            // Sinh password mới và tạo user luôn
            $plainPassword = Str::password(16, true, true, true);
            Log::info('Generated plain password', ['plainPassword' => $plainPassword]);
            $hashedPassword = Hash::make($plainPassword);


            $user = User::create([
                'username' => $request->username,
                'emails' => $pending->email,
                'password' => $hashedPassword,
            ]);

            Log::info("User created successfully", [
                'user_id' => $user->id,
                'username' => $user->name,
                'email' => $user->emails
            ]);

            $pending->update([
                'password' => $hashedPassword,
                'step' => 'password',
            ]);

//            return redirect()->route('register.password', $token)
//                ->with('password', $plainPassword);

            return redirect()->route('register.password', $token)
                ->with([
                    'password' => $plainPassword,
                ]);

        } catch (\Throwable $e) {
            Log::error("Failed to submit username / create user", [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            return back()->withErrors('Đã có lỗi xảy ra. Vui lòng thử lại.');
        }
    }

    // Bước 3: show password
    public function showPasswordPage(string $token)
    {
        Log::info("Show password page started", ['token' => $token]);

        try {
            $pending = PendingRegistration::where('token', $token)->firstOrFail();

            return Inertia::render('Auth/ShowPassword', [
                'password' => session('password'), // chỉ hiển thị 1 lần
                'token' => $token,
            ]);

        } catch (\Throwable $e) {
            Log::error("Failed to show password page", [
                'token' => $token,
                'error' => $e->getMessage()
            ]);
            abort(404, 'Pending registration not found or expired');
        }
    }

    // Bước 4: finalize (chỉ cần redirect tới login)
    public function finalize(Request $request, string $token)
    {
        Log::info("Finalize registration - redirect to login", ['token' => $token]);
        return redirect()->route('login')->with('success', 'Đăng ký thành công. Vui lòng đăng nhập.');
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
