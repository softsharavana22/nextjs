<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\AuthController;

Route::prefix('frontend')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'frontend.auth'])->group(function () {
        Route::get('/dashboard', [AuthController::class, 'dashboard']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});
