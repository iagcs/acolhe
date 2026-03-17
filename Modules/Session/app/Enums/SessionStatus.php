<?php

namespace Modules\Session\Enums;

enum SessionStatus: string
{
    case Scheduled = 'scheduled';
    case PendingConfirmation = 'pending_confirmation';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
    case NoShow = 'no_show';

    private const ALLOWED_TRANSITIONS = [
        'scheduled' => ['pending_confirmation', 'confirmed', 'cancelled', 'completed', 'no_show'],
        'pending_confirmation' => ['confirmed', 'cancelled'],
        'confirmed' => ['cancelled', 'completed', 'no_show'],
        'cancelled' => [],
        'completed' => [],
        'no_show' => [],
    ];

    public /**
     * Determine whether the current session status may transition to the given target status.
     *
     * @param self $target The desired target session status.
     * @return bool `true` if the transition is allowed, `false` otherwise.
     */
    function canTransitionTo(self $target): bool
    {
        return in_array($target->value, self::ALLOWED_TRANSITIONS[$this->value] ?? []);
    }

    public /**
     * Determine whether the current session status is terminal (has no allowed next statuses).
     *
     * @return bool `true` if the status has no allowed transitions, `false` otherwise.
     */
    function isTerminal(): bool
    {
        return empty(self::ALLOWED_TRANSITIONS[$this->value] ?? []);
    }
}
