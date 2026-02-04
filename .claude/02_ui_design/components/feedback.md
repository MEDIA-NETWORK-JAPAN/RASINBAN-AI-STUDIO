# Feedback Components

フィードバック・通知系のコンポーネントです。

## AlertBanner

ページ内警告バナー。

### バリエーション

#### Warning（警告）

```html
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-md">
  <div class="flex">
    <div class="flex-shrink-0">
      <i class="fas fa-exclamation-triangle text-yellow-600"></i>
    </div>
    <div class="ml-3">
      <p class="text-sm text-yellow-700 font-bold">{{ $title }}</p>
      <p class="text-sm text-yellow-700 mt-1">{{ $message }}</p>
    </div>
  </div>
</div>
```

#### Error（エラー）

```html
<div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-md">
  <div class="flex">
    <div class="flex-shrink-0">
      <i class="fas fa-exclamation-circle text-red-600"></i>
    </div>
    <div class="ml-3">
      <p class="text-sm text-red-700 font-bold">{{ $title }}</p>
      <p class="text-sm text-red-700 mt-1">{{ $message }}</p>
    </div>
  </div>
</div>
```

#### Info（情報）

```html
<div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-md">
  <div class="flex">
    <div class="flex-shrink-0">
      <i class="fas fa-info-circle text-blue-600"></i>
    </div>
    <div class="ml-3">
      <p class="text-sm text-blue-700 font-bold">{{ $title }}</p>
      <p class="text-sm text-blue-700 mt-1">{{ $message }}</p>
    </div>
  </div>
</div>
```

#### Success（成功）

```html
<div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-r-md">
  <div class="flex">
    <div class="flex-shrink-0">
      <i class="fas fa-check-circle text-green-600"></i>
    </div>
    <div class="ml-3">
      <p class="text-sm text-green-700 font-bold">{{ $title }}</p>
      <p class="text-sm text-green-700 mt-1">{{ $message }}</p>
    </div>
  </div>
</div>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| type | string | No | warning/error/info/success |
| title | string | No | タイトル |
| message | string | Yes | メッセージ |
| dismissible | boolean | No | 閉じるボタン表示 |

### 使用モック
- A03_team_edit.html（Danger Zone警告）
- A09_export.html（取扱注意警告）

---

## Toast

一時的な通知トースト。

### 構造

```html
<div x-data="{ show: false, message: '', type: 'success' }"
     x-show="show"
     x-transition:enter="transform ease-out duration-300 transition"
     x-transition:enter-start="translate-y-2 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-100"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed bottom-4 right-4 z-50">

  <!-- Success -->
  <div x-show="type === 'success'"
       class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
    <i class="fas fa-check-circle"></i>
    <span x-text="message"></span>
  </div>

  <!-- Error -->
  <div x-show="type === 'error'"
       class="bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3">
    <i class="fas fa-exclamation-circle"></i>
    <span x-text="message"></span>
  </div>

</div>
```

### Livewire連携

```php
// コンポーネント内
$this->dispatch('toast', type: 'success', message: '保存しました');

// Blade
<x-feedback.toast />
```

### 使用モック
- A04_csv_import.html（インポート完了通知）

---

## LogConsole

処理ログ表示エリア。

### 構造

```html
<div class="bg-gray-900 rounded-lg p-4 h-48 overflow-y-auto font-mono text-xs">
  <template x-for="log in logs" :key="log.id">
    <div class="flex gap-2 mb-1">
      <span class="text-gray-500" x-text="log.time"></span>
      <span :class="{
        'text-green-400': log.type === 'success',
        'text-red-400': log.type === 'error',
        'text-yellow-400': log.type === 'warning',
        'text-gray-400': log.type === 'info'
      }" x-text="log.message"></span>
    </div>
  </template>
</div>
```

### ログ形式

```javascript
logs: [
  { id: 1, time: '14:30:01', type: 'info', message: 'インポート開始...' },
  { id: 2, time: '14:30:02', type: 'success', message: '行1: 東京本社 営業部 - 登録完了' },
  { id: 3, time: '14:30:03', type: 'error', message: '行2: メールアドレス重複エラー' },
]
```

### 使用モック
- A04_csv_import.html

---

## ProcessingStatus

処理中表示。

### 構造

```html
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
  <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $title }}</h3>
  <p class="text-sm text-gray-500 mb-6">{{ $description }}</p>

  <!-- Progress Bar -->
  <div class="w-full bg-gray-200 rounded-full h-4 mb-2 overflow-hidden">
    <div class="bg-indigo-600 h-4 rounded-full transition-all duration-300 ease-out"
         :style="'width: ' + progress + '%'"></div>
  </div>
  <p class="text-xs text-gray-400 font-mono text-right" x-text="progress + '%'"></p>
</div>
```

### 使用モック
- A04_csv_import.html
- A09_export.html

---

## CompletionStatus

処理完了表示。

### 構造

```html
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <!-- Success Header -->
  <div class="px-6 py-4 border-b border-gray-100 bg-green-50 flex items-center justify-between">
    <h2 class="text-base font-bold text-green-800 flex items-center gap-2">
      <i class="fas fa-check-circle text-green-600"></i>
      {{ $title }}
    </h2>
    <span class="text-xs text-green-700 font-medium">{{ $subInfo }}</span>
  </div>

  <!-- Content -->
  <div class="p-6">
    {{ $slot }}
  </div>
</div>
```

### 使用モック
- A04_csv_import.html（インポート完了）
- A09_export.html（エクスポート完了）

---

## LimitWarning

制限超過警告（インライン表示）。

### 構造

```html
<p x-show="count > limit"
   class="text-xs text-red-600 font-bold mt-1 text-center animate-pulse">
  <i class="fas fa-exclamation-triangle"></i>
  {{ $message }}
</p>
```

### 使用モック
- A08_usage_edit_modal.html
