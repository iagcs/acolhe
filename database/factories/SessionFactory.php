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

    /**
     * Generate default attribute values for a Session model instance.
     *
     * Returned array contains the attributes commonly required to create a Session:
     * - `psychologist_id`: a User factory instance to create or associate a psychologist.
     * - `patient_id`: a Patient factory instance to create or associate a patient.
     * - `starts_at`: a DateTime between 1 and 30 days in the future.
     * - `ends_at`: a DateTime set 50 minutes after `starts_at`.
     * - `status`: defaults to `'scheduled'`.
     * - `type`: defaults to `'online'`.
     * - `price`: defaults to `150.00`.
     *
     * @return array<string,mixed> Associative array of default attribute values for the Session model.
     */
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

    /**
     * Configure the factory to produce sessions with a status of 'confirmed'.
     *
     * @return static The factory instance configured to produce sessions with `status` set to `'confirmed'`.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Mark the generated session as cancelled and set cancellation metadata.
     *
     * Sets the session's `status` to 'cancelled', `cancelled_at` to the current timestamp, and `cancellation_reason` to 'Paciente cancelou.'.
     *
     * @return static The factory instance configured with the cancelled state.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Paciente cancelou.',
        ]);
    }

    /**
     * Configure the factory to produce a session whose status is "completed".
     *
     * @return static The factory instance configured to create a session with status `completed`.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Sets the factory state to generate a session with status 'no_show'.
     *
     * @return static The factory configured to create a session with status `no_show`.
     */
    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'no_show',
        ]);
    }

    /**
     * Set the factory state to create an in-person session.
     *
     * @return static The factory instance with the `type` attribute set to `'in_person'`.
     */
    public function inPerson(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'in_person',
        ]);
    }

    /**
     * Configure the factory to create a session that occurred in the past.
     *
     * Sets `starts_at` to a datetime between 1 and 30 days ago and `ends_at` to 50 minutes after `starts_at`.
     *
     * @return static The factory instance with past `starts_at` and corresponding `ends_at`.
     */
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
