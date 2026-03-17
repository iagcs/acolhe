<?php

use Illuminate\Support\Facades\Route;
use Modules\Agenda\Http\Controllers\CalendarFeedController;
use Modules\Agenda\Http\Controllers\CalendarTokenController;

Route::middleware('throttle:60,1')->get('calendar/feed/{token}', CalendarFeedController::class)->name('calendar.feed');

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('calendar/token', [CalendarTokenController::class, 'show'])->name('calendar.token.show');
    Route::post('calendar/token', [CalendarTokenController::class, 'store'])->name('calendar.token.store');
});
