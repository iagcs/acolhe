<?php

namespace Modules\Session\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Session\Models\Session;

class SessionCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Session $session,
    ) {}
}
