<?php

use Illuminate\Support\Facades\Route;
use Modules\AIScreening\Http\Controllers\AIScreeningController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('aiscreenings', AIScreeningController::class)->names('aiscreening');
});
