<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Protected API routes
Route::middleware(['auth:api', 'throttle:60,1'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::prefix('location')->group(function () {
        Route::post('/', [LocationController::class, 'store']);
        Route::get('/', [LocationController::class, 'index']);
    });

    Route::get('/employees/me', [AuthController::class, 'me']);
});

// Admin API routes (no auth required for now - add auth in production)
Route::prefix('admin')->group(function () {
    Route::get('/locations/latest', [DashboardController::class, 'getLatestLocations']);
    Route::get('/employees/{id}/history', [DashboardController::class, 'getEmployeeHistory']);
    Route::get('/stats', [DashboardController::class, 'getStats']);
});
