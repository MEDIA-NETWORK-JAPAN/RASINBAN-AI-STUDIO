# A02: 拠点一覧

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A02 |
| 画面名 | 拠点・ユーザー管理（一覧） |
| URL | `/admin/teams` |
| モック | [mocks/A02_team_list.html](../mocks/A02_team_list.html) |

## 概要

登録されている拠点（Team）の一覧表示、検索、新規作成を行う画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader` (アクションボタン付き)
- `forms/SearchBar`
- `forms/SelectInput`
- `data-display/DataTable`
- `data-display/Pagination`
- `data-display/StatusBadge`
- `buttons/Button` (primary)
- `modals/Modal` (新規作成)

## 画面構成

### 1. ヘッダーエリア

- タイトル: 「拠点・ユーザー管理」
- アクションボタン:
  - CSV一括登録 → A04へ遷移
  - 新規作成 → モーダル表示

### 2. 検索・フィルタバー

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| 拠点名検索 | TextInput | 部分一致検索 |
| プラン | SelectInput | 全て/Light/Standard/Pro |
| ステータス | SelectInput | 全て/Active/Inactive |

### 3. 拠点一覧テーブル

| カラム | 内容 | ソート |
|--------|------|--------|
| 拠点名 | `team.name` + オーナー名 | o |
| プラン | `team.plan.name` | o |
| ユーザー数 | `team.users.count()` | o |
| 今月利用 | 利用数/上限 + プログレスバー | o |
| ステータス | Active/Inactive バッジ | - |
| 操作 | 編集ボタン | - |

### 4. 新規作成モーダル

フォームフィールド：
| フィールド | タイプ | 必須 |
|-----------|--------|------|
| 拠点名 | TextInput | Yes |
| プラン | SelectInput | Yes |
| オーナーメール | TextInput (email) | Yes |
| オーナー名 | TextInput | Yes |

## データ取得

```php
// Livewire Component
public $search = '';
public $planFilter = '';
public $statusFilter = '';

public function getTeamsProperty()
{
    return Team::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->when($this->planFilter, fn($q) => $q->where('plan_id', $this->planFilter))
        ->when($this->statusFilter !== '', fn($q) => $q->where('is_active', $this->statusFilter))
        ->withCount('users')
        ->with(['plan', 'owner'])
        ->withCurrentMonthUsage()
        ->orderBy('name')
        ->paginate(10);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 検索入力 | リアルタイムフィルタリング (debounce 300ms) |
| フィルタ変更 | 即時フィルタリング |
| 新規作成ボタン | モーダル表示 |
| モーダル保存 | Team作成 + オーナーUser作成 → 一覧更新 |
| 編集ボタン | A03へ遷移 |
| ページネーション | ページ切替 |

## Livewire実装

```php
// app/Livewire/Admin/TeamList.php
class TeamList extends Component
{
    use WithPagination;

    public $search = '';
    public $planFilter = '';
    public $statusFilter = '';

    // 新規作成モーダル
    public $showCreateModal = false;
    public $newTeam = [
        'name' => '',
        'plan_id' => '',
        'owner_email' => '',
        'owner_name' => '',
    ];

    protected $queryString = ['search', 'planFilter', 'statusFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createTeam()
    {
        $this->validate([
            'newTeam.name' => 'required|string|max:255',
            'newTeam.plan_id' => 'required|exists:plans,id',
            'newTeam.owner_email' => 'required|email|unique:users,email',
            'newTeam.owner_name' => 'required|string|max:255',
        ]);

        // Team + Owner作成処理
        // ...

        $this->showCreateModal = false;
        $this->dispatch('toast', type: 'success', message: '拠点を作成しました');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| 拠点名 | required, string, max:255 |
| プラン | required, exists:plans,id |
| オーナーメール | required, email, unique:users,email |
| オーナー名 | required, string, max:255 |
