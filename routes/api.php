<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/biometric/verify', [AuthController::class, 'biometricVerify']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/profile', [AuthController::class, 'profile']);
    Route::post('/auth/register-device', [AuthController::class, 'registerDevice']);
    Route::post('/auth/biometric/enroll', [AuthController::class, 'biometricEnroll']);
    Route::post('/auth/biometric/revoke', [AuthController::class, 'biometricRevoke']);
    // Backward compatibility
    Route::post('/auth/register-biometric', [AuthController::class, 'registerBiometricToken']);
    Route::post('/auth/remove-biometric', [AuthController::class, 'removeBiometricToken']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Attendance routes
    Route::get('/attendance/today', [AttendanceController::class, 'getToday']);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/history', [AttendanceController::class, 'getHistory']);
    Route::get('/attendance/locations', [AttendanceController::class, 'getLocations']);
    // Backward compatibility
    Route::get('/attendance/status', [AttendanceController::class, 'getStatus']);
    Route::get('/attendance/monthly-summary', [AttendanceController::class, 'getMonthlySummary']);
    
    // Shipments
    Route::get('/shipments', [ShipmentController::class, 'index']);
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show']);
    Route::post('/shipments/{shipment}/accept', [ShipmentController::class, 'accept']);
    Route::patch('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);
    Route::post('/shipments/{shipment}/photos/pickup', [ShipmentController::class, 'uploadPickupPhoto']);
    Route::post('/shipments/{shipment}/photos/delivery', [ShipmentController::class, 'uploadDeliveryPhoto']);
    Route::post('/shipments/{shipment}/photos/toll', [ShipmentController::class, 'uploadTollPhoto']);
    Route::post('/shipments/{shipment}/signature', [ShipmentController::class, 'uploadSignature']);
    Route::get('/shipments/count/pending', [ShipmentController::class, 'countPending']);
    Route::get('/shipments/count/active', [ShipmentController::class, 'countActive']);
    Route::get('/shipments/count/delivered-today', [ShipmentController::class, 'countDeliveredToday']);
    // Backward compatibility
    Route::post('/shipments/{shipment}/acknowledge', [ShipmentController::class, 'acknowledge']);
    Route::post('/shipments/{shipment}/proofs', [ShipmentController::class, 'uploadProofs']);
    
    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::get('/notifications/count/unread', [NotificationController::class, 'countUnread']);
    // Backward compatibility
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
    
    // Driver profile
    Route::get('/driver/profile', [DriverController::class, 'profile']);
    Route::patch('/driver/profile', [DriverController::class, 'updateProfile']);
    Route::get('/driver/license-status', [DriverController::class, 'licenseStatus']);
    Route::get('/driver/documents', [DriverController::class, 'documents']);
});

// Backward compatibility - keep v1 routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    Route::middleware(['auth:sanctum', 'driver.license'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/profile', [AuthController::class, 'profile']);
        Route::post('/auth/register-device', [AuthController::class, 'registerDevice']);
        Route::post('/auth/register-biometric', [AuthController::class, 'registerBiometricToken']);
        Route::post('/auth/remove-biometric', [AuthController::class, 'removeBiometricToken']);

        Route::get('/dashboard', [DashboardController::class, 'index']);

        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
        Route::get('/attendance/status', [AttendanceController::class, 'getStatus']);
        Route::get('/attendance/history', [AttendanceController::class, 'getHistory']);
        Route::get('/attendance/today', [AttendanceController::class, 'getToday']);
        Route::get('/attendance/monthly-summary', [AttendanceController::class, 'getMonthlySummary']);
        Route::get('/attendance/locations', [AttendanceController::class, 'getLocations']);
        
        Route::apiResource('shipments', ShipmentController::class)->only(['index', 'show']);
        Route::post('/shipments/{shipment}/acknowledge', [ShipmentController::class, 'acknowledge']);
        Route::post('/shipments/{shipment}/status', [ShipmentController::class, 'updateStatus']);
        Route::post('/shipments/{shipment}/proofs', [ShipmentController::class, 'uploadProofs']);
        
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
        
        Route::get('/driver/profile', [DriverController::class, 'profile']);
        Route::get('/driver/license-status', [DriverController::class, 'licenseStatus']);
        Route::get('/driver/documents', [DriverController::class, 'documents']);
    });
});


