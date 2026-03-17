<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'psychologist_id' => User::factory(),
            'name' => fake('pt_BR')->name(),
            'phone' => fake('pt_BR')->cellphoneNumber(),
            'email' => fake('pt_BR')->unique()->safeEmail(),
            'birth_date' => fake()->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d'),
            'notes' => null,
            'is_active' => true,
            'risk_score' => 0,
            'risk_level' => 'low',
            'session_price_override' => null,
            'billing_enabled' => true,
            'ai_screening_status' => 'none',
            'metadata' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function highRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_score' => fake()->numberBetween(70, 100),
            'risk_level' => 'high',
        ]);
    }

    public function mediumRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_score' => fake()->numberBetween(30, 69),
            'risk_level' => 'medium',
        ]);
    }

    public function withScreening(): static
    {
        return $this->state(fn (array $attributes) => [
            'ai_screening_status' => 'completed',
            'ai_screening_summary' => 'Paciente relata ansiedade moderada, com histórico de episódios de pânico. Recomenda-se avaliação aprofundada.',
        ]);
    }

    public function withCustomPrice(float $price): static
    {
        return $this->state(fn (array $attributes) => [
            'session_price_override' => $price,
        ]);
    }
}
