<?php

namespace App\Livewire\User;

use App\Models\MonthlyApiUsage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.user-layout', ['title' => 'ユーザーダッシュボード'])]
class Dashboard extends Component
{
    public function render(): \Illuminate\View\View
    {
        $user = auth()->user();
        $currentMonth = now()->format('Y-m');

        $usages = MonthlyApiUsage::with(['difyApp'])
            ->where('user_id', $user->id)
            ->where('usage_month', $currentMonth)
            ->get()
            ->map(function ($usage) use ($user) {
                $endpointLimit = $user->plan?->planLimits
                    ->where('endpoint', $usage->endpoint)
                    ->first()?->limit_count ?? 0;

                return [
                    'app_name' => $usage->difyApp?->name ?? '-',
                    'endpoint' => $usage->endpoint,
                    'request_count' => $usage->request_count,
                    'endpoint_limit' => $endpointLimit,
                    'usage_rate' => $endpointLimit > 0
                        ? min(100, round($usage->request_count / $endpointLimit * 100))
                        : 0,
                ];
            });

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
