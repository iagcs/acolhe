<?php

use Illuminate\Support\Facades\Route;
use Modules\RiskScore\Http\Controllers\RiskScoreController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('riskscores', RiskScoreController::class)->names('riskscore');
});
