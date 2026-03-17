<?php

namespace Modules\Session\Actions;

use Modules\Auth\Models\User;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Events\SessionCancelled;
use Modules\Session\Exceptions\InvalidSessionStatusTransitionException;
use Modules\Session\Models\Session;

class CancelSessionAction
{
    /**
     * Create a new CancelSessionAction.
     *
     * @param ShowSessionAction $showSessionAction Action used to retrieve a session before cancellation.
     */
    public function __construct(
        private ShowSessionAction $showSessionAction,
    ) {}

    /**
     * Cancel the specified session for the given user.
     *
     * Validates that the session can transition to `Cancelled`, sets the session's status to `SessionStatus::Cancelled`,
     * records `cancelled_at` with the current timestamp, dispatches the `SessionCancelled` event, and returns the refreshed session.
     *
     * @param User $user The user performing the cancellation.
     * @param string $id The session identifier.
     * @return Session The updated session with status set to `Cancelled` and `cancelled_at` populated.
     * @throws InvalidSessionStatusTransitionException If the session cannot transition to `Cancelled`.
     */
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
