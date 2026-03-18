<?php

namespace Modules\WhatsApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\WhatsApp\Jobs\ProcessIncomingMessageJob;

/**
 * Receives raw Evolution API webhook events.
 *
 * Security: validates an HMAC-SHA256 signature sent in the
 * `x-evolution-signature` header. Dispatches processing off-queue
 * and returns 200 immediately to avoid Evolution API retries.
 */
class WebhookController extends Controller
{
    /**
     * POST /api/whatsapp/webhook
     */
    public function receive(Request $request): JsonResponse
    {
        if (! $this->validateSignature($request)) {
            Log::warning('[WhatsApp] Invalid webhook signature', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();

        // Log raw payload in non-production to debug payload structure
        if (config('app.env') !== 'production') {
            Log::debug('[WhatsApp] Raw webhook payload', ['payload' => $payload]);
        }

        // Skip non-message events (status updates, etc.) early
        $event = $payload['event'] ?? '';
        if (! in_array($event, ['messages.upsert', 'messages.update'])) {
            return response()->json(['ok' => true]);
        }

        ProcessIncomingMessageJob::dispatch($payload);

        return response()->json(['ok' => true]);
    }

    // ── Private ────────────────────────────────────────────────────────────

    private function validateSignature(Request $request): bool
    {
        $secret = config('whatsapp.webhook_secret');

        // If no secret is configured, skip validation (dev-only)
        if (empty($secret)) {
            return true;
        }

        $signature = $request->header('x-evolution-signature') ?? '';
        $expected  = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }
}
