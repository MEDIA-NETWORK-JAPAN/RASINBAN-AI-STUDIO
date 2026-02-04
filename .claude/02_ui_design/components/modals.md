# Modal Components

モーダルダイアログ系のコンポーネントです。

## Modal

汎用モーダルダイアログ。

### 構造

```html
<!-- Backdrop -->
<div x-show="open"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

  <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75 transition-opacity"
         @click="open = false"></div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

    <!-- Panel -->
    <div class="inline-block align-middle bg-white rounded-xl text-left overflow-hidden
                shadow-2xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full border border-gray-100"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

      <!-- Header -->
      <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-100
                  flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
          <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg">
            <i class="fas fa-{{ $icon }}"></i>
          </span>
          {{ $title }}
        </h3>
        <button @click="open = false" class="text-gray-400 hover:text-gray-500">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="px-6 py-6">
        {{ $slot }}
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-gray-100">
        {{ $footer }}
      </div>

    </div>
  </div>
</div>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| title | string | Yes | モーダルタイトル |
| icon | string | No | ヘッダーアイコン |
| maxWidth | string | No | sm:max-w-lg (デフォルト) / sm:max-w-2xl |

### Slots

| Slot | Description |
|------|-------------|
| default | モーダル本文 |
| footer | フッターボタン群 |

### サイズバリエーション

| Size | Class |
|------|-------|
| small | `sm:max-w-md` |
| default | `sm:max-w-lg` |
| large | `sm:max-w-2xl` |
| xlarge | `sm:max-w-4xl` |

### 使用モック
- A02: 拠点新規作成モーダル
- A03: ユーザー招待モーダル、APIキー再生成確認
- A05: Difyアプリ登録モーダル
- A06: 削除確認モーダル
- A08: 利用回数修正モーダル

---

## ConfirmModal

削除確認など危険な操作用のモーダル。

### 構造

```html
<div x-show="confirmOpen"
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition>

  <div class="flex items-center justify-center min-h-screen px-4">

    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="confirmOpen = false"></div>

    <!-- Panel -->
    <div class="relative bg-white rounded-xl max-w-md w-full shadow-2xl">

      <!-- Icon -->
      <div class="pt-6 pb-4 text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
          <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
        </div>
      </div>

      <!-- Content -->
      <div class="px-6 pb-4 text-center">
        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $title }}</h3>
        <p class="text-sm text-gray-600">{{ $message }}</p>
      </div>

      <!-- Confirmation Input (オプション) -->
      <div class="px-6 pb-4" x-show="requireConfirmation">
        <label class="block text-sm font-medium text-gray-700 mb-2">
          確認のため <code class="bg-gray-100 px-1 rounded">{{ $confirmText }}</code> と入力してください
        </label>
        <input type="text" x-model="confirmInput"
               class="block w-full rounded-md border-gray-300 shadow-sm
                      focus:border-red-500 focus:ring-red-500 sm:text-sm">
      </div>

      <!-- Footer -->
      <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-xl">
        <button type="button"
                @click="executeDelete()"
                :disabled="requireConfirmation && confirmInput !== confirmText"
                class="inline-flex justify-center rounded-lg px-4 py-2
                       bg-red-600 text-white font-medium hover:bg-red-700
                       disabled:opacity-50 disabled:cursor-not-allowed">
          削除する
        </button>
        <button type="button"
                @click="confirmOpen = false"
                class="inline-flex justify-center rounded-lg px-4 py-2
                       bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
          キャンセル
        </button>
      </div>

    </div>
  </div>
</div>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| title | string | Yes | 確認タイトル |
| message | string | Yes | 確認メッセージ |
| confirmText | string | No | 入力確認テキスト（省略時は入力不要） |
| actionLabel | string | No | アクションボタンラベル（デフォルト: 削除する） |

### 使用モック
- A03_team_edit.html（拠点削除）
- A06_dify_app_edit.html（アプリ削除）

---

## ModalTrigger

モーダルを開くトリガーボタン。

### Livewire連携

```html
<!-- トリガーボタン -->
<button type="button"
        wire:click="$dispatch('openModal', { modal: 'create-team' })"
        class="...">
  新規作成
</button>

<!-- モーダルコンポーネント -->
<livewire:modals.create-team />
```

### Alpine.js版

```html
<div x-data="{ open: false }">
  <!-- トリガー -->
  <button @click="open = true">開く</button>

  <!-- モーダル -->
  <template x-if="open">
    <x-ui.modal title="タイトル">
      コンテンツ
    </x-ui.modal>
  </template>
</div>
```
