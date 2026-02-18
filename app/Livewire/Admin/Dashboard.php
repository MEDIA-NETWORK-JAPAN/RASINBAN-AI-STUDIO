<?php

namespace App\Livewire\Admin;

use App\Models\DifyApp;
use App\Models\MonthlyApiUsage;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => 'ダッシュボード'])]
class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        $currentMonth = now()->format('Y-m');

        $teamsCount = Team::where('personal_team', false)->count();
        $totalRequests = MonthlyApiUsage::where('usage_month', $currentMonth)->sum('request_count');
        $activeAppsCount = DifyApp::where('is_active', true)->count();

        return view('livewire.admin.dashboard', [
            'teamsCount' => $teamsCount,
            'totalRequests' => $totalRequests,
            'activeAppsCount' => $activeAppsCount,
        ]);
    }
}
