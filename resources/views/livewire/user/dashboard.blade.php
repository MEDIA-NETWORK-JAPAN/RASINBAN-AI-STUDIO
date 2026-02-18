<div class="space-y-6">
    @if ($usages->isEmpty())
        <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
            <i class="fas fa-chart-bar text-3xl text-gray-300 mb-3"></i>
            <p>今月の利用実績はありません。</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
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
