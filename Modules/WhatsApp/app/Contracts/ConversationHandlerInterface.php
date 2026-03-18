<?php

namespace Modules\WhatsApp\Contracts;

use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;

/**
 * AI-ready contract for conversation flows.
 *
 * Each flow (Onboarding, Booking, Confirmation, Reschedule) implements this.
 * To replace a structured flow with an AI-powered one, bind the interface
 * to the new implementation in WhatsAppServiceProvider — no other code changes.
 */
interface ConversationHandlerInterface
{
    /**
     * Whether this handler should process the current conversation+input.
     */
    public function canHandle(WhatsAppConversation $conversation, IncomingMessageDTO $message): bool;

    /**
     * Process the message and return a reply. The state machine will apply
     * the reply's nextState and contextPatch automatically.
     */
    public function handle(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply;
}
