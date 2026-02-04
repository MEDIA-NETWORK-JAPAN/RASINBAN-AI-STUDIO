# Layout Components

ページ全体の構造を定義するレイアウトコンポーネントです。

## AppLayout

アプリケーション全体のベースレイアウト。サイドバーとメインコンテンツエリアを含みます。

### 構造

```html
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ sidebarOpen: false }">
  <!-- Mobile Header -->
  <x-layout.mobile-header />

  <div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <x-layout.sidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">
      <!-- Desktop Header -->
      <x-layout.page-header :title="$title" />

      <!-- Scrollable Content -->
      <main class="flex-1 overflow-y-auto p-4 lg:p-8">
        {{ $slot }}
      </main>
    </div>
  </div>
</body>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| title | string | Yes | ページタイトル |
| description | string | No | タイトル下の説明文 |

---

## Sidebar

左サイドバーナビゲーション。デスクトップでは常時表示、モバイルではスライドイン。

### 構造

```html
<!-- Overlay (mobile) -->
<div x-show="sidebarOpen" @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" x-cloak></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200
              transform transition-transform duration-300 ease-in-out
              lg:static lg:translate-x-0 flex flex-col">

  <!-- Logo Area -->
  <div class="h-16 flex items-center px-6 border-b border-gray-100">
    <div class="flex items-center gap-2 text-indigo-600">
      <i class="fas fa-network-wired text-xl"></i>
      <span class="font-bold text-xl tracking-tight text-gray-900">
        Gateway <span class="text-indigo-600">Admin</span>
      </span>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
    {{ $navigation }}
  </nav>

  <!-- User Profile -->
  <div class="p-4 border-t border-gray-100">
    {{ $userProfile }}
  </div>
</aside>
```

---

## MobileHeader

モバイル用ヘッダー。ハンバーガーメニューとロゴを表示。

```html
<div class="lg:hidden flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3 sticky top-0 z-30">
  <div class="flex items-center gap-3">
    <button @click="sidebarOpen = !sidebarOpen"
            class="text-gray-500 hover:text-gray-700 focus:outline-none">
      <i class="fas fa-bars text-xl"></i>
    </button>
    <span class="font-bold text-lg text-gray-900">Gateway Admin</span>
  </div>
</div>
```

---

## PageHeader

デスクトップ用ページヘッダー。タイトル、説明、アクションボタンを表示。

### 構造

```html
<header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
  <div>
    <h1 class="text-xl font-bold text-gray-800">{{ $title }}</h1>
    @if($description)
      <p class="text-xs text-gray-500 mt-1">{{ $description }}</p>
    @endif
  </div>

  <div class="flex gap-2">
    {{ $actions }}
  </div>
</header>
```

### Props

| Prop | Type | Required | Description |
|------|------|----------|-------------|
| title | string | Yes | ページタイトル |
| description | string | No | 説明文 |

### Slots

| Slot | Description |
|------|-------------|
| actions | 右側のアクションボタン群 |
