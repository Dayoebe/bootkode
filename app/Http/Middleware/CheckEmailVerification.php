<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckEmailVerification
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && !$request->user()->hasVerifiedEmail()) {
            // Store warning in session for display in layout
            session()->put('email_verification_warning', [
                'email' => $request->user()->email,
                'message' => 'Please verify your email address to unlock all features.',
            ]);
        }

        return $next($request);
    }
}
