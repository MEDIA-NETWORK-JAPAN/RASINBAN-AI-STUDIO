@props([
    'colspan' => 1,
    'message' => '',
    'icon' => null,
])

<tr>
    <td colspan="{{ $colspan }}" class="px-6 py-12 text-center">
        <div class="flex flex-col items-center gap-3">
            <i class="fas fa-{{ $icon ?? 'search' }} text-2xl text-gray-300"></i>
            <p class="text-sm text-gray-500">{{ $message }}</p>
        </div>
    </td>
</tr>
