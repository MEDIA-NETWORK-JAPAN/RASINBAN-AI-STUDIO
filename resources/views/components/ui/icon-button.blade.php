@props([
    'icon' => '',
    'title' => null,
    'color' => null,
])

@php
    $hoverClass = match($color) {
        'indigo' => 'hover:text-indigo-600',
        'red'    => 'hover:text-red-600',
        'gray'   => 'hover:text-gray-600',
        default  => 'hover:text-indigo-600',
    };
@endphp

<button
    type="button"
    @if ($title) title="{{ $title }}" @endif
    {{ $attributes->merge(['class' => "text-gray-400 {$hoverClass} transition-colors p-1"]) }}
>
    <i class="fas {{ $icon }}"></i>
</button>
