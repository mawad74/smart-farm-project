<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Http\Resources\LogResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log as Logger;

class LogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $user_id = $request->input('user_id');
            $farm_id = $request->input('farm_id');
            $status = $request->input('status');

            $logsQuery = Log::with('user', 'farm');

            // Search
            if ($search) {
                $logsQuery->where('action', 'like', "%{$search}%")
                          ->orWhere('message', 'like', "%{$search}%");
            }

            // Filter by user
            if ($user_id) {
                $logsQuery->where('user_id', $user_id);
            }

            // Filter by farm
            if ($farm_id) {
                $logsQuery->where('farm_id', $farm_id);
            }

            // Filter by status
            if ($status) {
                $logsQuery->where('status', $status);
            }

            // Sort
            if ($sort === 'newest') {
                $logsQuery->latest();
            } elseif ($sort === 'oldest') {
                $logsQuery->oldest();
            }

            $logs = $logsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => LogResource::collection($logs),
                'message' => 'Logs retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Logger::error('Failed to retrieve logs', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving logs. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $log = Log::with('user', 'farm')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new LogResource($log),
                'message' => 'Log retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Logger::error('Failed to retrieve log', ['log_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Log with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }
}