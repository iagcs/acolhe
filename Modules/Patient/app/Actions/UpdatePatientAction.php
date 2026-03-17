<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\DTOs\UpdatePatientData;
use Modules\Patient\Models\Patient;

class UpdatePatientAction
{
    public function __construct(
        private ShowPatientAction $showPatientAction,
    ) {}

    public function execute(User $user, string $id, UpdatePatientData $data): Patient
    {
        $patient = $this->showPatientAction->execute($user, $id);

        $patient->update($data->toArray());

        return $patient->refresh();
    }
}
