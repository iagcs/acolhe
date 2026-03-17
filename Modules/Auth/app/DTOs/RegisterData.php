<?php

namespace Modules\Auth\DTOs;

use Modules\Auth\Enums\TherapeuticApproach;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class RegisterData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public string $crp,
        public string $phone,
        public TherapeuticApproach $therapeutic_approach,
        public int $session_duration,
        public int $session_interval,
        public float $session_price,
        #[DataCollectionOf(AvailabilityData::class)]
        public array $availabilities,
    ) {}
}
