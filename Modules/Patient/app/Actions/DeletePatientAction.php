<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\Events\PatientDeleted;

class DeletePatientAction
{
    public function __construct(
        private ShowPatientAction $showPatientAction,
    ) {}

    public function execute(User $user, string $id): void
    {
        $patient = $this->showPatientAction->execute($user, $id);

        $patient->delete();

        PatientDeleted::dispatch($patient);
    }
}
