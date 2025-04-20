<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminSettingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $farm_id = $request->input('farm_id');
        $parameter = $request->input('parameter');

        $settingsQuery = Setting::with('farm');

        // البحث
        if ($search) {
            $settingsQuery->where('parameter', 'like', "%{$search}%")
                          ->orWhere('value', 'like', "%{$search}%");
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $settingsQuery->where('farm_id', $farm_id);
        }

        // فلترة حسب الـ Parameter
        if ($parameter) {
            $settingsQuery->where('parameter', $parameter);
        }

        // الترتيب
        if ($sort === 'newest') {
            $settingsQuery->latest();
        } elseif ($sort === 'oldest') {
            $settingsQuery->oldest();
        }

        $settings = $settingsQuery->paginate(10);
        $farms = Farm::all(); // جلب كل المزارع للفلترة
        $parameters = Setting::select('parameter')->distinct()->pluck('parameter'); // جلب أسماء الـ Parameters المميزة للفلترة

        return view('admin.settings.index', compact('settings', 'farms', 'parameters'));
    }

    public function create()
    {
        $farms = Farm::all();
        return view('admin.settings.create', compact('farms'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'parameter' => 'required|string|max:255',
                'value' => 'required|numeric',
            ]);

            $setting = new Setting();
            $setting->farm_id = $request->farm_id;
            $setting->parameter = $request->parameter;
            $setting->value = $request->value;
            $setting->save();

            Log::info('Setting created successfully', ['setting_id' => $setting->id]);
            return redirect()->route('admin.settings.index')->with('success', 'Setting created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for setting creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create setting', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create setting: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $setting = Setting::findOrFail($id);
        $farms = Farm::all();
        return view('admin.settings.edit', compact('setting', 'farms'));
    }

    public function update(Request $request, $id)
    {
        $setting = Setting::findOrFail($id);

        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'parameter' => 'required|string|max:255',
                'value' => 'required|numeric',
            ]);

            $setting->farm_id = $request->farm_id;
            $setting->parameter = $request->parameter;
            $setting->value = $request->value;
            $setting->save();

            Log::info('Setting updated successfully', ['setting_id' => $setting->id]);
            return redirect()->route('admin.settings.index')->with('success', 'Setting updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for setting update', ['setting_id' => $setting->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update setting', ['setting_id' => $setting->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update setting: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $setting = Setting::findOrFail($id);
            $setting->delete();

            Log::info('Setting deleted successfully', ['setting_id' => $id]);
            return redirect()->route('admin.settings.index')->with('success', 'Setting deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete setting', ['setting_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.settings.index')->with('error', 'Failed to delete setting: ' . $e->getMessage());
        }
    }
}