<?php

namespace Modules\Auth\DTOs;

use Spatie\LaravelData\Data;

class AvailabilityData extends Data
{
    public function __construct(
        public int $day_of_week,
        public string $start_time,
        public string $end_time,
    ) {}
}
