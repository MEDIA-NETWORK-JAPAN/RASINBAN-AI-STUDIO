@props([
    'type' => 'success',
    'message' => '',
])

@php
    $styles = match($type) {
        'error' => ['bg' => 'bg-red-600 text-white', 'icon' => 'fa-exclamation-circle'],
        default => ['bg' => 'bg-green-600 text-white', 'icon' => 'fa-check-circle'],
    };
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, 3000)"
    class="fixed bottom-4 right-4 z-50 flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg {{ $styles['bg'] }}"
>
    <i class="fas {{ $styles['icon'] }}"></i>
    <span class="text-sm font-medium">{{ $message }}</span>
</div>
