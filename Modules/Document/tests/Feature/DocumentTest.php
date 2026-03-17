<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;
use Modules\Document\Models\Document;
use Modules\Patient\Models\Patient;

// ── Auth ──

it('returns 401 unauthenticated on index', function () {
    $this->getJson('/api/v1/documents?patient_id='.fake()->uuid())->assertUnauthorized();
});

it('returns 401 unauthenticated on store', function () {
    $this->postJson('/api/v1/documents', [])->assertUnauthorized();
});

it('returns 401 unauthenticated on show', function () {
    $this->getJson('/api/v1/documents/'.fake()->uuid())->assertUnauthorized();
});

it('returns 401 unauthenticated on destroy', function () {
    $this->deleteJson('/api/v1/documents/'.fake()->uuid())->assertUnauthorized();
});

// ── Index ──

it('lists documents for a patient', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    Document::factory()->count(3)->forPatient($patient)->create();

    $response = $this->actingAs($user)->getJson("/api/v1/documents?patient_id={$patient->id}");

    $response->assertOk()
        ->assertJsonPath('total', 3)
        ->assertJsonCount(3, 'documents');
});

it('returns documents with correct fields', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    Document::factory()->forPatient($patient)->create(['name' => 'Atestado.pdf', 'category' => 'Atestado']);

    $response = $this->actingAs($user)->getJson("/api/v1/documents?patient_id={$patient->id}");

    $response->assertOk();
    $doc = $response->json('documents.0');
    expect($doc)->toHaveKeys(['id', 'patient_id', 'name', 'type', 'category', 'size_bytes', 'created_at', 'download_url']);
    expect($doc['name'])->toBe('Atestado.pdf');
    expect($doc['category'])->toBe('Atestado');
});

it('returns empty documents for patient with no documents', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);

    $response = $this->actingAs($user)->getJson("/api/v1/documents?patient_id={$patient->id}");

    $response->assertOk()
        ->assertJsonPath('total', 0)
        ->assertJsonCount(0, 'documents');
});

it('cannot list documents for another psychologist patient', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);
    Document::factory()->forPatient($patient)->create();

    $response = $this->actingAs($user)->getJson("/api/v1/documents?patient_id={$patient->id}");

    $response->assertNotFound();
});

it('requires patient_id on index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/v1/documents');

    $response->assertUnprocessable()->assertJsonValidationErrors(['patient_id']);
});

it('returns documents ordered by newest first', function () {
    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $first = Document::factory()->forPatient($patient)->create(['created_at' => now()->subDays(2)]);
    $second = Document::factory()->forPatient($patient)->create(['created_at' => now()->subDay()]);
    $third = Document::factory()->forPatient($patient)->create(['created_at' => now()]);

    $response = $this->actingAs($user)->getJson("/api/v1/documents?patient_id={$patient->id}");

    $response->assertOk();
    $ids = collect($response->json('documents'))->pluck('id')->all();
    expect($ids[0])->toBe($third->id);
    expect($ids[2])->toBe($first->id);
});

// ── Store ──

it('uploads a pdf and returns 201', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->create('atestado.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
        'name' => 'Atestado Médico',
        'category' => 'Atestado',
    ]);

    $response->assertCreated()
        ->assertJsonPath('document.name', 'Atestado Médico')
        ->assertJsonPath('document.type', 'pdf')
        ->assertJsonPath('document.category', 'Atestado');

    $this->assertDatabaseHas('documents', [
        'patient_id' => $patient->id,
        'psychologist_id' => $user->id,
        'name' => 'Atestado Médico',
        'type' => 'pdf',
    ]);
});

it('uploads an image and detects type correctly', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->image('foto.jpg');

    $response = $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
    ]);

    $response->assertCreated()
        ->assertJsonPath('document.type', 'image');
});

it('defaults name to original filename when name is omitted', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->create('original-name.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
    ]);

    $response->assertCreated()
        ->assertJsonPath('document.name', 'original-name.pdf');
});

it('stores file on local private disk', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

    $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
    ]);

    $doc = Document::where('patient_id', $patient->id)->first();
    Storage::disk('local')->assertExists($doc->path);
});

it('rejects invalid mime type', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->create('video.mp4', 100, 'video/mp4');

    $response = $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['file']);
});

it('rejects files larger than 20 MB', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    // 21 MB
    $file = UploadedFile::fake()->create('big.pdf', 21 * 1024, 'application/pdf');

    $response = $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['file']);
});

it('cannot upload document to another psychologist patient', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);
    $file = UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf');

    $response = $this->actingAs($user)->postJson('/api/v1/documents', [
        'patient_id' => $patient->id,
        'file' => $file,
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors(['patient_id']);
});

// ── Show (Download) ──

it('downloads a document', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
    $path = Storage::disk('local')->putFile("documents/{$user->id}/{$patient->id}", $file);

    $document = Document::factory()->forPatient($patient)->create([
        'path' => $path,
        'name' => 'report.pdf',
    ]);

    $response = $this->actingAs($user)->get("/api/v1/documents/{$document->id}");

    $response->assertOk();
    $response->assertHeader('Content-Disposition');
});

it('cannot download another psychologist document', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);
    $document = Document::factory()->forPatient($patient)->create();

    $response = $this->actingAs($user)->get("/api/v1/documents/{$document->id}");

    $response->assertNotFound();
});

// ── Destroy ──

it('soft-deletes a document and removes from disk', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $user->id]);
    $file = UploadedFile::fake()->create('to-delete.pdf', 100, 'application/pdf');
    $path = Storage::disk('local')->putFile("documents/{$user->id}/{$patient->id}", $file);

    $document = Document::factory()->forPatient($patient)->create(['path' => $path]);

    $response = $this->actingAs($user)->deleteJson("/api/v1/documents/{$document->id}");

    $response->assertOk()->assertJsonPath('document.id', $document->id);

    $this->assertSoftDeleted('documents', ['id' => $document->id]);
    Storage::disk('local')->assertMissing($path);
});

it('cannot delete another psychologist document', function () {
    Storage::fake('local');

    $user = User::factory()->create();
    $other = User::factory()->create();
    $patient = Patient::factory()->create(['psychologist_id' => $other->id]);
    $document = Document::factory()->forPatient($patient)->create();

    $response = $this->actingAs($user)->deleteJson("/api/v1/documents/{$document->id}");

    $response->assertNotFound();
    $this->assertDatabaseHas('documents', ['id' => $document->id, 'deleted_at' => null]);
});
