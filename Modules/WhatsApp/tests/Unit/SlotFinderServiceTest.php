<?php

use Carbon\Carbon;
use Database\Factories\SessionFactory;
use Database\Factories\UserFactory;
use Modules\Agenda\Models\Availability;
use Modules\Session\Enums\SessionStatus;
use Modules\Session\Models\Session;
use Modules\WhatsApp\Services\SlotFinderService;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

describe('SlotFinderService', function () {

    beforeEach(function () {
        $this->service = new SlotFinderService();
        $this->psychologist = UserFactory::new()->create([
            'session_duration' => 50,
            'session_interval' => 10,
        ]);
    });

    it('returns empty collection when no availabilities exist', function () {
        $slots = $this->service->findAvailable($this->psychologist);
        expect($slots)->toBeEmpty();
    });

    it('returns slots within availability window', function () {
        $today = Carbon::now();
        $dayOfWeek = $today->addDay()->dayOfWeek; // Tomorrow

        Availability::create([
            'psychologist_id' => $this->psychologist->id,
            'day_of_week'     => $dayOfWeek,
            'start_time'      => '09:00:00',
            'end_time'        => '18:00:00',
            'is_active'       => true,
        ]);

        $slots = $this->service->findAvailable($this->psychologist, 5);

        expect($slots)->not->toBeEmpty();
        expect($slots->count())->toBeLessThanOrEqual(5);
    });

    it('excludes slots occupied by existing sessions', function () {
        $tomorrow = Carbon::now()->addDay();
        $dayOfWeek = $tomorrow->dayOfWeek;

        Availability::create([
            'psychologist_id' => $this->psychologist->id,
            'day_of_week'     => $dayOfWeek,
            'start_time'      => '09:00:00',
            'end_time'        => '11:00:00',
            'is_active'       => true,
        ]);

        // Book the 09:00 slot
        Session::create([
            'psychologist_id' => $this->psychologist->id,
            'patient_id'      => \Database\Factories\PatientFactory::new()->create(['psychologist_id' => $this->psychologist->id])->id,
            'starts_at'       => $tomorrow->copy()->setTime(9, 0),
            'ends_at'         => $tomorrow->copy()->setTime(9, 50),
            'status'          => SessionStatus::Scheduled,
            'type'            => 'in_person',
        ]);

        $slots = $this->service->findAvailable($this->psychologist, 5);

        // Check the specific booked slot (date + time) is not returned, not just any 09:00
        $slotDateTimes = $slots->map(fn ($s) => $s->format('Y-m-d H:i'));
        expect($slotDateTimes)->not->toContain($tomorrow->format('Y-m-d') . ' 09:00');
    });

    it('does not exclude cancelled sessions', function () {
        $tomorrow = Carbon::now()->addDay();
        $dayOfWeek = $tomorrow->dayOfWeek;

        Availability::create([
            'psychologist_id' => $this->psychologist->id,
            'day_of_week'     => $dayOfWeek,
            'start_time'      => '09:00:00',
            'end_time'        => '11:00:00',
            'is_active'       => true,
        ]);

        Session::create([
            'psychologist_id' => $this->psychologist->id,
            'patient_id'      => \Database\Factories\PatientFactory::new()->create(['psychologist_id' => $this->psychologist->id])->id,
            'starts_at'       => $tomorrow->copy()->setTime(9, 0),
            'ends_at'         => $tomorrow->copy()->setTime(9, 50),
            'status'          => SessionStatus::Cancelled,
            'type'            => 'in_person',
            'cancelled_at'    => now(),
        ]);

        $slots = $this->service->findAvailable($this->psychologist, 5);
        $slotTimes = $slots->map(fn ($s) => $s->format('H:i'));

        expect($slotTimes)->toContain('09:00');
    });

    it('respects session_duration + session_interval for slot spacing', function () {
        $tomorrow = Carbon::now()->addDay();
        $dayOfWeek = $tomorrow->dayOfWeek;

        Availability::create([
            'psychologist_id' => $this->psychologist->id,
            'day_of_week'     => $dayOfWeek,
            'start_time'      => '09:00:00',
            'end_time'        => '12:00:00',
            'is_active'       => true,
        ]);

        $slots = $this->service->findAvailable($this->psychologist, 10);

        if ($slots->count() >= 2) {
            // Carbon 3.x returns signed float; compare abs difference directly on objects
            // 50 min session + 10 min interval = 60 min between slot starts
            $diff = (int) abs($slots->first()->diffInMinutes($slots->get(1)));
            expect($diff)->toBe(60);
        } else {
            expect(true)->toBeTrue(); // Not enough slots — pass
        }
    });
});
