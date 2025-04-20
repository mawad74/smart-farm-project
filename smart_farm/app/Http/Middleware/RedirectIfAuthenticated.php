<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // توجيه المستخدم بناءً على دوره
                if (Auth::user()->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif (Auth::user()->role === 'farmer_manager') {
                    return redirect()->route('farmer_manager.dashboard');
                }

                // الافتراضي باستخدام RouteServiceProvider::HOME
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}