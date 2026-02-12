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
- `modals/ConfirmModal` (APIキー再生成確認)
- `feedback/AlertBanner`

## 画面構成

### 1. パンくずナビゲーション

`拠点・ユーザー管理 > {拠点名}`

### 2. 基本情報セクション

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| 拠点名 | TextInput | 必須 |
| プラン | SelectInput | Light/Standard/Pro (月間リクエスト制限表示付き) |

### 3. 所属ユーザー一覧セクション

テーブルカラム：
| カラム | 内容 |
|--------|------|
| ユーザー名 / メール | 名前 + メールアドレス (グレー表示) + アバター |
| 登録日 | 登録日付 (YYYY-MM-DD) |
| 操作 | 編集ボタン、削除ボタン |

アクション：
- ユーザー追加ボタン → 新規ユーザー追加モーダル表示
- 編集ボタン → ユーザー編集モーダル表示

### 4. APIキー管理セクション

| 項目 | 説明 |
|------|------|
| 現在のAPIキー | マスク表示 (demo_key_********************) + 表示ボタン + コピーボタン + 再生成ボタン |
| 最終利用 | APIキー最終利用日時 (YYYY-MM-DD HH:MM:SS) |

動作仕様：
- 表示ボタンクリック → ローディング後に平文表示
- 再生成ボタンクリック → 確認モーダル表示 → 確認後に新規キー発行 → 平文表示

### 5. Danger Zone

- チーム削除ボタン
- 説明: このチームとそのすべての利用データを完全に削除します

## データ取得

```php
// Livewire Component
public Team $team;

public function mount(Team $team)
{
    $this->team = $team->load(['plan', 'users', 'apiKeys']);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 基本情報変更 | フォーム入力 |
| 保存ボタン | Team更新 → トースト表示 |
| ユーザー追加ボタン | 新規ユーザー追加モーダル表示 |
| ユーザー編集ボタン | ユーザー編集モーダル表示（currentUser データバインド） |
| ユーザー削除ボタン | 確認モーダル表示 |
| ユーザー削除確認 | ユーザー削除実行 |
| APIキー表示ボタン | マスク解除 → 平文表示 |
| APIキーコピーボタン | クリップボードにコピー |
| APIキー再生成ボタン | 確認モーダル表示 |
| 再生成確認 | 新規キー発行 → 平文表示 |
| チーム削除ボタン | 削除実行（確認なし、実装時は確認モーダル推奨） |

## Livewire実装

```php
// app/Livewire/Admin/TeamEdit.php
class TeamEdit extends Component
{
    public Team $team;

    // フォーム
    public $name;
    public $plan_id;
    public $is_active;

    // モーダル
    public $showInviteModal = false;
    public $showRegenerateModal = false;
    public $showDeleteModal = false;

    // ユーザー追加フォーム
    public $newUserName = '';
    public $newUserEmail = '';
    public $newUserPassword = '';

    // ユーザー編集フォーム
    public $currentUser = [];
    public $editUserName = '';
    public $editUserEmail = '';


    // APIキー表示状態
    public $showApiKey = false;
    public $newApiKey = null; // 再生成時のみセット

    protected $rules = [
        'name' => 'required|string|max:255',
        'plan_id' => 'required|exists:plans,id',
    ];

    public function save()
    {
        $this->validate();

        $this->team->update([
            'name' => $this->name,
            'plan_id' => $this->plan_id,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('toast', type: 'success', message: '保存しました');
    }

    public function regenerateApiKey()
    {
        $plainKey = $this->team->regenerateApiKey();
        $this->newApiKey = $plainKey;
        $this->showRegenerateModal = false;
        $this->dispatch('toast', type: 'success', message: 'APIキーを再生成しました');
    }

    public function deleteTeam()
    {
        if ($this->deleteConfirmation !== $this->team->name) {
            return;
        }

        $this->team->delete();
        return redirect()->route('admin.teams.index')
            ->with('success', '拠点を削除しました');
    }
}
```

## バリデーション

### 基本情報
| フィールド | ルール |
|-----------|--------|
| 拠点名 | required, string, max:255 |
| プラン | required, exists:plans,id |

### ユーザー追加
| フィールド | ルール |
|-----------|--------|
| お名前 | required, string, max:255 |
| メールアドレス | required, email |
| 初期パスワード | required, string, min:10, regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/, not_in:password,admin,12345678,qwerty |

### ユーザー編集
| フィールド | ルール |
|-----------|--------|
| お名前 | required, string, max:255 |
| メールアドレス | required, email, unique:users,email (自身を除外) |
