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
        if (!$user->hasPermission($permission)) {
            if ($user->hasRole('system-admin')) {
                return $next($request);
            }

            if ($user->hasRole(['branch-admin', 'tutor', 'student'])) {
                $branch = $user->tutor?->branch;
                session(['branch' => $branch]);
            }

            return redirect('permission-denied');
        }
        return $next($request);
    }
}
