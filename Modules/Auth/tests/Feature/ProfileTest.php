<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;

it('updates bio successfully', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'bio' => 'Psicóloga especializada em TCC.',
    ]);

    $response->assertOk()->assertJsonPath('user.bio', 'Psicóloga especializada em TCC.');

    $user->refresh();
    expect($user->bio)->toBe('Psicóloga especializada em TCC.');
});

it('uploads photo successfully', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');

    $response = $this->actingAs($user)->patch('/api/v1/profile', [
        'photo' => $file,
    ]);

    $response->assertOk();

    $user->refresh();
    expect($user->photo)->not->toBeNull();
    Storage::disk('public')->assertExists($user->photo);
});

it('returns 422 when photo is too large', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg')->size(3000);

    $response = $this->actingAs($user)->patch('/api/v1/profile', [
        'photo' => $file,
    ], ['Accept' => 'application/json']);

    $response->assertUnprocessable()->assertJsonValidationErrors(['photo']);
});

it('returns 422 when bio is too long', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->patchJson('/api/v1/profile', [
        'bio' => str_repeat('a', 1001),
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['bio']);
});

it('returns 401 unauthenticated', function () {
    $this->patchJson('/api/v1/profile', ['bio' => 'test'])->assertUnauthorized();
});
