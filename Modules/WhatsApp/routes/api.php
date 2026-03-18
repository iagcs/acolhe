<?php

use Illuminate\Support\Facades\Route;
use Modules\WhatsApp\Http\Controllers\WebhookController;
use Modules\WhatsApp\Http\Controllers\WhatsAppController;

/*
 * Public — HMAC signature validated inside the controller.
 * No Sanctum middleware: Evolution API sends unauthenticated POST requests.
 */
Route::post('whatsapp/webhook', [WebhookController::class, 'receive'])
    ->name('whatsapp.webhook');

/*
 * Authenticated — psychologist management endpoints.
 */
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::get('whatsapp/status',       [WhatsAppController::class, 'status'])->name('whatsapp.status');
    Route::post('whatsapp/connect',     [WhatsAppController::class, 'connect'])->name('whatsapp.connect');
    Route::get('whatsapp/conversations', [WhatsAppController::class, 'conversations'])->name('whatsapp.conversations');
});
