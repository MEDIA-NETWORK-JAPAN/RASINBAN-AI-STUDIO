<?php

namespace App\Livewire\User;

use App\Models\MonthlyApiUsage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.admin-layout', ['title' => 'ユーザーダッシュボード'])]
class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');

        $usages = MonthlyApiUsage::where('user_id', $user->id)
            ->where('usage_month', $currentMonth)
            ->get();

        $totalRequests = $usages->sum('request_count');
        $planLimit = $user->plan?->planLimits->sum('limit_count') ?? 0;
        $usageRate = $planLimit > 0 ? min(round($totalRequests / $planLimit * 100), 100) : 0;

        return view('livewire.user.dashboard', [
            'usages' => $usages,
            'user' => $user,
            'totalRequests' => $totalRequests,
            'planLimit' => $planLimit,
            'usageRate' => $usageRate,
        ]);
    }
}
