<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserPermission
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // System admin bypass
        if ($user->hasRole('system-admin')) {
            return $next($request);
        }

        // Ensure branch context exists for staff (branch-scoped)
        if ($user->user_type === 'staff') {
            if (empty($user->branch_id)) {
                abort(403, 'You are not assigned to any branch.');
            }

            $branch = \App\Models\Branch::find($user->branch_id);
            if (!$branch) {
                abort(403, 'You are not assigned to any branch.');
            }

            session(['user_branch_id' => $branch->id]);
            session(['user_branch' => $branch]);
            session(['branch' => $branch]);
        }

        if ($user->hasRole('student')) {
            session(['user_branch_id' => null]);
            session(['branch' => null]);
        }

        $raw = trim((string) $permission);
        $normalized = strtolower($raw);
        $normalized = preg_replace('/\s+/', '-', $normalized);
        $normalized = str_replace('_', '-', $normalized);

        if (!$user->hasPermission($raw) && !$user->hasPermission($normalized)) {
            return redirect('permission-denied');
        }
        return $next($request);
    }
}
