@props([
    'title' => '',
    'icon' => null,
])

<div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            {{-- Header --}}
            <div class="bg-white px-6 pt-5 pb-4">
                <div class="flex items-center gap-3 mb-4">
                    @if ($icon)
                        <div class="flex-shrink-0">
                            <i class="fas fa-{{ $icon }} text-indigo-600 text-xl"></i>
                        </div>
                    @endif
                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                        {{ $title }}
                    </h3>
                </div>

                {{-- Content --}}
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @if (isset($footer))
                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
