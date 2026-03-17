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

    public function __construct(
        private readonly string $userId,
        private readonly ?string $excludeSessionId = null,
        private readonly ?Carbon $existingStartsAt = null,
        private readonly ?Carbon $existingEndsAt = null,
    ) {}

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

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
