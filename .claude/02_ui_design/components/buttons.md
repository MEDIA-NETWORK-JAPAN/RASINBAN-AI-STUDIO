# Button Components

ボタン系のコンポーネントです。

## Button

汎用ボタンコンポーネント。

### バリエーション

#### Primary（主要アクション）

```html
<button type="button"
        class="inline-flex items-center px-4 py-2 border border-transparent
               text-sm font-medium rounded-lg shadow-sm text-white
               bg-indigo-600 hover:bg-indigo-700 focus:outline-none
               focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
               disabled:opacity-50 disabled:cursor-not-allowed">
  <i class="fas fa-plus mr-2"></i>
  新規作成
</button>
```

#### Secondary（サブアクション）

```html
<button type="button"
        class="inline-flex items-center px-4 py-2
               border border-gray-300 shadow-sm text-sm font-medium rounded-lg
               text-gray-700 bg-white hover:bg-gray-50 focus:outline-none
               focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
  キャンセル
</button>
```

#### Danger（危険なアクション）

```html
<button type="button"
        class="inline-flex items-center px-4 py-2 border border-transparent
               text-sm font-medium rounded-lg shadow-sm text-white
               bg-red-600 hover:bg-red-700 focus:outline-none
               focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
  <i class="fas fa-trash mr-2"></i>
  削除
</button>
```

#### Ghost（テキストリンク風）

```html
<button type="button"
        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
  すべて見る &rarr;
</button>
```

### Props

| Prop | Type | Required | Default | Description |
|------|------|----------|---------|-------------|
| variant | string | No | primary | primary/secondary/danger/ghost |
| size | string | No | md | sm/md/lg |
| icon | string | No | - | FontAwesomeアイコン |
| iconPosition | string | No | left | left/right |
| disabled | boolean | No | false | 無効状態 |
| loading | boolean | No | false | ローディング状態 |

### サイズバリエーション

| Size | Padding | Text |
|------|---------|------|
| sm | `px-3 py-1.5` | `text-xs` |
| md | `px-4 py-2` | `text-sm` |
| lg | `px-6 py-3` | `text-base` |

### ローディング状態

```html
<button type="button" disabled
        class="inline-flex items-center px-4 py-2 border border-transparent
               text-sm font-medium rounded-lg shadow-sm text-white
               bg-indigo-600 opacity-50 cursor-not-allowed">
  <i class="fas fa-circle-notch fa-spin mr-2"></i>
  保存中...
</button>
```

### 使用モック
- A02: 新規作成ボタン
- A03: 保存、キャンセル、削除ボタン
- A04: インポート開始ボタン
- A05: 新規登録ボタン
- A06: 登録、キャンセル、削除ボタン
- A08: 修正を保存ボタン
- A09: エクスポート、ダウンロードボタン

---

## IconButton

アイコンのみのボタン。テーブル内のアクションなどに使用。

### 構造

```html
<!-- 編集 -->
<button type="button"
        class="text-gray-400 hover:text-indigo-600 transition-colors p-1"
        title="編集">
  <i class="fas fa-edit"></i>
</button>

<!-- 削除 -->
<button type="button"
        class="text-gray-400 hover:text-red-600 transition-colors p-1"
        title="削除">
  <i class="fas fa-trash"></i>
</button>

<!-- コピー -->
<button type="button"
        class="text-gray-400 hover:text-indigo-600 transition-colors p-1"
        title="コピー">
  <i class="fas fa-copy"></i>
</button>

<!-- 表示切替 -->
<button type="button"
        class="text-gray-400 hover:text-gray-600 transition-colors p-1">
  <i class="fas fa-eye"></i>
</button>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| icon | string | Yes | FontAwesomeアイコン |
| title | string | No | ツールチップ |
| color | string | No | ホバー時の色（indigo/red/gray） |

### 使用モック
- A01, A02, A03, A05, A06, A07

---

## ButtonGroup

ボタンをグループ化して配置。

### 構造

```html
<!-- 右寄せ配置 -->
<div class="flex justify-end gap-3">
  <x-ui.button variant="secondary">キャンセル</x-ui.button>
  <x-ui.button variant="primary">保存</x-ui.button>
</div>

<!-- モーダルフッター配置 -->
<div class="flex flex-row-reverse gap-3">
  <x-ui.button variant="primary">保存</x-ui.button>
  <x-ui.button variant="secondary">キャンセル</x-ui.button>
</div>
```

### 使用モック
- A03, A06, A08（モーダルフッター）

---

## PresetButton

プリセット値選択用の小さなボタン。

### 構造

```html
<button type="button"
        @click="applyPreset('value')"
        class="text-xs px-2 py-1 bg-white border border-gray-300 rounded
               hover:bg-gray-50 text-gray-600">
  プリセット名
</button>
```

### 使用モック
- A08_usage_edit_modal.html（修正理由のプリセット）
