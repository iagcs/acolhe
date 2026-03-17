<?php

namespace Modules\Session\Actions;

use Modules\Auth\Models\User;
use Modules\Session\Exceptions\SessionNotFoundException;
use Modules\Session\Models\Session;

class ShowSessionAction
{
    /**
     * Retrieve a session belonging to the given user by its id.
     *
     * @param User   $user The user that owns the session.
     * @param string $id   The session identifier.
     * @return Session The matching Session instance.
     * @throws SessionNotFoundException If no session with the given id exists for the user.
     */
    public function execute(User $user, string $id): Session
    {
        $session = $user->sessions()->with('patient:id,name')->find($id);

        if (! $session) {
            throw new SessionNotFoundException;
        }

        return $session;
    }
}
