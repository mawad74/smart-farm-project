<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Report;
use App\Models\Alert;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class FarmerManagerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $farm = Farm::where('user_id', $user->id)->first(); // المزرعة بتاعة الـ Farmer Manager

        // التحقق من وجود مزرعة
        if (!$farm) {
            return redirect('/dashboard')->with('error', 'No farm associated with your account. Please contact the admin to assign a farm.');
        }

        $reports = Report::where('user_id', $user->id)->count(); // عدد التقارير
        $recentAlerts = Alert::where('user_id', $user->id)->latest()->take(5)->get(); // آخر 5 تنبيهات
        $pendingTasks = Task::where('farm_id', $farm->id)->where('status', 'pending')->count(); // عدد المهام المعلقة

        return view('farmer_manager.dashboard', compact('farm', 'reports', 'recentAlerts', 'pendingTasks'));
    }
}