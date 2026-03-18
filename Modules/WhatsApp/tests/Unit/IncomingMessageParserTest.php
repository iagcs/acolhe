<?php

use Modules\WhatsApp\Services\IncomingMessageParser;

describe('IncomingMessageParser', function () {

    beforeEach(function () {
        $this->parser = new IncomingMessageParser();
    });

    it('parses a plain text message', function () {
        $payload = [
            'event'       => 'messages.upsert',
            'destination' => '5511988888888',
            'data'        => [
                'key'     => ['remoteJid' => '5511999999999@s.whatsapp.net', 'fromMe' => false, 'id' => 'msg1'],
                'message' => ['conversation' => 'Quero agendar'],
            ],
        ];

        $dto = $this->parser->parse($payload);

        expect($dto)->not->toBeNull();
        expect($dto->phone)->toBe('5511999999999');
        expect($dto->psychologistPhone)->toBe('5511988888888');
        expect($dto->text)->toBe('Quero agendar');
        expect($dto->messageType)->toBe('text');
        expect($dto->buttonId)->toBeNull();
        expect($dto->listRowId)->toBeNull();
    });

    it('parses a button reply message', function () {
        $payload = [
            'event'       => 'messages.upsert',
            'destination' => '5511988888888',
            'data'        => [
                'key'     => ['remoteJid' => '5511999999999@s.whatsapp.net', 'fromMe' => false, 'id' => 'msg2'],
                'message' => [
                    'buttonsResponseMessage' => [
                        'selectedButtonId'    => 'btn_confirm',
                        'selectedDisplayText' => '✅ Confirmar',
                    ],
                ],
            ],
        ];

        $dto = $this->parser->parse($payload);

        expect($dto)->not->toBeNull();
        expect($dto->messageType)->toBe('button_reply');
        expect($dto->buttonId)->toBe('btn_confirm');
        expect($dto->text)->toBe('✅ Confirmar');
        expect($dto->listRowId)->toBeNull();
    });

    it('parses a list reply message', function () {
        $payload = [
            'event'       => 'messages.upsert',
            'destination' => '5511988888888',
            'data'        => [
                'key'     => ['remoteJid' => '5511999999999@s.whatsapp.net', 'fromMe' => false, 'id' => 'msg3'],
                'message' => [
                    'listResponseMessage' => [
                        'title'              => 'Seg 20/jan',
                        'singleSelectReply'  => ['selectedRowId' => 'slot_2026-01-20T10:00'],
                    ],
                ],
            ],
        ];

        $dto = $this->parser->parse($payload);

        expect($dto)->not->toBeNull();
        expect($dto->messageType)->toBe('list_reply');
        expect($dto->listRowId)->toBe('slot_2026-01-20T10:00');
        expect($dto->buttonId)->toBeNull();
    });

    it('returns null for fromMe messages', function () {
        $payload = [
            'event'       => 'messages.upsert',
            'destination' => '5511988888888',
            'data'        => [
                'key'     => ['remoteJid' => '5511999999999@s.whatsapp.net', 'fromMe' => true, 'id' => 'msg4'],
                'message' => ['conversation' => 'Test'],
            ],
        ];

        expect($this->parser->parse($payload))->toBeNull();
    });

    it('returns null for non-message events', function () {
        $payload = ['event' => 'connection.update', 'data' => []];
        expect($this->parser->parse($payload))->toBeNull();
    });

    it('effectiveInput prefers listRowId over text', function () {
        $payload = [
            'event'       => 'messages.upsert',
            'destination' => '5511988888888',
            'data'        => [
                'key'     => ['remoteJid' => '5511999999999@s.whatsapp.net', 'fromMe' => false],
                'message' => [
                    'listResponseMessage' => [
                        'title'             => 'Seg',
                        'singleSelectReply' => ['selectedRowId' => 'slot_abc'],
                    ],
                ],
            ],
        ];

        $dto = $this->parser->parse($payload);
        expect($dto->effectiveInput())->toBe('slot_abc');
    });
});
