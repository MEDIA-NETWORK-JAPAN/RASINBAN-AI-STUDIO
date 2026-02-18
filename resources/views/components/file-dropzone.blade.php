@props([
    'name' => 'file',
    'accept' => '.csv',
])

<div
    x-data="{ isDragging: false, file: null }"
    @dragover.prevent="isDragging = true"
    @dragleave.prevent="isDragging = false"
    @drop.prevent="
        isDragging = false;
        const droppedFile = $event.dataTransfer.files[0];
        if (droppedFile) {
            file = droppedFile;
            const dt = new DataTransfer();
            dt.items.add(droppedFile);
            $refs.fileInput.files = dt.files;
        }
    "
    @click="$refs.fileInput.click()"
    :class="isDragging ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 bg-white hover:border-indigo-400 hover:bg-indigo-50/50'"
    class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors"
>
    <input
        type="file"
        name="{{ $name }}"
        x-ref="fileInput"
        accept="{{ $accept }}"
        class="hidden"
        @change="file = $event.target.files[0]"
    />

    <div x-show="!file">
        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
        <p class="text-sm text-gray-600 mb-1">ファイルをドラッグ＆ドロップ、またはクリックして選択</p>
        <p class="text-xs text-gray-400">CSV (UTF-8), 最大10MB</p>
    </div>

    <div x-show="file" x-cloak>
        <i class="fas fa-file-csv text-3xl text-green-500 mb-2"></i>
        <p class="text-sm text-gray-700 font-medium" x-text="file ? file.name : ''"></p>
        <button
            type="button"
            @click.stop="file = null"
            class="mt-2 text-gray-400 hover:text-red-500"
        >
            <i class="fas fa-times text-sm"></i>
        </button>
    </div>
</div>
