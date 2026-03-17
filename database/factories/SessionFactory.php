<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition(): array
    {
        $startsAt = fake()->dateTimeBetween('+1 day', '+30 days');
        $endsAt = (clone $startsAt)->modify('+50 minutes');

        return [
            'psychologist_id' => User::factory(),
            'patient_id' => Patient::factory(),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'status' => 'scheduled',
            'type' => 'online',
            'price' => 150.00,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Paciente cancelou.',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'no_show',
        ]);
    }

    public function inPerson(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'in_person',
        ]);
    }

    public function past(): static
    {
        return $this->state(function (array $attributes) {
            $startsAt = fake()->dateTimeBetween('-30 days', '-1 day');
            $endsAt = (clone $startsAt)->modify('+50 minutes');

            return [
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ];
        });
    }
}
