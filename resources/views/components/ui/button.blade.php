@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'disabled' => false,
    'loading' => false,
    'type' => 'button',
])

@php
    $variantClasses = match($variant) {
        'secondary' => 'border border-gray-300 shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
        'danger'    => 'border border-transparent shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500',
        'ghost'     => 'text-indigo-600 hover:text-indigo-800 font-medium',
        default     => 'border border-transparent shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
    };

    $sizeClasses = match($size) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };

    $disabledClasses = ($disabled || $loading) ? 'opacity-50 cursor-not-allowed' : '';
@endphp

<button
    type="{{ $type }}"
    {{ ($disabled || $loading) ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-lg {$variantClasses} {$sizeClasses} {$disabledClasses}"]) }}
>
    @if ($loading)
        <i class="fas fa-circle-notch fa-spin mr-2"></i>
    @elseif ($icon && $iconPosition === 'left')
        <i class="fas {{ $icon }} mr-2"></i>
    @endif

    {{ $slot }}

    @if (!$loading && $icon && $iconPosition === 'right')
        <i class="fas {{ $icon }} ml-2"></i>
    @endif
</button>
