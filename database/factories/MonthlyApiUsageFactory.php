<?php

namespace Database\Factories;

use App\Models\DifyApp;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MonthlyApiUsage>
 */
class MonthlyApiUsageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'dify_app_id' => DifyApp::factory(),
            'usage_month' => now()->format('Y-m'),
            'endpoint' => fake()->randomElement(['/chat-messages', '/completion-messages', '/workflows/run']),
            'request_count' => fake()->numberBetween(0, 10000),
            'last_request_at' => now()->subHours(fake()->numberBetween(1, 720)),
        ];
    }

    /**
     * Indicate usage for a specific month.
     */
    public function forMonth(string $month): static
    {
        return $this->state(fn (array $attributes) => [
            'usage_month' => $month,
        ]);
    }

    /**
     * Indicate that no requests have been made.
     */
    public function unused(): static
    {
        return $this->state(fn (array $attributes) => [
            'request_count' => 0,
            'last_request_at' => null,
        ]);
    }
}
