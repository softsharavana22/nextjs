<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Http\Request;


Route::prefix('admin')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'admin.auth'])->group(function () {
        // ✅ TOKEN VALIDATION API (Heartbeat)
        Route::get('/current-time', function (Request $request) {
            return response()->json([
                'status' => 200,
                'time'   => now()->toDateTimeString(),
                'user'   => auth()->user(), // ✅ always works // $request->user(), // ✅ works
            ]);
        });    	


        Route::get('/dashboard', [AuthController::class, 'dashboard']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::get('/settings', [SettingsController::class, 'index']);
        Route::post('/settings', [SettingsController::class, 'update']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
