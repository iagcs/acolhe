<?php

namespace Modules\WhatsApp\DTOs;

/**
 * Normalised representation of any incoming WhatsApp message.
 * Covers text, button reply, and list reply message types.
 */
readonly class IncomingMessageDTO
{
    public function __construct(
        /** Sender phone in E.164 (e.g. 5511999999999) */
        public string $phone,

        /** Psychologist's WhatsApp number (the "to" side) */
        public string $psychologistPhone,

        /** Plain text of the message (may be empty for button/list replies) */
        public string $text,

        /** Selected button ID (for buttonsResponseMessage) */
        public ?string $buttonId,

        /** Selected list row ID (for listResponseMessage) */
        public ?string $listRowId,

        /** One of: text | button_reply | list_reply */
        public string $messageType,

        /** Raw Evolution API message ID for deduplication */
        public ?string $externalId,
    ) {}

    /**
     * The "effective input" used by flows for intent matching.
     * Prefers button/list selection ID over free text.
     */
    public function effectiveInput(): string
    {
        return $this->listRowId ?? $this->buttonId ?? mb_strtolower(trim($this->text));
    }
}
