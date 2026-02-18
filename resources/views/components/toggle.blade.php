@props([
    'name' => '',
    'checked' => false,
    'label' => null,
])

@if ($label)
    <label class="inline-flex items-center gap-3 cursor-pointer">
        <div class="relative">
            <input
                type="checkbox"
                name="{{ $name }}"
                class="sr-only peer"
                {{ $checked ? 'checked' : '' }}
            />
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
        </div>
        <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
    </label>
@else
    <div class="relative inline-flex cursor-pointer">
        <input
            type="checkbox"
            name="{{ $name }}"
            class="sr-only peer"
            {{ $checked ? 'checked' : '' }}
        />
        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
    </div>
@endif
