# Form Components

フォーム入力系のコンポーネントです。

## TextInput

テキスト入力フィールド。

### 構造

```html
<div>
  <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
  <input type="{{ $type }}"
         name="{{ $name }}"
         value="{{ $value }}"
         placeholder="{{ $placeholder }}"
         class="block w-full rounded-md border-gray-300 shadow-sm
                focus:border-indigo-500 focus:ring-indigo-500
                sm:text-sm py-2 px-3"
         {{ $required ? 'required' : '' }}>
  @if($hint)
    <p class="text-xs text-gray-400 mt-1">{{ $hint }}</p>
  @endif
</div>
```

### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| label | string | Yes | - | ラベル |
| name | string | Yes | - | name属性 |
| type | string | No | text | input type |
| value | string | No | - | 初期値 |
| placeholder | string | No | - | プレースホルダー |
| required | boolean | No | false | 必須 |
| hint | string | No | - | ヒントテキスト |

### 使用モック
- A02, A03, A04, A06, A07

---

## SelectInput

セレクトボックス。

### 構造

```html
<div>
  <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
  <select name="{{ $name }}"
          class="block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500
                 sm:text-sm py-2 px-3 bg-white">
    @foreach($options as $option)
      <option value="{{ $option['value'] }}" {{ $option['value'] === $selected ? 'selected' : '' }}>
        {{ $option['label'] }}
      </option>
    @endforeach
  </select>
</div>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| label | string | Yes | ラベル |
| name | string | Yes | name属性 |
| options | array | Yes | [{value, label}, ...] |
| selected | string | No | 選択値 |

### 使用モック
- A02, A03, A07

---

## ToggleSwitch

ON/OFF切り替えスイッチ。

### 構造

```html
<label class="relative inline-flex items-center cursor-pointer">
  <input type="checkbox" name="{{ $name }}" class="sr-only peer" {{ $checked ? 'checked' : '' }}>
  <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4
              peer-focus:ring-indigo-300 rounded-full peer
              peer-checked:after:translate-x-full peer-checked:after:border-white
              after:content-[''] after:absolute after:top-[2px] after:left-[2px]
              after:bg-white after:border-gray-300 after:border after:rounded-full
              after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
  </div>
  @if($label)
    <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
  @endif
</label>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| name | string | Yes | name属性 |
| checked | boolean | No | ON状態 |
| label | string | No | ラベル |

### Alpine.js版（リアクティブ）

```html
<label class="flex items-center cursor-pointer">
  <div class="relative">
    <input type="checkbox" class="sr-only" x-model="isActive">
    <div class="block bg-gray-300 w-11 h-6 rounded-full"
         :class="{ 'bg-indigo-600': isActive }"></div>
    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"
         :class="{ 'transform translate-x-5': isActive }"></div>
  </div>
  <span class="ml-3 text-sm font-medium text-gray-700" x-text="isActive ? 'Active' : 'Inactive'"></span>
</label>
```

### 使用モック
- A03, A05, A06, A07

---

## Textarea

複数行テキスト入力。

### 構造

```html
<div>
  <label class="block text-sm font-bold text-gray-700 mb-2">
    {{ $label }}
    @if($required)
      <span class="text-red-500 text-xs font-normal">*必須</span>
    @endif
  </label>
  <textarea name="{{ $name }}"
            rows="{{ $rows }}"
            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500
                   block w-full sm:text-sm border border-gray-300 rounded-md py-2 px-3"
            placeholder="{{ $placeholder }}">{{ $value }}</textarea>
</div>
```

### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| label | string | Yes | - | ラベル |
| name | string | Yes | - | name属性 |
| rows | number | No | 3 | 行数 |
| placeholder | string | No | - | プレースホルダー |
| required | boolean | No | false | 必須 |

### 使用モック
- A08_usage_edit_modal.html

---

## MonthInput

年月選択フィールド。

### 構造

```html
<div>
  <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
  <input type="month"
         name="{{ $name }}"
         value="{{ $value }}"
         class="block w-full rounded-md border-gray-300 shadow-sm
                focus:border-indigo-500 focus:ring-indigo-500
                sm:text-sm border py-2 px-3">
</div>
```

### 使用モック
- A07_usage_list.html

---

## SearchBar

検索バー。フィルタ機能付きの検索入力。

### 構造

```html
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
    <!-- Search Input -->
    <div class="col-span-1 md:col-span-2">
      <label class="block text-xs font-medium text-gray-500 mb-1">検索</label>
      <div class="relative">
        <input type="text"
               placeholder="{{ $placeholder }}"
               class="block w-full pl-10 rounded-md border-gray-300 shadow-sm
                      focus:border-indigo-500 focus:ring-indigo-500
                      sm:text-sm py-2 px-3">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <i class="fas fa-search text-gray-400"></i>
        </div>
      </div>
    </div>

    <!-- Filter Select -->
    <div class="col-span-1">
      {{ $filters }}
    </div>

    <!-- Action Button -->
    <div class="col-span-1 flex justify-end">
      {{ $actions }}
    </div>
  </div>
</div>
```

### 使用モック
- A02, A05, A07

---

## FileDropzone

ファイルドラッグ＆ドロップエリア。

### 構造

```html
<div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center
            hover:border-indigo-400 hover:bg-indigo-50/50 transition-colors cursor-pointer"
     @dragover.prevent="isDragging = true"
     @dragleave.prevent="isDragging = false"
     @drop.prevent="handleDrop($event)"
     :class="{ 'border-indigo-500 bg-indigo-50': isDragging }">

  <input type="file" class="hidden" accept=".csv" @change="handleFile($event)">

  <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-4"></i>
  <p class="text-sm font-medium text-gray-700">
    ファイルをドラッグ＆ドロップ
  </p>
  <p class="text-xs text-gray-500 mt-1">
    または<span class="text-indigo-600 hover:underline cursor-pointer">クリックして選択</span>
  </p>
  <p class="text-xs text-gray-400 mt-2">
    対応形式: CSV (UTF-8), 最大10MB
  </p>
</div>
```

### ファイル選択後の表示

```html
<div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
  <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
    <i class="fas fa-file-csv text-green-600 text-xl"></i>
  </div>
  <div class="flex-1 min-w-0">
    <p class="text-sm font-medium text-gray-900 truncate">{{ $fileName }}</p>
    <p class="text-xs text-gray-500">{{ $fileSize }} - {{ $rowCount }}行</p>
  </div>
  <button type="button" class="text-gray-400 hover:text-red-500">
    <i class="fas fa-times"></i>
  </button>
</div>
```

### 使用モック
- A04_csv_import.html

---

## ApiKeyField

APIキー表示・コピー用フィールド。

### 構造

```html
<div>
  <label class="block text-xs font-medium text-gray-500 mb-1">{{ $label }}</label>
  <div class="flex items-center gap-2">
    <input type="{{ showKey ? 'text' : 'password' }}"
           :value="apiKey"
           readonly
           class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm
                  sm:text-sm py-2 px-3 font-mono text-gray-600">
    <button type="button"
            @click="showKey = !showKey"
            class="p-2 text-gray-400 hover:text-gray-600">
      <i class="fas" :class="showKey ? 'fa-eye-slash' : 'fa-eye'"></i>
    </button>
    <button type="button"
            @click="copyToClipboard()"
            class="p-2 text-gray-400 hover:text-indigo-600">
      <i class="fas fa-copy"></i>
    </button>
  </div>
</div>
```

### 使用モック
- A03_team_edit.html
- A06_dify_app_edit.html
