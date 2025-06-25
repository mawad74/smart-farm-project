<?php

namespace App\Http\Controllers\Api\FarmerManager;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Http\Resources\FarmResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FarmController extends Controller
{
    public function __construct()
    {
        $this->middleware('role_farmer_manager');
    }

    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');

            $farmsQuery = Farm::with('user')->where('user_id', Auth::guard('api')->id());

            if ($search) {
                $farmsQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('location', 'like', "%{$search}%")
                           ->orWhereHas('user', function ($query) use ($search) {
                               $query->where('name', 'like', "%{$search}%");
                           });
            }

            if ($sort === 'newest') {
                $farmsQuery->latest();
            } elseif ($sort === 'oldest') {
                $farmsQuery->oldest();
            }

            $farms = $farmsQuery->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => FarmResource::collection($farms),
                'message' => 'Farms retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve farms for farmer', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving farms.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            $farm = Farm::create([
                'user_id' => Auth::guard('api')->id(),
                'name' => $request->name,
                'location' => $request->location,
            ]);

            Log::info('Farm created by Farmer Manager', ['farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new FarmResource($farm->load('user')),
                'message' => 'Farm created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for farm creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create farm', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the farm.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $farm = Farm::with('user')->where('user_id', Auth::guard('api')->id())->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new FarmResource($farm),
                'message' => 'Farm retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve farm for farmer', ['farm_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Farm with ID {$id} not found or you do not have access.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $farm = Farm::where('user_id', Auth::guard('api')->id())->findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
            ]);

            $farm->update($validated);

            Log::info('Farm updated by Farmer Manager', ['farm_id' => $farm->id]);
            return response()->json([
                'status' => 'success',
                'data' => new FarmResource($farm->load('user')),
                'message' => 'Farm updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for farm update', ['farm_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update farm', ['farm_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Farm with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $farm = Farm::where('user_id', Auth::guard('api')->id())->findOrFail($id);
            $farm->delete();

            Log::info('Farm deleted by Farmer Manager', ['farm_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Farm with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete farm', ['farm_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Farm with ID {$id} not found or you do not have access to delete.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}