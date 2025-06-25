<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $user_id = $request->input('user_id');
            $status = $request->input('status');

            $subscriptionsQuery = Subscription::with('user');

            // Search
            if ($search) {
                $subscriptionsQuery->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Filter by user
            if ($user_id) {
                $subscriptionsQuery->where('user_id', $user_id);
            }

            // Filter by status
            if ($status) {
                $subscriptionsQuery->where('status', $status);
            }

            // Sort
            if ($sort === 'newest') {
                $subscriptionsQuery->latest();
            } elseif ($sort === 'oldest') {
                $subscriptionsQuery->oldest();
            }

            $subscriptions = $subscriptionsQuery->paginate(10);

            // جلب الاشتراكات المنتهية أو القريبة من الانتهاء
            $expiredSubscriptions = Subscription::where('status', 'active')
                                               ->where('end_date', '<', Carbon::today())
                                               ->get();
            $expiringSoonSubscriptions = Subscription::where('status', 'active')
                                                     ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(3)])
                                                     ->get();

            return response()->json([
                'status' => 'success',
                'data' => SubscriptionResource::collection($subscriptions),
                'expired_subscriptions' => SubscriptionResource::collection($expiredSubscriptions),
                'expiring_soon_subscriptions' => SubscriptionResource::collection($expiringSoonSubscriptions),
                'message' => 'Subscriptions retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve subscriptions', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving subscriptions. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:active,expired,cancelled',
            ]);

            $subscription = Subscription::create($validated);

            Log::info('Subscription created successfully', ['subscription_id' => $subscription->id]);
            return response()->json([
                'status' => 'success',
                'data' => new SubscriptionResource($subscription->load('user')),
                'message' => 'Subscription created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for subscription creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the subscription. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $subscription = Subscription::with('user')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new SubscriptionResource($subscription),
                'message' => 'Subscription retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve subscription', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Subscription with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $subscription = Subscription::findOrFail($id);

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:active,expired,cancelled',
            ]);

            $subscription->update($validated);

            Log::info('Subscription updated successfully', ['subscription_id' => $subscription->id]);
            return response()->json([
                'status' => 'success',
                'data' => new SubscriptionResource($subscription->load('user')),
                'message' => 'Subscription updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for subscription update', ['subscription_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update subscription', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Subscription with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            Log::info('Subscription deleted successfully', ['subscription_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Subscription with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete subscription', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Subscription with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}