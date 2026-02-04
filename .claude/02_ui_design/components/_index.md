# Components Index

共通コンポーネント一覧です。各コンポーネントの詳細は個別ファイルを参照してください。

## コンポーネント一覧

| カテゴリ | コンポーネント | ファイル |
|---------|---------------|---------|
| **Layout** | AppLayout, Sidebar, MobileHeader, PageHeader | [layout.md](./layout.md) |
| **Navigation** | NavItem, NavSection, Breadcrumb | [navigation.md](./navigation.md) |
| **Data Display** | DataTable, Pagination, KpiCard, ProgressBar, StatusBadge, EmptyState | [data-display.md](./data-display.md) |
| **Forms** | TextInput, SelectInput, ToggleSwitch, Textarea, MonthInput, SearchBar, FileDropzone | [forms.md](./forms.md) |
| **Buttons** | Button, IconButton | [buttons.md](./buttons.md) |
| **Modals** | Modal, ConfirmModal | [modals.md](./modals.md) |
| **Feedback** | AlertBanner, Toast, LogConsole | [feedback.md](./feedback.md) |

## 使用画面マトリクス

| コンポーネント | A01 | A02 | A03 | A04 | A05 | A06 | A07 | A08 | A09 |
|---------------|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| AppLayout     | o | o | o | o | o | o | o | - | o |
| Sidebar       | o | o | o | o | o | o | o | - | o |
| PageHeader    | o | o | o | o | o | o | o | - | o |
| DataTable     | o | o | o | o | o | - | o | - | - |
| Pagination    | - | o | - | - | o | - | o | - | - |
| KpiCard       | o | - | - | - | - | - | - | - | - |
| ProgressBar   | o | - | - | o | - | - | o | o | o |
| StatusBadge   | - | o | o | - | o | - | - | - | - |
| Button        | - | o | o | o | o | o | - | o | o |
| Modal         | - | o | o | - | o | o | - | o | - |
| ConfirmModal  | - | - | o | - | - | o | - | - | - |
| SearchBar     | - | o | - | - | o | - | o | - | - |
| ToggleSwitch  | - | - | o | - | o | o | o | - | - |
| FileDropzone  | - | - | - | o | - | - | - | - | - |
| AlertBanner   | - | - | o | - | - | - | - | - | o |

`o` = 使用, `-` = 未使用

## Blade コンポーネント配置先

実装時は以下のディレクトリに配置します：

```
resources/views/components/
├── layout/
│   ├── app.blade.php
│   ├── sidebar.blade.php
│   └── page-header.blade.php
├── navigation/
│   ├── nav-item.blade.php
│   └── breadcrumb.blade.php
├── ui/
│   ├── button.blade.php
│   ├── badge.blade.php
│   ├── card.blade.php
│   ├── modal.blade.php
│   ├── data-table.blade.php
│   ├── pagination.blade.php
│   ├── progress-bar.blade.php
│   └── empty-state.blade.php
├── forms/
│   ├── input.blade.php
│   ├── select.blade.php
│   ├── toggle.blade.php
│   ├── textarea.blade.php
│   └── dropzone.blade.php
└── feedback/
    ├── alert.blade.php
    └── toast.blade.php
```

## 命名規則

### Blade コンポーネント
- ケバブケース: `data-table.blade.php`
- 使用時: `<x-ui.data-table />`

### Props
- キャメルケース: `$itemsPerPage`
- Blade属性: `items-per-page="10"`

### CSS クラス
- Tailwind ユーティリティクラスを直接使用
- カスタムクラスは最小限に
