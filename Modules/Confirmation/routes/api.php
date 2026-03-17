<?php

use Illuminate\Support\Facades\Route;
use Modules\Confirmation\Http\Controllers\ConfirmationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('confirmations', ConfirmationController::class)->names('confirmation');
});
