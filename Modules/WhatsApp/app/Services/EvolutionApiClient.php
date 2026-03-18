<?php

namespace Modules\WhatsApp\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * HTTP wrapper around the Evolution API (self-hosted).
 *
 * All outbound messages are dispatched via SendWhatsAppMessageJob
 * to avoid blocking the webhook response thread.
 */
class EvolutionApiClient
{
    private string $baseUrl;
    private string $apiKey;
    private string $instance;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('whatsapp.evolution_url'), '/');
        $this->apiKey   = config('whatsapp.api_key');
        $this->instance = config('whatsapp.instance_name');
    }

    // ── Message sending ────────────────────────────────────────────────────

    /**
     * Send a plain text message.
     * Evolution API v2.3+ format: { number, text }
     */
    public function sendText(string $phone, string $message): array
    {
        return $this->post("/message/sendText/{$this->instance}", [
            'number' => $phone,
            'text'   => $message,
        ]);
    }

    /**
     * Send a button message (up to 3 buttons).
     * Evolution API v2.3+ format: { number, title, description, footer, buttons[] }
     *
     * @param  array  $buttons  [['id' => 'btn_x', 'text' => 'Label'], ...]
     */
    public function sendButtons(string $phone, string $message, array $buttons): array
    {
        $payload = array_map(fn ($b) => [
            'type'        => 'reply',
            'displayText' => $b['text'],
            'id'          => $b['id'],
        ], $buttons);

        return $this->post("/message/sendButtons/{$this->instance}", [
            'number'      => $phone,
            'title'       => 'PsiAgenda',
            'description' => $message,
            'footer'      => 'PsiAgenda',
            'buttons'     => $payload,
        ]);
    }

    /**
     * Send a list (select) message (up to 10 rows).
     * Falls back to numbered text if the list endpoint is unavailable.
     *
     * @param  array  $sections  [['title' => 'Section', 'rows' => [['id' => 'r1', 'title' => '...', 'description' => '...']]]]
     */
    public function sendList(string $phone, string $title, string $body, string $buttonText, array $sections): array
    {
        $response = $this->post("/message/sendList/{$this->instance}", [
            'number'      => $phone,
            'title'       => $title,
            'description' => $body,
            'buttonText'  => $buttonText,
            'footerText'  => 'PsiAgenda',
            'sections'    => $sections,
        ]);

        // If list endpoint fails (known bug in some v2.3.x builds), fall back to text
        if (isset($response['error'])) {
            $text = "*{$title}*\n\n{$body}\n\n";
            $n = 1;
            foreach ($sections as $section) {
                foreach ($section['rows'] as $row) {
                    $text .= "{$n}. {$row['title']}\n";
                    $n++;
                }
            }
            $text .= "\n_Responda com o número da opção desejada._";
            return $this->sendText($phone, $text);
        }

        return $response;
    }

    // ── Connection management ──────────────────────────────────────────────

    /**
     * Get the current connection status of the WhatsApp instance.
     */
    public function getConnectionStatus(): array
    {
        return $this->get("/instance/connectionState/{$this->instance}");
    }

    /**
     * Fetch the QR code for connecting a new instance.
     */
    public function getQrCode(): array
    {
        return $this->get("/instance/connect/{$this->instance}");
    }

    // ── Internal HTTP helpers ──────────────────────────────────────────────

    private function post(string $path, array $data): array
    {
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}{$path}", $data);

        return $this->handleResponse($response, $path);
    }

    private function get(string $path): array
    {
        $response = Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}{$path}");

        return $this->handleResponse($response, $path);
    }

    private function headers(): array
    {
        return [
            'apikey'       => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    private function handleResponse(Response $response, string $path): array
    {
        if ($response->serverError()) {
            Log::error('[WhatsApp] Evolution API server error', [
                'path'   => $path,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            // Allow caller (job) to retry
            $response->throw();
        }

        if ($response->clientError()) {
            Log::warning('[WhatsApp] Evolution API client error', [
                'path'   => $path,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        }

        return $response->json() ?? [];
    }
}
