@props([
    'items' => [],
])

<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center gap-2">
        @foreach ($items as $index => $item)
            @if ($index > 0)
                <li class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                </li>
            @endif

            <li>
                @if (!empty($item['href']))
                    <a href="{{ $item['href'] }}" class="text-sm text-gray-500 hover:text-gray-700">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-sm text-gray-900 font-medium">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
