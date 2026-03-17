<?php

namespace Modules\Agenda\Actions;

use Modules\Auth\Models\User;
use Modules\Session\Enums\SessionStatus;

class GenerateCalendarFeedAction
{
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

    private function escapeIcal(string $text): string
    {
        return str_replace(
            ['\\', ';', ',', "\n"],
            ['\\\\', '\\;', '\\,', '\\n'],
            $text
        );
    }
}
