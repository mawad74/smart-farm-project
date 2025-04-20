<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerManagerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'farmer_manager') {
            return $next($request);
        }

        return redirect('/login')->with('error', 'Unauthorized access. Farmer Managers only.');
    }
}