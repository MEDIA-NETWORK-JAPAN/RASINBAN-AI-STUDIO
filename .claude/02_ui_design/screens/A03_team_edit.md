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
- `forms/ToggleSwitch`
- `forms/ApiKeyField`
- `data-display/DataTable`
- `data-display/StatusBadge`
- `buttons/Button` (primary, secondary, danger)
- `buttons/IconButton`
- `modals/Modal` (ユーザー招待)
- `modals/ConfirmModal` (削除確認, APIキー再生成)
- `feedback/AlertBanner`

## 画面構成

### 1. パンくずナビゲーション

`拠点・ユーザー管理 > {拠点名}`

### 2. 基本情報セクション

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| 拠点名 | TextInput | 必須 |
| プラン | SelectInput | Light/Standard/Pro |
| ステータス | ToggleSwitch | Active/Inactive |

### 3. 所属ユーザー一覧セクション

テーブルカラム：
| カラム | 内容 |
|--------|------|
| ユーザー名 | 名前 + メールアドレス |
| 役割 | Owner/Admin/Member バッジ |
| 操作 | 役割変更、削除（Ownerは削除不可） |

アクション：
- ユーザー招待ボタン → モーダル表示

### 4. APIキー管理セクション

| 項目 | 説明 |
|------|------|
| 現在のAPIキー | マスク表示 + 表示/コピーボタン |
| 作成日時 | APIキー作成日 |
| 再生成ボタン | 確認モーダル後に新規キー発行 |

注意: APIキーは再生成時のみ平文表示。以降はマスク表示。

### 5. Danger Zone

- 拠点削除ボタン
- 削除確認モーダル（拠点名入力必須）

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
| 基本情報変更 | リアルタイムバリデーション |
| 保存ボタン | Team更新 → トースト表示 |
| ユーザー招待 | モーダル表示 → User作成 + Team紐付け |
| ユーザー削除 | 確認後削除（Ownerは不可） |
| APIキー表示 | マスク解除 |
| APIキーコピー | クリップボードにコピー |
| APIキー再生成 | 確認モーダル → 新規キー発行 → 表示 |
| 拠点削除 | 確認モーダル（名前入力） → 削除 → A02へ遷移 |

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

    // 招待フォーム
    public $inviteEmail = '';
    public $inviteName = '';
    public $inviteRole = 'member';

    // 削除確認
    public $deleteConfirmation = '';

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

### ユーザー招待
| フィールド | ルール |
|-----------|--------|
| メールアドレス | required, email, unique:users,email |
| ユーザー名 | required, string, max:255 |
| 役割 | required, in:admin,member |
