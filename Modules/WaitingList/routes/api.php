<?php

use Illuminate\Support\Facades\Route;
use Modules\WaitingList\Http\Controllers\WaitingListController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('waitinglists', WaitingListController::class)->names('waitinglist');
});
