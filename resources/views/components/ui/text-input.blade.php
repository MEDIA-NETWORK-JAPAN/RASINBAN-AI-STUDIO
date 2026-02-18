@props([
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => null,
    'required' => false,
    'hint' => null,
    'name' => null,
])

<div>
    @if ($label)
        <label @if ($name) for="{{ $name }}" @endif class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        @if ($name) name="{{ $name }}" id="{{ $name }}" @endif
        @if ($placeholder !== null) placeholder="{{ $placeholder }}" @endif
        @if ($value !== null) value="{{ $value }}" @endif
        @if ($required) required @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm']) }}
    />

    @if ($hint)
        <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
    @endif
</div>
