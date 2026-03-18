<?php

namespace Modules\WhatsApp\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\WhatsApp\Services\EvolutionApiClient;

/**
 * Sends a WhatsApp message via Evolution API.
 *
 * Retried 3× with exponential backoff on 5xx errors.
 * Accepts text, buttons, and list payloads — use the appropriate static
 * constructor on ConversationReply to build the arguments.
 */
class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 10;

    public function __construct(
        private readonly string $phone,
        private readonly string $type,
        private readonly string $message,
        private readonly array  $extra = [],
    ) {}

    public function handle(EvolutionApiClient $client): void
    {
        try {
            match ($this->type) {
                'buttons' => $client->sendButtons($this->phone, $this->message, $this->extra),
                'list'    => $client->sendList(
                    $this->phone,
                    'Horários disponíveis',
                    $this->message,
                    $this->extra['buttonText'] ?? 'Ver opções',
                    $this->extra['sections']   ?? [],
                ),
                default   => $client->sendText($this->phone, $this->message),
            };
        } catch (\Throwable $e) {
            Log::error('[WhatsApp] Failed to send message', [
                'phone'   => $this->phone,
                'type'    => $this->type,
                'attempt' => $this->attempts(),
                'error'   => $e->getMessage(),
            ]);

            $this->release($this->backoff * $this->attempts());
        }
    }
}
