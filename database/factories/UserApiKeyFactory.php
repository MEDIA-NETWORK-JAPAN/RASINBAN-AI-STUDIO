<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserApiKey>
 */
class UserApiKeyFactory extends Factory
{
    public function definition(): array
    {
        $plainKey = 'key_'.Str::random(59);

        return [
            'user_id' => User::factory(),
            'name' => 'default',
            'key_hash' => hash('sha256', $plainKey),
            'key_encrypted' => encrypt($plainKey),
            'is_active' => true,
            'last_used_at' => null,
            'expires_at' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_used_at' => now()->subHours(fake()->numberBetween(1, 48)),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
