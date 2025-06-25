<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (!Auth::user()->is_active) {
                Auth::logout();
                return redirect('/login')->with('error', 'Your account is inactive. Please contact the admin to activate your account.');
            }
        }

        return $next($request);
    }
}