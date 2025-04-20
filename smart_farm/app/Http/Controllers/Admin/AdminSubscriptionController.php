<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $user_id = $request->input('user_id');
        $status = $request->input('status');

        $subscriptionsQuery = Subscription::with('user');

        // البحث
        if ($search) {
            $subscriptionsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // فلترة حسب المستخدم
        if ($user_id) {
            $subscriptionsQuery->where('user_id', $user_id);
        }

        // فلترة حسب الحالة
        if ($status) {
            $subscriptionsQuery->where('status', $status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $subscriptionsQuery->latest();
        } elseif ($sort === 'oldest') {
            $subscriptionsQuery->oldest();
        }

        $subscriptions = $subscriptionsQuery->paginate(10);
        $users = User::where('role', 'farmer_manager')->get();

        // جلب الاشتراكات المنتهية أو القريبة من الانتهاء لعرضها للـ Admin
        $expiredSubscriptions = Subscription::where('status', 'active')
                                            ->where('end_date', '<', Carbon::today())
                                            ->get();
        $expiringSoonSubscriptions = Subscription::where('status', 'active')
                                                 ->whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(3)])
                                                 ->get();

        return view('admin.subscriptions.index', compact('subscriptions', 'users', 'expiredSubscriptions', 'expiringSoonSubscriptions'));
    }

    public function create()
    {
        $users = User::where('role', 'farmer_manager')->get();
        return view('admin.subscriptions.create', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:active,expired,canceled',
            ]);

            $subscription = new Subscription();
            $subscription->user_id = $request->user_id;
            $subscription->start_date = $request->start_date;
            $subscription->end_date = $request->end_date;
            $subscription->status = $request->status;
            $subscription->save();

            Log::info('Subscription created successfully', ['subscription_id' => $subscription->id]);
            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for subscription creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create subscription', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create subscription: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $subscription = Subscription::findOrFail($id);
        $users = User::where('role', 'farmer_manager')->get();
        return view('admin.subscriptions.edit', compact('subscription', 'users'));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'status' => 'required|in:active,expired,canceled',
            ]);

            $subscription->user_id = $request->user_id;
            $subscription->start_date = $request->start_date;
            $subscription->end_date = $request->end_date;
            $subscription->status = $request->status;
            $subscription->save();

            Log::info('Subscription updated successfully', ['subscription_id' => $subscription->id]);
            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for subscription update', ['subscription_id' => $subscription->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update subscription', ['subscription_id' => $subscription->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update subscription: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            Log::info('Subscription deleted successfully', ['subscription_id' => $id]);
            return redirect()->route('admin.subscriptions.index')->with('success', 'Subscription deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete subscription', ['subscription_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.subscriptions.index')->with('error', 'Failed to delete subscription: ' . $e->getMessage());
        }
    }
}