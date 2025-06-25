<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerAlertController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager'); // Middleware للـ Farmer Manager
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $alertsQuery = Alert::where('farm_id', $farm->id);

        if ($search) {
            $alertsQuery->where('message', 'like', "%{$search}%")
                        ->orWhere('severity', 'like', "%{$search}%");
        }

        if ($sort === 'newest') {
            $alertsQuery->latest();
        } elseif ($sort === 'oldest') {
            $alertsQuery->oldest();
        }

        $alerts = $alertsQuery->paginate(10); // استخدام paginate بدل get
        return view('farmer_manager.alerts.index', compact('alerts'));
    }

    public function create()
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        return view('farmer_manager.alerts.create', compact('farm'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();

        try {
            $validated = $request->validate([
                'message' => 'required|string|max:255',
                'severity' => 'required|in:high,medium,low',
                'date' => 'required|date',
            ]);

            $alert = Alert::create([
                'farm_id' => $farm->id,
                'user_id' => $user->id,
                'message' => $request->message,
                'severity' => $request->severity,
                'date' => $request->date,
            ]);

            Log::info('Alert created by Farmer Manager', ['alert_id' => $alert->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.alerts.index')->with('success', 'Alert created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for alert creation', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create alert', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create alert.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $alert = Alert::where('farm_id', $farm->id)->findOrFail($id);
        return view('farmer_manager.alerts.edit', compact('alert'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $alert = Alert::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $validated = $request->validate([
                'message' => 'required|string|max:255',
                'severity' => 'required|in:high,medium,low',
                'date' => 'required|date',
            ]);

            $alert->update([
                'message' => $request->message,
                'severity' => $request->severity,
                'date' => $request->date,
            ]);

            Log::info('Alert updated by Farmer Manager', ['alert_id' => $alert->id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.alerts.index')->with('success', 'Alert updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for alert update', ['alert_id' => $alert->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update alert', ['alert_id' => $alert->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update alert.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->firstOrFail();
        $alert = Alert::where('farm_id', $farm->id)->findOrFail($id);

        try {
            $alert->delete();
            Log::info('Alert deleted by Farmer Manager', ['alert_id' => $id, 'farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.alerts.index')->with('success', 'Alert deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete alert', ['alert_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.alerts.index')->with('error', 'Failed to delete alert.');
        }
    }
}