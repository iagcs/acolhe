<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;

class DeletePatientAction
{
    public function __construct(
        private ShowPatientAction $showPatientAction,
    ) {}

    public function execute(User $user, string $id): void
    {
        $patient = $this->showPatientAction->execute($user, $id);

        $patient->delete();
    }
}
