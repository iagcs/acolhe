<?php

namespace Modules\Session\Actions;

use Modules\Auth\Models\User;
use Modules\Session\Exceptions\SessionNotFoundException;
use Modules\Session\Models\Session;

class ShowSessionAction
{
    public function execute(User $user, string $id): Session
    {
        $session = $user->sessions()->with('patient:id,name')->find($id);

        if (! $session) {
            throw new SessionNotFoundException;
        }

        return $session;
    }
}
