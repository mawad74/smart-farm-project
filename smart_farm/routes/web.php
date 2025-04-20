<?php

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
use App\Http\Controllers\FarmerManagerDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

// الصفحة الرئيسية (Welcome Page)
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

    // Alerts
    Route::get('/admin/alerts', [AdminAlertController::class, 'index'])->name('admin.alerts.index');
    Route::get('/admin/alerts/{id}/edit', [AdminAlertController::class, 'edit'])->name('admin.alerts.edit');
    Route::put('/admin/alerts/{id}', [AdminAlertController::class, 'update'])->name('admin.alerts.update');
    Route::delete('/admin/alerts/{id}', [AdminAlertController::class, 'destroy'])->name('admin.alerts.destroy');

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

    // Logs
    Route::get('/admin/logs', [AdminLogController::class, 'index'])->name('admin.logs.index');
});

// Dashboard للـ Farmer Manager
Route::middleware(['auth', 'check_status', 'farmer_manager'])->group(function () {
    Route::get('/farmer-manager/dashboard', [FarmerManagerDashboardController::class, 'index'])->name('farmer_manager.dashboard');
});