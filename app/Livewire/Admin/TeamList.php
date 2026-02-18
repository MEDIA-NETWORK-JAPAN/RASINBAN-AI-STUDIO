<?php

namespace App\Livewire\Admin;

use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.admin-layout', ['title' => '拠点一覧'])]
class TeamList extends Component
{
    use WithPagination;

    public function render(): \Illuminate\View\View
    {
        $teams = Team::paginate(50);

        return view('livewire.admin.team-list', [
            'teams' => $teams,
        ]);
    }
}
