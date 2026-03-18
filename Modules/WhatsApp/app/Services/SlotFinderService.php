<?php

namespace Modules\WhatsApp\Services;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Modules\Auth\Models\User;
use Modules\Session\Enums\SessionStatus;

/**
 * Finds available appointment slots for a psychologist.
 *
 * Algorithm:
 *  1. Load active Availability rows (day_of_week + time window)
 *  2. Generate candidate slots within the next 14 days
 *  3. Subtract slots already occupied by non-cancelled Sessions
 *  4. Return the first N available slots sorted by datetime
 */
class SlotFinderService
{
    /**
     * @return Collection<CarbonImmutable>
     */
    public function findAvailable(User $psychologist, int $limit = 5, int $lookAheadDays = 14): Collection
    {
        $duration = (int) ($psychologist->session_duration ?? 50);
        $interval = (int) ($psychologist->session_interval ?? 10);
        $slotSize = $duration + $interval; // minutes between slot starts

        $availabilities = $psychologist->availabilities()
            ->where('is_active', true)
            ->get();

        if ($availabilities->isEmpty()) {
            return collect();
        }

        // Collect all booked slots for the window
        $now      = CarbonImmutable::now();
        $windowEnd = $now->addDays($lookAheadDays);

        $bookedSlots = $psychologist->sessions()
            ->whereNotIn('status', [SessionStatus::Cancelled->value])
            ->whereBetween('starts_at', [$now, $windowEnd])
            ->get(['starts_at'])
            ->pluck('starts_at')
            ->map(fn ($dt) => $dt->format('Y-m-d H:i:s'));

        $slots = collect();

        for ($dayOffset = 0; $dayOffset <= $lookAheadDays; $dayOffset++) {
            $date        = $now->addDays($dayOffset);
            $dayOfWeek   = (int) $date->dayOfWeek; // 0=Sunday, 6=Saturday (Carbon default)

            $dayAvailabilities = $availabilities->where('day_of_week', $dayOfWeek);

            foreach ($dayAvailabilities as $availability) {
                $slotStart = CarbonImmutable::parse(
                    $date->toDateString() . ' ' . $availability->start_time
                );
                $windowClose = CarbonImmutable::parse(
                    $date->toDateString() . ' ' . $availability->end_time
                )->subMinutes($duration);

                while ($slotStart->lte($windowClose)) {
                    // Must be in the future (at least 1h from now)
                    if ($slotStart->gt($now->addHour())) {
                        $key = $slotStart->format('Y-m-d H:i:s');
                        if (! $bookedSlots->contains($key)) {
                            $slots->push($slotStart);
                        }
                    }

                    $slotStart = $slotStart->addMinutes($slotSize);
                }
            }

            if ($slots->count() >= $limit) {
                break;
            }
        }

        return $slots->sortBy(fn ($s) => $s->timestamp)->take($limit)->values();
    }
}
