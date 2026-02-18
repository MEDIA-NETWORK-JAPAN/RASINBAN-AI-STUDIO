@props([
    'label' => '',
    'value' => '',
    'icon' => null,
    'color' => 'indigo',
    'subValue' => null,
    'subColor' => 'gray',
])

@php
    $colorStyles = match($color) {
        'blue'  => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-600'],
        'green' => ['bg' => 'bg-green-50',  'icon' => 'text-green-600'],
        default => ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-600'],
    };

    $subColorClass = match($subColor) {
        'green' => 'text-green-600',
        'red'   => 'text-red-600',
        default => 'text-gray-500',
    };
@endphp

<div class="bg-white rounded-lg shadow p-5 flex items-start gap-4">
    <div class="p-3 rounded-lg {{ $colorStyles['bg'] }}">
        @if ($icon)
            <i class="fas {{ $icon }} {{ $colorStyles['icon'] }} text-xl"></i>
        @else
            <span class="{{ $colorStyles['icon'] }} text-xl">&#9632;</span>
        @endif
    </div>

    <div class="flex-1">
        <p class="text-sm text-gray-500">{{ $label }}</p>
        <p class="text-3xl font-bold text-gray-900 mt-1">{{ $value }}</p>

        @if ($subValue)
            <p class="text-sm mt-1 {{ $subColorClass }}">{{ $subValue }}</p>
        @endif
    </div>
</div>
