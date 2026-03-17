<?php

namespace Modules\Patient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Patient\Models\Patient;

class PatientUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Patient $patient,
    ) {}
}
