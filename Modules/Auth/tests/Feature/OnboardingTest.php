<?php

use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

it('returns correct defaults for a new user', function () {
    $user = User::factory()->create(['photo' => null, 'bio' => null]);

    $response = $this->actingAs($user)->getJson('/api/v1/onboarding/status');

    $response
        ->assertOk()
        ->assertJson([
            'has_photo' => false,
            'has_bio' => false,
            'patient_count' => 0,
            'session_count' => 0,
            'dismissed' => false,
        ]);
});

it('returns has_photo true when photo is set', function () {
    $user = User::factory()->create(['photo' => 'photos/test.jpg']);

    $response = $this->actingAs($user)->getJson('/api/v1/onboarding/status');

    $response->assertOk()->assertJsonPath('has_photo', true);
});

it('returns has_bio true when bio is set', function () {
    $user = User::factory()->create(['bio' => 'Psicóloga especializada em TCC.']);

    $response = $this->actingAs($user)->getJson('/api/v1/onboarding/status');

    $response->assertOk()->assertJsonPath('has_bio', true);
});

it('returns correct patient_count and session_count', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    Session::create([
        'psychologist_id' => $user->id,
        'patient_id' => $patient->id,
        'starts_at' => now()->addDay(),
        'ends_at' => now()->addDay()->addMinutes(50),
        'status' => 'scheduled',
        'type' => 'online',
        'price' => $user->session_price,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/onboarding/status');

    $response->assertOk()
        ->assertJsonPath('patient_count', 1)
        ->assertJsonPath('session_count', 1);
});

it('returns dismissed true after dismissal', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patchJson('/api/v1/onboarding/dismiss');

    $response = $this->actingAs($user)->getJson('/api/v1/onboarding/status');

    $response->assertOk()->assertJsonPath('dismissed', true);
});

it('dismiss is idempotent', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->patchJson('/api/v1/onboarding/dismiss')->assertOk();
    $this->actingAs($user)->patchJson('/api/v1/onboarding/dismiss')->assertOk();

    $response = $this->actingAs($user)->getJson('/api/v1/onboarding/status');

    $response->assertOk()->assertJsonPath('dismissed', true);
});

it('dismiss preserves other settings', function () {
    $user = User::factory()->create([
        'settings' => ['reminder_24h' => true, 'auto_confirm' => false],
    ]);

    $this->actingAs($user)->patchJson('/api/v1/onboarding/dismiss');

    $user->refresh();
    expect($user->settings['reminder_24h'])->toBeTrue();
    expect($user->settings['auto_confirm'])->toBeFalse();
    expect($user->settings['onboarding_dismissed'])->toBeTrue();
});

it('onboarding status returns 401 unauthenticated', function () {
    $this->getJson('/api/v1/onboarding/status')->assertUnauthorized();
});

it('onboarding dismiss returns 401 unauthenticated', function () {
    $this->patchJson('/api/v1/onboarding/dismiss')->assertUnauthorized();
});
