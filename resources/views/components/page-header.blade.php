@props([
    'title' => '',
    'description' => null,
])

<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-800">{{ $title }}</h1>

        @if ($description)
            <p class="mt-1 text-xs text-gray-500">{{ $description }}</p>
        @endif
    </div>

    @if (isset($actions))
        <div class="flex items-center gap-3">
            {{ $actions }}
        </div>
    @endif
</div>
