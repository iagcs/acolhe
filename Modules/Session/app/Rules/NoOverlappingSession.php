<?php

namespace Modules\Session\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Models\Session;

class NoOverlappingSession implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    /**
     * Create a NoOverlappingSession rule for a specific psychologist and optional existing session context.
     *
     * @param string $userId The psychologist's user ID to check for conflicting sessions.
     * @param string|null $excludeSessionId Optional session ID to exclude from the overlap check (useful when updating an existing session).
     * @param Carbon|null $existingStartsAt Optional original session start time used to preserve original duration when duration is not provided.
     * @param Carbon|null $existingEndsAt Optional original session end time used to preserve original duration when duration is not provided.
     */
    public function __construct(
        private readonly string $userId,
        private readonly ?string $excludeSessionId = null,
        private readonly ?Carbon $existingStartsAt = null,
        private readonly ?Carbon $existingEndsAt = null,
    ) {}

    /**
     * Set request data used by the rule when resolving session start and end times.
     *
     * @param array $data The request data used to resolve `scheduled_at` and `duration_minutes`.
     * @return static The rule instance for method chaining.
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Validates that the proposed session time does not overlap any existing non-cancelled session for the psychologist.
     *
     * If an overlapping session exists (excluding an optional session id provided to the rule), invokes the provided
     * failure callback with a localized message.
     *
     * @param string $attribute The validated attribute name.
     * @param mixed $value The value of the validated attribute.
     * @param Closure $fail Callback to register a validation failure when an overlap is detected.
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startsAt = $this->resolveStartsAt($value, $attribute);
        $endsAt = $this->resolveEndsAt($startsAt);

        if (! $startsAt || ! $endsAt) {
            return;
        }

        $query = Session::where('psychologist_id', $this->userId)
            ->whereNotIn('status', [SessionStatus::Cancelled])
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt);

        if ($this->excludeSessionId) {
            $query->where('id', '!=', $this->excludeSessionId);
        }

        if ($query->exists()) {
            $fail('Já existe uma sessão agendada neste horário.');
        }
    }

    /**
     * Resolve the session start time from the current validation context.
     *
     * Chooses the start time in this order of precedence: the provided attribute value when the attribute
     * is 'scheduled_at', the rule's data 'scheduled_at' entry if present, or the existing start time
     * passed to the rule.
     *
     * @param mixed  $value     The value of the attribute being validated.
     * @param string $attribute The name of the attribute being validated.
     * @return Carbon|null The resolved start time as a Carbon instance, or `null` if none is available.
     */
    private function resolveStartsAt(mixed $value, string $attribute): ?Carbon
    {
        if ($attribute === 'scheduled_at') {
            return Carbon::parse($value);
        }

        if (isset($this->data['scheduled_at'])) {
            return Carbon::parse($this->data['scheduled_at']);
        }

        return $this->existingStartsAt;
    }

    /**
     * Compute the session end time based on the provided start time.
     *
     * Uses `data['duration_minutes']` when present, otherwise preserves the original duration
     * from `$existingStartsAt`/`$existingEndsAt` if available, and falls back to a 50-minute default.
     *
     * @param Carbon $startsAt The start time to base the end time on.
     * @return Carbon|null A Carbon instance representing the computed end time.
     */
    private function resolveEndsAt(Carbon $startsAt): ?Carbon
    {
        $durationMinutes = $this->data['duration_minutes'] ?? null;

        if ($durationMinutes) {
            return $startsAt->copy()->addMinutes((int) $durationMinutes);
        }

        if ($this->existingEndsAt && $this->existingStartsAt) {
            $originalDuration = $this->existingStartsAt->diffInMinutes($this->existingEndsAt);

            return $startsAt->copy()->addMinutes($originalDuration);
        }

        return $startsAt->copy()->addMinutes(50);
    }
}
