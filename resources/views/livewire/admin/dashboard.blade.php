<div class="space-y-6">
    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-ui.kpi-card
            label="契約拠点数"
            :value="$teamsCount"
            icon="fa-building"
            color="indigo"
        />
        <x-ui.kpi-card
            label="総リクエスト"
            :value="number_format($totalRequests)"
            icon="fa-exchange-alt"
            color="blue"
        />
        <x-ui.kpi-card
            label="稼働アプリ"
            :value="$activeAppsCount"
            icon="fa-robot"
            color="green"
        />
    </div>

    {{-- 利用率上位ユーザー --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-medium text-gray-900">利用率上位ユーザー（今月）</h3>
        </div>
        @if ($topUsers->isEmpty())
            <div class="px-6 py-8 text-center text-gray-500">
                <i class="fas fa-chart-bar text-3xl text-gray-300 mb-3"></i>
                <p>今月の利用実績はありません。</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ユーザー名</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">拠点名</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">プラン</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">利用回数</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">利用率</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($topUsers as $row)
                        @php
                            $barColor = $row['usage_rate'] >= 90 ? 'bg-red-500' : 'bg-blue-500';
                        @endphp
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['user_name'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['team_name'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $row['plan_name'] }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($row['request_count']) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                                        <div class="{{ $barColor }} h-2 rounded-full" style="width: {{ $row['usage_rate'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-600 w-10 text-right">{{ $row['usage_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if ($row['team_id'])
                                    <a href="{{ route('admin.teams.edit', $row['team_id']) }}" class="text-sm text-indigo-600 hover:text-indigo-900">編集</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-3 border-t border-gray-200 text-right">
                <a href="{{ route('admin.usages') }}" class="text-sm text-indigo-600 hover:text-indigo-900">すべて見る →</a>
            </div>
        @endif
    </div>
</div>
