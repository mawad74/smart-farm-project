<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminFarmController;
use App\Http\Controllers\Admin\AdminLogController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminAlertController;
use App\Http\Controllers\Admin\AdminSensorController;
use App\Http\Controllers\Admin\AdminPlantController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminControlCommandController;
use App\Http\Controllers\Admin\AdminActuatorController;
use App\Http\Controllers\Admin\AdminScheduleController;
use App\Http\Controllers\Admin\AdminDiseaseDetectionController;
use App\Http\Controllers\Admin\AdminFinancialRecordController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminWeatherDataController;

use App\Http\Controllers\FarmerManager\FarmerManagerActuatorController;
use App\Http\Controllers\FarmerManagerDashboardController;
use App\Http\Controllers\FarmerManager\FarmerManagerAlertController;
use App\Http\Controllers\FarmerManager\FarmerManagerControlCommandController;
use App\Http\Controllers\FarmerManager\FarmerManagerDiseaseDetectionController;
use App\Http\Controllers\FarmerManager\FarmerManagerFarmController;
use App\Http\Controllers\FarmerManager\FarmerManagerPlantController;
use App\Http\Controllers\FarmerManager\FarmerManagerProfileController;
use App\Http\Controllers\FarmerManager\FarmerManagerReportController;
use App\Http\Controllers\FarmerManager\FarmerManagerScheduleController;
use App\Http\Controllers\FarmerManager\FarmerManagerSensorController;
use App\Http\Controllers\FarmerManager\FarmerManagerSubscriptionController;
use App\Http\Controllers\FarmerManager\FarmerManagerWeatherDataController;

//(Welcome Page)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Auth::routes();

// توجيه المستخدم بعد تسجيل الدخول بناءً على الدور
Route::get('/dashboard', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->role === 'farmer_manager') {
            return redirect()->route('farmer_manager.dashboard');
        }
    }
    return redirect('/login');
})->name('dashboard');

Route::post('/mark-notification-as-read/{id}', function ($id) {
    $notification = \App\Models\Notification::findOrFail($id);
    if ($notification->user_id === auth()->id()) {
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
    return response()->json(['error' => 'Unauthorized'], 403);
})->middleware('auth', 'farmer_manager');

// Dashboard للـ Admin
Route::middleware(['auth', 'check_status', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Users
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Farms
    Route::get('/admin/farms', [AdminFarmController::class, 'index'])->name('admin.farms.index');
    Route::get('/admin/farms/create', [AdminFarmController::class, 'create'])->name('admin.farms.create');
    Route::post('/admin/farms', [AdminFarmController::class, 'store'])->name('admin.farms.store');
    Route::get('/admin/farms/{id}/edit', [AdminFarmController::class, 'edit'])->name('admin.farms.edit');
    Route::put('/admin/farms/{id}', [AdminFarmController::class, 'update'])->name('admin.farms.update');
    Route::delete('/admin/farms/{id}', [AdminFarmController::class, 'destroy'])->name('admin.farms.destroy');

    // Reports
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/create', [AdminReportController::class, 'create'])->name('admin.reports.create');
    Route::post('/admin/reports', [AdminReportController::class, 'store'])->name('admin.reports.store');
    Route::get('/admin/reports/{id}', [AdminReportController::class, 'show'])->name('admin.reports.show');
    Route::get('/admin/reports/{id}/edit', [AdminReportController::class, 'edit'])->name('admin.reports.edit');
    Route::put('/admin/reports/{id}', [AdminReportController::class, 'update'])->name('admin.reports.update');
    Route::delete('/admin/reports/{id}', [AdminReportController::class, 'destroy'])->name('admin.reports.destroy');
    Route::get('/admin/reports/{id}/export', [AdminReportController::class, 'exportToPDF'])->name('admin.reports.export');
    Route::post('/admin/reports/approve/{id}', [AdminReportController::class, 'approveRequest'])->name('admin.reports.approveRequest');
    Route::post('/admin/reports/reject/{id}', [AdminReportController::class, 'rejectRequest'])->name('admin.reports.rejectRequest');

    // Sensors
    Route::get('/admin/sensors', [AdminSensorController::class, 'index'])->name('admin.sensors.index');
    Route::get('/admin/sensors/create', [AdminSensorController::class, 'create'])->name('admin.sensors.create');
    Route::post('/admin/sensors', [AdminSensorController::class, 'store'])->name('admin.sensors.store');
    Route::get('/admin/sensors/{id}/edit', [AdminSensorController::class, 'edit'])->name('admin.sensors.edit');
    Route::put('/admin/sensors/{id}', [AdminSensorController::class, 'update'])->name('admin.sensors.update');
    Route::delete('/admin/sensors/{id}', [AdminSensorController::class, 'destroy'])->name('admin.sensors.destroy');
    Route::get('/admin/sensors/{id}/readings', [AdminSensorController::class, 'showReadings'])->name('admin.sensors.readings');

    // Plants
    Route::get('/admin/plants', [AdminPlantController::class, 'index'])->name('admin.plants.index');
    Route::get('/admin/plants/create', [AdminPlantController::class, 'create'])->name('admin.plants.create');
    Route::post('/admin/plants', [AdminPlantController::class, 'store'])->name('admin.plants.store');
    Route::get('/admin/plants/{id}/edit', [AdminPlantController::class, 'edit'])->name('admin.plants.edit');
    Route::put('/admin/plants/{id}', [AdminPlantController::class, 'update'])->name('admin.plants.update');
    Route::delete('/admin/plants/{id}', [AdminPlantController::class, 'destroy'])->name('admin.plants.destroy');

    // Subscriptions
    Route::get('/admin/subscriptions', [AdminSubscriptionController::class, 'index'])->name('admin.subscriptions.index');
    Route::get('/admin/subscriptions/create', [AdminSubscriptionController::class, 'create'])->name('admin.subscriptions.create');
    Route::post('/admin/subscriptions', [AdminSubscriptionController::class, 'store'])->name('admin.subscriptions.store');
    Route::get('/admin/subscriptions/{id}/edit', [AdminSubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
    Route::put('/admin/subscriptions/{id}', [AdminSubscriptionController::class, 'update'])->name('admin.subscriptions.update');
    Route::delete('/admin/subscriptions/{id}', [AdminSubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');

    // Control Commands
    Route::get('/admin/control-commands', [AdminControlCommandController::class, 'index'])->name('admin.control-commands.index');
    Route::get('/admin/control-commands/create', [AdminControlCommandController::class, 'create'])->name('admin.control-commands.create');
    Route::post('/admin/control-commands', [AdminControlCommandController::class, 'store'])->name('admin.control-commands.store');
    Route::get('/admin/control-commands/{id}/edit', [AdminControlCommandController::class, 'edit'])->name('admin.control-commands.edit');
    Route::put('/admin/control-commands/{id}', [AdminControlCommandController::class, 'update'])->name('admin.control-commands.update');
    Route::delete('/admin/control-commands/{id}', [AdminControlCommandController::class, 'destroy'])->name('admin.control-commands.destroy');

    // Actuators
    Route::get('/admin/actuators', [AdminActuatorController::class, 'index'])->name('admin.actuators.index');
    Route::get('/admin/actuators/create', [AdminActuatorController::class, 'create'])->name('admin.actuators.create');
    Route::post('/admin/actuators', [AdminActuatorController::class, 'store'])->name('admin.actuators.store');
    Route::get('/admin/actuators/{id}/edit', [AdminActuatorController::class, 'edit'])->name('admin.actuators.edit');
    Route::put('/admin/actuators/{id}', [AdminActuatorController::class, 'update'])->name('admin.actuators.update');
    Route::delete('/admin/actuators/{id}', [AdminActuatorController::class, 'destroy'])->name('admin.actuators.destroy');

    // Schedules
    Route::get('/admin/schedules', [AdminScheduleController::class, 'index'])->name('admin.schedules.index');
    Route::get('/admin/schedules/create', [AdminScheduleController::class, 'create'])->name('admin.schedules.create');
    Route::post('/admin/schedules', [AdminScheduleController::class, 'store'])->name('admin.schedules.store');
    Route::get('/admin/schedules/{id}/edit', [AdminScheduleController::class, 'edit'])->name('admin.schedules.edit');
    Route::put('/admin/schedules/{id}', [AdminScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::delete('/admin/schedules/{id}', [AdminScheduleController::class, 'destroy'])->name('admin.schedules.destroy');

    // Disease Detections
    Route::get('/admin/disease-detections', [AdminDiseaseDetectionController::class, 'index'])->name('admin.disease-detections.index');
    Route::get('/admin/disease-detections/create', [AdminDiseaseDetectionController::class, 'create'])->name('admin.disease-detections.create');
    Route::post('/admin/disease-detections', [AdminDiseaseDetectionController::class, 'store'])->name('admin.disease-detections.store');
    Route::get('/admin/disease-detections/{id}/edit', [AdminDiseaseDetectionController::class, 'edit'])->name('admin.disease-detections.edit');
    Route::put('/admin/disease-detections/{id}', [AdminDiseaseDetectionController::class, 'update'])->name('admin.disease-detections.update');
    Route::delete('/admin/disease-detections/{id}', [AdminDiseaseDetectionController::class, 'destroy'])->name('admin.disease-detections.destroy');

    // Financial Records
    Route::get('/admin/financial-records', [AdminFinancialRecordController::class, 'index'])->name('admin.financial-records.index');
    Route::get('/admin/financial-records/create', [AdminFinancialRecordController::class, 'create'])->name('admin.financial-records.create');
    Route::post('/admin/financial-records', [AdminFinancialRecordController::class, 'store'])->name('admin.financial-records.store');
    Route::get('/admin/financial-records/{id}/edit', [AdminFinancialRecordController::class, 'edit'])->name('admin.financial-records.edit');
    Route::put('/admin/financial-records/{id}', [AdminFinancialRecordController::class, 'update'])->name('admin.financial-records.update');
    Route::delete('/admin/financial-records/{id}', [AdminFinancialRecordController::class, 'destroy'])->name('admin.financial-records.destroy');

    // Settings
    Route::get('/admin/settings', [AdminSettingController::class, 'index'])->name('admin.settings.index');
    Route::get('/admin/settings/create', [AdminSettingController::class, 'create'])->name('admin.settings.create');
    Route::post('/admin/settings', [AdminSettingController::class, 'store'])->name('admin.settings.store');
    Route::get('/admin/settings/{id}/edit', [AdminSettingController::class, 'edit'])->name('admin.settings.edit');
    Route::put('/admin/settings/{id}', [AdminSettingController::class, 'update'])->name('admin.settings.update');
    Route::delete('/admin/settings/{id}', [AdminSettingController::class, 'destroy'])->name('admin.settings.destroy');

    // Weather Data
    Route::get('/admin/weather-data', [AdminWeatherDataController::class, 'index'])->name('admin.weather-data.index');
    Route::get('/admin/weather-data/create', [AdminWeatherDataController::class, 'create'])->name('admin.weather-data.create');
    Route::post('/admin/weather-data', [AdminWeatherDataController::class, 'store'])->name('admin.weather-data.store');
    Route::get('/admin/weather-data/{id}/edit', [AdminWeatherDataController::class, 'edit'])->name('admin.weather-data.edit');
    Route::put('/admin/weather-data/{id}', [AdminWeatherDataController::class, 'update'])->name('admin.weather-data.update');
    Route::delete('/admin/weather-data/{id}', [AdminWeatherDataController::class, 'destroy'])->name('admin.weather-data.destroy');

    
});

// Dashboard للـ Farmer Manager
Route::middleware(['auth', 'check_status', 'farmer_manager'])->group(function () {
    Route::get('/farmer-manager/dashboard', [FarmerManagerDashboardController::class, 'index'])->name('farmer_manager.dashboard');
    
    // Profile
    Route::get('/farmer-manager/profile/edit', [FarmerManagerProfileController::class, 'edit'])->name('farmer_manager.profile.edit');
    Route::put('/farmer-manager/profile', [FarmerManagerProfileController::class, 'update'])->name('farmer_manager.profile.update');

    // Farms
    Route::get('/farmer-manager/farms', [FarmerManagerFarmController::class, 'index'])->name('farmer_manager.farms.index');
    Route::get('/farmer-manager/farms/create', [FarmerManagerFarmController::class, 'create'])->name('farmer_manager.farms.create');
    Route::post('/farmer-manager/farms', [FarmerManagerFarmController::class, 'store'])->name('farmer_manager.farms.store');
    Route::get('/farmer-manager/farms/{id}/edit', [FarmerManagerFarmController::class, 'edit'])->name('farmer_manager.farms.edit');
    Route::put('/farmer-manager/farms/{id}', [FarmerManagerFarmController::class, 'update'])->name('farmer_manager.farms.update');
    Route::delete('/farmer-manager/farms/{id}', [FarmerManagerFarmController::class, 'destroy'])->name('farmer_manager.farms.destroy');

    // Plants
    Route::get('/farmer-manager/plants', [FarmerManagerPlantController::class, 'index'])->name('farmer_manager.plants.index');
    Route::get('/farmer-manager/plants/create', [FarmerManagerPlantController::class, 'create'])->name('farmer_manager.plants.create');
    Route::post('/farmer-manager/plants', [FarmerManagerPlantController::class, 'store'])->name('farmer_manager.plants.store');
    Route::get('/farmer-manager/plants/{id}/edit', [FarmerManagerPlantController::class, 'edit'])->name('farmer_manager.plants.edit');
    Route::put('/farmer-manager/plants/{id}', [FarmerManagerPlantController::class, 'update'])->name('farmer_manager.plants.update');
    Route::delete('/farmer-manager/plants/{id}', [FarmerManagerPlantController::class, 'destroy'])->name('farmer_manager.plants.destroy');

    // Sensors
    Route::get('/farmer-manager/sensors', [FarmerManagerSensorController::class, 'index'])->name('farmer_manager.sensors.index');
    Route::get('/farmer-manager/sensors/create', [FarmerManagerSensorController::class, 'create'])->name('farmer_manager.sensors.create');
    Route::post('/farmer-manager/sensors', [FarmerManagerSensorController::class, 'store'])->name('farmer_manager.sensors.store');
    Route::get('/farmer-manager/sensors/{id}/edit', [FarmerManagerSensorController::class, 'edit'])->name('farmer_manager.sensors.edit');
    Route::put('/farmer-manager/sensors/{id}', [FarmerManagerSensorController::class, 'update'])->name('farmer_manager.sensors.update');
    Route::delete('/farmer-manager/sensors/{id}', [FarmerManagerSensorController::class, 'destroy'])->name('farmer_manager.sensors.destroy');

    // Actuators
    Route::get('/farmer-manager/actuators', [FarmerManagerActuatorController::class, 'index'])->name('farmer_manager.actuators.index');
    Route::get('/farmer-manager/actuators/create', [FarmerManagerActuatorController::class, 'create'])->name('farmer_manager.actuators.create');
    Route::post('/farmer-manager/actuators', [FarmerManagerActuatorController::class, 'store'])->name('farmer_manager.actuators.store');
    Route::get('/farmer-manager/actuators/{id}/edit', [FarmerManagerActuatorController::class, 'edit'])->name('farmer_manager.actuators.edit');
    Route::put('/farmer-manager/actuators/{id}', [FarmerManagerActuatorController::class, 'update'])->name('farmer_manager.actuators.update');
    Route::delete('/farmer-manager/actuators/{id}', [FarmerManagerActuatorController::class, 'destroy'])->name('farmer_manager.actuators.destroy');

    // Weather Data
    Route::get('/farmer-manager/weather-data', [FarmerManagerWeatherDataController::class, 'index'])->name('farmer_manager.weather-data.index');
    Route::get('/farmer-manager/weather-data/create', [FarmerManagerWeatherDataController::class, 'create'])->name('farmer_manager.weather-data.create');
    Route::post('/farmer-manager/weather-data', [FarmerManagerWeatherDataController::class, 'store'])->name('farmer_manager.weather-data.store');
    Route::get('/farmer-manager/weather-data/{id}/edit', [FarmerManagerWeatherDataController::class, 'edit'])->name('farmer_manager.weather-data.edit');
    Route::put('/farmer-manager/weather-data/{id}', [FarmerManagerWeatherDataController::class, 'update'])->name('farmer_manager.weather-data.update');
    Route::delete('/farmer-manager/weather-data/{id}', [FarmerManagerWeatherDataController::class, 'destroy'])->name('farmer_manager.weather-data.destroy');

    // Alerts
    Route::get('/farmer-manager/alerts', [FarmerManagerAlertController::class, 'index'])->name('farmer_manager.alerts.index');
    Route::get('/farmer-manager/alerts/create', [FarmerManagerAlertController::class, 'create'])->name('farmer_manager.alerts.create');
    Route::post('/farmer-manager/alerts', [FarmerManagerAlertController::class, 'store'])->name('farmer_manager.alerts.store');
    Route::get('/farmer-manager/alerts/{id}/edit', [FarmerManagerAlertController::class, 'edit'])->name('farmer_manager.alerts.edit');
    Route::put('/farmer-manager/alerts/{id}', [FarmerManagerAlertController::class, 'update'])->name('farmer_manager.alerts.update');
    Route::delete('/farmer-manager/alerts/{id}', [FarmerManagerAlertController::class, 'destroy'])->name('farmer_manager.alerts.destroy');

    // Control Commands
    Route::get('/farmer-manager/control-commands', [FarmerManagerControlCommandController::class, 'index'])->name('farmer_manager.control-commands.index');
    Route::get('/farmer-manager/control-commands/create', [FarmerManagerControlCommandController::class, 'create'])->name('farmer_manager.control-commands.create');
    Route::post('/farmer-manager/control-commands', [FarmerManagerControlCommandController::class, 'store'])->name('farmer_manager.control-commands.store');
    Route::get('/farmer-manager/control-commands/{id}/edit', [FarmerManagerControlCommandController::class, 'edit'])->name('farmer_manager.control-commands.edit');
    Route::put('/farmer-manager/control-commands/{id}', [FarmerManagerControlCommandController::class, 'update'])->name('farmer_manager.control-commands.update');
    Route::delete('/farmer-manager/control-commands/{id}', [FarmerManagerControlCommandController::class, 'destroy'])->name('farmer_manager.control-commands.destroy');

    // Disease Detections
    Route::get('/farmer-manager/disease-detections', [FarmerManagerDiseaseDetectionController::class, 'index'])->name('farmer_manager.disease-detections.index');
    Route::get('/farmer-manager/disease-detections/create', [FarmerManagerDiseaseDetectionController::class, 'create'])->name('farmer_manager.disease-detections.create');
    Route::post('/farmer-manager/disease-detections', [FarmerManagerDiseaseDetectionController::class, 'store'])->name('farmer_manager.disease-detections.store');
    Route::get('/farmer-manager/disease-detections/{id}/edit', [FarmerManagerDiseaseDetectionController::class, 'edit'])->name('farmer_manager.disease-detections.edit');
    Route::put('/farmer-manager/disease-detections/{id}', [FarmerManagerDiseaseDetectionController::class, 'update'])->name('farmer_manager.disease-detections.update');
    Route::delete('/farmer-manager/disease-detections/{id}', [FarmerManagerDiseaseDetectionController::class, 'destroy'])->name('farmer_manager.disease-detections.destroy');

    // Schedules
    Route::get('/farmer-manager/schedules', [FarmerManagerScheduleController::class, 'index'])->name('farmer_manager.schedules.index');
    Route::get('/farmer-manager/schedules/create', [FarmerManagerScheduleController::class, 'create'])->name('farmer_manager.schedules.create');
    Route::post('/farmer-manager/schedules', [FarmerManagerScheduleController::class, 'store'])->name('farmer_manager.schedules.store');
    Route::get('/farmer-manager/schedules/{id}/edit', [FarmerManagerScheduleController::class, 'edit'])->name('farmer_manager.schedules.edit');
    Route::put('/farmer-manager/schedules/{id}', [FarmerManagerScheduleController::class, 'update'])->name('farmer_manager.schedules.update');
    Route::delete('/farmer-manager/schedules/{id}', [FarmerManagerScheduleController::class, 'destroy'])->name('farmer_manager.schedules.destroy');

    // Subscriptions
    Route::get('/farmer-manager/subscriptions/show', [FarmerManagerSubscriptionController::class, 'show'])->name('farmer_manager.subscriptions.show');

    // Reports
    Route::get('/farmer-manager/reports', [FarmerManagerReportController::class, 'index'])->name('farmer_manager.reports.index');
    Route::get('/farmer-manager/reports/{id}', [FarmerManagerReportController::class, 'show'])->name('farmer_manager.reports.show');
    Route::post('/farmer-manager/reports/request', [FarmerManagerReportController::class, 'storeRequest'])->name('farmer_manager.reports.storeRequest');
    Route::get('/farmer-manager/reports/{id}/export', [FarmerManagerReportController::class, 'exportToPDF'])->name('farmer_manager.reports.export');
});