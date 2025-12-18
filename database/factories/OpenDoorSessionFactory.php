<?php

namespace Database\Factories;

use App\Models\OpenDoorSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpenDoorSessionFactory extends Factory
{
    protected $model = OpenDoorSession::class;

    public function definition(): array
    {
        return [
            'title' => 'Jornada Portes Obertes - ' . fake()->month(),
            'description' => fake()->paragraph(),
            'session_date' => fake()->dateTimeBetween('+1 week', '+2 months'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'capacity' => fake()->numberBetween(20, 50),
            'registered_count' => 0,
            'status' => 'published',
            'is_active' => true,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'draft',
            'is_active' => false,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'session_date' => fake()->dateTimeBetween('-2 months', '-1 week'),
            'status' => 'completed',
            'is_active' => false,
        ]);
    }

    public function full(): static
    {
        return $this->state(function (array $attributes) {
            $capacity = $attributes['capacity'] ?? 20;
            return [
                'registered_count' => $capacity,
            ];
        });
    }

    public function tomorrow(): static
    {
        return $this->state(fn(array $attributes) => [
            'session_date' => now()->addDay(),
        ]);
    }

    public function inOneWeek(): static
    {
        return $this->state(fn(array $attributes) => [
            'session_date' => now()->addWeek(),
        ]);
    }

    public function yesterday(): static
    {
        return $this->state(fn(array $attributes) => [
            'session_date' => now()->subDay(),
            'status' => 'completed',
        ]);
    }
}
