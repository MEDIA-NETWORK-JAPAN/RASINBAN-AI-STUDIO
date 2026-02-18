<div class="space-y-6">
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">アプリ名</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Slug</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">ステータス</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @foreach ($apps as $app)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                            <a href="{{ route('admin.apps.edit', $app) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $app->name }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ $app->slug }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            @if ($app->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">有効</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">無効</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <a href="{{ route('admin.apps.edit', $app) }}" class="text-indigo-600 hover:text-indigo-900">編集</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>
        {{ $apps->links() }}
    </div>
</div>
