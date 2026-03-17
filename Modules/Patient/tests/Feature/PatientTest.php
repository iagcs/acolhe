<?php

use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;

// ── List ──

it('paginates patients (15 per page by default)', function () {
    $user = User::factory()->create();
    Patient::factory()->count(20)->create(['psychologist_id' => $user->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/patients');

    $response->assertOk()
        ->assertJsonCount(15, 'data')
        ->assertJsonPath('per_page', 15)
        ->assertJsonPath('total', 20);
});

it('does not list patients from other psychologists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    Patient::factory()->count(2)->create(['psychologist_id' => $user->id]);
    Patient::factory()->count(3)->create(['psychologist_id' => $other->id]);

    $response = $this->actingAs($user)->getJson('/api/v1/patients');

    $response->assertOk()->assertJsonPath('total', 2);
});

it('returns patients ordered by name', function () {
    $user = User::factory()->create();
    Patient::factory()->create(['psychologist_id' => $user->id, 'name' => 'Zélia']);
    Patient::factory()->create(['psychologist_id' => $user->id, 'name' => 'Ana']);

    $response = $this->actingAs($user)->getJson('/api/v1/patients');

    $response->assertOk();
    $names = collect($response->json('data'))->pluck('name')->all();
    expect($names)->toBe(['Ana', 'Zélia']);
});

it('returns empty data when psychologist has no patients', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/patients');

    $response->assertOk()->assertJsonCount(0, 'data');
});

it('searches patients by name', function () {
    $user = User::factory()->create();
    Patient::factory()->create(['psychologist_id' => $user->id, 'name' => 'Maria Silva']);
    Patient::factory()->create(['psychologist_id' => $user->id, 'name' => 'João Santos']);

    $response = $this->actingAs($user)->getJson('/api/v1/patients?search=Maria');

    $response->assertOk()->assertJsonPath('total', 1);
    expect($response->json('data.0.name'))->toBe('Maria Silva');
});

it('filters patients by is_active', function () {
    $user = User::factory()->create();
    Patient::factory()->create(['psychologist_id' => $user->id, 'is_active' => true]);
    Patient::factory()->create(['psychologist_id' => $user->id, 'is_active' => false]);

    $response = $this->actingAs($user)->getJson('/api/v1/patients?is_active=true');

    $response->assertOk()->assertJsonPath('total', 1);
    expect($response->json('data.0.is_active'))->toBeTrue();
});

it('list patients returns 401 unauthenticated', function () {
    $this->getJson('/api/v1/patients')->assertUnauthorized();
});

// ── Store ──

it('creates a patient with all fields and returns 201', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/patients', [
        'name' => 'Maria Silva',
        'email' => 'maria@example.com',
        'phone' => '11988887777',
        'birth_date' => '1990-05-15',
        'notes' => 'Paciente com ansiedade.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('patient.name', 'Maria Silva')
        ->assertJsonPath('patient.email', 'maria@example.com');
});

it('creates a patient with only required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/patients', [
        'name' => 'João Santos',
        'phone' => '11999998888',
    ]);

    $response->assertCreated()->assertJsonPath('patient.name', 'João Santos');
});

it('persists the patient with correct psychologist_id', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/api/v1/patients', [
        'name' => 'Ana Costa',
        'phone' => '11999997777',
    ]);

    $this->assertDatabaseHas('patients', [
        'psychologist_id' => $user->id,
        'name' => 'Ana Costa',
        'is_active' => true,
    ]);
});

it('returns 422 when name is missing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/v1/patients', []);

    $response->assertUnprocessable()->assertJsonValidationErrors(['name']);
});

it('store returns 401 unauthenticated', function () {
    $this->postJson('/api/v1/patients', ['name' => 'Test'])->assertUnauthorized();
});

// ── Show ──

it('shows a patient', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/v1/patients/{$patient->id}");

    $response->assertOk()
        ->assertJsonPath('patient.id', $patient->id)
        ->assertJsonPath('patient.name', $patient->name);
});

it('returns 404 for non-existent patient', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/patients/non-existent-id');

    $response->assertNotFound()
        ->assertJsonPath('error_code', 'PATIENT_NOT_FOUND');
});

it('returns 404 for another psychologist patient', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);

    $response = $this->actingAs($user)->getJson("/api/v1/patients/{$patient->id}");

    $response->assertNotFound();
});

it('show returns 401 unauthenticated', function () {
    $this->getJson('/api/v1/patients/some-id')->assertUnauthorized();
});

// ── Update ──

it('partially updates a patient', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'psychologist_id' => $user->id,
        'name' => 'Old Name',
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/patients/{$patient->id}", [
        'name' => 'New Name',
    ]);

    $response->assertOk()->assertJsonPath('patient.name', 'New Name');
    $this->assertDatabaseHas('patients', ['id' => $patient->id, 'name' => 'New Name']);
});

it('toggles patient is_active', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create([
        'psychologist_id' => $user->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($user)->putJson("/api/v1/patients/{$patient->id}", [
        'is_active' => false,
    ]);

    $response->assertOk()->assertJsonPath('patient.is_active', false);
});

it('returns 422 for invalid update data', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $response = $this->actingAs($user)->putJson("/api/v1/patients/{$patient->id}", [
        'email' => 'not-an-email',
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
});

it('returns 404 when updating another psychologist patient', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);

    $response = $this->actingAs($user)->putJson("/api/v1/patients/{$patient->id}", [
        'name' => 'Hacked',
    ]);

    $response->assertNotFound();
});

it('update returns 401 unauthenticated', function () {
    $this->putJson('/api/v1/patients/some-id', ['name' => 'Test'])->assertUnauthorized();
});

// ── Delete ──

it('soft-deletes a patient', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/patients/{$patient->id}");

    $response->assertNoContent();
    $this->assertSoftDeleted('patients', ['id' => $patient->id]);
});

it('returns 404 when deleting another psychologist patient', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/patients/{$patient->id}");

    $response->assertNotFound();
});

it('delete returns 401 unauthenticated', function () {
    $this->deleteJson('/api/v1/patients/some-id')->assertUnauthorized();
});
