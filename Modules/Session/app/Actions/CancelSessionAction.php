<?php

namespace Modules\Session\Actions;

use Modules\Auth\Models\User;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Events\SessionCancelled;
use Modules\Session\Exceptions\InvalidSessionStatusTransitionException;
use Modules\Session\Models\Session;

class CancelSessionAction
{
    public function __construct(
        private ShowSessionAction $showSessionAction,
    ) {}

    public function execute(User $user, string $id): Session
    {
        $session = $this->showSessionAction->execute($user, $id);

        if (! $session->status->canTransitionTo(SessionStatus::Cancelled)) {
            throw new InvalidSessionStatusTransitionException;
        }

        $session->update([
            'status' => SessionStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $session->refresh();

        SessionCancelled::dispatch($session);

        return $session;
    }
}
