<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $farm_id = $request->input('farm_id');
            $plant_id = $request->input('plant_id');
            $actuator_id = $request->input('actuator_id');
            $status = $request->input('status');

            $schedulesQuery = Schedule::with('farm', 'plant', 'actuator');

            // Search
            if ($search) {
                $schedulesQuery->whereHas('farm', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('plant', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('actuator', function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                });
            }

            // Filter by farm
            if ($farm_id) {
                $schedulesQuery->where('farm_id', $farm_id);
            }

            // Filter by plant
            if ($plant_id) {
                $schedulesQuery->where('plant_id', $plant_id);
            }

            // Filter by actuator
            if ($actuator_id) {
                $schedulesQuery->where('actuator_id', $actuator_id);
            }

            // Filter by status
            if ($status) {
                $schedulesQuery->where('status', $status);
            }

            // Sort
            if ($sort === 'newest') {
                $schedulesQuery->latest();
            } elseif ($sort === 'oldest') {
                $schedulesQuery->oldest();
            }

            $schedules = $schedulesQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ScheduleResource::collection($schedules),
                'message' => 'Schedules retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve schedules', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving schedules. Please try again later.',
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
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'required|boolean',
                'priority_zone' => 'nullable|integer',
            ]);

            $schedule = new Schedule();
            $schedule->farm_id = $request->farm_id;
            $schedule->plant_id = $request->plant_id;
            $schedule->actuator_id = $request->actuator_id;
            $schedule->schedule_time = $request->schedule_time;
            $schedule->status = $request->status;
            $schedule->weather_forecast_integration = $request->weather_forecast_integration;
            $schedule->priority_zone = $request->priority_zone;
            $schedule->save();

            Log::info('Schedule created successfully', ['schedule_id' => $schedule->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule->load(['farm', 'plant', 'actuator'])),
                'message' => 'Schedule created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create schedule', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the schedule. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $schedule = Schedule::with('farm', 'plant', 'actuator')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule),
                'message' => 'Schedule retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);

            $validated = $request->validate([
                'farm_id' => 'required|exists:farms,id',
                'plant_id' => 'required|exists:plants,id',
                'actuator_id' => 'required|exists:actuators,id',
                'schedule_time' => 'required|date',
                'status' => 'required|in:pending,completed',
                'weather_forecast_integration' => 'required|boolean',
                'priority_zone' => 'nullable|integer',
            ]);

            $schedule->farm_id = $request->farm_id;
            $schedule->plant_id = $request->plant_id;
            $schedule->actuator_id = $request->actuator_id;
            $schedule->schedule_time = $request->schedule_time;
            $schedule->status = $request->status;
            $schedule->weather_forecast_integration = $request->weather_forecast_integration;
            $schedule->priority_zone = $request->priority_zone;
            $schedule->save();

            Log::info('Schedule updated successfully', ['schedule_id' => $schedule->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule->load(['farm', 'plant', 'actuator'])),
                'message' => 'Schedule updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule update', ['schedule_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();

            Log::info('Schedule deleted successfully', ['schedule_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Schedule with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}