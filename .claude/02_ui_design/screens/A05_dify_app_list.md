# A05: Difyアプリ一覧

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A05 |
| 画面名 | Difyアプリ管理（一覧） |
| URL | `/admin/apps` |
| モック | [mocks/A05_dify_app_list.html](../mocks/A05_dify_app_list.html) |

## 概要

登録されているDifyアプリの一覧表示、ステータス切替、新規登録を行う画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `forms/SearchBar`
- `forms/ToggleSwitch`
- `data-display/DataTable`
- `data-display/Pagination`
- `data-display/StatusBadge`
- `buttons/Button`
- `buttons/IconButton`
- `modals/Modal`

## 画面構成

### 1. ヘッダーエリア

- タイトル: 「Difyアプリ管理」
- アクションボタン: 新規登録

### 2. 検索バー

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| アプリ名/Slug検索 | TextInput | 部分一致 (debounce 300ms) |

### 3. アプリ一覧テーブル

| カラム | 内容 | 説明 |
|--------|------|------|
| アプリ名 | `name` + アイコン | クリックでモーダル表示 |
| スラッグ | `slug` | URLパス部分（monospace表示） |
| 接続設定 | APIキー設定状態 | 緑チェックマーク表示 |
| ステータス | ToggleSwitch | Active/Inactive切替 |
| 最終更新 | `updated_at` | 相対時刻表示 |

注記: アプリ名クリックで詳細編集モーダルを開く（編集列は削除）

### 4. 新規登録モーダル

| フィールド | タイプ | 必須 | 説明 |
|-----------|--------|------|------|
| アプリ名 | TextInput | Yes | 表示名 |
| Slug | TextInput | Yes | URLパス（英数字、ハイフン） |
| Dify APIキー | TextInput | Yes | Bearer token |
| Difyエンドポイント | TextInput | Yes | Dify APIのURL |

## データ取得

```php
// Livewire Component
public $search = '';

public function getAppsProperty()
{
    return DifyApp::query()
        ->when($this->search, fn($q) => $q->where(function($q) {
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('slug', 'like', "%{$this->search}%");
        }))
        ->orderBy('name')
        ->paginate(10);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 検索入力 | リアルタイムフィルタリング (debounce 300ms) |
| ステータストグル | 即時更新（確認なし） |
| 新規登録ボタン | モーダル表示 |
| モーダル保存 | DifyApp作成 → 一覧更新 |
| アプリ名クリック | 詳細編集モーダル表示 |

## Livewire実装

```php
// app/Livewire/Admin/DifyAppList.php
class DifyAppList extends Component
{
    use WithPagination;

    public $search = '';

    public $showCreateModal = false;
    public $newApp = [
        'name' => '',
        'slug' => '',
        'api_key' => '',
        'endpoint_url' => '',
    ];

    public function toggleStatus(DifyApp $app)
    {
        $app->update(['is_active' => !$app->is_active]);
        $this->dispatch('toast',
            type: 'success',
            message: $app->is_active ? 'アプリを有効化しました' : 'アプリを無効化しました'
        );
    }

    public function createApp()
    {
        $this->validate([
            'newApp.name' => 'required|string|max:255',
            'newApp.slug' => 'required|string|max:100|regex:/^[a-z0-9-]+$/|unique:dify_apps,slug',
            'newApp.api_key' => 'required|string',
            'newApp.endpoint_url' => 'required|url',
        ]);

        DifyApp::create([
            'name' => $this->newApp['name'],
            'slug' => $this->newApp['slug'],
            'api_key_encrypted' => encrypt($this->newApp['api_key']),
            'endpoint_url' => $this->newApp['endpoint_url'],
            'is_active' => true,
        ]);

        $this->reset('newApp');
        $this->showCreateModal = false;
        $this->dispatch('toast', type: 'success', message: 'アプリを登録しました');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| アプリ名 | required, string, max:255 |
| Slug | required, string, max:100, regex:/^[a-z0-9-]+$/, unique:dify_apps,slug |
| APIキー | required, string |
| エンドポイントURL | required, url |
