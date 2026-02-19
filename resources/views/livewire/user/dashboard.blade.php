<div class="space-y-6">

    {{-- ユーザー情報サマリー --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="h-16 w-16 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0 mx-auto sm:mx-0">
                <span class="text-white text-2xl font-bold">{{ mb_substr($user->name, 0, 1) }}</span>
            </div>
            <div class="flex-1 text-center sm:text-left">
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <div class="flex flex-wrap gap-2 mt-2 justify-center sm:justify-start">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        <i class="fas fa-building mr-1 text-gray-400"></i>
                        {{ $user->currentTeam?->name ?? '-' }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $user->plan?->name ?? 'プランなし' }}
                    </span>
                    @if ($user->apiKeys->where('is_active', true)->isNotEmpty())
                        <x-ui.status-badge status="active" label="APIキー: 有効" />
                    @else
                        <x-ui.status-badge status="inactive" label="APIキー: 未設定" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- KPIカード --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- 今月の総利用回数 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">今月の総利用回数</h3>
                <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                    <i class="fas fa-exchange-alt"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold text-gray-900">{{ number_format($totalRequests) }}</span>
                <span class="text-sm text-gray-400">回</span>
            </div>
            <p class="text-xs text-gray-400 mt-2">全アプリ合計</p>
        </div>

        {{-- プラン月間上限 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">プラン月間上限</h3>
                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                @if ($planLimit > 0)
                    <span class="text-3xl font-bold text-gray-900">{{ number_format($planLimit) }}</span>
                    <span class="text-sm text-gray-400">回/月</span>
                @else
                    <span class="text-3xl font-bold text-gray-900">-</span>
                    <span class="text-sm text-gray-400">上限なし</span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $user->plan?->name ?? '-' }}</p>
        </div>

        {{-- 今月の利用率 --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-500">今月の利用率</h3>
                <div class="p-2 rounded-lg {{ $usageRate > 90 ? 'bg-red-50 text-red-600' : ($usageRate > 70 ? 'bg-yellow-50 text-yellow-600' : 'bg-green-50 text-green-600') }}">
                    <i class="fas fa-percent"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-bold {{ $usageRate > 90 ? 'text-red-600' : ($usageRate > 70 ? 'text-yellow-600' : 'text-gray-900') }}">
                    {{ $usageRate }}%
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1.5 mt-3">
                <div class="h-1.5 rounded-full transition-all duration-500 {{ $usageRate > 90 ? 'bg-red-500' : ($usageRate > 70 ? 'bg-yellow-500' : 'bg-blue-500') }}"
                     style="width: {{ $usageRate }}%"></div>
            </div>
        </div>
    </div>

    {{-- アプリ別利用状況テーブル --}}
    <x-ui.data-table title="アプリ別利用状況">
        <x-slot name="header">
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difyアプリ / エンドポイント</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">利用状況 (実績 / 上限)</th>
        </x-slot>

        <x-slot name="body">
            @forelse ($usages as $usage)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">{{ $usage['app_name'] }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $usage['endpoint'] }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs">
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-sm font-bold {{ $usage['usage_rate'] >= 100 ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($usage['request_count']) }} 回
                                </span>
                                <span class="text-xs text-gray-400">
                                    / {{ $usage['endpoint_limit'] > 0 ? number_format($usage['endpoint_limit']) : '∞' }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $usage['usage_rate'] >= 90 ? 'bg-red-500' : 'bg-blue-500' }}"
                                     style="width: {{ $usage['usage_rate'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1 text-right">{{ $usage['usage_rate'] }}%</p>
                        </div>
                    </td>
                </tr>
            @empty
                <x-empty-state colspan="2" icon="inbox" message="今月の利用データはありません。" />
            @endforelse
        </x-slot>

        <x-slot name="footer">
            <p class="text-xs text-gray-400">
                <i class="fas fa-info-circle mr-1"></i>
                利用状況は数分遅延する場合があります。上限超過時はAPIリクエストが拒否されます。
            </p>
        </x-slot>
    </x-ui.data-table>

</div>
