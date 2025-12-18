<?php

namespace Database\Factories;

use App\Models\OpenDoorRegistration;
use App\Models\OpenDoorSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OpenDoorRegistrationFactory extends Factory
{
    protected $model = OpenDoorRegistration::class;

    public function definition(): array
    {
        return [
            'open_door_session_id' => OpenDoorSession::factory(),
            'student_name' => fake()->firstName(),
            'student_surname' => fake()->lastName() . ' ' . fake()->lastName(),
            'student_birthdate' => fake()->dateTimeBetween('-18 years', '-10 years'),
            'current_school' => 'Escola ' . fake()->lastName(),
            'current_grade' => fake()->randomElement(['6è Primària', '1r ESO', '2n ESO', '3r ESO', '4t ESO']),
            'tutor_name' => fake()->firstName(),
            'tutor_surname' => fake()->lastName(),
            'tutor_email' => fake()->unique()->safeEmail(),
            'tutor_phone' => '6' . fake()->numerify('########'),
            'tutor_relationship' => fake()->randomElement(['father', 'mother', 'tutor']),
            'interested_grades' => fake()->randomElements(['eso', 'batxillerat', 'cfgm', 'cfgs'], rand(1, 2)),
            'how_did_you_know' => fake()->randomElement(['web', 'social_media', 'friends', 'school']),
            'comments' => fake()->optional(0.3)->sentence(),
            'status' => 'pending',
            'confirmation_token' => Str::uuid(),
        ];
    }

    /**
     * Configure the factory to not dispatch events (no emails)
     */
    public function configure(): static
    {
        return $this->afterMaking(function (OpenDoorRegistration $registration) {
            // Nothing to do after making
        })->afterCreating(function (OpenDoorRegistration $registration) {
            // Events already dispatched, but we can use withoutEvents in tests
        });
    }

    public function confirmed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function attended(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'attended',
            'confirmed_at' => now()->subDays(7),
            'attended_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function noShow(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'no_show',
            'confirmed_at' => now()->subDays(7),
        ]);
    }
}
