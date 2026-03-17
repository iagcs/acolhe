<?php

namespace Modules\Agenda\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Auth\Models\User;

class ListAvailabilitiesAction
{
    public function execute(User $user): Collection
    {
        return $user->availabilities()
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();
    }
}
