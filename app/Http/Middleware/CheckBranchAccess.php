<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBranchAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // System admin can access everything
        if ($user->hasRole('system-admin')) {
            return $next($request);
        }

        // All staff (non-system-admin) are branch-scoped
        if ($user->user_type === 'staff') {
            $userBranch = $this->getUserBranch($user);

            if (!$userBranch) {
                return redirect()->route('no-branch');
            }

            // Store branch in session for easy access
            session(['user_branch_id' => $userBranch->id]);
            session(['user_branch' => $userBranch]);
            session(['branch' => $userBranch]);

            return $next($request);
        }

        // Students can only access their own data
        if ($user->hasRole('student')) {
            session(['user_branch_id' => null]);
            session(['branch' => null]);
            return $next($request);
        }

        return $next($request);
    }

    /**
     * Get user's assigned branch
     */
    private function getUserBranch($user)
    {
        if (empty($user->branch_id)) {
            return null;
        }

        return \App\Models\Branch::find($user->branch_id);
    }
}
