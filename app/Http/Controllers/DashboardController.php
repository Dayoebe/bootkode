<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Redirects the authenticated user to their respective dashboard based on their role.
     */
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->route('login');
        }
    
        
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }
    
        return redirect()->route($user->getDashboardRouteName());
    }
}

