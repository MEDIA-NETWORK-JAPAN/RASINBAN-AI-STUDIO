@props([
    'name' => '',
    'label' => null,
    'placeholder' => null,
    'rows' => 3,
    'required' => false,
    'value' => null,
])

<div>
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if ($required)
                <span class="text-red-500 text-xs ml-1">*必須</span>
            @endif
        </label>
    @endif

    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        rows="{{ $rows }}"
        @if ($placeholder !== null) placeholder="{{ $placeholder }}" @endif
        {{ $attributes->merge(['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm']) }}
    >{{ $value }}</textarea>
</div>
