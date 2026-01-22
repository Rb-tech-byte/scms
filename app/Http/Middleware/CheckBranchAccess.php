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

        // Branch admin and tutors can only access their branch data
        if ($user->hasRole(['branch-admin', 'tutor'])) {
            // Get user's branch
            $userBranch = $this->getUserBranch($user);
            
            if (!$userBranch) {
                abort(403, 'You are not assigned to any branch.');
            }

            // Store branch in session for easy access
            session(['user_branch_id' => $userBranch->id]);
            session(['user_branch' => $userBranch]);

            return $next($request);
        }

        // Students can only access their own data
        if ($user->hasRole('student')) {
            session(['user_branch_id' => null]);
            return $next($request);
        }

        return $next($request);
    }

    /**
     * Get user's assigned branch
     */
    private function getUserBranch($user)
    {
        // Check if user has tutor record
        if ($user->tutor) {
            return $user->tutor->branch;
        }

        // Check if user has applicant record
        $applicant = $user->applicants()->first();
        if ($applicant) {
            return $applicant->branch;
        }

        // Check if user has branch_id directly
        if (isset($user->branch_id)) {
            return \App\Models\Branch::find($user->branch_id);
        }

        return null;
    }
}
