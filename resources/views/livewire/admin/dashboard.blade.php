<?php

use App\Models\DifyApp;
use App\Models\MonthlyApiUsage;
use App\Models\Team;
use Livewire\Attributes\Layout;

new #[Layout('components.admin-layout', ['title' => 'ダッシュボード'])]
class extends \Livewire\Volt\Component
{
    public function with(): array
    {
        $currentMonth = now()->format('Y-m');

        $teamsCount = Team::where('personal_team', false)->count();
        $totalRequests = MonthlyApiUsage::where('usage_month', $currentMonth)->sum('request_count');
        $activeAppsCount = DifyApp::where('is_active', true)->count();

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

        return [
            'teamsCount' => $teamsCount,
            'totalRequests' => $totalRequests,
            'activeAppsCount' => $activeAppsCount,
            'topUsers' => $topUsers,
        ];
    }
}
?>

<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.kpi-card
            label="契約拠点数"
            :value="$teamsCount"
            icon="fa-building"
            color="indigo"
            sub-value="前月比"
        />
        <x-ui.kpi-card
            label="今月の総リクエスト"
            :value="number_format($totalRequests)"
            icon="fa-exchange-alt"
            color="blue"
            sub-value="システム全体"
        />
        <x-ui.kpi-card
            label="稼働アプリ数"
            :value="$activeAppsCount"
            icon="fa-robot"
            color="green"
            sub-value="全システム正常稼働中"
        />
    </div>

    {{-- 利用率上位ユーザー --}}
    <x-ui.data-table title="利用率上位ユーザー（今月）">
        <x-slot name="headerActions">
            <a href="{{ route('admin.usages') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">すべて見る →</a>
        </x-slot>

        <x-slot name="header">
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ユーザー名</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">拠点名</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">プラン</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">利用率</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
        </x-slot>

        <x-slot name="body">
            @if ($topUsers->isEmpty())
                <x-empty-state colspan="5" icon="chart-bar" message="今月の利用実績はありません。" />
            @else
                @foreach ($topUsers as $row)
                    @php
                        $barColor = $row['usage_rate'] >= 90 ? 'bg-red-500' : 'bg-blue-500';
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $row['user_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $row['team_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $row['plan_name'] }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold {{ $row['usage_rate'] >= 90 ? 'text-red-600' : 'text-gray-700' }}">{{ $row['usage_rate'] }}%</span>
                                <div class="w-24 bg-gray-100 rounded-full h-1.5">
                                    <div class="{{ $barColor }} h-1.5 rounded-full" style="width: {{ $row['usage_rate'] }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if ($row['team_id'])
                                <x-ui.icon-button
                                    icon="fa-edit"
                                    color="indigo"
                                    title="編集"
                                    onclick="window.location.href='{{ route('admin.teams.edit', $row['team_id']) }}'"
                                />
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endif
        </x-slot>
    </x-ui.data-table>
</div>
