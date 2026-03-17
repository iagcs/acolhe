<?php

namespace Modules\Session\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Models\Session;

class SessionStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Session $session,
        public readonly SessionStatus $from,
        public readonly SessionStatus $to,
    ) {}
}
