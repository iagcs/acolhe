<?php

use Modules\Agenda\Models\Availability;
use Modules\Auth\Models\User;

// ── GET /api/v1/availabilities ──────────────────────────────────────────────

it('returns 401 when unauthenticated', function () {
    $this->getJson('/api/v1/availabilities')->assertUnauthorized();
});

it('returns the authenticated psychologist availabilities', function () {
    $user = User::factory()->create();

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 1, // Monday
        'start_time' => '09:00',
        'end_time' => '18:00',
        'is_active' => true,
    ]);

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 3, // Wednesday
        'start_time' => '10:00',
        'end_time' => '17:00',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/availabilities');

    $response->assertOk()
        ->assertJsonCount(2, 'availabilities')
        ->assertJsonPath('availabilities.0.day_of_week', 1)
        ->assertJsonPath('availabilities.0.start_time', '09:00')
        ->assertJsonPath('availabilities.0.end_time', '18:00')
        ->assertJsonPath('availabilities.0.is_active', true);
});

it('only returns active availabilities', function () {
    $user = User::factory()->create();

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '18:00',
        'is_active' => true,
    ]);

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 2,
        'start_time' => '09:00',
        'end_time' => '18:00',
        'is_active' => false, // inactive — should be excluded
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/availabilities');

    $response->assertOk()
        ->assertJsonCount(1, 'availabilities')
        ->assertJsonPath('availabilities.0.day_of_week', 1);
});

it('returns empty array when no availabilities configured', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/availabilities');

    $response->assertOk()
        ->assertJsonCount(0, 'availabilities');
});

it('does not return availabilities from other psychologists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    Availability::create([
        'psychologist_id' => $other->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '18:00',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/availabilities');

    $response->assertOk()
        ->assertJsonCount(0, 'availabilities');
});

it('returns availabilities ordered by day of week then start time', function () {
    $user = User::factory()->create();

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 5,
        'start_time' => '09:00',
        'end_time' => '13:00',
        'is_active' => true,
    ]);

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 1,
        'start_time' => '14:00',
        'end_time' => '18:00',
        'is_active' => true,
    ]);

    Availability::create([
        'psychologist_id' => $user->id,
        'day_of_week' => 1,
        'start_time' => '09:00',
        'end_time' => '13:00',
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/availabilities');

    $response->assertOk()
        ->assertJsonCount(3, 'availabilities')
        ->assertJsonPath('availabilities.0.day_of_week', 1)
        ->assertJsonPath('availabilities.0.start_time', '09:00')
        ->assertJsonPath('availabilities.1.day_of_week', 1)
        ->assertJsonPath('availabilities.1.start_time', '14:00')
        ->assertJsonPath('availabilities.2.day_of_week', 5);
});
