<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Models\User;
use Modules\Document\Models\Document;
use Modules\Patient\Models\Patient;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        $psychologist = User::factory()->create();
        $patient = Patient::factory()->create(['psychologist_id' => $psychologist->id]);

        return [
            'patient_id' => $patient->id,
            'psychologist_id' => $psychologist->id,
            'name' => fake()->words(3, true) . '.pdf',
            'path' => 'documents/' . fake()->uuid() . '/test.pdf',
            'type' => 'pdf',
            'category' => fake()->randomElement(['Atestado', 'Recibo', 'Laudo', 'Encaminhamento', null]),
            'size_bytes' => fake()->numberBetween(10000, 5000000),
            'uploaded_by' => 'psychologist',
        ];
    }

    public function forPatient(Patient $patient): static
    {
        return $this->state(fn (array $attributes) => [
            'patient_id' => $patient->id,
            'psychologist_id' => $patient->psychologist_id,
        ]);
    }

    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'foto.jpg',
            'path' => 'documents/' . fake()->uuid() . '/foto.jpg',
            'type' => 'image',
        ]);
    }
}
