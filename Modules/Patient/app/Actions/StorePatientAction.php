<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\DTOs\PatientData;
use Modules\Patient\Events\PatientCreated;
use Modules\Patient\Models\Patient;

class StorePatientAction
{
    /**
     * Create a new Patient associated with the given User and dispatch a PatientCreated event.
     *
     * @param User $user The user to associate with the new patient.
     * @param PatientData $data DTO containing patient fields (`name`, `email`, `phone`, `birth_date`, `notes`).
     * @return Patient The created Patient model.
     */
    public function execute(User $user, PatientData $data): Patient
    {
        $patient = $user->patients()->create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'birth_date' => $data->birth_date,
            'notes' => $data->notes,
            'is_active' => true,
        ]);

        PatientCreated::dispatch($patient);

        return $patient;
    }
}
