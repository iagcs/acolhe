<?php

namespace Modules\Patient\Actions;

use Modules\Auth\Models\User;
use Modules\Patient\DTOs\UpdatePatientData;
use Modules\Patient\Events\PatientUpdated;
use Modules\Patient\Models\Patient;

class UpdatePatientAction
{
    public function __construct(
        private ShowPatientAction $showPatientAction,
    ) {}

    /**
     * Update a patient's record with the provided data and return the updated Patient.
     *
     * @param User $user The user performing the update.
     * @param string $id The identifier of the patient to update.
     * @param UpdatePatientData $data DTO containing the fields to update on the patient.
     * @return Patient The patient model refreshed with the latest persisted data.
     */
    public function execute(User $user, string $id, UpdatePatientData $data): Patient
    {
        $patient = $this->showPatientAction->execute($user, $id);

        $patient->update($data->toArray());

        $patient->refresh();

        PatientUpdated::dispatch($patient);

        return $patient;
    }
}
