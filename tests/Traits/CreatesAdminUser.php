<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Support\Facades\DB;

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

        $user = User::factory()->admin()->create(array_merge(['id' => 1], $attributes));

        // Reset auto-increment sequence to prevent conflicts when creating subsequent users
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        }

        return $user;
    }
}
