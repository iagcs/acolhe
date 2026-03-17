<?php

namespace Modules\Session\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Models\Session;

class SessionStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * Initialize the event with the affected session and its previous and new statuses.
     *
     * @param Session $session The session that changed.
     * @param SessionStatus $from The session's previous status.
     * @param SessionStatus $to The session's new status.
     */
    public function __construct(
        public readonly Session $session,
        public readonly SessionStatus $from,
        public readonly SessionStatus $to,
    ) {}
}
