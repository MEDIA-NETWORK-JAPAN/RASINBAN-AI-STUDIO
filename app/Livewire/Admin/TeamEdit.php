<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => '拠点編集'])]
class TeamEdit extends Component
{
    public Team $team;

    public function mount(Team $team): void
    {
        $this->team = $team;
    }

    public function render(): \Illuminate\View\View
    {
        $members = $this->team->users()->get();
        $apiKeys = $this->team->users()->with('apiKeys')->get()->flatMap(fn ($user) => $user->apiKeys);

        return view('livewire.admin.team-edit', [
            'team' => $this->team,
            'members' => $members,
            'apiKeys' => $apiKeys,
        ]);
    }
}
