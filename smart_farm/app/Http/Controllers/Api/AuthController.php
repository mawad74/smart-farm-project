<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
    
            $credentials = $request->only('email', 'password');
    
            if (!$token = Auth::guard('api')->attempt($credentials)) {
                throw ValidationException::withMessages([
                    'email' => ['The email or password you entered is incorrect. Please try again.'],
                ]);
            }
    
            $user = Auth::guard('api')->user();
            if (!$user->is_active) {
                Auth::guard('api')->logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account is inactive. Please contact the administrator to activate your account.'],
                ]);
            }
    
            // Update last login attempt
            $userForUpdate = User::find($user->id);
            $userForUpdate->last_login_attempt = now();
            $userForUpdate->save();
    
            Log::info('User logged in successfully', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                    'token' => $token,
                ],
                'message' => 'You have successfully logged in.'
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed. Please check the provided data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Login failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while trying to log in. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You are not logged in. Please log in to perform this action.',
                ], 401);
            }
    
            Auth::guard('api')->logout();
    
            Log::info('User logged out successfully', ['user_id' => $user->id]);
            return response()->json([
                'status' => 'success',
                'message' => 'You have successfully logged out.'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while trying to log out. Please try again later.',
                'errors' => [$e->getMessage()]
            ], 500);
        }
    }
}