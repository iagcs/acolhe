<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\Exceptions\PatientNotFoundException;
use Modules\Patient\Models\Patient;

class ShowPatientAction
{
    public function execute(User $user, string $id): Patient
    {
        $patient = $user->patients()->find($id);

        if (! $patient) {
            throw new PatientNotFoundException;
        }

        return $patient;
    }
}
