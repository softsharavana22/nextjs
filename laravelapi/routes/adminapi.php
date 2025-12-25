<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SettingsController;


Route::prefix('admin')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin.auth'])->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/settings', [SettingsController::class, 'index']);
        Route::post('/settings', [SettingsController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
