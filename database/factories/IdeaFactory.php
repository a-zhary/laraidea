<?php

namespace Database\Factories;

use App\IdeaStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Idea>
 */
class IdeaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'links' => collect(range(1, rand(1, 5)))
                ->map(fn () => fake()->url())
                ->values()
                ->all(),
            'status' => fake()->randomElement(IdeaStatus::values()),
        ];
    }
}
