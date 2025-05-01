<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\Actuator;
use App\Http\Resources\ActuatorResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ActuatorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $type = $request->input('type');
            $farm_id = $request->input('farm_id');
            $status = $request->input('status');
            $action_type = $request->input('action_type');

            $actuatorsQuery = Actuator::with('farm', 'plant');

            // Search
            if ($search) {
                $actuatorsQuery->where('type', 'like', "%{$search}%");
            }

            // Filter by type
            if ($type) {
                $actuatorsQuery->where('type', $type);
            }

            // Filter by farm
            if ($farm_id) {
                $actuatorsQuery->where('farm_id', $farm_id);
            }

            // Filter by status
            if ($status) {
                $actuatorsQuery->where('status', $status);
            }

            // Filter by action_type
            if ($action_type) {
                $actuatorsQuery->where('action_type', $action_type);
            }

            // Sort
            if ($sort === 'newest') {
                $actuatorsQuery->latest();
            } elseif ($sort === 'oldest') {
                $actuatorsQuery->oldest();
            }

            $actuators = $actuatorsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ActuatorResource::collection($actuators),
                'message' => 'Actuators retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve actuators', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving actuators. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'type' => 'required|in:irrigation_pump,ventilation,lighting', // تعديل القيم لتتطابق مع الـ Migration
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'required|in:on,off,toggle',
                'last_triggered_at' => 'nullable|date',
            ]);
    
            $actuator = new Actuator();
            $actuator->farm_id = $request->farm_id;
            $actuator->plant_id = $request->plant_id;
            $actuator->type = $request->type;
            $actuator->status = $request->status;
            $actuator->action_type = $request->action_type;
            $actuator->last_triggered_at = $request->last_triggered_at;
            $actuator->save();
    
            Log::info('Actuator created successfully', ['actuator_id' => $actuator->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator->load(['farm', 'plant'])),
                'message' => 'Actuator created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create actuator', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the actuator. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $actuator = Actuator::with('farm', 'plant')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator),
                'message' => 'Actuator retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $actuator = Actuator::findOrFail($id);
    
            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'type' => 'required|in:irrigation_pump,ventilation,lighting', // تعديل القيم لتتطابق مع الـ Migration
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'required|in:on,off,toggle',
                'last_triggered_at' => 'nullable|date',
            ]);
    
            $actuator->farm_id = $request->farm_id;
            $actuator->plant_id = $request->plant_id;
            $actuator->type = $request->type;
            $actuator->status = $request->status;
            $actuator->action_type = $request->action_type;
            $actuator->last_triggered_at = $request->last_triggered_at;
            $actuator->save();
    
            Log::info('Actuator updated successfully', ['actuator_id' => $actuator->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator->load(['farm', 'plant'])),
                'message' => 'Actuator updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator update', ['actuator_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $actuator = Actuator::findOrFail($id);
            $actuator->delete();

            Log::info('Actuator deleted successfully', ['actuator_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Actuator with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}