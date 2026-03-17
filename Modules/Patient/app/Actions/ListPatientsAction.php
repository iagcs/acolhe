<?php

namespace Modules\Patient\Actions;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Auth\Models\User;

class ListPatientsAction
{
    public function execute(
        User $user,
        ?string $search = null,
        ?bool $isActive = null,
        int $perPage = 15,
    ): LengthAwarePaginator {
        $query = $user->patients()->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->paginate($perPage);
    }
}
