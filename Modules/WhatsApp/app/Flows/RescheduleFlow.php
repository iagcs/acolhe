<?php

namespace Modules\WhatsApp\Flows;

use Carbon\CarbonImmutable;
use Modules\Session\Actions\CancelSessionAction;
use Modules\Session\Actions\StoreSessionAction;
use Modules\Session\DTOs\SessionData;
use Modules\Session\Models\Session;
use Modules\WhatsApp\Contracts\ConversationHandlerInterface;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;
use Modules\WhatsApp\Services\SlotFinderService;

/**
 * Handles appointment rescheduling.
 *
 * Triggered when:
 *  - ConfirmationFlow sets state to 'reschedule_slots'
 *  - Patient explicitly says "remarcar" while in idle state
 *
 * State flow:
 *   reschedule_slots  → reschedule_confirm  (slot selected from list)
 *   reschedule_confirm → idle               (old cancelled, new created)
 */
class RescheduleFlow implements ConversationHandlerInterface
{
    /** Max times a single session can be rescheduled */
    private const MAX_RESCHEDULES = 2;

    public function __construct(
        private readonly SlotFinderService $slotFinder,
        private readonly CancelSessionAction $cancelSessionAction,
        private readonly StoreSessionAction $storeSessionAction,
    ) {}

    public function canHandle(WhatsAppConversation $conversation, IncomingMessageDTO $message): bool
    {
        if (in_array($conversation->state, ['reschedule_slots', 'reschedule_confirm'])) {
            return true;
        }

        // Handle explicit "remarcar" from idle when there's a session context
        if ($conversation->state === 'idle' && $conversation->patient_id !== null) {
            $input = $message->effectiveInput();
            return str_contains($input, 'remarc') || str_contains($input, 'mudar');
        }

        return false;
    }

    public function handle(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        return match ($conversation->state) {
            'idle', 'reschedule_slots' => $this->showSlots($conversation),
            'reschedule_confirm'       => $this->handleConfirmation($conversation, $message),
            default                    => $this->showSlots($conversation),
        };
    }

    // ── Steps ──────────────────────────────────────────────────────────────

    private function showSlots(WhatsAppConversation $conversation): ConversationReply
    {
        $sessionId = $conversation->getContext('pending_confirmation_session_id');
        $session   = $sessionId ? Session::find($sessionId) : null;

        if ($session && $session->reschedule_count >= self::MAX_RESCHEDULES) {
            return ConversationReply::text(
                message: "⚠️ Esta sessão já foi remarcada o máximo de vezes permitido.\nPor favor, entre em contato diretamente.",
                nextState: 'idle',
            );
        }

        $psychologist = $conversation->psychologist;
        $slots = $this->slotFinder->findAvailable($psychologist, 3);

        if ($slots->isEmpty()) {
            return ConversationReply::text(
                message: "😔 Não há horários disponíveis no momento.\nPor favor, tente mais tarde ou entre em contato diretamente.",
                nextState: 'idle',
            );
        }

        $rows = $slots->map(fn (CarbonImmutable $slot) => [
            'id'          => 'rslot_' . $slot->format('Y-m-d\TH:i'),
            'title'       => $this->formatSlotTitle($slot),
            'description' => $slot->format('H:i') . ' – ' . $slot->addMinutes((int) ($psychologist->session_duration ?? 50))->format('H:i'),
        ])->values()->toArray();

        return ConversationReply::list(
            message: "📅 Escolha um novo horário:",
            sections: [['title' => 'Horários disponíveis', 'rows' => $rows]],
            listButtonText: 'Ver horários',
            nextState: 'reschedule_confirm',
        );
    }

    private function handleConfirmation(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        $rowId = $message->listRowId;

        if (! $rowId || ! str_starts_with($rowId, 'rslot_')) {
            return $this->showSlots($conversation);
        }

        $scheduledAt = str_replace('rslot_', '', $rowId);
        $psychologist = $conversation->psychologist;
        $duration = (int) ($psychologist->session_duration ?? 50);

        $patientId = $conversation->patient_id ?? $conversation->getContext('patient_id');
        $oldSessionId = $conversation->getContext('pending_confirmation_session_id');

        // Cancel the old session
        if ($oldSessionId) {
            try {
                $this->cancelSessionAction->execute($psychologist, $oldSessionId);
            } catch (\Throwable) {
                // Old session may already be cancelled — continue
            }
        }

        // Create the new session
        $session = $this->storeSessionAction->execute($psychologist, new SessionData(
            patient_id: $patientId,
            scheduled_at: $scheduledAt,
            duration_minutes: $duration,
            type: 'in_person',
        ));

        $slot  = CarbonImmutable::parse($scheduledAt);
        $label = $this->formatSlotTitle($slot) . ' às ' . $slot->format('H:i');

        return ConversationReply::text(
            message: "✅ Sessão remarcada para *{$label}*.\n\nTe esperamos! 💙",
            nextState: 'idle',
            contextPatch: ['last_session_id' => $session->id],
        );
    }

    private function formatSlotTitle(CarbonImmutable $slot): string
    {
        $weekdays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $months   = ['', 'jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

        return $weekdays[$slot->dayOfWeek] . ' ' . $slot->day . '/' . $months[$slot->month];
    }
}
