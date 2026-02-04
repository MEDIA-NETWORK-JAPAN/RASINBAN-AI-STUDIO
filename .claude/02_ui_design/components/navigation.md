# Navigation Components

ナビゲーション関連のコンポーネントです。

## NavSection

サイドバー内のナビゲーショングループ。

### 構造

```html
<p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
  {{ $label }}
</p>
{{ $slot }}
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| label | string | Yes | セクションラベル（Main, Management, System） |

---

## NavItem

サイドバー内のナビゲーションリンク。

### 構造

**通常状態**
```html
<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg
          text-gray-600 hover:bg-gray-50 hover:text-gray-900 group transition-colors">
  <i class="{{ $icon }} w-5 text-center text-gray-400 group-hover:text-gray-600"></i>
  {{ $label }}
</a>
```

**アクティブ状態**
```html
<a href="{{ $href }}"
   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg
          bg-indigo-50 text-indigo-700 group">
  <i class="{{ $icon }} w-5 text-center group-hover:text-indigo-600"></i>
  {{ $label }}
</a>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| href | string | Yes | リンク先URL |
| icon | string | Yes | FontAwesomeアイコンクラス |
| label | string | Yes | メニュー名 |
| active | boolean | No | アクティブ状態 |

### ナビゲーション項目一覧

| セクション | アイコン | ラベル | 対象画面 |
|-----------|---------|--------|---------|
| Main | `fa-chart-line` | ダッシュボード | A01 |
| Management | `fa-building` | 拠点・ユーザー管理 | A02, A03, A04 |
| Management | `fa-robot` | Difyアプリ管理 | A05, A06 |
| Management | `fa-file-invoice-dollar` | 利用状況・制限 | A07, A08 |
| System | `fa-server` | 災害復旧 (DR) | A09 |

---

## Breadcrumb

パンくずナビゲーション。詳細・編集画面で使用。

### 構造

```html
<nav class="flex mb-4" aria-label="Breadcrumb">
  <ol class="inline-flex items-center space-x-1 md:space-x-3">
    <li class="inline-flex items-center">
      <a href="{{ $items[0]['href'] }}"
         class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600">
        {{ $items[0]['label'] }}
      </a>
    </li>
    @foreach(array_slice($items, 1) as $item)
    <li>
      <div class="flex items-center">
        <i class="fas fa-chevron-right text-gray-400 text-xs mx-2"></i>
        @if($loop->last)
          <span class="text-sm font-medium text-gray-700">{{ $item['label'] }}</span>
        @else
          <a href="{{ $item['href'] }}"
             class="text-sm font-medium text-gray-500 hover:text-indigo-600">
            {{ $item['label'] }}
          </a>
        @endif
      </div>
    </li>
    @endforeach
  </ol>
</nav>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| items | array | Yes | [{href, label}, ...] の配列 |

### 使用例

```php
<x-navigation.breadcrumb :items="[
  ['href' => route('admin.teams.index'), 'label' => '拠点・ユーザー管理'],
  ['href' => null, 'label' => '東京本社 営業部']
]" />
```

### 使用モック
- A03_team_edit.html
- A06_dify_app_edit.html
