<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');

            $subscriptionsQuery = Subscription::where('user_id', $user->id)->with('user');

            if ($search) {
                $subscriptionsQuery->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if ($sort === 'newest') {
                $subscriptionsQuery->latest();
            } elseif ($sort === 'oldest') {
                $subscriptionsQuery->oldest();
            }

            $subscriptions = $subscriptionsQuery->paginate(10);

            // جلب الاشتراك المنتهي أو القريب من الانتهاء
            $expiredSubscription = $subscriptions->firstWhere('status', 'active') && Carbon::parse($subscriptions->firstWhere('status', 'active')->end_date)->isPast();
            $expiringSoonSubscription = $subscriptions->firstWhere('status', 'active') && Carbon::parse($subscriptions->firstWhere('status', 'active')->end_date)->between(Carbon::today(), Carbon::today()->addDays(3));

            return response()->json([
                'status' => 'success',
                'data' => SubscriptionResource::collection($subscriptions),
                'expired_subscription' => $expiredSubscription ? new SubscriptionResource($subscriptions->firstWhere('status', 'active')) : null,
                'expiring_soon_subscription' => $expiringSoonSubscription ? new SubscriptionResource($subscriptions->firstWhere('status', 'active')) : null,
                'message' => 'Subscriptions retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve subscriptions for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving subscriptions. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $subscription = Subscription::where('user_id', $user->id)->with('user')->findOrFail($id);

            // التحقق من حالة الاشتراك
            $isExpired = $subscription->end_date && Carbon::parse($subscription->end_date)->isPast() && $subscription->status === 'active';
            $isExpiringSoon = $subscription->end_date && Carbon::parse($subscription->end_date)->between(Carbon::today(), Carbon::today()->addDays(3)) && $subscription->status === 'active';

            return response()->json([
                'status' => 'success',
                'data' => new SubscriptionResource($subscription),
                'is_expired' => $isExpired,
                'is_expiring_soon' => $isExpiringSoon,
                'message' => 'Subscription retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve subscription for farmer', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Subscription with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }
}