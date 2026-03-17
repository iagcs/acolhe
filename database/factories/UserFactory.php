<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Auth\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $name = fake('pt_BR')->name();
        $slug = Str::slug($name).'-'.Str::random(4);

        return [
            'name' => $name,
            'email' => fake('pt_BR')->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake('pt_BR')->cellphoneNumber(),
            'crp' => sprintf('%02d/%05d', fake()->numberBetween(1, 27), fake()->numberBetween(10000, 99999)),
            'timezone' => 'America/Sao_Paulo',
            'session_duration' => fake()->randomElement([50, 60]),
            'session_interval' => fake()->randomElement([10, 15]),
            'session_price' => fake()->randomFloat(2, 150, 400),
            'therapeutic_approach' => fake()->randomElement(['tcc', 'psychoanalysis', 'humanistic', 'systemic', 'gestalt']),
            'plan' => 'professional',
            'plan_expires_at' => now()->addYear(),
            'slug' => $slug,
            'remember_token' => Str::random(10),
            'settings' => [
                'reminder_24h' => true,
                'reminder_2h' => true,
                'auto_confirm' => false,
                'whatsapp_notifications' => true,
            ],
            'ai_settings' => [
                'approach' => 'tcc',
                'enabled' => true,
                'max_questions' => 5,
            ],
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function freePlan(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => 'free',
            'plan_expires_at' => null,
        ]);
    }

    public function soloPlan(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => 'solo',
            'plan_expires_at' => now()->addYear(),
        ]);
    }

    public function clinicPlan(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => 'clinic',
            'plan_expires_at' => now()->addYear(),
        ]);
    }
}
