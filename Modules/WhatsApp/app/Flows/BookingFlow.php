<?php

namespace Modules\WhatsApp\Flows;

use Carbon\CarbonImmutable;
use Modules\Patient\Models\Patient;
use Modules\Session\Actions\StoreSessionAction;
use Modules\Session\DTOs\SessionData;
use Modules\WhatsApp\Contracts\ConversationHandlerInterface;
use Modules\WhatsApp\DTOs\ConversationReply;
use Modules\WhatsApp\DTOs\IncomingMessageDTO;
use Modules\WhatsApp\Models\WhatsAppConversation;
use Modules\WhatsApp\Services\SlotFinderService;

/**
 * Handles appointment booking via a list-select + button-confirm flow.
 *
 * State flow:
 *   idle (booking intent detected) → booking_slots
 *   booking_slots (slot selected)  → booking_confirm
 *   booking_confirm (confirmed)    → idle
 */
class BookingFlow implements ConversationHandlerInterface
{
    /** Keywords that trigger the booking flow */
    private const BOOKING_KEYWORDS = [
        'agendar', 'marcar', 'sessão', 'sessao', 'horário', 'horario', 'consulta', 'agenda',
    ];

    public function __construct(
        private readonly SlotFinderService $slotFinder,
        private readonly StoreSessionAction $storeSessionAction,
    ) {}

    public function canHandle(WhatsAppConversation $conversation, IncomingMessageDTO $message): bool
    {
        if (in_array($conversation->state, ['booking_slots', 'booking_confirm'])) {
            return true;
        }

        if ($conversation->state === 'idle' && $conversation->patient_id !== null) {
            return $this->hasBookingIntent($message->effectiveInput());
        }

        return false;
    }

    public function handle(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        return match ($conversation->state) {
            'idle'            => $this->showSlots($conversation),
            'booking_slots'   => $this->handleSlotSelection($conversation, $message),
            'booking_confirm' => $this->handleConfirmation($conversation, $message),
            default           => $this->showSlots($conversation),
        };
    }

    // ── Steps ──────────────────────────────────────────────────────────────

    private function showSlots(WhatsAppConversation $conversation): ConversationReply
    {
        $psychologist = $conversation->psychologist;
        $slots = $this->slotFinder->findAvailable($psychologist, 5);

        if ($slots->isEmpty()) {
            return ConversationReply::text(
                message: "😔 Não há horários disponíveis nos próximos 14 dias.\n\nEntre em contato diretamente para verificar disponibilidade.",
                nextState: 'idle',
            );
        }

        $duration = (int) ($psychologist->session_duration ?? 50);

        // Store slot index → ISO string in context so user can pick by number (1-5)
        $slotMap = $slots->values()->mapWithKeys(
            fn (CarbonImmutable $slot, int $i) => [(string) ($i + 1) => $slot->format('Y-m-d\TH:i')]
        )->toArray();

        $rows = $slots->map(fn (CarbonImmutable $slot) => [
            'id'          => 'slot_' . $slot->format('Y-m-d\TH:i'),
            // Title includes time so the text fallback is readable
            'title'       => $this->formatSlotTitle($slot) . ' às ' . $slot->format('H:i'),
            'description' => $slot->format('H:i') . ' – ' . $slot->addMinutes($duration)->format('H:i'),
        ])->values()->toArray();

        return ConversationReply::list(
            message: "📅 Ótimo! Aqui estão os próximos horários disponíveis:",
            sections: [['title' => 'Horários disponíveis', 'rows' => $rows]],
            listButtonText: 'Ver horários',
            nextState: 'booking_slots',
            contextPatch: ['slot_map' => $slotMap],
        );
    }

    private function handleSlotSelection(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        $input = trim($message->effectiveInput());
        $rowId = $message->listRowId;

        // Prefer list reply ID (interactive list), then check if user typed a number (1-5)
        if ($rowId && str_starts_with($rowId, 'slot_')) {
            $scheduledAt = str_replace('slot_', '', $rowId);
        } elseif (preg_match('/^[1-5]$/', $input)) {
            // User picked by number from the text fallback list
            $slotMap = $conversation->getContext('slot_map', []);
            $scheduledAt = $slotMap[$input] ?? null;
            if (! $scheduledAt) {
                return $this->showSlots($conversation);
            }
        } else {
            // Unrecognised input while in booking_slots — re-show the list
            return $this->showSlots($conversation);
        }

        try {
            $slot = CarbonImmutable::parse($scheduledAt);
        } catch (\Exception) {
            return $this->showSlots($conversation);
        }

        $label = $this->formatSlotTitle($slot) . ' às ' . $slot->format('H:i');

        return ConversationReply::buttons(
            message: "Confirmar sessão para *{$label}*?",
            buttons: [
                ['id' => 'book_yes', 'text' => '✅ Confirmar'],
                ['id' => 'book_no',  'text' => '❌ Cancelar'],
            ],
            nextState: 'booking_confirm',
            contextPatch: ['pending_slot' => $scheduledAt],
        );
    }

    private function handleConfirmation(WhatsAppConversation $conversation, IncomingMessageDTO $message): ConversationReply
    {
        $input = $message->effectiveInput();

        if ($input === 'book_no' || str_contains($input, 'cancelar') || str_contains($input, 'não')) {
            return ConversationReply::text(
                message: "Tudo bem! Agendamento cancelado. Se quiser marcar outro horário, é só dizer *agendar*. 💙",
                nextState: 'idle',
            );
        }

        $scheduledAt = $conversation->getContext('pending_slot');
        $patientId   = $conversation->patient_id ?? $conversation->getContext('patient_id');

        if (! $scheduledAt || ! $patientId) {
            return ConversationReply::text(
                message: "Algo deu errado. Por favor, tente novamente dizendo *agendar*.",
                nextState: 'idle',
            );
        }

        $psychologist = $conversation->psychologist;
        $duration = (int) ($psychologist->session_duration ?? 50);

        $session = $this->storeSessionAction->execute($psychologist, new SessionData(
            patient_id: $patientId,
            scheduled_at: $scheduledAt,
            duration_minutes: $duration,
            type: 'in_person',
        ));

        $label = $this->formatSlotTitle(CarbonImmutable::parse($scheduledAt))
            . ' às ' . CarbonImmutable::parse($scheduledAt)->format('H:i');

        return ConversationReply::text(
            message: "✅ Sessão agendada com sucesso!\n\n📅 *{$label}*\n\nTe esperamos. Até lá! 💙",
            nextState: 'idle',
            contextPatch: ['last_session_id' => $session->id],
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function hasBookingIntent(string $input): bool
    {
        $normalised = $this->normalise($input);
        foreach (self::BOOKING_KEYWORDS as $keyword) {
            if (str_contains($normalised, $keyword)) {
                return true;
            }
        }
        return false;
    }

    private function normalise(string $text): string
    {
        $text = mb_strtolower($text);
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return $text;
    }

    private function formatSlotTitle(CarbonImmutable $slot): string
    {
        $weekdays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $months   = ['', 'jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

        return $weekdays[$slot->dayOfWeek] . ' ' . $slot->day . '/' . $months[$slot->month];
    }
}
