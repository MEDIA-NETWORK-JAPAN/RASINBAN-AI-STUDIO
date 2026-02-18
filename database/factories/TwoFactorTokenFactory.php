<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TwoFactorToken>
 */
class TwoFactorTokenFactory extends Factory
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
            'token' => fake()->numerify('######'),
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ];
    }

    /**
     * Indicate that the token is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subMinutes(fake()->numberBetween(1, 60)),
        ]);
    }

    /**
     * Indicate that the token has exceeded max attempts.
     */
    public function maxAttempts(): static
    {
        return $this->state(fn (array $attributes) => [
            'attempts' => 5,
        ]);
    }
}
