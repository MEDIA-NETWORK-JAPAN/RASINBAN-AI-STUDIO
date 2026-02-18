@props([
    'status' => 'active',
    'label' => '',
])

@php
    $styles = match($status) {
        'inactive' => ['badge' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'],
        'warning'  => ['badge' => 'bg-yellow-100 text-yellow-800', 'dot' => 'bg-yellow-500'],
        'error'    => ['badge' => 'bg-red-100 text-red-800', 'dot' => 'bg-red-500'],
        default    => ['badge' => 'bg-green-100 text-green-800', 'dot' => 'bg-green-500'],
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $styles['badge'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $styles['dot'] }}"></span>
    {{ $label }}
</span>
