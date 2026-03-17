<?php

namespace Modules\Patient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Patient\Models\Patient;

class PatientUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new PatientUpdated event carrying the updated Patient.
     *
     * @param \Modules\Patient\Models\Patient $patient The Patient model instance that was updated and will be exposed by the event.
     */
    public function __construct(
        public readonly Patient $patient,
    ) {}
}
