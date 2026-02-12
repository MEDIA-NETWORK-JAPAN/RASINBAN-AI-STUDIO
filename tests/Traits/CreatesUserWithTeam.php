<?php

namespace Tests\Traits;

use App\Models\Team;
use App\Models\User;

trait CreatesUserWithTeam
{
    /**
     * Create a regular user with a personal team.
     */
    protected function createUserWithTeam(array $userAttributes = [], array $teamAttributes = []): User
    {
        $user = User::factory()->withPersonalTeam()->create($userAttributes);

        if (! empty($teamAttributes) && $user->currentTeam) {
            $user->currentTeam->update($teamAttributes);
        }

        return $user->fresh();
    }

    /**
     * Create a regular user and add them to an existing team.
     */
    protected function createUserForTeam(Team $team, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);

        $team->users()->attach($user, ['role' => 'editor']);
        $user->current_team_id = $team->id;
        $user->save();

        return $user->fresh();
    }
}
