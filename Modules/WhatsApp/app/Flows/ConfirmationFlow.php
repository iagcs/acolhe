<?php

namespace Modules\WhatsApp\Flows;

use Modules\Session\Actions\UpdateSessionAction;
use Modules\Session\DTOs\UpdateSessionData;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Models\Session;
use Modules\WhatsApp\Contracts\ConversationHandlerInterface;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;

/**
 * Handles the patient's response to a session confirmation reminder.
 *
 * Triggered when conversation state = 'confirm_session' (set by the
 * reminder notification service before sending the interactive reminder).
 *
 * State flow:
 *   confirm_session
 *     └─ ✅ Confirmar  → idle   (session status → confirmed)
 *     └─ 📅 Remarcar   → reschedule_slots (delegates to RescheduleFlow via state)
 */
class ConfirmationFlow implements ConversationHandlerInterface
{
    public function __construct(
        private readonly UpdateSessionAction $updateSessionAction,
    ) {}

    public function canHandle(WhatsAppConversation $conversation, IncomingMessageDTO $message): bool
    {
        return $conversation->state === 'confirm_session';
    }

    public function handle(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        $input = $message->effectiveInput();

        if ($this->isReschedule($input)) {
            // Hand off to RescheduleFlow by transitioning state
            return ConversationReply::text(
                message: '',  // RescheduleFlow will immediately produce output on next message
                nextState: 'reschedule_slots',
                contextPatch: [],
            );
        }

        return $this->confirmSession($conversation);
    }

    // ── Steps ──────────────────────────────────────────────────────────────

    private function confirmSession(WhatsAppConversation $conversation): ConversationReply
    {
        $sessionId = $conversation->getContext('pending_confirmation_session_id');

        if (! $sessionId) {
            return ConversationReply::text(
                message: "✅ Confirmado! Te esperamos na sessão. 💙",
                nextState: 'idle',
            );
        }

        /** @var Session|null $session */
        $session = Session::find($sessionId);

        if (! $session || $session->status->isTerminal()) {
            return ConversationReply::text(
                message: "✅ Certo! Até logo. 💙",
                nextState: 'idle',
            );
        }

        if ($session->status->canTransitionTo(SessionStatus::Confirmed)) {
            $this->updateSessionAction->execute(
                $conversation->psychologist,
                $sessionId,
                UpdateSessionData::from(['status' => SessionStatus::Confirmed->value]),
            );
        }

        $dateLabel = $session->starts_at->translatedFormat('l\, d/m') . ' às ' . $session->starts_at->format('H:i');

        return ConversationReply::text(
            message: "✅ Sessão Confirmada!\n\nTe esperamos *{$dateLabel}*. Até lá! 💙",
            nextState: 'idle',
        );
    }

    private function isReschedule(string $input): bool
    {
        return in_array($input, ['btn_reschedule', 'remarcar'])
            || str_contains($input, 'remarc')
            || str_contains($input, 'mudar');
    }
}
