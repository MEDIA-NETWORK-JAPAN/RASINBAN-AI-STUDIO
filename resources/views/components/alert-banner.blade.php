@props([
    'type' => 'info',
    'title' => null,
    'message' => '',
    'dismissible' => false,
])

@php
    $styles = match($type) {
        'warning' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-400', 'text' => 'text-yellow-800', 'icon' => 'fa-exclamation-triangle'],
        'error'   => ['bg' => 'bg-red-50',    'border' => 'border-red-400',    'text' => 'text-red-800',    'icon' => 'fa-exclamation-circle'],
        'success' => ['bg' => 'bg-green-50',  'border' => 'border-green-400',  'text' => 'text-green-800',  'icon' => 'fa-check-circle'],
        default   => ['bg' => 'bg-blue-50',   'border' => 'border-blue-400',   'text' => 'text-blue-800',   'icon' => 'fa-info-circle'],
    };
@endphp

<div
    x-data="{ visible: true }"
    x-show="visible"
    class="border-l-4 p-4 {{ $styles['bg'] }} {{ $styles['border'] }}"
>
    <div class="flex items-start gap-3">
        <i class="fas {{ $styles['icon'] }} {{ $styles['text'] }} mt-0.5"></i>

        <div class="flex-1">
            @if ($title)
                <p class="font-bold {{ $styles['text'] }}">{{ $title }}</p>
            @endif
            <p class="text-sm {{ $styles['text'] }}">{{ $message }}</p>
        </div>

        @if ($dismissible)
            <button
                type="button"
                @click="visible = false"
                class="{{ $styles['text'] }} hover:opacity-75"
            >
                <i class="fas fa-times text-sm"></i>
            </button>
        @endif
    </div>
</div>
