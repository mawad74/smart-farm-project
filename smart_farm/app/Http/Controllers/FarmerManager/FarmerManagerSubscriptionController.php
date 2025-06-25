<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FarmerManagerSubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager'); // Middleware للـ Farmer Manager
    }

    public function show()
    {
        $user = Auth::user();
        $subscription = Subscription::where('user_id', $user->id)->first();

        if (!$subscription) {
            Log::warning('No subscription found for user', ['user_id' => $user->id]);
            return redirect()->route('farmer_manager.dashboard')->with('error', 'No subscription found.');
        }

        // التحقق من حالة الاشتراك
        $isExpired = $subscription->end_date && Carbon::parse($subscription->end_date)->isPast() && $subscription->status === 'active';
        $isExpiringSoon = $subscription->end_date && Carbon::parse($subscription->end_date)->between(Carbon::today(), Carbon::today()->addDays(3)) && $subscription->status === 'active';

        return view('farmer_manager.subscriptions.show', compact('subscription', 'isExpired', 'isExpiringSoon'));
    }
}