<?php

use Illuminate\Support\Facades\Route;
use Modules\Reminder\Http\Controllers\ReminderController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('reminders', ReminderController::class)->names('reminder');
});
