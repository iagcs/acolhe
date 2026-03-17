<?php

use Modules\Auth\Models\User;

it('logs in with valid credentials and returns a token', function () {
    $user = User::factory()->create([
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure(['token', 'user'])
        ->assertJsonPath('user.email', 'psi@example.com');

    expect($response->json('token'))->toBeString()->not->toBeEmpty();
});

it('returns 422 with invalid credentials', function () {
    User::factory()->create([
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'psi@example.com',
        'password' => 'wrong-password',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when email is missing', function () {
    $response = $this->postJson('/api/v1/login', [
        'password' => 'secret123',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when password is missing', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'psi@example.com',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

it('returns 422 when email format is invalid', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'not-an-email',
        'password' => 'secret123',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('does not expose password in the response', function () {
    User::factory()->create([
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    $response->assertOk();

    expect($response->json('user'))->not->toHaveKey('password');
});

it('creates a personal access token in the database', function () {
    $user = User::factory()->create([
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    $this->postJson('/api/v1/login', [
        'email' => 'psi@example.com',
        'password' => 'secret123',
    ]);

    expect($user->tokens)->toHaveCount(1);
});
