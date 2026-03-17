<?php

namespace Modules\Session\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Auth\Models\User;

class ListSessionsAction
{
    /**
     * List sessions for a user applying optional date, patient, and status filters with pagination.
     *
     * The returned paginator contains session models ordered by `starts_at` and eager-loads the `patient` relation (only `id` and `name`).
     *
     * @param User $user The owner whose sessions will be listed.
     * @param string|null $from Optional lower bound for `starts_at` (date/time string).
     * @param string|null $to Optional upper bound for `starts_at` (date/time string).
     * @param string|null $patientId Optional patient ID to filter sessions by.
     * @param string|null $status Optional session status to filter by.
     * @param int $perPage Number of sessions per page.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator A paginator of session models with the `patient` relation loaded.
     */
    public function execute(
        User $user,
        ?string $from = null,
        ?string $to = null,
        ?string $patientId = null,
        ?string $status = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = $user->sessions()
            ->with('patient:id,name')
            ->orderBy('starts_at');

        if ($from) {
            $query->where('starts_at', '>=', $from);
        }

        if ($to) {
            $query->where('starts_at', '<=', $to);
        }

        if ($patientId) {
            $query->where('patient_id', $patientId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}
