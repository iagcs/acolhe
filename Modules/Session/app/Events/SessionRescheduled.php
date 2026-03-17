<?php

namespace Modules\Session\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Session\Models\Session;

class SessionRescheduled
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new SessionRescheduled event that carries the rescheduled session.
     *
     * @param \Modules\Session\Models\Session $session The session that was rescheduled.
     */
    public function __construct(
        public readonly Session $session,
    ) {}
}
