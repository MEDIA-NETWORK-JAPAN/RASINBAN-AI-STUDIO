@props([
    'name' => '',
    'label' => null,
    'value' => null,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <input
        type="month"
        name="{{ $name }}"
        id="{{ $name }}"
        @if ($value !== null) value="{{ $value }}" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm']) }}
    />
</div>
