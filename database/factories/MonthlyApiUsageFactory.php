<?php

namespace Database\Factories;

use App\Models\DifyApp;
use App\Models\Team;
use App\Models\User;
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
            'user_id' => User::factory(),
            'team_id' => Team::factory(),
            'dify_app_id' => DifyApp::factory(),
            'usage_month' => now()->format('Y-m'),
            'endpoint' => fake()->randomElement(['/chat-messages', '/completion-messages', '/workflows/run']),
            'request_count' => fake()->numberBetween(0, 10000),
            'last_request_at' => now()->subHours(fake()->numberBetween(1, 720)),
        ];
    }

    /**
     * Bind to a specific user and their primary team to ensure consistency.
     *
     * Precondition: $user must already have a personal team.
     * Use User::factory()->withPersonalTeam()->create() before calling this.
     *
     * Example:
     *   $user = User::factory()->withPersonalTeam()->create();
     *   MonthlyApiUsage::factory()->forUser($user)->create();
     *
     * @throws \RuntimeException if the user has no owned team
     */
    public function forUser(User $user): static
    {
        $team = $user->currentTeam ?? $user->ownedTeams()->first();

        if ($team === null) {
            throw new \RuntimeException(
                'MonthlyApiUsageFactory::forUser() requires a user with a personal team. '
                .'Use User::factory()->withPersonalTeam()->create() first.'
            );
        }

        return $this->state([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);
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
