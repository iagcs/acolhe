<?php

namespace Modules\WhatsApp\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\WhatsApp\Models\WhatsAppConversation;
use Modules\WhatsApp\Services\EvolutionApiClient;

/**
 * Authenticated management endpoints for the psychologist dashboard.
 */
class WhatsAppController extends Controller
{
    public function __construct(
        private readonly EvolutionApiClient $client,
    ) {}

    /**
     * GET /api/v1/whatsapp/status
     * Returns the Evolution API connection status for the psychologist's instance.
     */
    public function status(): JsonResponse
    {
        $data = $this->client->getConnectionStatus();

        return response()->json(['status' => $data]);
    }

    /**
     * POST /api/v1/whatsapp/connect
     * Returns a QR code payload to connect a new WhatsApp number.
     */
    public function connect(): JsonResponse
    {
        $data = $this->client->getQrCode();

        return response()->json(['qr' => $data]);
    }

    /**
     * GET /api/v1/whatsapp/conversations
     * Lists recent conversations for the authenticated psychologist.
     */
    public function conversations(Request $request): JsonResponse
    {
        /** @var \Modules\Auth\Models\User $user */
        $user = $request->user();

        $conversations = WhatsAppConversation::where('psychologist_id', $user->id)
            ->with('patient:id,name,phone')
            ->orderByDesc('last_message_at')
            ->limit(50)
            ->get(['id', 'phone', 'patient_id', 'state', 'last_message_at']);

        return response()->json(['conversations' => $conversations]);
    }
}
