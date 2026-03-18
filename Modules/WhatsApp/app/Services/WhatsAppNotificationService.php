<?php

namespace Modules\WhatsApp\Services;

use Carbon\Carbon;
use Modules\Session\Models\Session;
use Modules\WhatsApp\Jobs\SendWhatsAppMessageJob;
use Modules\WhatsApp\Models\WhatsAppConversation;

/**
 * Public API for other modules (Reminder, Receipt, etc.) to send WhatsApp messages.
 *
 * Sets the conversation into 'confirm_session' state before sending the
 * interactive reminder, so the bot knows to handle the patient's reply as
 * a confirmation or reschedule request.
 */
class WhatsAppNotificationService
{
    /**
     * Send a session reminder with interactive Confirm/Reschedule buttons.
     *
     * @param  string  $type  One of: '48h', '24h', '2h', 'fallback'
     */
    public function sendReminder(Session $session, string $type = '24h'): void
    {
        $patient      = $session->patient;
        $psychologist = $session->psychologist;

        if (! $patient?->phone || ! $psychologist) {
            return;
        }

        $startsAt  = Carbon::parse($session->starts_at);
        $dateLabel = $this->formatDateTime($startsAt);
        $name      = $patient->name;

        $prefix = match ($type) {
            '48h'      => "Oi *{$name}*! 👋 Lembrete: você tem uma sessão em 2 dias,\n📅 *{$dateLabel}*",
            '24h'      => "Oi *{$name}*! 👋 Lembrete: você tem uma sessão amanhã,\n📅 *{$dateLabel}*",
            '2h'       => "Oi *{$name}*! ⏰ Sua sessão é *hoje às " . $startsAt->format('H:i') . "*.",
            'fallback' => "Oi *{$name}*, ainda não recebemos sua confirmação para *{$dateLabel}*.",
            default    => "Oi *{$name}*! Lembrete da sua sessão: *{$dateLabel}*.",
        };

        $isInteractive = in_array($type, ['48h', '24h', 'fallback']);

        // Prepare conversation state so the bot handles the reply correctly
        $conversation = WhatsAppConversation::firstOrCreate(
            ['phone' => $patient->phone, 'psychologist_id' => $psychologist->id],
            ['state' => 'idle', 'context' => [], 'last_message_at' => now(), 'expires_at' => now()->addHours(24)],
        );

        if ($isInteractive) {
            $conversation->state = 'confirm_session';
            $conversation->patchContext(['pending_confirmation_session_id' => $session->id]);
            $conversation->last_message_at = now();
            $conversation->expires_at      = now()->addHours(24);
            $conversation->save();
        }

        if ($isInteractive) {
            SendWhatsAppMessageJob::dispatch($patient->phone, 'buttons', $prefix, [
                ['id' => 'btn_confirm',    'text' => '✅ Confirmar'],
                ['id' => 'btn_reschedule', 'text' => '📅 Remarcar'],
            ]);
        } else {
            // 2h reminder is informational only
            SendWhatsAppMessageJob::dispatch($patient->phone, 'text', $prefix);
        }
    }

    /**
     * Send a plain text notification (receipts, generic messages, etc.)
     */
    public function sendText(string $phone, string $message): void
    {
        SendWhatsAppMessageJob::dispatch($phone, 'text', $message);
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    private function formatDateTime(Carbon $dt): string
    {
        $weekdays = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $months   = ['', 'jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];

        return $weekdays[$dt->dayOfWeek] . ' ' . $dt->day . '/' . $months[$dt->month] . ' às ' . $dt->format('H:i');
    }
}
