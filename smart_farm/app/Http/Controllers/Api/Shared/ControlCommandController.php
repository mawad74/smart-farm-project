<?php

namespace App\Http\Controllers\Api\Shared;

use App\Http\Controllers\Controller;
use App\Models\ControlCommand;
use App\Http\Resources\ControlCommandResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class ControlCommandController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $actuator_id = $request->input('actuator_id');
            $user_id = $request->input('user_id');
            $status = $request->input('status');

            $commandsQuery = ControlCommand::with('actuator', 'user');

            // Search
            if ($search) {
                $commandsQuery->where('command_type', 'like', "%{$search}%")
                              ->orWhereHas('user', function ($query) use ($search) {
                                  $query->where('name', 'like', "%{$search}%");
                              })
                              ->orWhereHas('actuator', function ($query) use ($search) {
                                  $query->where('type', 'like', "%{$search}%");
                              });
            }

            // Filter by actuator
            if ($actuator_id) {
                $commandsQuery->where('actuator_id', $actuator_id);
            }

            // Filter by user
            if ($user_id) {
                $commandsQuery->where('user_id', $user_id);
            }

            // Filter by status
            if ($status !== null) {
                $commandsQuery->where('status', $status);
            }

            // Sort
            if ($sort === 'newest') {
                $commandsQuery->latest();
            } elseif ($sort === 'oldest') {
                $commandsQuery->oldest();
            }

            $commands = $commandsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => ControlCommandResource::collection($commands),
                'message' => 'Control Commands retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve control commands', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving control commands. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'actuator_id' => 'required|exists:actuators,id',
                'user_id' => 'required|exists:users,id',
                'command_type' => 'required|in:turn_on,turn_off,toggle', // تحديد قيم معينة
                'executed_at' => 'required|date',
                'status' => 'required|boolean',
            ]);

            $command = new ControlCommand();
            $command->actuator_id = $request->actuator_id;
            $command->user_id = $request->user_id;
            $command->command_type = $request->command_type;
            $command->executed_at = $request->executed_at;
            $command->status = $request->status;
            $command->save();

            Log::info('Control Command created successfully', ['command_id' => $command->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ControlCommandResource($command->load(['actuator', 'user'])),
                'message' => 'Control Command created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for control command creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create control command', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the control command. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $command = ControlCommand::with('actuator', 'user')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ControlCommandResource($command),
                'message' => 'Control Command retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve control command', ['command_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Control Command with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $command = ControlCommand::findOrFail($id);

            $validated = $request->validate([
                'actuator_id' => 'required|exists:actuators,id',
                'user_id' => 'required|exists:users,id',
                'command_type' => 'required|in:turn_on,turn_off,toggle', // تحديد قيم معينة
                'executed_at' => 'required|date',
                'status' => 'required|boolean',
            ]);

            $command->actuator_id = $request->actuator_id;
            $command->user_id = $request->user_id;
            $command->command_type = $request->command_type;
            $command->executed_at = $request->executed_at;
            $command->status = $request->status;
            $command->save();

            Log::info('Control Command updated successfully', ['command_id' => $command->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ControlCommandResource($command->load(['actuator', 'user'])),
                'message' => 'Control Command updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for control command update', ['command_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update control command', ['command_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Control Command with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $command = ControlCommand::findOrFail($id);
            $command->delete();

            Log::info('Control Command deleted successfully', ['command_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Control Command with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete control command', ['command_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Control Command with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}