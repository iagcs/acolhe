<?php

namespace Modules\WhatsApp\Services;

use Modules\WhatsApp\DTOs\IncomingMessageDTO;

/**
 * Normalises the raw Evolution API webhook payload into a typed DTO.
 *
 * Evolution API sends different message structures depending on type:
 *   - conversation              → plain text
 *   - buttonsResponseMessage    → button selection
 *   - listResponseMessage       → list row selection
 */
class IncomingMessageParser
{
    /**
     * Parse the raw webhook payload array and return an IncomingMessageDTO.
     * Returns null if the payload is not a parseable inbound user message.
     */
    public function parse(array $payload): ?IncomingMessageDTO
    {
        // Only handle message upsert events from non-self messages
        if (($payload['event'] ?? '') !== 'messages.upsert') {
            return null;
        }

        $data = $payload['data'] ?? [];
        $key  = $data['key'] ?? [];

        // Skip messages sent by us
        if (($key['fromMe'] ?? true) === true) {
            return null;
        }

        $msg = $data['message'] ?? [];

        [$messageType, $text, $buttonId, $listRowId] = $this->extractContent($msg);

        if ($text === null && $buttonId === null && $listRowId === null) {
            return null;
        }

        // remoteJid is like "5511999999999@s.whatsapp.net" — strip suffix
        $phone = $this->stripJid($key['remoteJid'] ?? '');

        // Evolution API v2.x sends the connected instance number in "sender".
        // "destination" in v2 is the webhook URL, not a phone — so we use "sender".
        // Fallback to "destination" only if it doesn't look like a URL (v1 compat).
        $destination = $payload['destination'] ?? '';
        $psychologistRaw = (str_starts_with($destination, 'http') ? '' : $destination)
            ?: ($payload['sender'] ?? '');
        $psychologistPhone = $this->stripJid($psychologistRaw);

        return new IncomingMessageDTO(
            phone: $phone,
            psychologistPhone: $psychologistPhone,
            text: $text ?? '',
            buttonId: $buttonId,
            listRowId: $listRowId,
            messageType: $messageType,
            externalId: $key['id'] ?? null,
        );
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function extractContent(array $msg): array
    {
        // Plain text
        if (isset($msg['conversation'])) {
            return ['text', $msg['conversation'], null, null];
        }

        // Extended text
        if (isset($msg['extendedTextMessage']['text'])) {
            return ['text', $msg['extendedTextMessage']['text'], null, null];
        }

        // Button reply
        if (isset($msg['buttonsResponseMessage'])) {
            $btn = $msg['buttonsResponseMessage'];
            return [
                'button_reply',
                $btn['selectedDisplayText'] ?? '',
                $btn['selectedButtonId'] ?? null,
                null,
            ];
        }

        // List reply
        if (isset($msg['listResponseMessage'])) {
            $list = $msg['listResponseMessage'];
            return [
                'list_reply',
                $list['title'] ?? '',
                null,
                $list['singleSelectReply']['selectedRowId'] ?? null,
            ];
        }

        return ['unknown', null, null, null];
    }

    private function stripJid(string $jid): string
    {
        return preg_replace('/@.*$/', '', $jid);
    }
}
