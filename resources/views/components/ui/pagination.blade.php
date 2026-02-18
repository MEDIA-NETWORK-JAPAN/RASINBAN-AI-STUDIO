@props([
    'total' => 0,
    'from' => 0,
    'to' => 0,
    'currentPage' => 1,
    'totalPages' => 1,
])

<div class="flex items-center justify-between px-4 py-3">
    <div class="text-sm text-gray-600">
        全 {{ $total }} 件中 {{ $from }} - {{ $to }} 件
    </div>

    <div class="flex items-center gap-1">
        {{-- Previous button --}}
        <button
            type="button"
            {{ $currentPage <= 1 ? 'disabled' : '' }}
            class="p-2 rounded-md text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <i class="fas fa-chevron-left text-xs"></i>
        </button>

        {{-- Page number buttons --}}
        @for ($page = 1; $page <= $totalPages; $page++)
            <button
                type="button"
                class="px-3 py-1 rounded-md text-sm font-medium
                    {{ $page === (int) $currentPage
                        ? 'bg-indigo-50 text-indigo-600 font-semibold'
                        : 'text-gray-600 hover:bg-gray-100'
                    }}"
            >
                {{ $page }}
            </button>
        @endfor

        {{-- Next button --}}
        <button
            type="button"
            {{ $currentPage >= $totalPages ? 'disabled' : '' }}
            class="p-2 rounded-md text-gray-500 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <i class="fas fa-chevron-right text-xs"></i>
        </button>
    </div>
</div>
