<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleFarmerManager
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->check()) {
            if (Auth::guard('api')->user()->role === 'farmer_manager') {
                return $next($request);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You must be a Farmer Manager to access this resource.',
                'errors' => []
            ], 403);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'You are not logged in. Please log in to access this resource.',
            'errors' => []
        ], 401);
    }
}