<?php

use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

// ── Calendar Feed ──────────────────────────────────────────────────────

it('returns ics content for valid token', function () {
    $user = User::factory()->create(['calendar_token' => 'valid-token-123']);
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->get('/api/calendar/feed/valid-token-123');

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
    expect($response->getContent())->toContain('BEGIN:VCALENDAR');
    expect($response->getContent())->toContain('BEGIN:VEVENT');
});

it('returns 404 for invalid token', function () {
    $this->get('/api/calendar/feed/invalid-token')->assertNotFound();
});

it('contains upcoming sessions in feed', function () {
    $user = User::factory()->create(['calendar_token' => 'feed-token']);
    $patient = Patient::factory()->create([
        'psychologist_id' => $user->id,
        'name' => 'Ana Souza',
    ]);

    Session::factory()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'status' => 'scheduled',
    ]);

    $response = $this->get('/api/calendar/feed/feed-token');

    expect($response->getContent())->toContain('Ana Souza');
});

it('excludes cancelled sessions from feed', function () {
    $user = User::factory()->create(['calendar_token' => 'feed-token-2']);
    $patient = Patient::factory()->create([
        'psychologist_id' => $user->id,
        'name' => 'Cancelled Patient',
    ]);

    Session::factory()->cancelled()->create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
    ]);

    $response = $this->get('/api/calendar/feed/feed-token-2');

    expect($response->getContent())->not->toContain('Cancelled Patient');
});

// ── Calendar Token ─────────────────────────────────────────────────────

it('returns current token', function () {
    $user = User::factory()->create(['calendar_token' => 'my-token']);

    $response = $this->actingAs($user)->getJson('/api/v1/calendar/token');

    $response->assertOk()
        ->assertJsonPath('token', 'my-token');
});

it('returns null when no token exists', function () {
    $user = User::factory()->create(['calendar_token' => null]);

    $response = $this->actingAs($user)->getJson('/api/v1/calendar/token');

    $response->assertOk()
        ->assertJsonPath('token', null)
        ->assertJsonPath('url', null);
});

it('generates a new token', function () {
    $user = User::factory()->create(['calendar_token' => null]);

    $response = $this->actingAs($user)->postJson('/api/v1/calendar/token');

    $response->assertCreated();
    expect($response->json('token'))->toHaveLength(64);
    expect($response->json('url'))->toContain('/api/calendar/feed/');
});

it('regenerates token', function () {
    $user = User::factory()->create(['calendar_token' => 'old-token']);

    $response = $this->actingAs($user)->postJson('/api/v1/calendar/token');

    $response->assertCreated();
    expect($response->json('token'))->not->toBe('old-token');
    expect($response->json('token'))->toHaveLength(64);
});

it('returns 401 for unauthenticated token requests', function () {
    $this->getJson('/api/v1/calendar/token')->assertUnauthorized();
    $this->postJson('/api/v1/calendar/token')->assertUnauthorized();
});
