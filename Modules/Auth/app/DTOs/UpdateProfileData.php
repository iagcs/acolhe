<?php

namespace Modules\Auth\DTOs;

use Spatie\LaravelData\Data;

class UpdateProfileData extends Data
{
    public function __construct(
        public ?string $photo,
        public ?string $bio,
    ) {}
}
