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

        // ユーザーごとに集約し、利用率降順上位5件を取得
        $topUsers = MonthlyApiUsage::with(['user.plan.planLimits', 'team'])
            ->where('usage_month', $currentMonth)
            ->get()
            ->groupBy('user_id')
            ->map(function ($rows) {
                $user = $rows->first()->user;
                $team = $rows->first()->team;
                $requestCount = $rows->sum('request_count');
                $planLimit = $user?->plan?->planLimits->sum('limit_count') ?? 0;
                $usageRate = $planLimit > 0 ? min((int) round($requestCount / $planLimit * 100), 100) : 0;

                return [
                    'user_name' => $user?->name ?? '-',
                    'team_name' => $team?->name ?? '-',
                    'plan_name' => $user?->plan?->name ?? '-',
                    'request_count' => $requestCount,
                    'usage_rate' => $usageRate,
                    'team_id' => $team?->id,
                ];
            })
            ->sortByDesc('usage_rate')
            ->take(5)
            ->values();

        return view('livewire.admin.dashboard', [
            'teamsCount' => $teamsCount,
            'totalRequests' => $totalRequests,
            'activeAppsCount' => $activeAppsCount,
            'topUsers' => $topUsers,
        ]);
    }
}
