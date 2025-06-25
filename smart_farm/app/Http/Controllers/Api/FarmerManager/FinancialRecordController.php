<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\FinancialRecord;
use App\Http\Resources\FinancialRecordResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FinancialRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();

            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
            $type = $request->input('type');

            $recordsQuery = FinancialRecord::where('farm_id', $farm->id)->with('farm');

            if ($search) {
                $recordsQuery->where('value', 'like', "%{$search}%")
                             ->orWhere('description', 'like', "%{$search}%");
            }

            if ($type) {
                $recordsQuery->where('type', $type);
            }

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
            Log::error('Failed to retrieve financial records for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving financial records.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::guard('api')->user();
            $farm = $user->farms()->firstOrFail();
            $record = FinancialRecord::where('farm_id', $farm->id)->with('farm')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => new FinancialRecordResource($record),
                'message' => 'Financial record retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve financial record for farmer', ['record_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Financial record with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }
}