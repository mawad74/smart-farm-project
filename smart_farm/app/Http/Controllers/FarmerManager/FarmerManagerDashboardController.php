<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Alert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerManagerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $farm = Farm::where('user_id', $user->id)->first();

        if (!$farm) {
            return redirect('/dashboard')->with('error', 'No farm associated with your account. Please contact the admin.');
        }

        $recentAlerts = Alert::where('user_id', $user->id)->latest()->take(5)->get();

        return view('farmer_manager.dashboard', compact('farm', 'recentAlerts'));
    }
}