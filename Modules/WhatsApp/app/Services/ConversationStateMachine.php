<?php

namespace Modules\WhatsApp\Services;

use Illuminate\Support\Facades\Log;
use Modules\Auth\Models\User;
use Modules\WhatsApp\Contracts\ConversationHandlerInterface;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;
use Modules\WhatsApp\Models\WhatsAppMessage;

/**
 * Routes incoming messages to the appropriate handler and manages
 * conversation state persistence.
 *
 * The list of registered handlers is injected by WhatsAppServiceProvider,
 * making this class unaware of concrete flow implementations — including
 * any future AI-powered handlers.
 */
class ConversationStateMachine
{
    /**
     * @param  ConversationHandlerInterface[]  $handlers
     */
    public function __construct(
        private readonly array $handlers,
    ) {}

    /**
     * Process one inbound message end-to-end:
     *  1. Resolve or create the conversation
     *  2. Log the inbound message
     *  3. Find the appropriate handler
     *  4. Apply state + context changes
     *  5. Log the outbound reply
     *  6. Return the reply for the job to dispatch via EvolutionApiClient
     */
    public function process(IncomingMessageDTO $message, User $psychologist): ?ConversationReply
    {
        $conversation = $this->resolveConversation($message, $psychologist);

        $this->logMessage($conversation, 'inbound', $message->text ?: $message->buttonId ?: $message->listRowId, $message->messageType, $message->externalId);

        $handler = $this->findHandler($conversation, $message);

        if (! $handler) {
            Log::warning('[WhatsApp] No handler matched', [
                'phone'   => $message->phone,
                'state'   => $conversation->state,
                'input'   => $message->effectiveInput(),
            ]);
            return null;
        }

        $reply = $handler->handle($conversation, $message);

        // Apply state + context changes
        $conversation->state = $reply->nextState;
        $conversation->patchContext($reply->contextPatch);
        $conversation->last_message_at = now();
        $conversation->expires_at      = now()->addHours(24);

        // If patient was created/identified in context, link it
        if ($patientId = $conversation->getContext('patient_id')) {
            $conversation->patient_id = $patientId;
        }

        $conversation->save();

        $replyText = $this->buildReplyText($reply);
        $this->logMessage($conversation, 'outbound', $replyText, $reply->replyType);

        return $reply;
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function resolveConversation(IncomingMessageDTO $message, User $psychologist): WhatsAppConversation
    {
        return WhatsAppConversation::firstOrCreate(
            [
                'phone'            => $message->phone,
                'psychologist_id'  => $psychologist->id,
            ],
            [
                'state'           => 'idle',
                'context'         => [],
                'last_message_at' => now(),
                'expires_at'      => now()->addHours(24),
            ]
        );
    }

    private function findHandler(WhatsAppConversation $conversation, IncomingMessageDTO $message): ?ConversationHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->canHandle($conversation, $message)) {
                return $handler;
            }
        }
        return null;
    }

    private function buildReplyText(ConversationReply $reply): string
    {
        if ($reply->replyType === 'buttons') {
            $btns = implode(' | ', array_column($reply->buttons, 'text'));
            return $reply->message . ' [' . $btns . ']';
        }
        if ($reply->replyType === 'list') {
            $rows = collect($reply->sections)->flatMap(fn ($s) => $s['rows'])->pluck('title')->implode(', ');
            return $reply->message . ' [' . $rows . ']';
        }
        return $reply->message;
    }

    private function logMessage(WhatsAppConversation $conversation, string $direction, ?string $content, string $type, ?string $externalId = null): void
    {
        WhatsAppMessage::create([
            'conversation_id' => $conversation->id,
            'direction'       => $direction,
            'raw_content'     => $content ?? '',
            'message_type'    => $type,
            'external_id'     => $externalId,
            'status'          => $direction === 'outbound' ? 'sent' : 'delivered',
        ]);
    }
}
