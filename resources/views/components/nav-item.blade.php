@props([
    'href' => '#',
    'label' => '',
    'icon' => null,
    'active' => false,
])

<a
    href="{{ $href }}"
    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors
        {{ $active
            ? 'bg-indigo-50 text-indigo-700'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
        }}"
>
    @if ($icon)
        <i class="fas {{ $icon }} w-4 text-center {{ $active ? 'text-indigo-600' : 'text-gray-400' }}"></i>
    @endif
    {{ $label }}
</a>
