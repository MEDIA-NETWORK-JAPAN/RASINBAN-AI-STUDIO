<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeamApiKey>
 */
class TeamApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plainKey = 'key_'.Str::random(40);

        return [
            'team_id' => Team::factory(),
            'key_hash' => hash('sha256', $plainKey),
            'key_encrypted' => $plainKey,
            'is_active' => true,
            'last_used_at' => null,
            'expires_at' => null,
        ];
    }

    /**
     * Indicate that the API key is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the API key has been used.
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_used_at' => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }

    /**
     * Indicate that the API key is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
