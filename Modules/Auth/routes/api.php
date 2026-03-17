<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\OnboardingController;
use Modules\Auth\Http\Controllers\ProfileController;

Route::prefix('v1')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::patch('profile', [ProfileController::class, 'update']);

    Route::get('onboarding/status', [OnboardingController::class, 'status']);
    Route::patch('onboarding/dismiss', [OnboardingController::class, 'dismiss']);
});
