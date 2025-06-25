<?php

use App\Http\Controllers\Api\Admin\ActuatorController as AdminActuatorController;
use App\Http\Controllers\Api\Admin\AlertController as AdminAlertController;
use App\Http\Controllers\Api\Admin\ControlCommandController as AdminControlCommandController;
use App\Http\Controllers\Api\Admin\DiseaseDetectionController as AdminDiseaseDetectionController;
use App\Http\Controllers\Api\Admin\FarmController as AdminFarmController;
use App\Http\Controllers\Api\Admin\FinancialRecordController;
use App\Http\Controllers\Api\Admin\PlantController as AdminPlantController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\ReportRequestController;
use App\Http\Controllers\Api\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Api\Admin\SensorController as AdminSensorController;
use App\Http\Controllers\Api\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\WeatherDataController as AdminWeatherDataController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Devices\SensorDataController;
use App\Http\Controllers\Api\FarmerManager\ActuatorController as FarmerManagerActuatorController;
use App\Http\Controllers\Api\FarmerManager\AlertController as FarmerManagerAlertController;
use App\Http\Controllers\Api\FarmerManager\ControlCommandController as FarmerManagerControlCommandController;
use App\Http\Controllers\Api\FarmerManager\DiseaseDetectionController as FarmerManagerDiseaseDetectionController;
use App\Http\Controllers\Api\FarmerManager\FarmController as FarmerManagerFarmController;
use App\Http\Controllers\Api\FarmerManager\FinancialRecordController as FarmerManagerFinancialRecordController;
use App\Http\Controllers\Api\FarmerManager\PlantController as FarmerManagerPlantController;
use App\Http\Controllers\Api\FarmerManager\ReportController as FarmerManagerReportController;
use App\Http\Controllers\Api\FarmerManager\ReportRequestController as FarmerManagerReportRequestController;
use App\Http\Controllers\Api\FarmerManager\ScheduleController as FarmerManagerScheduleController;
use App\Http\Controllers\Api\FarmerManager\SensorController as FarmerManagerSensorController;
use App\Http\Controllers\Api\FarmerManager\SubscriptionController as FarmerManagerSubscriptionController;
use App\Http\Controllers\Api\FarmerManager\WeatherDataController as FarmerManagerWeatherDataController;
use App\Http\Controllers\Api\Sensor\StoreHumiditySensorController;
use App\Http\Controllers\Api\Sensor\StoreLdrSensorController;
use App\Http\Controllers\Api\Sensor\StoreSoilMoistureSensorController;
use App\Http\Controllers\Api\Sensor\StoreTemperatureSensorController;
use Illuminate\Support\Facades\Route;

// API Routes
Route::group([], function () {
    // Public Routes (No Authentication)
    Route::post('login', [AuthController::class, 'login']);
    // Devices Route (No Authentication)
    Route::prefix('devices')->group(function () {
        Route::post('sensors/data', [SensorDataController::class, 'store']);
    });
});

Route::prefix('sensors')->group(function () {
    Route::post('temperature', [StoreTemperatureSensorController::class, '__invoke']);
    Route::post('humidity', [StoreHumiditySensorController::class, '__invoke']);
    Route::post('soil-moisture', [StoreSoilMoistureSensorController::class, '__invoke']);
    Route::post('ldr', [StoreLdrSensorController::class, '__invoke']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Admin-only Routes
    Route::prefix('admin')->middleware('role_admin')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::post('users', [UserController::class, 'store']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::put('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);

        Route::get('subscriptions', [AdminSubscriptionController::class, 'index']);
        Route::post('subscriptions', [AdminSubscriptionController::class, 'store']);
        Route::get('subscriptions/{id}', [AdminSubscriptionController::class, 'show']);
        Route::put('subscriptions/{id}', [AdminSubscriptionController::class, 'update']);
        Route::delete('subscriptions/{id}', [AdminSubscriptionController::class, 'destroy']);

        Route::get('reports', [ReportController::class, 'index']);
        Route::post('reports', [ReportController::class, 'store']);
        Route::get('reports/{id}', [ReportController::class, 'show']);
        Route::put('reports/{id}', [ReportController::class, 'update']);
        Route::delete('reports/{id}', [ReportController::class, 'destroy']);
        Route::get('reports/{id}/export', [ReportController::class, 'exportToPDF']);

        Route::get('report-requests', [ReportRequestController::class, 'index']);
        Route::post('report-requests/{id}/approve', [ReportRequestController::class, 'approve']);
        Route::post('report-requests/{id}/reject', [ReportRequestController::class, 'reject']);

        Route::get('financial-records', [FinancialRecordController::class, 'index']);
        Route::post('financial-records', [FinancialRecordController::class, 'store']);
        Route::get('financial-records/{id}', [FinancialRecordController::class, 'show']);
        Route::put('financial-records/{id}', [FinancialRecordController::class, 'update']);
        Route::delete('financial-records/{id}', [FinancialRecordController::class, 'destroy']);

        Route::get('sensors', [AdminSensorController::class, 'index']);
        Route::post('sensors', [AdminSensorController::class, 'store']);
        Route::get('sensors/{id}', [AdminSensorController::class, 'show']);
        Route::get('sensors/{id}/readings', [AdminSensorController::class, 'readings']);
        Route::put('sensors/{id}', [AdminSensorController::class, 'update']);
        Route::delete('sensors/{id}', [AdminSensorController::class, 'destroy']);

        Route::get('farms', [AdminFarmController::class, 'index']);
        Route::post('farms', [AdminFarmController::class, 'store']);
        Route::get('farms/{id}', [AdminFarmController::class, 'show']);
        Route::put('farms/{id}', [AdminFarmController::class, 'update']);
        Route::delete('farms/{id}', [AdminFarmController::class, 'destroy']);

        Route::get('plants', [AdminPlantController::class, 'index']);
        Route::post('plants', [AdminPlantController::class, 'store']);
        Route::get('plants/{id}', [AdminPlantController::class, 'show']);
        Route::put('plants/{id}', [AdminPlantController::class, 'update']);
        Route::delete('plants/{id}', [AdminPlantController::class, 'destroy']);

        Route::get('actuators', [AdminActuatorController::class, 'index']);
        Route::post('actuators', [AdminActuatorController::class, 'store']);
        Route::get('actuators/{id}', [AdminActuatorController::class, 'show']);
        Route::put('actuators/{id}', [AdminActuatorController::class, 'update']);
        Route::delete('actuators/{id}', [AdminActuatorController::class, 'destroy']);

        Route::get('schedules', [AdminScheduleController::class, 'index']);
        Route::post('schedules', [AdminScheduleController::class, 'store']);
        Route::get('schedules/{id}', [AdminScheduleController::class, 'show']);
        Route::put('schedules/{id}', [AdminScheduleController::class, 'update']);
        Route::delete('schedules/{id}', [AdminScheduleController::class, 'destroy']);

        Route::get('control-commands', [AdminControlCommandController::class, 'index']);
        Route::post('control-commands', [AdminControlCommandController::class, 'store']);
        Route::get('control-commands/{id}', [AdminControlCommandController::class, 'show']);
        Route::put('control-commands/{id}', [AdminControlCommandController::class, 'update']);
        Route::delete('control-commands/{id}', [AdminControlCommandController::class, 'destroy']);

        Route::get('disease-detections', [AdminDiseaseDetectionController::class, 'index']);
        Route::post('disease-detections', [AdminDiseaseDetectionController::class, 'store']);
        Route::get('disease-detections/{id}', [AdminDiseaseDetectionController::class, 'show']);
        Route::put('disease-detections/{id}', [AdminDiseaseDetectionController::class, 'update']);
        Route::delete('disease-detections/{id}', [AdminDiseaseDetectionController::class, 'destroy']);

        Route::get('alerts', [AdminAlertController::class, 'index']);
        Route::post('alerts', [AdminAlertController::class, 'store']);
        Route::get('alerts/{id}', [AdminAlertController::class, 'show']);
        Route::put('alerts/{id}', [AdminAlertController::class, 'update']);
        Route::delete('alerts/{id}', [AdminAlertController::class, 'destroy']);

        Route::get('weather-data', [AdminWeatherDataController::class, 'index']);
        Route::post('weather-data', [AdminWeatherDataController::class, 'store']);
        Route::get('weather-data/{id}', [AdminWeatherDataController::class, 'show']);
        Route::put('weather-data/{id}', [AdminWeatherDataController::class, 'update']);
        Route::delete('weather-data/{id}', [AdminWeatherDataController::class, 'destroy']);
    });

    // Farmer Manager Routes
    Route::prefix('farmer')->middleware('role_farmer_manager')->group(function () {
        Route::get('farms', [FarmerManagerFarmController::class, 'index']);
        Route::post('farms', [FarmerManagerFarmController::class, 'store']);
        Route::get('farms/{id}', [FarmerManagerFarmController::class, 'show']);
        Route::put('farms/{id}', [FarmerManagerFarmController::class, 'update']);
        Route::delete('farms/{id}', [FarmerManagerFarmController::class, 'destroy']);

        Route::get('sensors', [FarmerManagerSensorController::class, 'index']);
        Route::post('sensors', [FarmerManagerSensorController::class, 'store']);
        Route::get('sensors/{id}', [FarmerManagerSensorController::class, 'show']);
        Route::get('sensors/{id}/readings', [FarmerManagerSensorController::class, 'readings']);
        Route::put('sensors/{id}', [FarmerManagerSensorController::class, 'update']);
        Route::delete('sensors/{id}', [FarmerManagerSensorController::class, 'destroy']);

        Route::get('plants', [FarmerManagerPlantController::class, 'index']);
        Route::post('plants', [FarmerManagerPlantController::class, 'store']);
        Route::get('plants/{id}', [FarmerManagerPlantController::class, 'show']);
        Route::put('plants/{id}', [FarmerManagerPlantController::class, 'update']);
        Route::delete('plants/{id}', [FarmerManagerPlantController::class, 'destroy']);

        Route::get('actuators', [FarmerManagerActuatorController::class, 'index']);
        Route::post('actuators', [FarmerManagerActuatorController::class, 'store']);
        Route::get('actuators/{id}', [FarmerManagerActuatorController::class, 'show']);
        Route::put('actuators/{id}', [FarmerManagerActuatorController::class, 'update']);
        Route::delete('actuators/{id}', [FarmerManagerActuatorController::class, 'destroy']);

        Route::get('schedules', [FarmerManagerScheduleController::class, 'index']);
        Route::post('schedules', [FarmerManagerScheduleController::class, 'store']);
        Route::get('schedules/{id}', [FarmerManagerScheduleController::class, 'show']);
        Route::put('schedules/{id}', [FarmerManagerScheduleController::class, 'update']);
        Route::delete('schedules/{id}', [FarmerManagerScheduleController::class, 'destroy']);

        Route::get('control-commands', [FarmerManagerControlCommandController::class, 'index']);
        Route::post('control-commands', [FarmerManagerControlCommandController::class, 'store']);
        Route::get('control-commands/{id}', [FarmerManagerControlCommandController::class, 'show']);
        Route::put('control-commands/{id}', [FarmerManagerControlCommandController::class, 'update']);
        Route::delete('control-commands/{id}', [FarmerManagerControlCommandController::class, 'destroy']);

        Route::get('disease-detections', [FarmerManagerDiseaseDetectionController::class, 'index']);
        Route::post('disease-detections', [FarmerManagerDiseaseDetectionController::class, 'store']);
        Route::get('disease-detections/{id}', [FarmerManagerDiseaseDetectionController::class, 'show']);
        Route::put('disease-detections/{id}', [FarmerManagerDiseaseDetectionController::class, 'update']);
        Route::delete('disease-detections/{id}', [FarmerManagerDiseaseDetectionController::class, 'destroy']);

        Route::get('alerts', [FarmerManagerAlertController::class, 'index']);
        Route::post('alerts', [FarmerManagerAlertController::class, 'store']);
        Route::get('alerts/{id}', [FarmerManagerAlertController::class, 'show']);
        Route::put('alerts/{id}', [FarmerManagerAlertController::class, 'update']);
        Route::delete('alerts/{id}', [FarmerManagerAlertController::class, 'destroy']);

        Route::get('weather-data', [FarmerManagerWeatherDataController::class, 'index']);
        Route::post('weather-data', [FarmerManagerWeatherDataController::class, 'store']);
        Route::get('weather-data/{id}', [FarmerManagerWeatherDataController::class, 'show']);
        Route::put('weather-data/{id}', [FarmerManagerWeatherDataController::class, 'update']);
        Route::delete('weather-data/{id}', [FarmerManagerWeatherDataController::class, 'destroy']);

        Route::get('subscriptions', [FarmerManagerSubscriptionController::class, 'index']);
        Route::get('subscriptions/{id}', [FarmerManagerSubscriptionController::class, 'show']);

        Route::get('reports', [FarmerManagerReportController::class, 'index']);
        Route::get('reports/{id}', [FarmerManagerReportController::class, 'show']);
        Route::get('reports/{id}/export', [FarmerManagerReportController::class, 'exportToPDF']);

        Route::get('report-requests', [FarmerManagerReportRequestController::class, 'index']);
        Route::post('report-requests', [FarmerManagerReportRequestController::class, 'store']);

        Route::get('financial-records', [FarmerManagerFinancialRecordController::class, 'index']);
        Route::get('financial-records/{id}', [FarmerManagerFinancialRecordController::class, 'show']);
    });

    // Shared Routes (Admin and Farmer Manager with Role-specific filtering)
    Route::prefix('shared')->middleware('auth:api')->group(function () {
    });
});

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
