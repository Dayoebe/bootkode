<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmailVerification
{
    public function handle(Request $request, Closure $next)
    {
        // Simply pass through, allowing unverified users to access
        // The alert component will show in the dashboard
        if ($request->user() && !$request->user()->hasVerifiedEmail()) {
            // Store in session for the alert component to use
            session()->put('email_not_verified', true);
            session()->put('verification_email', $request->user()->email);
        }

        return $next($request);
    }
}