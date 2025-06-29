<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Http\Resources\FarmResource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class FarmController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');

            $farmsQuery = Farm::with('user');

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
            Log::error('Failed to retrieve farms', ['error' => $e->getMessage()]);
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
                'user_id' => 'required|exists:users,id',
            ]);

            $farm = Farm::create($validated);

            Log::info('Farm created by Admin', ['farm_id' => $farm->id]);
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
            $farm = Farm::with('user')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new FarmResource($farm),
                'message' => 'Farm retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve farm', ['farm_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Farm with ID {$id} not found.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $farm = Farm::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'user_id' => 'required|exists:users,id',
            ]);

            $farm->update($validated);

            Log::info('Farm updated by Admin', ['farm_id' => $farm->id]);
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
            $farm = Farm::findOrFail($id);
            $farm->delete();

            Log::info('Farm deleted by Admin', ['farm_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "Farm with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete farm', ['farm_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "Farm with ID {$id} not found or an error occurred.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}