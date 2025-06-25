<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Http\Resources\FinancialRecordResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FinancialRecordController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $farm_id = $request->input('farm_id');
            $type = $request->input('type');

            $recordsQuery = FinancialRecord::with('farm');

            // Search
            if ($search) {
                $recordsQuery->where('value', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
            }

            // Filter by farm
            if ($farm_id) {
                $recordsQuery->where('farm_id', $farm_id);
            }

            // Filter by type
            if ($type) {
                $recordsQuery->where('type', $type);
            }

            // Sort
            if ($sort === 'newest') {
                $recordsQuery->latest();
            } elseif ($sort === 'oldest') {
                $recordsQuery->oldest();
            }

            $records = $recordsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => FinancialRecordResource::collection($records),
                'message' => 'Financial records retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve financial records', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving financial records. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
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
            return response()->json([
                'status' => 'success',
                'data' => new FinancialRecordResource($record->load('farm')),
                'message' => 'Financial record created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for financial record creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create financial record', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the financial record. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $record = FinancialRecord::with('farm')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new FinancialRecordResource($record),
                'message' => 'Financial record retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve financial record', ['record_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Financial record with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $record = FinancialRecord::findOrFail($id);

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
            return response()->json([
                'status' => 'success',
                'data' => new FinancialRecordResource($record->load('farm')),
                'message' => 'Financial record updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for financial record update', ['record_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update financial record', ['record_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Financial record with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $record = FinancialRecord::findOrFail($id);
            $record->delete();

            Log::info('Financial Record deleted successfully', ['record_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Financial record with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete financial record', ['record_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Financial record with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}