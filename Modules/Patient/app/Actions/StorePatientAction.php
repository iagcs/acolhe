<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\DTOs\PatientData;
use Modules\Patient\Models\Patient;

class StorePatientAction
{
    public function execute(User $user, PatientData $data): Patient
    {
        return $user->patients()->create([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'birth_date' => $data->birth_date,
            'notes' => $data->notes,
            'is_active' => true,
        ]);
    }
}
