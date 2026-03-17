<?php

namespace Modules\Session\DTOs;

use Spatie\LaravelData\Data;

class SessionData extends Data
{
    /**
     * Create a SessionData DTO representing a patient session.
     *
     * @param string $patient_id Identifier of the patient.
     * @param string $scheduled_at Scheduled date and time as a string.
     * @param int $duration_minutes Session length in minutes; defaults to 50.
     * @param string $type Session type (e.g., 'in_person'); defaults to 'in_person'.
     * @param string|null $notes Optional free-form notes about the session.
     */
    public function __construct(
        public string $patient_id,
        public string $scheduled_at,
        public int $duration_minutes = 50,
        public string $type = 'in_person',
        public ?string $notes = null,
    ) {}
}
