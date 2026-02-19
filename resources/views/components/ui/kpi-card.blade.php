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

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        <div class="p-2 rounded-lg {{ $colorStyles['bg'] }}">
            @if ($icon)
                <i class="fas {{ $icon }} {{ $colorStyles['icon'] }} text-base"></i>
            @else
                <span class="{{ $colorStyles['icon'] }} text-base">&#9632;</span>
            @endif
        </div>
    </div>

    <div class="flex items-baseline gap-2">
        <p class="text-3xl font-bold text-gray-900">{{ $value }}</p>
    </div>

    @if ($subValue)
        <p class="text-xs mt-2 {{ $subColorClass }}">{{ $subValue }}</p>
    @endif
</div>
