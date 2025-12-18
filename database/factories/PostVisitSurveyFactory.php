<?php

namespace Database\Factories;

use App\Models\PostVisitSurvey;
use App\Models\OpenDoorRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostVisitSurveyFactory extends Factory
{
    protected $model = PostVisitSurvey::class;

    public function definition(): array
    {
        return [
            'open_door_registration_id' => OpenDoorRegistration::factory()->attended(),
            'survey_token' => Str::uuid(),
            'status' => 'pending',
            'sent_at' => now(),
            'expires_at' => now()->addDays(14),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'overall_rating' => fake()->numberBetween(3, 5),
            'information_rating' => fake()->numberBetween(3, 5),
            'attention_rating' => fake()->numberBetween(3, 5),
            'facilities_rating' => fake()->numberBetween(3, 5),
            'doubts_resolved' => fake()->boolean(85),
            'liked_most' => fake()->optional(0.7)->sentence(),
            'improvements' => fake()->optional(0.5)->sentence(),
            'enrollment_interest' => fake()->randomElement(['very_high', 'high', 'medium', 'low']),
            'additional_comments' => fake()->optional(0.3)->sentence(),
            'completed_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'expired',
            'sent_at' => now()->subDays(20),
            'expires_at' => now()->subDays(6),
        ]);
    }
}
