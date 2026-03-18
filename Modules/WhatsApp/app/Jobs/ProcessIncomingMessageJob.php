<?php

namespace Modules\WhatsApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Auth\Models\User;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\Services\ConversationStateMachine;
use Modules\WhatsApp\Services\EvolutionApiClient;
use Modules\WhatsApp\Services\IncomingMessageParser;

/**
 * Processes a raw Evolution API webhook payload off the queue.
 *
 * The WebhookController dispatches this job immediately and returns 200,
 * preventing Evolution API from retrying due to slow processing.
 */
class ProcessIncomingMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // Don't retry — idempotency is not guaranteed

    public function __construct(
        private readonly array $payload,
    ) {}

    public function handle(
        IncomingMessageParser $parser,
        ConversationStateMachine $stateMachine,
        EvolutionApiClient $client,
    ): void {
        $message = $parser->parse($this->payload);

        if (! $message) {
            return; // Not a user message (status update, echo, etc.)
        }

        // Resolve the psychologist by their WhatsApp phone number
        $psychologist = User::where('phone', $message->psychologistPhone)->first();

        if (! $psychologist) {
            Log::warning('[WhatsApp] Received message for unknown psychologist phone', [
                'phone' => $message->psychologistPhone,
            ]);
            return;
        }

        $reply = $stateMachine->process($message, $psychologist);

        if ($reply) {
            $this->sendReply($client, $message->phone, $reply);
        }
    }

    // ── Private ────────────────────────────────────────────────────────────

    private function sendReply(EvolutionApiClient $client, string $phone, ConversationReply $reply): void
    {
        match ($reply->replyType) {
            'buttons' => $client->sendButtons($phone, $reply->message, $reply->buttons),
            'list'    => $client->sendList(
                $phone,
                'Opções',
                $reply->message,
                $reply->listButtonText,
                $reply->sections,
            ),
            default   => $client->sendText($phone, $reply->message),
        };
    }
}
