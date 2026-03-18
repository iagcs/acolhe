<?php

use Illuminate\Support\Facades\Queue;
use Modules\WhatsApp\Jobs\ProcessIncomingMessageJob;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('WebhookController', function () {

    function makePayload(array $override = []): array
    {
        return array_merge([
            'event'       => 'messages.upsert',
            'instance'    => 'psiagenda',
            'destination' => '5511988888888',
            'data'        => [
                'key'     => ['remoteJid' => '5511999999999@s.whatsapp.net', 'fromMe' => false, 'id' => 'abc123'],
                'message' => ['conversation' => 'Oi'],
            ],
        ], $override);
    }

    it('accepts a valid payload without webhook secret configured and dispatches job', function () {
        Queue::fake();
        config(['whatsapp.webhook_secret' => '']);

        $response = $this->postJson('/api/whatsapp/webhook', makePayload());

        $response->assertOk()->assertJson(['ok' => true]);
        Queue::assertPushed(ProcessIncomingMessageJob::class);
    });

    it('returns 401 when HMAC signature is invalid', function () {
        config(['whatsapp.webhook_secret' => 'my-secret']);

        $response = $this->postJson('/api/whatsapp/webhook', makePayload(), [
            'x-evolution-signature' => 'sha256=invalidsignature',
        ]);

        $response->assertUnauthorized();
    });

    it('accepts request with valid HMAC signature', function () {
        Queue::fake();
        $secret  = 'test-secret';
        $body    = json_encode(makePayload());
        $sig     = 'sha256=' . hash_hmac('sha256', $body, $secret);

        config(['whatsapp.webhook_secret' => $secret]);

        $response = $this->call('POST', '/api/whatsapp/webhook', [], [], [], [
            'HTTP_X-EVOLUTION-SIGNATURE' => $sig,
            'CONTENT_TYPE'               => 'application/json',
        ], $body);

        $response->assertOk();
        Queue::assertPushed(ProcessIncomingMessageJob::class);
    });

    it('ignores non-message events and does not dispatch job', function () {
        Queue::fake();
        config(['whatsapp.webhook_secret' => '']);

        $response = $this->postJson('/api/whatsapp/webhook', [
            'event'    => 'connection.update',
            'instance' => 'psiagenda',
        ]);

        $response->assertOk();
        Queue::assertNothingPushed();
    });

    it('returns 200 immediately without waiting for processing', function () {
        Queue::fake();
        config(['whatsapp.webhook_secret' => '']);

        $start = microtime(true);
        $this->postJson('/api/whatsapp/webhook', makePayload());
        $elapsed = microtime(true) - $start;

        expect($elapsed)->toBeLessThan(1.0); // must return in under 1 second
    });
});
