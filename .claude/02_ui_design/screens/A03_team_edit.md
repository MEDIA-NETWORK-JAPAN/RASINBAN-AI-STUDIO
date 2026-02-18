# A03: 拠点編集

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A03 |
| 画面名 | 拠点詳細・編集 |
| URL | `/admin/teams/{id}` |
| モック | [mocks/A03_team_edit.html](../mocks/A03_team_edit.html) |

## 概要

拠点の詳細情報編集、所属ユーザー管理、APIキー管理、拠点削除を行う画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `navigation/Breadcrumb`
- `forms/TextInput`
- `forms/SelectInput`
- `forms/ApiKeyField`
- `data-display/DataTable`
- `data-display/StatusBadge`
- `buttons/Button` (primary, secondary, danger)
- `buttons/IconButton`
- `modals/Modal` (ユーザー追加、ユーザー編集)
- `modals/ConfirmModal` (ユーザーAPIキー再生成確認)
- `feedback/AlertBanner`

## 画面構成

### 1. パンくずナビゲーション

`拠点・ユーザー管理 > {拠点名}`

### 2. 基本情報セクション

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| 拠点名 | TextInput | 必須（プランはユーザー単位のため削除） |

### 3. 所属ユーザー一覧セクション

テーブルカラム：
| カラム | 内容 |
|--------|------|
| ユーザー名 / メール | 名前 + メールアドレス (グレー表示) + アバター |
| プラン | `user.plan.name` + APIキー状態バッジ |
| 登録日 | 登録日付 (YYYY-MM-DD) |
| 操作 | 編集ボタン、削除ボタン |

アクション：
- ユーザー追加ボタン → 新規ユーザー追加モーダル表示
- 編集ボタン → ユーザー編集モーダル表示（プラン + APIキー管理を含む）

**ユーザー追加モーダル**（追加フィールド）:
| フィールド | タイプ | 必須 | 説明 |
|-----------|--------|------|------|
| お名前 | TextInput | Yes | |
| メールアドレス | TextInput (email) | Yes | |
| 初期パスワード | TextInput (password) | Yes | 10文字以上 |
| プラン | SelectInput | Yes | Light/Standard/Pro |

ユーザー追加時にAPIキーが自動生成され `user_api_keys` に保存される。

**ユーザー編集モーダル**（追加フィールド）:
| フィールド | タイプ | 説明 |
|-----------|--------|------|
| お名前 | TextInput | |
| メールアドレス | TextInput (email) | |
| プラン | SelectInput | Light/Standard/Pro |
| APIキー | ApiKeyField | マスク表示 + 表示/コピー/再生成 |

APIキー再生成動作：
- 再生成ボタンクリック → 確認モーダル表示 → 確認後に新規キー発行 → 平文表示

### 4. Danger Zone

- チーム削除ボタン
- 説明: このチームとそのすべての利用データを完全に削除します

## データ取得

```php
// Livewire Component
public Team $team;

public function mount(Team $team): void
{
    $this->team = $team->load(['users.plan', 'users.apiKeys']);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 基本情報変更 | フォーム入力 |
| 保存ボタン | Team更新（チーム名のみ） → トースト表示 |
| ユーザー追加ボタン | 新規ユーザー追加モーダル表示（プラン選択付き） |
| ユーザー編集ボタン | ユーザー編集モーダル表示（プラン + APIキー管理含む） |
| ユーザー削除ボタン | 確認モーダル表示 |
| ユーザー削除確認 | ユーザーとUserApiKey削除実行 |
| ユーザー編集モーダル内APIキー表示ボタン | マスク解除 → 平文表示 |
| ユーザー編集モーダル内APIキーコピーボタン | クリップボードにコピー |
| ユーザー編集モーダル内APIキー再生成ボタン | 確認モーダル表示 |
| APIキー再生成確認 | user_api_keysに新規キー発行 → 平文表示 |
| チーム削除ボタン | 確認モーダル表示 → 削除実行 |

## Livewire実装

```php
// app/Livewire/Admin/TeamEdit.php
class TeamEdit extends Component
{
    public Team $team;

    // フォーム（チーム名のみ）
    public $name;

    // モーダル
    public $showAddUserModal = false;
    public $showEditUserModal = false;
    public $showRegenerateApiKeyModal = false;
    public $showDeleteUserModal = false;
    public $showDeleteTeamModal = false;

    // ユーザー追加フォーム
    public $newUserName = '';
    public $newUserEmail = '';
    public $newUserPassword = '';
    public $newUserPlanId = '';

    // ユーザー編集フォーム
    public $editingUser = null;
    public $editUserName = '';
    public $editUserEmail = '';
    public $editUserPlanId = '';

    // ユーザーAPIキー表示状態（ユーザー編集モーダル内）
    public $showEditUserApiKey = false;
    public $editUserNewApiKey = null; // 再生成時のみセット

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function save(): void
    {
        $this->validate();
        $this->team->update(['name' => $this->name]);
        $this->dispatch('toast', type: 'success', message: '保存しました');
    }

    public function regenerateUserApiKey(): void
    {
        $plainKey = Str::random(64);
        $this->editingUser->apiKeys()->update(['is_active' => false]);
        $this->editingUser->apiKeys()->create([
            'key_hash' => hash('sha256', $plainKey),
            'key_encrypted' => encrypt($plainKey),
            'is_active' => true,
        ]);
        $this->editUserNewApiKey = $plainKey;
        $this->showRegenerateApiKeyModal = false;
        $this->dispatch('toast', type: 'success', message: 'APIキーを再生成しました');
    }

    public function deleteTeam(): void
    {
        $this->team->delete();
        $this->redirect(route('admin.teams.index'), navigate: true);
    }
}
```

## バリデーション

### 基本情報
| フィールド | ルール |
|-----------|--------|
| 拠点名 | required, string, max:255 |

### ユーザー追加
| フィールド | ルール |
|-----------|--------|
| お名前 | required, string, max:255 |
| メールアドレス | required, email, unique:users,email |
| 初期パスワード | required, string, min:10, regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/, not_in:password,admin,12345678,qwerty |
| プラン | required, exists:plans,id |

### ユーザー編集
| フィールド | ルール |
|-----------|--------|
| お名前 | required, string, max:255 |
| メールアドレス | required, email, unique:users,email (自身を除外) |
| プラン | required, exists:plans,id |
