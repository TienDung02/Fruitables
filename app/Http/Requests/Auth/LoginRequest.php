<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'emails' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson() || $this->header('X-Inertia')) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        parent::failedValidation($validator);
    }
//    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
//    {
//        if ($this->expectsJson() || $this->header('X-Inertia')) {
//            throw new \Illuminate\Validation\ValidationException($validator);
//        }
//
//        parent::failedValidation($validator);
//    }
    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        Log::info('🔍 [authenticate] Bắt đầu kiểm tra credentials');

//        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('emails', 'password'), $this->boolean('remember'))) {
            Log::error('❌ [authenticate] ĐĂNG NHẬP SAI - Sắp throw exception');
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'emails' => 'Thông tin đăng nhập không chính xác',
            ]);
        }

        Log::info('✅ [authenticate] ĐĂNG NHẬP THÀNH CÔNG');
        RateLimiter::clear($this->throttleKey());
    }
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'emails' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('emails')).'|'.$this->ip());
    }
    /**
     * ENHANCED: Multi-layer rate limiting system
     * Prevents session cycling attacks by tracking multiple identifiers
     */
    private function ensureMultiLayerRateLimit(): void
    {
        $email = $this->input('email', '');
        $ip = $this->ip();
        $sessionId = session()->getId();
        $fingerprint = $this->createBrowserFingerprint();

        // Layer 1: Email-based rate limiting (original)
        $emailKey = $this->throttleKey();
        $emailLimited = RateLimiter::tooManyAttempts($emailKey, 5);

        // Layer 2: IP-based rate limiting (prevents IP-level brute force)
        $ipKey = 'login_attempts_ip:' . $ip;
        $ipLimited = RateLimiter::tooManyAttempts($ipKey, 15); // 15 attempts per IP

        // Layer 3: Session-based rate limiting (prevents session cycling)
        $sessionKey = 'login_attempts_session:' . $sessionId;
        $sessionLimited = RateLimiter::tooManyAttempts($sessionKey, 3); // Max 3 per session

        // Layer 4: Browser fingerprint rate limiting (advanced protection)
        $fingerprintKey = 'login_attempts_fingerprint:' . $fingerprint;
        $fingerprintLimited = RateLimiter::tooManyAttempts($fingerprintKey, 5);

        // Check tất cả các rate limiters
        if ($emailLimited || $ipLimited || $sessionLimited || $fingerprintLimited) {
            $this->handleMultiLayerRateLimit($emailKey, $ipKey, $sessionKey, $fingerprintKey);
        }

        // SECURITY: Track session creation patterns để detect cycling
        $this->trackSessionPatterns($ip, $sessionId);
    }

    /**
     * Hit multiple rate limiters khi login fails
     */
    private function hitMultipleRateLimiters(): void
    {
        $email = $this->input('email', '');
        $ip = $this->ip();
        $sessionId = session()->getId();
        $fingerprint = $this->createBrowserFingerprint();

        // Increment tất cả rate limit counters
        RateLimiter::hit($this->throttleKey()); // Email-based
        RateLimiter::hit('login_attempts_ip:' . $ip); // IP-based
        RateLimiter::hit('login_attempts_session:' . $sessionId); // Session-based
        RateLimiter::hit('login_attempts_fingerprint:' . $fingerprint); // Fingerprint-based
    }

    /**
     * Hit rate limiters CHỈ cho failed login attempts
     */
    private function hitFailedLoginRateLimiters(): void
    {
        $email = $this->input('email', '');
        $ip = $this->ip();
        $sessionId = session()->getId();
        $fingerprint = $this->createBrowserFingerprint();

        // SECURITY: Increment FAILED login counters
        RateLimiter::hit($this->throttleKey()); // Email-based failed attempts
        RateLimiter::hit('login_attempts_ip:' . $ip); // IP-based failed attempts
        RateLimiter::hit('login_attempts_session:' . $sessionId); // Session-based failed attempts
        RateLimiter::hit('login_attempts_fingerprint:' . $fingerprint); // Fingerprint-based failed attempts

        // SECURITY: Hit the BANKING-LEVEL failed login rate limiter
        $failedLoginKey = "failed_login_rate_limit:{$ip}";
        RateLimiter::hit($failedLoginKey, 60); // TTL 60 seconds

        Log::warning('Failed login attempt tracked', [
            'ip' => $ip,
            'email' => $email,
            'failed_attempts_this_minute' => RateLimiter::attempts($failedLoginKey),
            'banking_limit' => 3,
            'fingerprint' => substr($fingerprint, 0, 8) // First 8 chars for logging
        ]);
    }

    /**
     * Clear multiple rate limiters on successful login
     */
    private function clearMultipleRateLimiters(): void
    {
        $ip = $this->ip();
        $sessionId = session()->getId();
        $fingerprint = $this->createBrowserFingerprint();

        // Clear successful login attempts từ rate limiters
        RateLimiter::clear($this->throttleKey()); // Email-based
        RateLimiter::clear('login_attempts_ip:' . $ip); // IP-based
        RateLimiter::clear('login_attempts_session:' . $sessionId); // Session-based
        RateLimiter::clear('login_attempts_fingerprint:' . $fingerprint); // Fingerprint-based
    }

    /**
     * Handle rate limit exceeded cho multiple layers
     */
    private function handleMultiLayerRateLimit(string $emailKey, string $ipKey, string $sessionKey, string $fingerprintKey): void
    {
        event(new Lockout($this));

        // Determine which rate limit was exceeded để provide specific error
        $blockedReason = '';
        $waitTime = 0;

        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            $blockedReason = 'Too many attempts for this email address';
            $waitTime = RateLimiter::availableIn($emailKey);
        } elseif (RateLimiter::tooManyAttempts($sessionKey, 3)) {
            $blockedReason = 'Too many attempts in this session';
            $waitTime = RateLimiter::availableIn($sessionKey);
        } elseif (RateLimiter::tooManyAttempts($ipKey, 15)) {
            $blockedReason = 'Too many attempts from your IP address';
            $waitTime = RateLimiter::availableIn($ipKey);
        } elseif (RateLimiter::tooManyAttempts($fingerprintKey, 5)) {
            $blockedReason = 'Too many attempts from your device';
            $waitTime = RateLimiter::availableIn($fingerprintKey);
        }

        // SECURITY: Log detailed rate limit exceeded event
        Log::warning('Multi-layer rate limit exceeded', [
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'session_id' => session()->getId(),
            'blocked_reason' => $blockedReason,
            'wait_time_seconds' => $waitTime,
            'email_attempts' => RateLimiter::attempts($emailKey),
            'ip_attempts' => RateLimiter::attempts($ipKey),
            'session_attempts' => RateLimiter::attempts($sessionKey),
            'fingerprint_attempts' => RateLimiter::attempts($fingerprintKey),
            'timestamp' => now()
        ]);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $waitTime,
                'minutes' => ceil($waitTime / 60),
            ]) . ' (' . $blockedReason . ')',
        ]);
    }

    /**
     * Create browser fingerprint để detect same browser across sessions
     */
    private function createBrowserFingerprint(): string
    {
        $components = [
            $this->userAgent(),
            $this->header('Accept-Language'),
            $this->header('Accept-Encoding'),
            $this->header('Accept'),
            $this->header('DNT'), // Do Not Track
            // Không include IP để fingerprint stable across network changes
        ];

        // Create unique fingerprint từ browser characteristics
        return hash('sha256', implode('|', array_filter($components)));
    }

    /**
     * Track session creation patterns để detect session cycling attacks
     */
    private function trackSessionPatterns(string $ip, string $sessionId): void
    {
        // Track unique sessions per IP
        $sessionsKey = "unique_sessions_ip:{$ip}";
        $sessions = cache()->get($sessionsKey, []);

        if (!in_array($sessionId, $sessions)) {
            $sessions[] = $sessionId;
            cache()->put($sessionsKey, $sessions, now()->addHour());

            // SECURITY: Detect session cycling attack
            if (count($sessions) > 10) { // 10+ unique sessions từ 1 IP trong 1 giờ
                Log::warning('Session cycling attack detected', [
                    'ip' => $ip,
                    'unique_sessions_count' => count($sessions),
                    'attack_type' => 'session_cycling_bypass',
                    'sessions_sample' => array_slice($sessions, -5), // Last 5 sessions
                    'detection_time' => now()
                ]);

                // Auto-block IP performing session cycling
                cache()->put("blocked_ip:{$ip}", [
                    'reason' => 'session_cycling_attack',
                    'blocked_at' => now(),
                    'sessions_count' => count($sessions)
                ], now()->addHours(24));
            }
        }

        // Track session creation frequency
        $createKey = "session_creates_ip:{$ip}";
        cache()->increment($createKey, 1);

        // Set TTL nếu là lần đầu
        if (!cache()->has($createKey . '_ttl')) {
            cache()->put($createKey . '_ttl', true, now()->addHour());
            cache()->put($createKey, 1, now()->addHour());
        }

        $sessionCreates = cache()->get($createKey, 0);

        if ($sessionCreates > 20) { // 20+ session creates trong 1 giờ
            Log::warning('Rapid session creation detected', [
                'ip' => $ip,
                'session_creates_per_hour' => $sessionCreates,
                'current_session' => $sessionId
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     * SECURITY: Enhanced rate limiting với IP-based blocking
     *
     * @throws \Illuminate\Validation\ValidationException
     */
//    public function ensureIsNotRateLimited(): void
//    {
//        $maxAttempts = config('auth.max_login_attempts', 3); // đăng nhập sai tốt đa 3 lần
//        $decayMinutes = config('auth.login_lockout_minutes', 15); // khóa trong 15 phút
//
//        // Check user-specific attempts
//        if (RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts)) {
//            $this->handleRateLimitExceeded();
//        }
//        // nếu người dùng có username hay email đăng nhập sai quá 3 lần sẽ gọi hàm handleRateLimitExceeded()
//
//        // Check IP-based attempts (global protection)
//        $ipKey = 'login_attempts_ip:' . $this->ip();
//        if (RateLimiter::tooManyAttempts($ipKey, $maxAttempts * 2)) {
//            $this->handleRateLimitExceeded($ipKey);
//        }
//        // nếu IP đó đăng nhập sai quá 6 lần sẽ gọi hàm handleRateLimitExceeded()
//    }

    /**
     * Handle rate limit exceeded
     */
    private function handleRateLimitExceeded($key = null): void
    {
        $key = $key ?: $this->throttleKey();

        event(new Lockout($this));
        // phát ra sự kiện khóa tài khoản tạm thời do vi phạm rate limit

        // SECURITY: Log suspicious activity
        Log::critical('Rate limit exceeded', [
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(), //Thông tin về trình duyệt/thiết bị
            'key' => $key // Xác định chính xác khóa nào bị vượt quá
        ]);
        //Ghi lại thông tin chi tiết về sự kiện vượt quá giới hạn vào hệ thống log với mức độ critical.

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
        // Tạo và trả về thông báo lỗi cụ thể.
        // Thông báo lỗi sẽ hiển thị cho người dùng biết họ cần phải chờ bao lâu nữa mới có thể thử đăng nhập lại
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
//    public function throttleKey(): string
//    {
//        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
//    }

    /**
     * Get current rate limit status for frontend UX
     */
    private function getRateLimitStatus(): array
    {
        $ip = $this->ip();
        $sessionId = session()->getId();
        $email = $this->input('email', '');

        $emailKey = $this->throttleKey();
        $sessionKey = 'login_attempts_session:' . $sessionId;
        $ipKey = 'login_attempts_ip:' . $ip;
        $failedLoginKey = "failed_login_rate_limit:{$ip}";

        return [
            'is_rate_limited' => $this->isAnyRateLimitExceeded(),
            'email_attempts' => RateLimiter::attempts($emailKey),
            'email_limit' => 5,
            'session_attempts' => RateLimiter::attempts($sessionKey),
            'session_limit' => 3,
            'ip_failed_attempts' => RateLimiter::attempts($failedLoginKey),
            'ip_failed_limit' => 3, // Banking-level limit
            'next_attempt_available_in' => $this->getNextAvailableTime(),
            'blocked_reason' => $this->getBlockedReason(),
            'security_level' => 'banking_grade'
        ];
    }

    /**
     * Check if any rate limiter is exceeded
     */
    private function isAnyRateLimitExceeded(): bool
    {
        $ip = $this->ip();
        $sessionId = session()->getId();

        $emailKey = $this->throttleKey();
        $sessionKey = 'login_attempts_session:' . $sessionId;
        $ipKey = 'login_attempts_ip:' . $ip;
        $failedLoginKey = "failed_login_rate_limit:{$ip}";

        return RateLimiter::tooManyAttempts($emailKey, 5) ||
               RateLimiter::tooManyAttempts($sessionKey, 3) ||
               RateLimiter::tooManyAttempts($ipKey, 15) ||
               RateLimiter::tooManyAttempts($failedLoginKey, 3);
    }

    /**
     * Get next available attempt time
     */
    private function getNextAvailableTime(): int
    {
        $ip = $this->ip();
        $sessionId = session()->getId();

        $emailKey = $this->throttleKey();
        $sessionKey = 'login_attempts_session:' . $sessionId;
        $failedLoginKey = "failed_login_rate_limit:{$ip}";

        $times = [
            RateLimiter::availableIn($emailKey),
            RateLimiter::availableIn($sessionKey),
            RateLimiter::availableIn($failedLoginKey)
        ];

        return max($times);
    }

    /**
     * Get specific blocked reason for better UX
     */
    private function getBlockedReason(): ?string
    {
        $ip = $this->ip();
        $sessionId = session()->getId();

        $emailKey = $this->throttleKey();
        $sessionKey = 'login_attempts_session:' . $sessionId;
        $failedLoginKey = "failed_login_rate_limit:{$ip}";

        if (RateLimiter::tooManyAttempts($failedLoginKey, 3)) {
            return 'banking_level_security';
        } elseif (RateLimiter::tooManyAttempts($sessionKey, 3)) {
            return 'session_limit';
        } elseif (RateLimiter::tooManyAttempts($emailKey, 5)) {
            return 'email_limit';
        }

        return null;
    }
}
