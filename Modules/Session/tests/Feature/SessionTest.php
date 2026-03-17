<?php

use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

// ── Store ──────────────────────────────────────────────────────────────

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

it('returns 422 when session overlaps with an existing session', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $scheduledAt = now()->addDay()->startOfHour();

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $scheduledAt,
        'ends_at' => $scheduledAt->copy()->addMinutes(50),
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => $patient->id,
        'scheduled_at' => $scheduledAt->copy()->addMinutes(30)->toIso8601String(),
        'duration_minutes' => 50,
        'type' => 'online',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['scheduled_at']);
});

it('allows creating a session adjacent to an existing session (no gap)', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $scheduledAt = now()->addDay()->startOfHour();

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $scheduledAt,
        'ends_at' => $scheduledAt->copy()->addMinutes(50),
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => $patient->id,
        'scheduled_at' => $scheduledAt->copy()->addMinutes(50)->toIso8601String(),
        'duration_minutes' => 50,
        'type' => 'online',
    ]);

    $response->assertCreated();
});

it('ignores cancelled sessions when checking for overlaps', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $scheduledAt = now()->addDay()->startOfHour();

    Session::factory()->cancelled()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $scheduledAt,
        'ends_at' => $scheduledAt->copy()->addMinutes(50),
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => $patient->id,
        'scheduled_at' => $scheduledAt->toIso8601String(),
        'duration_minutes' => 50,
        'type' => 'online',
    ]);

    $response->assertCreated();
});

it('does not check overlap against other psychologists sessions', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $otherPatient = Patient::factory()->create(['psychologist_id' => $otherUser->id]);
    $scheduledAt = now()->addDay()->startOfHour();

    Session::factory()->create([
        'psychologist_id' => $otherUser->id,
        'patient_id' => $otherPatient->id,
        'starts_at' => $scheduledAt,
        'ends_at' => $scheduledAt->copy()->addMinutes(50),
    ]);

    $response = $this->actingAs($user)->postJson('/api/v1/sessions', [
        'patient_id' => $patient->id,
        'scheduled_at' => $scheduledAt->toIso8601String(),
        'duration_minutes' => 50,
        'type' => 'online',
    ]);

    $response->assertCreated();
});

// ── Index ──────────────────────────────────────────────────────────────

it('lists sessions paginated', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    Session::factory()->count(3)->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/sessions');

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure(['data', 'current_page', 'last_page', 'per_page', 'total']);
});

it('filters sessions by date range', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $inRange = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addDays(2),
        'ends_at' => now()->addDays(2)->addMinutes(50),
    ]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addDays(10),
        'ends_at' => now()->addDays(10)->addMinutes(50),
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/sessions?'.http_build_query([
        'from' => now()->addDay()->toDateTimeString(),
        'to' => now()->addDays(5)->toDateTimeString(),
    ]));

    $response->assertOk()->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.id', $inRange->id);
});

it('filters sessions by patient_id', function () {
    $user = User::factory()->create();
    $patient1 = Patient::factory()->create(['psychologist_id' => $user->id]);
    $patient2 = Patient::factory()->create(['psychologist_id' => $user->id]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient1->id,
    ]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient2->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/sessions?patient_id='.$patient1->id);

    $response->assertOk()->assertJsonCount(1, 'data');
});

it('filters sessions by status', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'status' => 'scheduled',
    ]);

    Session::factory()->confirmed()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/sessions?status=confirmed');

    $response->assertOk()->assertJsonCount(1, 'data');
    $response->assertJsonPath('data.0.status', 'confirmed');
});

it('includes patient name in session list', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'psychologist_id' => $user->id,
        'name' => 'Maria Silva',
    ]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/sessions');

    $response->assertOk()
        ->assertJsonPath('data.0.patient.name', 'Maria Silva');
});

it('scopes sessions to the authenticated psychologist', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $otherUser->id]);

    Session::factory()->create([
        'psychologist_id' => $otherUser->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/sessions');

    $response->assertOk()->assertJsonCount(0, 'data');
});

it('returns 401 unauthenticated for index', function () {
    $this->getJson('/api/v1/sessions')->assertUnauthorized();
});

// ── Show ───────────────────────────────────────────────────────────────

it('shows a session with patient data', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'psychologist_id' => $user->id,
        'name' => 'João Santos',
    ]);

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->getJson("/api/v1/sessions/{$session->id}");

    $response->assertOk()
        ->assertJsonPath('session.id', $session->id)
        ->assertJsonPath('session.patient.name', 'João Santos');
});

it('returns 404 for non-existent session', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/sessions/00000000-0000-0000-0000-000000000000');

    $response->assertNotFound()
        ->assertJsonPath('error_code', 'SESSION_NOT_FOUND');
});

it('returns 404 for another psychologist session', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $otherUser->id]);

    $session = Session::factory()->create([
        'psychologist_id' => $otherUser->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->getJson("/api/v1/sessions/{$session->id}");

    $response->assertNotFound();
});

it('returns 401 unauthenticated for show', function () {
    $this->getJson('/api/v1/sessions/some-id')->assertUnauthorized();
});

// ── Update ─────────────────────────────────────────────────────────────

it('partially updates a session (notes)', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'notes' => 'Notas atualizadas.',
    ]);

    $response->assertOk()
        ->assertJsonPath('session.private_notes', 'Notas atualizadas.');
});

it('transitions session status from scheduled to confirmed', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'status' => 'confirmed',
    ]);

    $response->assertOk()
        ->assertJsonPath('session.status', 'confirmed');
});

it('reschedules a session and increments reschedule_count', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $newTime = now()->addDays(3)->startOfHour();

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'reschedule_count' => 0,
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'scheduled_at' => $newTime->toIso8601String(),
        'duration_minutes' => 60,
    ]);

    $response->assertOk();
    $session->refresh();

    expect($session->starts_at->toDateTimeString())->toBe($newTime->toDateTimeString());
    expect($session->ends_at->toDateTimeString())->toBe($newTime->copy()->addMinutes(60)->toDateTimeString());
    expect($session->reschedule_count)->toBe(1);
});

it('returns 422 for invalid status transition', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $session = Session::factory()->completed()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'status' => 'scheduled',
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('error_code', 'INVALID_STATUS_TRANSITION');
});

it('returns 404 when updating another psychologist session', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $otherUser->id]);

    $session = Session::factory()->create([
        'psychologist_id' => $otherUser->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'notes' => 'Hack attempt',
    ]);

    $response->assertNotFound();
});

it('returns 422 when rescheduling to a time that overlaps another session', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $baseTime = now()->addDay()->startOfHour();

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $baseTime,
        'ends_at' => $baseTime->copy()->addMinutes(50),
    ]);

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $baseTime->copy()->addHours(3),
        'ends_at' => $baseTime->copy()->addHours(3)->addMinutes(50),
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'scheduled_at' => $baseTime->copy()->addMinutes(20)->toIso8601String(),
        'duration_minutes' => 50,
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['scheduled_at']);
});

it('allows rescheduling to a non-overlapping time', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $baseTime = now()->addDay()->startOfHour();

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $baseTime,
        'ends_at' => $baseTime->copy()->addMinutes(50),
    ]);

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => $baseTime->copy()->addHours(3),
        'ends_at' => $baseTime->copy()->addHours(3)->addMinutes(50),
    ]);

    $newTime = $baseTime->copy()->addHours(2);

    $response = $this->actingAs($user)->putJson("/api/v1/sessions/{$session->id}", [
        'scheduled_at' => $newTime->toIso8601String(),
        'duration_minutes' => 50,
    ]);

    $response->assertOk();
});

it('returns 401 unauthenticated for update', function () {
    $this->putJson('/api/v1/sessions/some-id', [])->assertUnauthorized();
});

// ── Cancel (DELETE) ────────────────────────────────────────────────────

it('cancels a session via DELETE', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $session = Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'status' => 'scheduled',
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/sessions/{$session->id}");

    $response->assertOk()
        ->assertJsonPath('session.status', 'cancelled');

    $session->refresh();
    expect($session->cancelled_at)->not->toBeNull();
});

it('returns 422 when cancelling an already cancelled session', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $session = Session::factory()->cancelled()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/sessions/{$session->id}");

    $response->assertStatus(422)
        ->assertJsonPath('error_code', 'INVALID_STATUS_TRANSITION');
});

it('returns 404 when cancelling another psychologist session', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $otherUser->id]);

    $session = Session::factory()->create([
        'psychologist_id' => $otherUser->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/sessions/{$session->id}");

    $response->assertNotFound();
});

it('returns 401 unauthenticated for cancel', function () {
    $this->deleteJson('/api/v1/sessions/some-id')->assertUnauthorized();
});
