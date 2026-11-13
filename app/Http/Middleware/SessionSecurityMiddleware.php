<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces SRS session integrity rules:
 * - Rejects blocked user accounts mid-session
 * - Enforces single concurrent active session per user
 * - Enforces configurable session inactivity timeout (default 5 minutes)
 */
class SessionSecurityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $session = $request->session();

        if ($user->is_blocked) {
            $this->terminateSession($request, $user, 'auth_blocked_session', 'Blocked user session terminated');
            return redirect()->route('login')
                ->with('error', 'Your account has been blocked. Please contact the System Administrator.');
        }

        if (!empty($user->active_session_id) && $user->active_session_id !== $session->getId()) {
            $this->terminateSession($request, $user, 'auth_concurrent_session_terminated', 'Concurrent session displaced by newer login');
            return redirect()->route('login')
                ->with('error', 'Your session ended because this account was signed in elsewhere.');
        }

        $idleMinutes = max(1, (int) config('session.idle_timeout_minutes', 5));
        $lastActivityTs = (int) $session->get('last_activity_ts', 0);
        $nowTs = now()->timestamp;

        if ($lastActivityTs > 0 && ($nowTs - $lastActivityTs) > ($idleMinutes * 60)) {
            if ($user->active_session_id === $session->getId()) {
                $user->forceFill(['active_session_id' => null])->save();
            }
            $this->terminateSession($request, $user, 'auth_session_idle_timeout', "Session expired after {$idleMinutes} minutes of inactivity");
            return redirect()->route('login')
                ->with('error', "Your session expired after {$idleMinutes} minutes of inactivity. Please sign in again.");
        }

        $session->put('last_activity_ts', $nowTs);

        if (!$user->last_activity_at || $user->last_activity_at->diffInSeconds(now()) >= 60) {
            $user->forceFill([
                'last_activity_at' => now(),
            ])->saveQuietly();
        }

        return $next($request);
    }

    private function terminateSession(Request $request, $user, string $event, string $description): void
    {
        activity('security')
            ->causedBy($user)
            ->withProperties(['ip' => $request->ip(), 'event' => $event])
            ->log($description);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

