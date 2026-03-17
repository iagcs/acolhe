<?php

use Modules\Auth\Models\User;

it('registers successfully and returns 201 with token and user', function () {
    $response = $this->postJson('/api/v1/register', validPayload());

    $response
        ->assertCreated()
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.email', 'psi@example.com');

    expect($response->json('token'))->toBeString()->not->toBeEmpty();
});

it('persists the user in the database with correct attributes', function () {
    $this->postJson('/api/v1/register', validPayload());

    $this->assertDatabaseHas('psychologists', [
        'email' => 'psi@example.com',
        'name' => 'Dr. João Silva',
        'crp' => '06/12345',
        'phone' => '11999999999',
        'therapeutic_approach' => 'tcc',
        'session_duration' => 50,
        'session_interval' => 10,
    ]);
});

it('creates availabilities in the database', function () {
    $this->postJson('/api/v1/register', validPayload());

    $user = User::where('email', 'psi@example.com')->first();

    expect($user->availabilities)->toHaveCount(2);
    expect($user->availabilities[0]->day_of_week)->toBe(1);
    expect($user->availabilities[0]->start_time)->toBe('08:00');
    expect($user->availabilities[0]->end_time)->toBe('12:00');
    expect($user->availabilities[0]->is_active)->toBeTrue();
});

it('defaults plan to free with 14-day expiry', function () {
    $this->postJson('/api/v1/register', validPayload());

    $user = User::where('email', 'psi@example.com')->first();

    expect($user->plan)->toBe('free');
    expect($user->plan_expires_at->toDateString())->toBe(now()->addDays(14)->toDateString());
});

it('generates a slug from the name', function () {
    $this->postJson('/api/v1/register', validPayload());

    $user = User::where('email', 'psi@example.com')->first();

    expect($user->slug)->toBe('dr-joao-silva');
});

it('returns 422 for duplicate email', function () {
    User::factory()->create(['email' => 'psi@example.com']);

    $response = $this->postJson('/api/v1/register', validPayload());

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when name is missing', function () {
    $response = $this->postJson('/api/v1/register', validPayload(['name' => null]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['name']);
});

it('returns 422 when email is missing', function () {
    $response = $this->postJson('/api/v1/register', validPayload(['email' => null]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['email']);
});

it('returns 422 when password is missing', function () {
    $response = $this->postJson('/api/v1/register', validPayload(['password' => null, 'password_confirmation' => null]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['password']);
});

it('returns 422 when crp is missing', function () {
    $response = $this->postJson('/api/v1/register', validPayload(['crp' => null]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['crp']);
});

it('returns 422 when phone is missing', function () {
    $response = $this->postJson('/api/v1/register', validPayload(['phone' => null]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['phone']);
});

it('returns 422 when password is less than 8 characters', function () {
    $response = $this->postJson('/api/v1/register', validPayload([
        'password' => 'short',
        'password_confirmation' => 'short',
    ]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['password']);
});

it('returns 422 when password confirmation does not match', function () {
    $response = $this->postJson('/api/v1/register', validPayload([
        'password_confirmation' => 'different-password',
    ]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['password']);
});

it('returns 422 for invalid therapeutic approach', function () {
    $response = $this->postJson('/api/v1/register', validPayload([
        'therapeutic_approach' => 'invalid',
    ]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['therapeutic_approach']);
});

it('returns 422 for invalid day_of_week', function () {
    $response = $this->postJson('/api/v1/register', validPayload([
        'availabilities' => [
            ['day_of_week' => 7, 'start_time' => '08:00', 'end_time' => '12:00'],
        ],
    ]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['availabilities.0.day_of_week']);
});

it('returns 422 when end_time is before start_time', function () {
    $response = $this->postJson('/api/v1/register', validPayload([
        'availabilities' => [
            ['day_of_week' => 1, 'start_time' => '14:00', 'end_time' => '08:00'],
        ],
    ]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['availabilities.0.end_time']);
});

it('returns 422 when availabilities is empty', function () {
    $response = $this->postJson('/api/v1/register', validPayload([
        'availabilities' => [],
    ]));

    $response->assertUnprocessable()->assertJsonValidationErrors(['availabilities']);
});

it('does not expose password in the response', function () {
    $response = $this->postJson('/api/v1/register', validPayload());

    $response->assertCreated();

    expect($response->json('user'))->not->toHaveKey('password');
});

function validPayload(array $overrides = []): array
{
    $base = [
        'name' => 'Dr. João Silva',
        'email' => 'psi@example.com',
        'password' => 'secret1234',
        'password_confirmation' => 'secret1234',
        'crp' => '06/12345',
        'phone' => '11999999999',
        'therapeutic_approach' => 'tcc',
        'session_duration' => 50,
        'session_interval' => 10,
        'session_price' => 200.00,
        'availabilities' => [
            ['day_of_week' => 1, 'start_time' => '08:00', 'end_time' => '12:00'],
            ['day_of_week' => 3, 'start_time' => '14:00', 'end_time' => '18:00'],
        ],
    ];

    return array_replace($base, $overrides);
}
