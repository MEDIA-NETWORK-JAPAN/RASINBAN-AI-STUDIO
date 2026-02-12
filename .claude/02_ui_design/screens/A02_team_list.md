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
- `forms/ToggleSwitch` (制限超過フィルタ)
- `forms/TextInput` (モーダル内)
- `data-display/DataTable`
- `data-display/Pagination`
- `data-display/ProgressBar` (利用率表示)
- `buttons/Button` (primary, secondary)
- `buttons/IconButton` (再生成)
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
| 検索 | TextInput | 拠点名・担当者名の部分一致検索 (debounce 300ms) |
| プランフィルタ | SelectInput | 全て/Light/Standard/Pro |
| 制限超過フィルタ | ToggleSwitch | ONで制限超過拠点のみ表示 |

### 3. 拠点一覧テーブル

| カラム | 内容 | ソート |
|--------|------|--------|
| 拠点名 | `team.name` | o |
| プラン / 利用率 | `team.plan.name` + 今月利用数/上限 + プログレスバー | - |
| 管理者 | オーナー名 + メールアドレス (グレー表示) | - |
| 最終アクセス | `team.last_accessed_at` (相対時刻) | o |

### 4. 新規作成モーダル

フォームフィールド：
| フィールド | タイプ | 必須 | 説明 |
|-----------|--------|------|------|
| 拠点名 | TextInput | Yes | 例: 福岡営業所 |
| 初期契約プラン | SelectInput | Yes | Light/Standard/Pro |
| 拠点接続用APIキー | TextInput (readonly) + ボタン | - | 自動生成、再生成ボタン付き |

## データ取得

```php
// Livewire Component
public $search = '';
public $planFilter = '';
public $overLimitFilter = false;

public function getTeamsProperty()
{
    return Team::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
            ->orWhereHas('owner', fn($q) => $q->where('name', 'like', "%{$this->search}%")))
        ->when($this->planFilter, fn($q) => $q->where('plan_id', $this->planFilter))
        ->when($this->overLimitFilter, fn($q) => $q->whereHas('currentMonthUsage', function($q) {
            $q->whereRaw('total_requests > plan_limits.monthly_limit');
        }))
        ->with(['plan', 'owner', 'currentMonthUsage'])
        ->orderBy('name')
        ->paginate(10);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 検索入力 | リアルタイムフィルタリング (debounce 300ms) |
| フィルタ変更 | 即時フィルタリング |
| 制限超過トグル | ON/OFF切替で即時フィルタリング |
| 新規作成ボタン | モーダル表示 |
| APIキー再生成ボタン | ランダムキー生成 (モーダル内) |
| モーダル保存 | Team作成 + TeamApiKey作成 → 一覧更新 |
| 拠点名クリック | A03へ遷移 |
| ページネーション | ページ切替 |

## Livewire実装

```php
// app/Livewire/Admin/TeamList.php
class TeamList extends Component
{
    use WithPagination;

    public $search = '';
    public $planFilter = '';
    public $overLimitFilter = false;

    // 新規作成モーダル
    public $showCreateModal = false;
    public $newTeam = [
        'name' => '',
        'plan_id' => '',
    ];
    public $newApiKey = '';

    protected $queryString = ['search', 'planFilter', 'overLimitFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateApiKey()
    {
        $this->newApiKey = Str::random(64);
    }

    public function openCreateModal()
    {
        $this->showCreateModal = true;
        $this->generateApiKey(); // 初回自動生成
    }

    public function createTeam()
    {
        $this->validate([
            'newTeam.name' => 'required|string|max:255',
            'newTeam.plan_id' => 'required|exists:plans,id',
            'newApiKey' => 'required|string|size:64',
        ]);

        // Team + TeamApiKey作成処理
        // ...

        $this->showCreateModal = false;
        $this->reset('newTeam', 'newApiKey');
        $this->dispatch('toast', type: 'success', message: '拠点を作成しました');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| 拠点名 | required, string, max:255 |
| プラン | required, exists:plans,id |
| APIキー | required, string, size:64 |
