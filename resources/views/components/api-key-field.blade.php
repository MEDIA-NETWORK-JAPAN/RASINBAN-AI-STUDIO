@props([
    'value' => '',
    'label' => null,
    'name' => 'api_key',
])

<div x-data="{ showKey: false }">
    @if ($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <div class="flex items-center gap-2">
        <input
            type="password"
            :type="showKey ? 'text' : 'password'"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            readonly
            {{ $attributes->merge(['class' => 'flex-1 rounded-md border-gray-300 shadow-sm text-sm font-mono bg-gray-50']) }}
        />

        {{-- Toggle visibility --}}
        <button
            type="button"
            @click="showKey = !showKey"
            class="text-gray-400 hover:text-gray-600 p-1"
        >
            <i :class="showKey ? 'fas fa-eye-slash' : 'fas fa-eye'" class="fas fa-eye"></i>
        </button>

        {{-- Copy --}}
        <button
            type="button"
            @click="navigator.clipboard.writeText('{{ $value }}')"
            class="text-gray-400 hover:text-gray-600 p-1"
        >
            <i class="fas fa-copy"></i>
        </button>
    </div>
</div>
