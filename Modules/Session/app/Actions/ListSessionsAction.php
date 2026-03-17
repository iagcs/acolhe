<?php

namespace Modules\Session\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Auth\Models\User;

class ListSessionsAction
{
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
