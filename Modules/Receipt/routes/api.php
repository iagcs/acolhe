<?php

use Illuminate\Support\Facades\Route;
use Modules\Receipt\Http\Controllers\ReceiptController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('receipts', ReceiptController::class)->names('receipt');
});
