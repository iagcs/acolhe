<?php

namespace Modules\Patient\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Patient\Models\Patient;

class PatientDeleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new PatientDeleted event instance.
     *
     * @param Patient $patient The patient model instance associated with the deletion event.
     */
    public function __construct(
        public readonly Patient $patient,
    ) {}
}
