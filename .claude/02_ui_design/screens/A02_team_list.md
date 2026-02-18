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
| 検索 | TextInput | 拠点名の部分一致検索 (debounce 300ms) |
| 制限超過フィルタ | ToggleSwitch | ONで制限超過拠点のみ表示 |

### 3. 拠点一覧テーブル

| カラム | 内容 | ソート |
|--------|------|--------|
| 拠点名 | `team.name` | o |
| 管理者 | オーナー名 + メールアドレス (グレー表示) | - |
| 最終アクセス | `team.last_accessed_at` (相対時刻) | o |

※ プランはユーザー単位（`users.plan_id`）のためテーブル列から削除

### 4. 新規作成モーダル

フォームフィールド：
| フィールド | タイプ | 必須 | 説明 |
|-----------|--------|------|------|
| 拠点名 | TextInput | Yes | 例: 福岡営業所 |
| 管理者名 | TextInput | Yes | 初期管理者ユーザーの名前 |
| 管理者メール | TextInput (email) | Yes | 初期管理者ユーザーのメールアドレス |
| 初期パスワード | TextInput (password) | Yes | 10文字以上 |
| 初期プラン | SelectInput | Yes | 管理者ユーザーのプラン: Light/Standard/Pro |
| 管理者APIキー | TextInput (readonly) + ボタン | - | 自動生成（`user_api_keys`に保存）、再生成ボタン付き |

## データ取得

```php
// Livewire Component
public $search = '';
public $overLimitFilter = false;

public function getTeamsProperty()
{
    return Team::query()
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->when($this->overLimitFilter, fn($q) => $q->whereHas('users', function($q) {
            $q->whereHas('monthlyApiUsages', function($q) {
                $q->whereRaw('count > monthly_limit');
            });
        }))
        ->with(['owner'])
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
| モーダル保存 | Team作成 + 管理者User作成 + UserApiKey作成 → 一覧更新 |
| 拠点名クリック | A03へ遷移 |
| ページネーション | ページ切替 |

## Livewire実装

```php
// app/Livewire/Admin/TeamList.php
class TeamList extends Component
{
    use WithPagination;

    public $search = '';
    public $overLimitFilter = false;

    // 新規作成モーダル
    public $showCreateModal = false;
    public $newTeam = ['name' => ''];
    public $newAdmin = [
        'name' => '',
        'email' => '',
        'password' => '',
        'plan_id' => '',
    ];
    public $newApiKey = '';

    protected $queryString = ['search', 'overLimitFilter'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function generateApiKey(): void
    {
        $this->newApiKey = Str::random(64);
    }

    public function openCreateModal(): void
    {
        $this->showCreateModal = true;
        $this->generateApiKey(); // 初回自動生成
    }

    public function createTeam(): void
    {
        $this->validate([
            'newTeam.name' => 'required|string|max:255',
            'newAdmin.name' => 'required|string|max:255',
            'newAdmin.email' => 'required|email|unique:users,email',
            'newAdmin.password' => 'required|string|min:10',
            'newAdmin.plan_id' => 'required|exists:plans,id',
            'newApiKey' => 'required|string|size:64',
        ]);

        // Team + User + UserApiKey作成処理
        DB::transaction(function () {
            $team = Team::create(['name' => $this->newTeam['name']]);
            $user = $team->users()->create([
                'name' => $this->newAdmin['name'],
                'email' => $this->newAdmin['email'],
                'password' => Hash::make($this->newAdmin['password']),
                'plan_id' => $this->newAdmin['plan_id'],
            ]);
            $user->apiKeys()->create([
                'key_hash' => hash('sha256', $this->newApiKey),
                'key_encrypted' => encrypt($this->newApiKey),
                'is_active' => true,
            ]);
        });

        $this->showCreateModal = false;
        $this->reset('newTeam', 'newAdmin', 'newApiKey');
        $this->dispatch('toast', type: 'success', message: '拠点を作成しました');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| 拠点名 | required, string, max:255 |
| 管理者名 | required, string, max:255 |
| 管理者メール | required, email, unique:users,email |
| 初期パスワード | required, string, min:10 |
| プラン（管理者用） | required, exists:plans,id |
| APIキー | required, string, size:64 |
