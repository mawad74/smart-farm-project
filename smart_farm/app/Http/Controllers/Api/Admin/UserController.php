<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $sort = $request->input('sort', 'newest');
    
            $usersQuery = User::with('farms');
    
            // Search
            if ($search) {
                $usersQuery->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
            }
    
            // Sort
            if ($sort === 'newest') {
                $usersQuery->latest();
            } elseif ($sort === 'oldest') {
                $usersQuery->oldest();
            }
    
            $users = $usersQuery->paginate(10);
    
            return response()->json([
                'status' => 'success',
                'data' => UserResource::collection($users),
                'message' => 'Users retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve users', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while retrieving users. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:admin,farmer_manager',
                'is_active' => 'required|boolean',
            ]);
    
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role = $request->role;
            $user->is_active = $request->is_active;
            $user->save();
    
            Log::info('User created successfully', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'data' => new UserResource($user),
                'message' => 'User created successfully.'
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation failed for user creation', ['errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create user', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while creating the user. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::with('farms')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => new UserResource($user),
                'message' => 'User retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve user', ['user_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "User with ID {$id} not found. Please check the ID and try again.",
                'errors' => [$e->getMessage()]
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
    
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed',
                'role' => 'required|in:admin,farmer_manager',
                'is_active' => 'required|boolean',
            ]);
    
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role = $request->role;
            $user->is_active = $request->is_active;
    
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
    
            $user->save();
    
            Log::info('User updated successfully', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'data' => new UserResource($user),
                'message' => 'User updated successfully.'
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed for user update', ['user_id' => $id, 'errors' => $e->errors()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data and try again.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update user', ['user_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "User with ID {$id} not found or an unexpected error occurred while updating. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
    
            Log::info('User deleted successfully', ['user_id' => $id]);
            return response()->json([
                'status' => 'success',
                'message' => "User with ID {$id} deleted successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete user', ['user_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => "User with ID {$id} not found or an unexpected error occurred while deleting. Please try again.",
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}