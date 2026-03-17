<?php

namespace Modules\Agenda\Actions;

use Modules\Auth\Models\User;
use Modules\Session\Enums\SessionStatus;

class GenerateCalendarFeedAction
{
    /**
     * Generate an iCalendar (ICS) feed containing the user's upcoming sessions.
     *
     * The feed includes sessions with status not equal to Cancelled starting from one month ago,
     * each represented as a VEVENT with UID, DTSTART/DTEND (America/Sao_Paulo), SUMMARY (patient name fallback to "Paciente"),
     * DESCRIPTION (session type label), and STATUS (CONFIRMED or TENTATIVE).
     *
     * @param User $user The user whose sessions will be exported to the calendar.
     * @return string The complete iCalendar content as an ICS-formatted string (lines joined with CRLF).
     */
    public function execute(User $user): string
    {
        $sessions = $user->sessions()
            ->with('patient:id,name')
            ->select('id', 'patient_id', 'starts_at', 'ends_at', 'type', 'status')
            ->where('status', '!=', SessionStatus::Cancelled)
            ->where('starts_at', '>=', now()->subMonth())
            ->orderBy('starts_at')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//PsiAgenda//Agenda//PT',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:PsiAgenda - '.$this->escapeIcal($user->name),
            'X-WR-TIMEZONE:America/Sao_Paulo',
        ];

        foreach ($sessions as $session) {
            $patientName = $session->patient?->name ?? 'Paciente';
            $dtStart = $session->starts_at->format('Ymd\THis');
            $dtEnd = $session->ends_at->format('Ymd\THis');
            $typeLabel = $session->type === 'online' ? 'Online' : 'Presencial';

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:'.$session->id.'@psiagenda';
            $lines[] = 'DTSTART;TZID=America/Sao_Paulo:'.$dtStart;
            $lines[] = 'DTEND;TZID=America/Sao_Paulo:'.$dtEnd;
            $lines[] = 'SUMMARY:'.$this->escapeIcal('Sessão - '.$patientName);
            $lines[] = 'DESCRIPTION:'.$this->escapeIcal($typeLabel);
            $lines[] = 'STATUS:'.($session->status === SessionStatus::Confirmed ? 'CONFIRMED' : 'TENTATIVE');
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines);
    }

    /**
     * Escape text so it is safe to include in an iCalendar (ICS) property value.
     *
     * @param string $text The text to escape for iCal consumption.
     * @return string The text with backslashes, semicolons, commas, and newlines replaced by iCal-safe escape sequences.
     */
    private function escapeIcal(string $text): string
    {
        return str_replace(
            ['\\', ';', ',', "\n"],
            ['\\\\', '\\;', '\\,', '\\n'],
            $text
        );
    }
}
