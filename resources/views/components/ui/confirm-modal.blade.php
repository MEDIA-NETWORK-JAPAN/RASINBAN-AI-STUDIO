@props([
    'title' => '',
    'message' => '',
    'confirmText' => null,
    'actionLabel' => '削除',
    'cancelLabel' => 'キャンセル',
])

<div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 pt-5 pb-4">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ $message }}</p>

                        @if ($confirmText)
                            <div class="mt-4">
                                <p class="text-sm text-gray-600 mb-1">
                                    確認のため <strong>{{ $confirmText }}</strong> と入力してください
                                </p>
                                <input
                                    type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="{{ $confirmText }}"
                                />
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700">
                    {{ $actionLabel }}
                </button>
                <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                    {{ $cancelLabel }}
                </button>
            </div>
        </div>
    </div>
</div>
