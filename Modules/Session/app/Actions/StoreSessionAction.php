<?php

namespace Modules\Session\Actions;

use Carbon\Carbon;
use Modules\Auth\Models\User;
use Modules\Session\DTOs\SessionData;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Events\SessionCreated;
use Modules\Session\Models\Session;

class StoreSessionAction
{
    /**
     * Create and persist a new session for the given user using the provided session data.
     *
     * @param User $user The user who will own the session.
     * @param SessionData $data Data used to build the session (including scheduled_at, duration_minutes, patient_id, type, and notes).
     * @return Session The newly created Session model.
     */
    public function execute(User $user, SessionData $data): Session
    {
        $startsAt = Carbon::parse($data->scheduled_at);
        $endsAt = $startsAt->copy()->addMinutes($data->duration_minutes);

        $session = $user->sessions()->create([
            'patient_id' => $data->patient_id,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'type' => $data->type,
            'private_notes' => $data->notes,
            'price' => $user->session_price,
            'status' => SessionStatus::Scheduled,
        ]);

        SessionCreated::dispatch($session);

        return $session;
    }
}
