<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordReset
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && ($user->force_password_reset || $user->isPasswordExpired())) {
            if (!$user->force_password_reset) {
                $user->forceFill(['force_password_reset' => true])->save();
            }
            if (!$request->is('force-password-reset*') && !$request->is('logout')) {
                return redirect()->route('password.force_reset');
            }
        }
        return $next($request);
    }
}
