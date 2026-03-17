<?php

use Illuminate\Support\Facades\Route;
use Modules\Agenda\Http\Controllers\AgendaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('agendas', AgendaController::class)->names('agenda');
});
