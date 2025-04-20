<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminFinancialRecordController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'newest');
        $farm_id = $request->input('farm_id');
        $type = $request->input('type');

        $recordsQuery = FinancialRecord::with('farm');

        // البحث
        if ($search) {
            $recordsQuery->where('value', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        }

        // فلترة حسب المزرعة
        if ($farm_id) {
            $recordsQuery->where('farm_id', $farm_id);
        }

        // فلترة حسب النوع
        if ($type) {
            $recordsQuery->where('type', $type);
        }

        // الترتيب
        if ($sort === 'newest') {
            $recordsQuery->latest();
        } elseif ($sort === 'oldest') {
            $recordsQuery->oldest();
        }

        $records = $recordsQuery->paginate(10);
        $farms = Farm::all(); // جلب كل المزارع للفلترة
        $types = ['resource_cost', 'labor_cost', 'revenue', 'profit_loss']; // الأنواع المتاحة للفلترة

        return view('admin.financial-records.index', compact('records', 'farms', 'types'));
    }

    public function create()
    {
        $farms = Farm::all();
        return view('admin.financial-records.create', compact('farms'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'type' => 'required|in:resource_cost,labor_cost,revenue,profit_loss',
                'value' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'timestamp' => 'required|date',
            ]);

            $record = new FinancialRecord();
            $record->farm_id = $request->farm_id;
            $record->type = $request->type;
            $record->value = $request->value;
            $record->description = $request->description;
            $record->timestamp = $request->timestamp;
            $record->save();

            Log::info('Financial Record created successfully', ['record_id' => $record->id]);
            return redirect()->route('admin.financial-records.index')->with('success', 'Financial Record created successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for financial record creation', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to create financial record', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create financial record: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $record = FinancialRecord::findOrFail($id);
        $farms = Farm::all();
        return view('admin.financial-records.edit', compact('record', 'farms'));
    }

    public function update(Request $request, $id)
    {
        $record = FinancialRecord::findOrFail($id);

        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'type' => 'required|in:resource_cost,labor_cost,revenue,profit_loss',
                'value' => 'required|numeric|min:0',
                'description' => 'nullable|string|max:1000',
                'timestamp' => 'required|date',
            ]);

            $record->farm_id = $request->farm_id;
            $record->type = $request->type;
            $record->value = $request->value;
            $record->description = $request->description;
            $record->timestamp = $request->timestamp;
            $record->save();

            Log::info('Financial Record updated successfully', ['record_id' => $record->id]);
            return redirect()->route('admin.financial-records.index')->with('success', 'Financial Record updated successfully.');
        } catch (ValidationException $e) {
            Log::error('Validation failed for financial record update', ['record_id' => $record->id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Failed to update financial record', ['record_id' => $record->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to update financial record: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $record = FinancialRecord::findOrFail($id);
            $record->delete();

            Log::info('Financial Record deleted successfully', ['record_id' => $id]);
            return redirect()->route('admin.financial-records.index')->with('success', 'Financial Record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete financial record', ['record_id' => $id, 'error' => $e->getMessage()]);
            return redirect()->route('admin.financial-records.index')->with('error', 'Failed to delete financial record: ' . $e->getMessage());
        }
    }
}