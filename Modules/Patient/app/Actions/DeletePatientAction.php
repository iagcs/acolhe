<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\Events\PatientDeleted;

class DeletePatientAction
{
    public function __construct(
        private ShowPatientAction $showPatientAction,
    ) {}

    /**
     * Deletes the patient identified by the given ID and dispatches a PatientDeleted event.
     *
     * Retrieves the patient visible to the provided user, deletes the patient record, and then
     * dispatches the PatientDeleted event with the deleted patient as payload.
     *
     * @param User $user The user performing the deletion (used to scope retrieval/authorization).
     * @param string $id The identifier of the patient to delete.
     */
    public function execute(User $user, string $id): void
    {
        $patient = $this->showPatientAction->execute($user, $id);

        $patient->delete();

        PatientDeleted::dispatch($patient);
    }
}
