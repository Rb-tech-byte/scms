<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendMail;
use App\Models\Notification as SystemNotification;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Defer attempt limits to the SRS 4-attempt cooldown / 2-lockout block policy below.
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return false;
    }

    /**
     * Check account block and temporary lockout status before attempting authentication.
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::where($this->username(), $request->input($this->username()))->first();

        if ($user) {
            if ($user->is_blocked) {
                activity('security')
                    ->causedBy($user)
                    ->withProperties(['ip' => $request->ip(), 'event' => 'auth_blocked_login_attempt'])
                    ->log('Login rejected: account is blocked');

                throw ValidationException::withMessages([
                    $this->username() => ['Your account has been blocked. Contact the System Administrator.'],
                ]);
            }

            if ($user->isLockedOut()) {
                $minutesLeft = max(1, (int) ceil(now()->diffInSeconds($user->locked_until) / 60));

                activity('security')
                    ->causedBy($user)
                    ->withProperties(['ip' => $request->ip(), 'event' => 'auth_locked_login_attempt'])
                    ->log('Login rejected: account temporarily locked');

                throw ValidationException::withMessages([
                    $this->username() => ["Account temporarily locked after repeated failures. Try again in {$minutesLeft} minute(s)."],
                ]);
            }
        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    /**
     * Track consecutive failed attempts, enforce 4-failure cooldown and 2-lockout blocking, and audit failures.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $email = (string) $request->input($this->username());
        $user = User::where($this->username(), $email)->first();

        if ($user) {
            $attempts = $user->failed_login_attempts + 1;

            if ($attempts >= 4) {
                $lockoutCount = $user->lockout_count + 1;
                $updates = [
                    'failed_login_attempts' => 0,
                    'lockout_count' => $lockoutCount,
                    'locked_until' => now()->addMinutes(5),
                ];

                if ($lockoutCount >= 2) {
                    $updates['is_blocked'] = true;
                    $updates['blocked_at'] = now();
                    $updates['blocked_reason'] = 'Automatic lockout after 2 consecutive failed-login lockouts.';
                }

                $user->forceFill($updates)->save();

                $this->dispatchSecurityAlert($user, $lockoutCount >= 2);

                activity('security')
                    ->causedBy($user)
                    ->withProperties(['ip' => $request->ip(), 'lockout_count' => $lockoutCount])
                    ->log($lockoutCount >= 2
                        ? 'Account automatically blocked after 2 lockout events'
                        : 'Account temporarily locked for 5 minutes after 4 failed login attempts');

                $message = $lockoutCount >= 2
                    ? 'Account locked due to repeated security lockouts. Contact System Administrator.'
                    : 'Too many failed login attempts (4). Account paused for 5 minutes.';

                throw ValidationException::withMessages([
                    $this->username() => [$message],
                ]);
            }

            $user->forceFill(['failed_login_attempts' => $attempts])->save();

            activity('security')
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip(), 'attempt' => $attempts])
                ->log("Failed login attempt ({$attempts}/4)");
        } else {
            activity('security')
                ->withProperties(['email' => $email, 'ip' => $request->ip()])
                ->log('Failed login attempt for unknown email');
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Enforce password policy & expiry, bind single active session ID, and audit successful logins.
     */
    protected function authenticated(Request $request, $user)
    {
        $user->forceFill([
            'failed_login_attempts' => 0,
            'lockout_count' => 0,
            'locked_until' => null,
            'active_session_id' => $request->session()->getId(),
            'last_activity_at' => now(),
        ])->save();

        $request->session()->put('last_activity_ts', now()->timestamp);

        activity('security')
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip(), 'event' => 'auth_login_success'])
            ->log('User signed in');

        $password = $request->password;
        $validator = Validator::make(['password' => $password], [
            'password' => [
                Password::min(8)->uncompromised()->mixedCase()->letters()->numbers()->symbols(),
            ],
        ]);

        if ($validator->fails() || $user->isPasswordExpired() || $user->force_password_reset) {
            $user->force_password_reset = true;
            $user->save();

            return redirect()->route('password.force_reset');
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Clear single-session lock when user logs out cleanly.
     */
    public function logout(Request $request)
    {
        $user = $this->guard()->user();
        if ($user) {
            $user->forceFill(['active_session_id' => null])->saveQuietly();
            activity('security')
                ->causedBy($user)
                ->withProperties(['ip' => $request->ip(), 'event' => 'auth_logout'])
                ->log('User logged out');
        }

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->loggedOut($request) ?: redirect('/');
    }

    private function dispatchSecurityAlert(User $user, bool $blocked): void
    {
        $template = NotificationTemplate::where('key', 'account_lockout_alert')->where('is_active', true)->first();
        $body = $template
            ? $template->render(['user_name' => $user->name, 'email' => $user->email])
            : "Security alert: {$user->name} ({$user->email}) triggered " . ($blocked ? 'account lockout.' : 'a 5-minute login pause.');

        SystemNotification::create([
            'user_id' => $user->id,
            'message' => $body,
        ]);

        try {
            $subject = $template?->subject ?: 'SCMIS Security Lockout Alert';
            Mail::to($user->email)->send(new SendMail($body, $subject));
        } catch (\Throwable $e) {
            // Keep login flow resilient if SMTP relay is unavailable locally
        }
    }
}
