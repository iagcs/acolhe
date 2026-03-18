<?php

namespace Modules\WhatsApp\Flows;

use Modules\WhatsApp\Contracts\ConversationHandlerInterface;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;

/**
 * Fallback handler — responds to greetings, "ajuda", and any unrecognised input.
 * Always runs last (lowest priority).
 */
class HelpFlow implements ConversationHandlerInterface
{
    public function canHandle(WhatsAppConversation $conversation, IncomingMessageDTO $message): bool
    {
        // Always handles idle conversations with unknown input
        return $conversation->state === 'idle';
    }

    public function handle(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        $isRegistered = $conversation->patient_id !== null;

        if ($isRegistered) {
            return ConversationReply::buttons(
                message: "Olá! 👋 Como posso te ajudar hoje?",
                buttons: [
                    ['id' => 'help_book',       'text' => '📅 Agendar sessão'],
                    ['id' => 'help_reschedule',  'text' => '🔄 Remarcar sessão'],
                ],
                nextState: 'idle',
            );
        }

        return ConversationReply::text(
            message: "Olá! 👋 Para começar, precisamos te cadastrar no sistema.\n\nQual é o seu *nome completo*?",
            nextState: 'onboarding_name',
        );
    }
}
