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

    public function canTransitionTo(self $target): bool
    {
        return in_array($target->value, self::ALLOWED_TRANSITIONS[$this->value] ?? []);
    }

    public function isTerminal(): bool
    {
        return empty(self::ALLOWED_TRANSITIONS[$this->value] ?? []);
    }
}
