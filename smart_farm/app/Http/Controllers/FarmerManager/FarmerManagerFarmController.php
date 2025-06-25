<?php

namespace App\Http\Controllers\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmerManagerFarmController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('farmer_manager');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $farmsQuery = Farm::where('user_id', $user->id);

        if ($search) {
            $farmsQuery->where('name', 'like', "%{$search}%")
                       ->orWhere('location', 'like', "%{$search}%");
        }

        if ($sort === 'newest') {
            $farmsQuery->latest();
        } elseif ($sort === 'oldest') {
            $farmsQuery->oldest();
        }

        $farms = $farmsQuery->paginate(10);
        return view('farmer_manager.farms.index', compact('farms'));
    }

    public function create()
    {
        $user = auth()->user();
        return view('farmer_manager.farms.create', compact('user'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);
            $farm = Farm::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'location' => $request->location,
            ]);
            Log::info('Farm created by Farmer Manager', ['farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.farms.index')->with('success', 'Farm created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create farm', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to create farm.');
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->findOrFail($id);
        return view('farmer_manager.farms.edit', compact('farm'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->findOrFail($id);
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);
            $farm->update([
                'name' => $request->name,
                'location' => $request->location,
            ]);
            Log::info('Farm updated by Farmer Manager', ['farm_id' => $farm->id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.farms.index')->with('success', 'Farm updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed', ['farm_id' => $farm->id, 'errors' => $e->errors(), 'user_id' => $user->id]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update farm', ['farm_id' => $farm->id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to update farm.');
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $farm = Farm::where('user_id', $user->id)->findOrFail($id);
        try {
            $farm->delete();
            Log::info('Farm deleted by Farmer Manager', ['farm_id' => $id, 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.farms.index')->with('success', 'Farm deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete farm', ['farm_id' => $id, 'error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->route('farmer_manager.farms.index')->with('error', 'Failed to delete farm.');
        }
    }
}