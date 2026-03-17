<?php

namespace Modules\Patient\DTOs;

use Spatie\LaravelData\Data;

class PatientData extends Data
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $email,
        public ?string $birth_date,
        public ?string $notes,
    ) {}
}
