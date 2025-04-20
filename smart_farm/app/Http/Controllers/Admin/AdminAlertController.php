<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminAlertController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $type = $request->input('type');
        $farm_id = $request->input('farm_id');
        $status = $request->input('status');

        $alertsQuery = Alert::with('farm');

        // البحث
        if ($search) {
            $alertsQuery->where('message', 'like', "%{$search}%");
        }

        // فلترة حسب النوع
        if ($type) {
            $alertsQuery->where('type', $type);
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $alertsQuery->where('farm_id', $farm_id);
        }

        // فلترة حسب الحالة
        if ($status) {
            $alertsQuery->where('status', $status);
        }

        // الترتيب
        if ($sort === 'newest') {
            $alertsQuery->latest();
        } elseif ($sort === 'oldest') {
            $alertsQuery->oldest();
        }

        $alerts = $alertsQuery->paginate(10);
        $farms = Farm::all(); // جلب كل المزارع للفلترة
        return view('admin.alerts.index', compact('alerts', 'farms'));
    }

    public function edit($id)
    {
        $alert = Alert::findOrFail($id);
        return view('admin.alerts.edit', compact('alert'));
    }

    public function update(Request $request, $id)
    {
        $alert = Alert::findOrFail($id);

        try {
            $validated = $request->validate([
                'status' => 'required|in:pending,dismissed,resolved',
            ]);

            $alert->status = $request->status;
            $alert->save();

            Log::info('Alert updated successfully', ['alert_id' => $alert->id]);
            return redirect()->route('admin.alerts.index')->with('success', 'Alert updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update alert', ['alert_id' => $alert->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update alert: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $alert = Alert::findOrFail($id);
            $alert->delete();

            Log::info('Alert deleted successfully', ['alert_id' => $id]);
            return redirect()->route('admin.alerts.index')->with('success', 'Alert deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete alert', ['alert_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.alerts.index')->with('error', 'Failed to delete alert: ' . $e->getMessage());
        }
    }
}