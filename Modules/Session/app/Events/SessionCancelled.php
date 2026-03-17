<?php

namespace Modules\Session\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Session\Models\Session;

class SessionCancelled
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance representing a cancelled session.
     *
     * @param Session $session The Session model instance that was cancelled.
     */
    public function __construct(
        public readonly Session $session,
    ) {}
}
