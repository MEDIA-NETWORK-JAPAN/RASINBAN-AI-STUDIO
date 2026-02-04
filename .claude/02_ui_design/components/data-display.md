# Data Display Components

データ表示系のコンポーネントです。

## DataTable

データ一覧表示用テーブル。

### 構造

```html
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
  <!-- Header (オプション) -->
  <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
    <h2 class="text-base font-bold text-gray-800">{{ $title }}</h2>
    {{ $headerActions }}
  </div>

  <!-- Table -->
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          {{ $header }}
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        {{ $body }}
      </tbody>
    </table>
  </div>

  <!-- Footer/Pagination (オプション) -->
  {{ $footer }}
</div>
```

### テーブルヘッダーセル

```html
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
  ヘッダー名
</th>
```

### テーブルボディセル

```html
<!-- 標準 -->
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">値</td>

<!-- サブテキスト付き -->
<td class="px-6 py-4 whitespace-nowrap">
  <div class="text-sm font-medium text-gray-900">メイン</div>
  <div class="text-xs text-gray-500">サブ</div>
</td>
```

### 行スタイル

```html
<!-- 標準行 -->
<tr class="hover:bg-gray-50 transition-colors">

<!-- 警告行（超過など） -->
<tr class="bg-red-50 hover:bg-red-100 transition-colors">
```

### 使用モック
- A01, A02, A03, A04, A05, A07

---

## Pagination

ページネーションコンポーネント。

### 構造

```html
<div class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
  <!-- Mobile -->
  <div class="flex-1 flex justify-between sm:hidden">
    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300
                       text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
      Previous
    </a>
    <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300
                       text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
      Next
    </a>
  </div>

  <!-- Desktop -->
  <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
    <div>
      <p class="text-sm text-gray-700">
        全 <span class="font-medium">{{ $total }}</span> 件中
        <span class="font-medium">{{ $from }}</span> - <span class="font-medium">{{ $to }}</span> 件
      </p>
    </div>
    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
      <!-- Previous -->
      <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300
                         bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
        <i class="fas fa-chevron-left text-xs"></i>
      </a>
      <!-- Page Numbers -->
      <a href="#" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600
                         relative inline-flex items-center px-4 py-2 border text-sm font-medium">
        1
      </a>
      <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50
                         relative inline-flex items-center px-4 py-2 border text-sm font-medium">
        2
      </a>
      <!-- Next -->
      <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300
                         bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
        <i class="fas fa-chevron-right text-xs"></i>
      </a>
    </nav>
  </div>
</div>
```

### 使用モック
- A02, A05, A07

---

## KpiCard

KPI表示カード。ダッシュボードで使用。

### 構造

```html
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-sm font-medium text-gray-500">{{ $label }}</h3>
    <div class="p-2 bg-{{ $color }}-50 rounded-lg text-{{ $color }}-600">
      <i class="{{ $icon }}"></i>
    </div>
  </div>
  <div class="flex items-baseline gap-2">
    <span class="text-3xl font-bold text-gray-900">{{ $value }}</span>
    @if($subValue)
      <span class="text-sm text-{{ $subColor }}-600 font-medium">{{ $subValue }}</span>
    @endif
  </div>
  @if($description)
    <p class="text-xs text-gray-400 mt-2">{{ $description }}</p>
  @endif
</div>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| label | string | Yes | カードタイトル |
| value | string | Yes | メイン数値 |
| icon | string | Yes | FontAwesomeアイコン |
| color | string | Yes | indigo/blue/green |
| subValue | string | No | サブ数値（前月比等） |
| subColor | string | No | green/red |
| description | string | No | 補足テキスト |

### 使用モック
- A01_admin_dashboard.html

---

## ProgressBar

使用率表示プログレスバー。

### 構造

```html
<div class="flex items-center gap-2">
  <span class="text-xs font-semibold {{ $percentage > 90 ? 'text-red-600' : 'text-gray-600' }}">
    {{ $percentage }}%
  </span>
  <div class="w-24 bg-gray-100 rounded-full h-1.5">
    <div class="h-1.5 rounded-full {{ $barColor }}" style="width: {{ $percentage }}%"></div>
  </div>
</div>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| percentage | number | Yes | パーセンテージ（0-100） |
| barColor | string | No | バーの色クラス |

### 色ルール

| 状態 | バーの色 |
|------|---------|
| 0-90% | `bg-blue-500` または `bg-indigo-500` |
| 91-100% | `bg-yellow-500` |
| 100%超過 | `bg-red-500` |

### 使用モック
- A01, A04, A07, A08, A09

---

## StatusBadge

ステータス表示バッジ。

### 構造

```html
<!-- Active -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
  <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-green-500"></span>
  Active
</span>

<!-- Inactive -->
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
  <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-gray-400"></span>
  Inactive
</span>
```

### バリエーション

| 状態 | 背景色 | テキスト色 | ドット色 |
|------|--------|----------|---------|
| active | `bg-green-100` | `text-green-800` | `bg-green-500` |
| inactive | `bg-gray-100` | `text-gray-600` | `bg-gray-400` |
| warning | `bg-yellow-100` | `text-yellow-800` | `bg-yellow-500` |
| error | `bg-red-100` | `text-red-800` | `bg-red-500` |

### 使用モック
- A02, A03, A05

---

## EmptyState

データなし時の表示。

### 構造

```html
<tr>
  <td colspan="{{ $colspan }}" class="px-6 py-12 text-center text-gray-500">
    <i class="fas fa-{{ $icon }} mb-2 text-2xl text-gray-300"></i>
    <p>{{ $message }}</p>
  </td>
</tr>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| colspan | number | Yes | カラム数 |
| icon | string | No | アイコン（デフォルト: search） |
| message | string | Yes | メッセージ |

### 使用モック
- A02, A04, A07
