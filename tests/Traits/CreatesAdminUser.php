<?php

namespace Tests\Traits;

use App\Models\User;

trait CreatesAdminUser
{
    /**
     * Create an admin user.
     */
    protected function createAdminUser(array $attributes = []): User
    {
        return User::factory()->admin()->create($attributes);
    }

    /**
     * Create an admin user with ID=1 (for two-factor auth tests).
     */
    protected function createAdminUserWithIdOne(array $attributes = []): User
    {
        // Delete existing user with ID=1 if exists
        User::where('id', 1)->delete();

        return User::factory()->admin()->create(array_merge(['id' => 1], $attributes));
    }
}
