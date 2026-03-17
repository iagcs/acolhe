<?php

use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;

it('creates a session and returns 201', function () {
    $user = User::factory()->create(['session_price' => 200.00]);
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => $patient->id,
        'scheduled_at' => now()->addDay()->toIso8601String(),
        'duration_minutes' => 50,
        'type' => 'online',
        'notes' => 'Primeira sessão.',
    ]);

    $response->assertCreated()->assertJsonPath('session.status', 'scheduled');
});

it('creates a session with correct starts_at, ends_at, price, and status', function () {
    $user = User::factory()->create(['session_price' => 250.00]);
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $scheduledAt = now()->addDay()->startOfHour();

    $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => $patient->id,
        'scheduled_at' => $scheduledAt->toIso8601String(),
        'duration_minutes' => 60,
        'type' => 'in_person',
    ]);

    $this->assertDatabaseHas('sessions', [
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $scheduledAt->toDateTimeString(),
        'ends_at' => $scheduledAt->copy()->addMinutes(60)->toDateTimeString(),
        'price' => '250.00',
        'status' => 'scheduled',
        'type' => 'in_person',
    ]);
});

it('returns 422 when required fields are missing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', []);

    $response->assertUnprocessable()->assertJsonValidationErrors(['patient_id', 'scheduled_at', 'type']);
});

it('returns 422 for invalid patient_id', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => '00000000-0000-0000-0000-000000000000',
        'scheduled_at' => now()->addDay()->toIso8601String(),
        'type' => 'online',
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['patient_id']);
});

it('returns 401 unauthenticated', function () {
    $this->postJson('/api/v1/sessions', [])->assertUnauthorized();
});
