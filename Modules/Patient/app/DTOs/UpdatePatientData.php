<?php

namespace Modules\Patient\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdatePatientData extends Data
{
    public function __construct(
        public string|Optional $name,
        public string|Optional $phone,
        public string|null|Optional $email,
        public string|null|Optional $birth_date,
        public string|null|Optional $notes,
        public bool|Optional $is_active,
    ) {}
}
