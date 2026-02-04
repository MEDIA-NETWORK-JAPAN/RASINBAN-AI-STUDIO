# A06: Difyアプリ編集

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A06 |
| 画面名 | Difyアプリ詳細・編集 |
| URL | `/admin/apps/{id}` |
| モック | [mocks/A06_dify_app_edit.html](../mocks/A06_dify_app_edit.html) |

## 概要

Difyアプリの詳細情報編集と削除を行う画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `navigation/Breadcrumb`
- `forms/TextInput`
- `forms/SelectInput`
- `forms/ToggleSwitch`
- `forms/ApiKeyField`
- `buttons/Button`
- `modals/ConfirmModal`
- `feedback/AlertBanner`

## 画面構成

### 1. パンくずナビゲーション

`Difyアプリ管理 > {アプリ名}`

### 2. 基本情報セクション

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| アプリ名 | TextInput | 表示名 |
| Slug | TextInput | URLパス（readonly推奨、変更時は警告） |
| エンドポイントURL | TextInput | Dify APIのURL |
| エンドポイントタイプ | SelectInput | chat/completion/workflow |
| ステータス | ToggleSwitch | Active/Inactive |

### 3. APIキー管理セクション

| 項目 | 説明 |
|------|------|
| Dify APIキー | マスク表示 + 表示/非表示切替 |
| 更新ボタン | 新しいAPIキーを設定 |

注意: APIキーは暗号化保存。表示時は復号。

### 4. Danger Zone

- アプリ削除ボタン
- 削除確認モーダル

## データ取得

```php
// Livewire Component
public DifyApp $app;

public function mount(DifyApp $app)
{
    $this->app = $app;
    $this->fill([
        'name' => $app->name,
        'slug' => $app->slug,
        'endpoint_url' => $app->endpoint_url,
        'endpoint_type' => $app->endpoint_type,
        'is_active' => $app->is_active,
    ]);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| フィールド変更 | リアルタイムバリデーション |
| 保存ボタン | DifyApp更新 → トースト表示 |
| APIキー表示 | マスク解除（復号表示） |
| APIキー更新 | 新しいキーを暗号化保存 |
| 削除ボタン | 確認モーダル表示 |
| 削除確認 | DifyApp削除 → A05へ遷移 |

## Livewire実装

```php
// app/Livewire/Admin/DifyAppEdit.php
class DifyAppEdit extends Component
{
    public DifyApp $app;

    public $name;
    public $slug;
    public $endpoint_url;
    public $endpoint_type;
    public $is_active;

    public $showApiKey = false;
    public $newApiKey = '';
    public $showDeleteModal = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/',
                       Rule::unique('dify_apps')->ignore($this->app->id)],
            'endpoint_url' => 'required|url',
            'endpoint_type' => 'required|in:chat,completion,workflow',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->app->update([
            'name' => $this->name,
            'slug' => $this->slug,
            'endpoint_url' => $this->endpoint_url,
            'endpoint_type' => $this->endpoint_type,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('toast', type: 'success', message: '保存しました');
    }

    public function updateApiKey()
    {
        $this->validate(['newApiKey' => 'required|string']);

        $this->app->update([
            'api_key_encrypted' => encrypt($this->newApiKey),
        ]);

        $this->newApiKey = '';
        $this->dispatch('toast', type: 'success', message: 'APIキーを更新しました');
    }

    public function getDecryptedApiKeyProperty()
    {
        return decrypt($this->app->api_key_encrypted);
    }

    public function deleteApp()
    {
        $this->app->delete();

        return redirect()->route('admin.apps.index')
            ->with('success', 'アプリを削除しました');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| アプリ名 | required, string, max:255 |
| Slug | required, string, max:100, regex:/^[a-z0-9-]+$/, unique (except self) |
| エンドポイントURL | required, url |
| タイプ | required, in:chat,completion,workflow |
| 新APIキー | required, string (更新時のみ) |

## 注意事項

- Slugの変更は既存の連携に影響するため、変更時は警告を表示
- APIキーは暗号化保存（Laravel encrypt/decrypt使用）
- 削除時は関連するmonthly_api_usagesも削除される（要確認）
