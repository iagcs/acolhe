<?php

namespace Modules\Session\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateSessionData extends Data
{
    public function __construct(
        public string|Optional $scheduled_at,
        public int|Optional $duration_minutes,
        public string|Optional $type,
        public string|null|Optional $notes,
        public string|Optional $status,
        public string|null|Optional $cancellation_reason,
    ) {}
}
