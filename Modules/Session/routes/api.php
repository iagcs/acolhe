<?php

use Illuminate\Support\Facades\Route;
use Modules\Session\Http\Controllers\SessionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('sessions', SessionController::class)->names('session');
});
