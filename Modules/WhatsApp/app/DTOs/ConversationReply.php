<?php

namespace Modules\WhatsApp\DTOs;

/**
 * What a flow returns after processing a message.
 * The state machine applies nextState + contextPatch, then sends the reply.
 */
readonly class ConversationReply
{
    public function __construct(
        /** One of: text | buttons | list */
        public string $replyType,

        /** Main message body */
        public string $message,

        /** For replyType = buttons: [['id' => 'btn_x', 'text' => 'Label']] */
        public array $buttons,

        /**
         * For replyType = list:
         * [['title' => 'Section', 'rows' => [['id' => 'r1', 'title' => '...', 'description' => '...']]]]
         */
        public array $sections,

        /** Button text label for list messages */
        public string $listButtonText,

        /** Next state to persist on the conversation */
        public string $nextState,

        /** Partial context update (merged into existing context) */
        public array $contextPatch,
    ) {}

    public static function text(string $message, string $nextState = 'idle', array $contextPatch = []): self
    {
        return new self(
            replyType: 'text',
            message: $message,
            buttons: [],
            sections: [],
            listButtonText: '',
            nextState: $nextState,
            contextPatch: $contextPatch,
        );
    }

    public static function buttons(string $message, array $buttons, string $nextState, array $contextPatch = []): self
    {
        return new self(
            replyType: 'buttons',
            message: $message,
            buttons: $buttons,
            sections: [],
            listButtonText: '',
            nextState: $nextState,
            contextPatch: $contextPatch,
        );
    }

    public static function list(string $message, array $sections, string $listButtonText, string $nextState, array $contextPatch = []): self
    {
        return new self(
            replyType: 'list',
            message: $message,
            buttons: [],
            sections: $sections,
            listButtonText: $listButtonText,
            nextState: $nextState,
            contextPatch: $contextPatch,
        );
    }
}
