<?php

namespace Modules\Session\Actions;

use Carbon\Carbon;
use Modules\Auth\Models\User;
use Modules\Session\DTOs\SessionData;
use Modules\Session\Models\Session;

class StoreSessionAction
{
    public function execute(User $user, SessionData $data): Session
    {
        $startsAt = Carbon::parse($data->scheduled_at);
        $endsAt = $startsAt->copy()->addMinutes($data->duration_minutes);

        return $user->sessions()->create([
            'patient_id' => $data->patient_id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'type' => $data->type,
            'private_notes' => $data->notes,
            'price' => $user->session_price,
            'status' => 'scheduled',
        ]);
    }
}
