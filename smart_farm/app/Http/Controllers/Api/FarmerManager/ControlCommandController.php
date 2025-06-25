<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\ControlCommand;
use App\Http\Resources\ControlCommandResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ControlCommandController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $actuator_id = $request->input('actuator_id');
            $status = $request->input('status');

            $commandsQuery = ControlCommand::where('user_id', $user->id)->with('actuator', 'user');

            if ($search) {
                $commandsQuery->where('command_type', 'like', "%{$search}%")
                              ->orWhereHas('actuator', function ($query) use ($search) {
                                  $query->where('type', 'like', "%{$search}%");
                              });
            }

            if ($actuator_id) {
                $commandsQuery->where('actuator_id', $actuator_id);
            }

            if ($status !== null) {
                $commandsQuery->where('status', $status);
            }

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
            Log::error('Failed to retrieve control commands for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving control commands.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            $validated = $request->validate([
                'actuator_id' => 'required|exists:actuators,id',
                'command_type' => 'required|string|max:255',
                'executed_at' => 'required|date',
                'status' => 'required|boolean',
            ]);

            $command = ControlCommand::create([
                'actuator_id' => $request->actuator_id,
                'user_id' => $user->id,
                'command_type' => $request->command_type,
                'executed_at' => $request->executed_at,
                'status' => $request->status,
            ]);

            Log::info('Control Command created by Farmer Manager', ['command_id' => $command->id, 'user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ControlCommandResource($command->load(['actuator', 'user'])),
                'message' => 'Control Command created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for control command creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create control command', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the control command.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $command = ControlCommand::where('user_id', $user->id)->with('actuator', 'user')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new ControlCommandResource($command),
                'message' => 'Control Command retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve control command for farmer', ['command_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Control Command with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::guard('api')->user();
            $command = ControlCommand::where('user_id', $user->id)->findOrFail($id);

            $validated = $request->validate([
                'actuator_id' => 'required|exists:actuators,id',
                'command_type' => 'required|string|max:255',
                'executed_at' => 'required|date',
                'status' => 'required|boolean',
            ]);

            $command->update($validated);

            Log::info('Control Command updated by Farmer Manager', ['command_id' => $command->id, 'user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'data' => new ControlCommandResource($command->load(['actuator', 'user'])),
                'message' => 'Control Command updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for control command update', ['command_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update control command', ['command_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Control Command with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $command = ControlCommand::where('user_id', $user->id)->findOrFail($id);
            $command->delete();

            Log::info('Control Command deleted by Farmer Manager', ['command_id' => $id, 'user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'message' => "Control Command with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete control command', ['command_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Control Command with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}
