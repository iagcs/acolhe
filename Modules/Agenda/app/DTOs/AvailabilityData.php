<?php

namespace Modules\Agenda\DTOs;

use Spatie\LaravelData\Data;

class AvailabilityData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly int $day_of_week,
        public readonly string $start_time,
        public readonly string $end_time,
        public readonly bool $is_active,
    ) {}
}
