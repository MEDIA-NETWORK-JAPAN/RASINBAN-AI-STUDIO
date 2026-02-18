@props([
    'label' => null,
    'options' => [],
    'selected' => null,
    'name' => null,
    'placeholder' => null,
])

<div>
    @if ($label)
        <label {{ $name ? "for=\"{$name}\"" : '' }} class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <select
        {{ $name ? "name=\"{$name}\" id=\"{$name}\"" : '' }}
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm']) }}
    >
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $option)
            <option
                value="{{ $option['value'] }}"
                {{ (string) $selected === (string) $option['value'] ? 'selected' : '' }}
            >
                {{ $option['label'] }}
            </option>
        @endforeach
    </select>
</div>
