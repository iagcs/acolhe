<?php

namespace Modules\Patient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Patient\Models\Patient;

class PatientCreated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new PatientCreated event containing the created patient.
     *
     * @param Patient $patient The patient instance that was created and is carried by the event.
     */
    public function __construct(
        public readonly Patient $patient,
    ) {}
}
