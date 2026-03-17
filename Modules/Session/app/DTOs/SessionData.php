<?php

namespace Modules\Session\DTOs;

use Spatie\LaravelData\Data;

class SessionData extends Data
{
    public function __construct(
        public string $patient_id,
        public string $scheduled_at,
        public int $duration_minutes = 50,
        public string $type = 'online',
        public ?string $notes = null,
    ) {}
}
