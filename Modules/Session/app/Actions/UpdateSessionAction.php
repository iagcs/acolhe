<?php

namespace Modules\Session\Actions;

use Carbon\Carbon;
use Modules\Auth\Models\User;
use Modules\Session\DTOs\UpdateSessionData;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Events\SessionCancelled;
use Modules\Session\Events\SessionRescheduled;
use Modules\Session\Events\SessionStatusChanged;
use Modules\Session\Exceptions\InvalidSessionStatusTransitionException;
use Modules\Session\Models\Session;
use Spatie\LaravelData\Optional;

class UpdateSessionAction
{
    public function __construct(
        private ShowSessionAction $showSessionAction,
    ) {}

    public function execute(User $user, string $id, UpdateSessionData $data): Session
    {
        $session = $this->showSessionAction->execute($user, $id);

        $updates = [];
        $oldStatus = $session->status;

        if (! $data->status instanceof Optional) {
            $targetStatus = SessionStatus::from($data->status);

            if (! $session->status->canTransitionTo($targetStatus)) {
                throw new InvalidSessionStatusTransitionException;
            }

            $updates['status'] = $targetStatus;

            if ($targetStatus === SessionStatus::Cancelled) {
                $updates['cancelled_at'] = now();
                if (! $data->cancellation_reason instanceof Optional) {
                    $updates['cancellation_reason'] = $data->cancellation_reason;
                }
            }
        }

        if (! $data->scheduled_at instanceof Optional) {
            $startsAt = Carbon::parse($data->scheduled_at);
            $duration = $data->duration_minutes instanceof Optional
                ? $session->starts_at->diffInMinutes($session->ends_at)
                : $data->duration_minutes;

            $updates['starts_at'] = $startsAt;
            $updates['ends_at'] = $startsAt->copy()->addMinutes($duration);
            $updates['reschedule_count'] = ($session->reschedule_count ?? 0) + 1;
        } elseif (! $data->duration_minutes instanceof Optional) {
            $updates['ends_at'] = $session->starts_at->copy()->addMinutes($data->duration_minutes);
        }

        if (! $data->type instanceof Optional) {
            $updates['type'] = $data->type;
        }

        if (! $data->notes instanceof Optional) {
            $updates['private_notes'] = $data->notes;
        }

        if (! empty($updates)) {
            $session->update($updates);
        }

        $session->refresh();

        if (isset($updates['status'])) {
            $newStatus = $updates['status'];

            if ($newStatus === SessionStatus::Cancelled) {
                SessionCancelled::dispatch($session);
            } else {
                SessionStatusChanged::dispatch($session, $oldStatus, $newStatus);
            }
        }

        if (isset($updates['starts_at'])) {
            SessionRescheduled::dispatch($session);
        }

        return $session;
    }
}
