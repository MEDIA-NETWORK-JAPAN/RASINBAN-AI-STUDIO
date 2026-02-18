@props([
    'title' => null,
])

<div class="bg-white rounded-lg shadow overflow-hidden">
    @if ($title || isset($headerActions))
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            @if ($title)
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
            @endif

            @if (isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            @if (isset($header))
                <thead class="bg-gray-50">
                    <tr>
                        {{ $header }}
                    </tr>
                </thead>
            @endif

            <tbody class="bg-white divide-y divide-gray-200">
                {{ $body }}
            </tbody>
        </table>
    </div>

    @if (isset($footer))
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>
