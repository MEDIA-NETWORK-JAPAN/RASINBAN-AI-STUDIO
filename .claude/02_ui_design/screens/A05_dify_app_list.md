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

### 2. 検索・フィルタバー

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| アプリ名/Slug検索 | TextInput | 部分一致 |
| ステータス | SelectInput | 全て/Active/Inactive |

### 3. アプリ一覧テーブル

| カラム | 内容 | 説明 |
|--------|------|------|
| アプリ名 | `name` + アイコン | アプリ識別名 |
| Slug | `slug` | URLパス部分（monospace表示） |
| エンドポイントタイプ | `endpoint_type` | chat/completion/workflow |
| ステータス | ToggleSwitch | Active/Inactive切替 |
| 操作 | 編集ボタン | A06へ遷移 |

### 4. 新規登録モーダル

| フィールド | タイプ | 必須 | 説明 |
|-----------|--------|------|------|
| アプリ名 | TextInput | Yes | 表示名 |
| Slug | TextInput | Yes | URLパス（英数字、ハイフン） |
| Dify APIキー | TextInput | Yes | Bearer token |
| Difyエンドポイント | TextInput | Yes | Dify APIのURL |
| タイプ | SelectInput | Yes | chat/completion/workflow |

## データ取得

```php
// Livewire Component
public $search = '';
public $statusFilter = '';

public function getAppsProperty()
{
    return DifyApp::query()
        ->when($this->search, fn($q) => $q->where(function($q) {
            $q->where('name', 'like', "%{$this->search}%")
              ->orWhere('slug', 'like', "%{$this->search}%");
        }))
        ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter))
        ->orderBy('name')
        ->paginate(10);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 検索入力 | リアルタイムフィルタリング |
| ステータストグル | 即時更新（確認なし） |
| 新規登録ボタン | モーダル表示 |
| モーダル保存 | DifyApp作成 → 一覧更新 |
| 編集ボタン | A06へ遷移 |

## Livewire実装

```php
// app/Livewire/Admin/DifyAppList.php
class DifyAppList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';

    public $showCreateModal = false;
    public $newApp = [
        'name' => '',
        'slug' => '',
        'api_key' => '',
        'endpoint_url' => '',
        'endpoint_type' => 'chat',
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
            'newApp.endpoint_type' => 'required|in:chat,completion,workflow',
        ]);

        DifyApp::create([
            'name' => $this->newApp['name'],
            'slug' => $this->newApp['slug'],
            'api_key_encrypted' => encrypt($this->newApp['api_key']),
            'endpoint_url' => $this->newApp['endpoint_url'],
            'endpoint_type' => $this->newApp['endpoint_type'],
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
| タイプ | required, in:chat,completion,workflow |
