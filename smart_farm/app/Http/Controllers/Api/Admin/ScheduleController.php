<?php

namespace App\Http\Controllers\Api\Admin;

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

            if ($search) {
                $schedulesQuery->whereHas('farm', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('plant', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('actuator', function ($query) use ($search) {
                    $query->where('type', 'like', "%{$search}%");
                });
            }

            if ($farm_id) {
                $schedulesQuery->where('farm_id', $farm_id);
            }

            if ($plant_id) {
                $schedulesQuery->where('plant_id', $plant_id);
            }

            if ($actuator_id) {
                $schedulesQuery->where('actuator_id', $actuator_id);
            }

            if ($status) {
                $schedulesQuery->where('status', $status);
            }

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
                'message' => 'An unexpected error occurred while retrieving schedules.',
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

            $schedule = Schedule::create($validated);

            Log::info('Schedule created by Admin', ['schedule_id' => $schedule->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule->load(['farm', 'plant', 'actuator'])),
                'message' => 'Schedule created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create schedule', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the schedule.',
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
                'message' => "Schedule with ID {$id} not found.",
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

            $schedule->update($validated);

            Log::info('Schedule updated by Admin', ['schedule_id' => $schedule->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ScheduleResource($schedule->load(['farm', 'plant', 'actuator'])),
                'message' => 'Schedule updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for schedule update', ['schedule_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();

            Log::info('Schedule deleted by Admin', ['schedule_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Schedule with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete schedule', ['schedule_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Schedule with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}