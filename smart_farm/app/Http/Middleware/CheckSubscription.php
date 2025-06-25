<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role === 'farmer_manager') {
            $subscription = Subscription::where('user_id', $user->id)->first();

            if ($subscription) {
                $endDate = $subscription->end_date;
                if ($endDate && now()->greaterThan($endDate) && $subscription->status === 'active') {
                    $subscription->update(['status' => 'expired']);
                    Log::info('Subscription automatically updated to expired', ['subscription_id' => $subscription->id, 'user_id' => $user->id]);
                }

                if ($subscription->status === 'expired' || $subscription->status === 'canceled') {
                    Auth::logout();
                    return redirect('/login')->with('error', 'Your subscription has expired. Please renew it to access your account.');
                }
            }
        }

        return $next($request);
    }
}