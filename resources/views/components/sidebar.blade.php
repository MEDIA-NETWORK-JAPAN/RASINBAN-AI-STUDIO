@props([])

<div
    class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-lg transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
>
    {{-- Logo area --}}
    <div class="flex items-center gap-2 px-6 py-4 border-b border-gray-100">
        <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
            <i class="fas fa-network-wired text-white text-sm"></i>
        </div>
        <span class="text-sm font-semibold text-gray-900">Gateway Admin</span>
    </div>

    {{-- Navigation --}}
    <nav class="px-3 py-4 space-y-1">
        @if (isset($navigation))
            {{ $navigation }}
        @endif
    </nav>
</div>
