<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Shared\FarmController;
use App\Http\Controllers\Api\Shared\PlantController;
use App\Http\Controllers\Api\Shared\SensorController;
use App\Http\Controllers\Api\Shared\ActuatorController;
use App\Http\Controllers\Api\Shared\ScheduleController;
use App\Http\Controllers\Api\Shared\ControlCommandController;
use App\Http\Controllers\Api\Shared\DiseaseDetectionController;
use App\Http\Controllers\Api\Shared\AlertController;
use App\Http\Controllers\Api\AuthController;

// API Routes
Route::group([], function () {
    // Public Routes (No Authentication)
    Route::post('login', [AuthController::class, 'login']);

    // Protected Routes (Require Authentication)
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // Admin-only Routes
        Route::prefix('admin')->middleware('role_admin')->group(function () {
            Route::get('users', [UserController::class, 'index']);
            Route::post('users', [UserController::class, 'store']);
            Route::get('users/{id}', [UserController::class, 'show']);
            Route::put('users/{id}', [UserController::class, 'update']);
            Route::delete('users/{id}', [UserController::class, 'destroy']);
        });

        // Shared Routes (Admin and Farmer Manager)
        Route::prefix('shared')->group(function () {
            // Farms Routes
            Route::get('farms', [FarmController::class, 'index']);
            Route::post('farms', [FarmController::class, 'store']);
            Route::get('farms/{id}', [FarmController::class, 'show']);
            Route::put('farms/{id}', [FarmController::class, 'update']);
            Route::delete('farms/{id}', [FarmController::class, 'destroy']);

            // Plants Routes
            Route::get('plants', [PlantController::class, 'index']);
            Route::post('plants', [PlantController::class, 'store']);
            Route::get('plants/{id}', [PlantController::class, 'show']);
            Route::put('plants/{id}', [PlantController::class, 'update']);
            Route::delete('plants/{id}', [PlantController::class, 'destroy']);

            // Sensors Routes
            Route::get('sensors', [SensorController::class, 'index']);
            Route::post('sensors', [SensorController::class, 'store']);
            Route::get('sensors/{id}', [SensorController::class, 'show']);
            Route::get('sensors/{id}/readings', [SensorController::class, 'readings']);
            Route::put('sensors/{id}', [SensorController::class, 'update']);
            Route::delete('sensors/{id}', [SensorController::class, 'destroy']);

            // Actuators Routes
            Route::get('actuators', [ActuatorController::class, 'index']);
            Route::post('actuators', [ActuatorController::class, 'store']);
            Route::get('actuators/{id}', [ActuatorController::class, 'show']);
            Route::put('actuators/{id}', [ActuatorController::class, 'update']);
            Route::delete('actuators/{id}', [ActuatorController::class, 'destroy']);

            // Schedules Routes
            Route::get('schedules', [ScheduleController::class, 'index']);
            Route::post('schedules', [ScheduleController::class, 'store']);
            Route::get('schedules/{id}', [ScheduleController::class, 'show']);
            Route::put('schedules/{id}', [ScheduleController::class, 'update']);
            Route::delete('schedules/{id}', [ScheduleController::class, 'destroy']);

            // Control Commands Routes
            Route::get('control-commands', [ControlCommandController::class, 'index']);
            Route::post('control-commands', [ControlCommandController::class, 'store']);
            Route::get('control-commands/{id}', [ControlCommandController::class, 'show']);
            Route::put('control-commands/{id}', [ControlCommandController::class, 'update']);
            Route::delete('control-commands/{id}', [ControlCommandController::class, 'destroy']);

            // Disease Detections Routes
            Route::get('disease-detections', [DiseaseDetectionController::class, 'index']);
            Route::post('disease-detections', [DiseaseDetectionController::class, 'store']);
            Route::get('disease-detections/{id}', [DiseaseDetectionController::class, 'show']);
            Route::put('disease-detections/{id}', [DiseaseDetectionController::class, 'update']);
            Route::delete('disease-detections/{id}', [DiseaseDetectionController::class, 'destroy']);

            // Alerts Routes
            Route::get('alerts', [AlertController::class, 'index']);
            Route::post('alerts', [AlertController::class, 'store']);
            Route::get('alerts/{id}', [AlertController::class, 'show']);
            Route::put('alerts/{id}', [AlertController::class, 'update']);
            Route::delete('alerts/{id}', [AlertController::class, 'destroy']);
        });
    });

    // Catch-all Route for GET Requests (Browser Access)
    Route::get('{any}', function () {
        return response()->json([
            'status' => 'error',
            'message' => 'Please log in and use an API client like Postman to access this endpoint.',
            'instructions' => [
                'step_1' => 'Log in using POST /api/login with your email and password to get a JWT token.',
                'step_2' => 'Use the token in the Authorization header (Bearer <token>) for authenticated requests.',
                'example' => [
                    'method' => 'POST',
                    'url' => 'http://127.0.0.1:8000/api/login',
                    'headers' => [
                        'Accept' => 'application/json'
                    ],
                    'body' => [
                        'email' => 'admin@example.com',
                        'password' => 'password'
                    ]
                ]
            ]
        ], 401);
    })->where('any', '.*');
});