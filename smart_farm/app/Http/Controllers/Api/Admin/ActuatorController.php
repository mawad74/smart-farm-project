<?php

namespace App\Http\Controllers\Api\Admin;

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

            if ($search) {
                $actuatorsQuery->where('type', 'like', "%{$search}%")
                               ->orWhere('action_type', 'like', "%{$search}%")
                               ->orWhereHas('farm', function ($query) use ($search) {
                                   $query->where('name', 'like', "%{$search}%");
                               })
                               ->orWhereHas('plant', function ($query) use ($search) {
                                   $query->where('name', 'like', "%{$search}%");
                               });
            }

            if ($type) {
                $actuatorsQuery->where('type', $type);
            }

            if ($farm_id) {
                $actuatorsQuery->where('farm_id', $farm_id);
            }

            if ($status) {
                $actuatorsQuery->where('status', $status);
            }

            if ($action_type) {
                $actuatorsQuery->where('action_type', $action_type);
            }

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
                'message' => 'An unexpected error occurred while retrieving actuators.',
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
                'type' => 'required|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255', // يمكن نحدده لاحقاً زي on/off/toggle
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator = Actuator::create($validated);

            Log::info('Actuator created by Admin', ['actuator_id' => $actuator->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator->load(['farm', 'plant'])),
                'message' => 'Actuator created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create actuator', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the actuator.',
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
                'message' => "Actuator with ID {$id} not found.",
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
                'type' => 'required|in:irrigation_pump,ventilation,lighting',
                'status' => 'required|in:active,inactive,faulty',
                'action_type' => 'nullable|string|max:255',
                'last_triggered_at' => 'nullable|date',
            ]);

            $actuator->update($validated);

            Log::info('Actuator updated by Admin', ['actuator_id' => $actuator->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ActuatorResource($actuator->load(['farm', 'plant'])),
                'message' => 'Actuator updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for actuator update', ['actuator_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $actuator = Actuator::findOrFail($id);
            $actuator->delete();

            Log::info('Actuator deleted by Admin', ['actuator_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Actuator with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete actuator', ['actuator_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Actuator with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}