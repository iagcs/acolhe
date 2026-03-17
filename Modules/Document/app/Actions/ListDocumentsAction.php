<?php

namespace Modules\Document\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Auth\Models\User;
use Modules\Patient\Exceptions\PatientNotFoundException;

class ListDocumentsAction
{
    public function execute(User $user, string $patientId): Collection
    {
        $patient = $user->patients()->find($patientId);

        if (! $patient) {
            throw new PatientNotFoundException;
        }

        return $patient->documents()
            ->orderByDesc('created_at')
            ->get();
    }
}
