<div class="space-y-6">
    {{-- ユーザーサマリーカード --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ユーザー情報 --}}
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-3">アカウント情報</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">ユーザー名</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">拠点名</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->currentTeam?->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">プラン</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $user->plan?->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">APIキー状態</dt>
                        <dd class="text-sm font-medium">
                            @if ($user->apiKeys->where('is_active', true)->isNotEmpty())
                                <span class="text-green-600">有効</span>
                            @else
                                <span class="text-gray-400">未設定</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- 今月の利用状況 --}}
            <div>
                <h3 class="text-sm font-medium text-gray-500 mb-3">今月の利用状況</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">総利用回数</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($totalRequests) }} 回</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">プラン上限</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            {{ $planLimit > 0 ? number_format($planLimit) . ' 回' : '上限なし' }}
                        </dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>利用率</span>
                        <span>{{ $usageRate }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div
                            class="{{ $usageRate >= 90 ? 'bg-red-500' : 'bg-blue-500' }} h-3 rounded-full transition-all"
                            style="width: {{ $usageRate }}%"
                        ></div>
                    </div>
                    @if ($usageRate >= 90)
                        <p class="mt-1 text-xs text-red-600">利用率が上限に近づいています。</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- エンドポイント別利用実績 --}}
    @if ($usages->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <i class="fas fa-chart-bar text-3xl text-gray-300 mb-3"></i>
            <p>今月の利用実績はありません。</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-base font-medium text-gray-900">エンドポイント別利用実績</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">エンドポイント</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">リクエスト数</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($usages as $usage)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $usage->endpoint }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($usage->request_count) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
