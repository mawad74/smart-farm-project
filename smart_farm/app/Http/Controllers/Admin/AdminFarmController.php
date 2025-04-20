<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminFarmController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');

        $farmsQuery = Farm::with('user');

        // البحث
        if ($search) {
            $farmsQuery->where('name', 'like', "%{$search}%")
                       ->orWhere('location', 'like', "%{$search}%")
                       ->orWhereHas('user', function ($query) use ($search) {
                           $query->where('name', 'like', "%{$search}%");
                       });
        }

        // الترتيب
        if ($sort === 'newest') {
            $farmsQuery->latest();
        } elseif ($sort === 'oldest') {
            $farmsQuery->oldest();
        }

        $farms = $farmsQuery->paginate(10);
        return view('admin.farms.index', compact('farms'));
    }

    public function create()
    {
        $users = User::where('role', 'farmer_manager')->get(); // جلب المستخدمين اللي دورهم Farmer Manager
        return view('admin.farms.create', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
            ]);

            $farm = new Farm();
            $farm->name = $request->name;
            $farm->location = $request->location;
            $farm->user_id = $request->user_id;
            $farm->save();

            Log::info('Farm created successfully', ['farm_id' => $farm->id]);
            return redirect()->route('admin.farms.index')->with('success', 'Farm created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for farm creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create farm', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create farm: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $farm = Farm::findOrFail($id);
        $users = User::where('role', 'farmer_manager')->get();
        return view('admin.farms.edit', compact('farm', 'users'));
    }

    public function update(Request $request, $id)
    {
        $farm = Farm::findOrFail($id);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
            ]);

            $farm->name = $request->name;
            $farm->location = $request->location;
            $farm->user_id = $request->user_id;
            $farm->save();

            Log::info('Farm updated successfully', ['farm_id' => $farm->id]);
            return redirect()->route('admin.farms.index')->with('success', 'Farm updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for farm update', ['farm_id' => $farm->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update farm', ['farm_id' => $farm->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update farm: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $farm = Farm::findOrFail($id);
            $farm->delete();

            Log::info('Farm deleted successfully', ['farm_id' => $id]);
            return redirect()->route('admin.farms.index')->with('success', 'Farm deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete farm', ['farm_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.farms.index')->with('error', 'Failed to delete farm: ' . $e->getMessage());
        }
    }
}