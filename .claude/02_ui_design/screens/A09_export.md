# A09: 災害復旧エクスポート

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A09 |
| 画面名 | 災害復旧 (DR) エクスポート |
| URL | `/admin/dr/export` |
| モック | [mocks/A09_export.html](../mocks/A09_export.html) |

## 概要

システム障害時に備え、現在の全拠点・ユーザー・APIキー設定を復旧用Seeder (OnPremiseSetupSeeder) で利用可能なJSON形式でエクスポートする画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `buttons/Button`
- `data-display/ProgressBar`
- `feedback/AlertBanner`
- `feedback/ProcessingStatus`
- `feedback/CompletionStatus`

## 画面構成

### 1. 説明カード

- アイコン: データベースアイコン
- タイトル: 「システム構成のエクスポート」
- 説明: 出力内容の説明
- 警告バナー: APIキーが含まれることの注意喚起

### 2. 生成ボタン

- 「バックアップデータを生成」ボタン

### 3. 処理中表示

- プログレスバー
- 「データを収集中...」メッセージ

### 4. 完了・ダウンロード

- 成功ヘッダー（ファイルサイズ表示）
- JSONプレビュー（コードブロック）
- ダウンロードボタン

## エクスポートデータ形式

```json
[
  {
    "team_name": "東京本社 営業部",
    "plan": "standard",
    "api_key_plain": "sk_live_xxxxxxxx",
    "users": [
      {
        "name": "田中 太郎",
        "email": "taro.tanaka@example.com",
        "role": "admin"
      },
      {
        "name": "鈴木 一郎",
        "email": "ichiro.suzuki@example.com",
        "role": "member"
      }
    ]
  }
]
```

## Livewire実装

```php
// app/Livewire/Admin/DrExport.php
class DrExport extends Component
{
    public $generating = false;
    public $completed = false;
    public $progress = 0;
    public $jsonData = '';
    public $fileSize = '';

    public function startExport()
    {
        $this->generating = true;
        $this->completed = false;
        $this->progress = 0;

        // 実際の実装ではジョブを使用
        $this->generateExportData();
    }

    public function generateExportData()
    {
        $data = [];

        $teams = Team::with(['users', 'plan', 'apiKeys'])->get();
        $total = $teams->count();

        foreach ($teams as $index => $team) {
            $data[] = [
                'team_name' => $team->name,
                'plan' => $team->plan->slug,
                'api_key_plain' => $team->getPlainApiKey(), // 復号化
                'users' => $team->users->map(fn($user) => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->membership->role,
                ])->toArray(),
            ];

            $this->progress = (($index + 1) / $total) * 100;
        }

        $this->jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->fileSize = $this->formatBytes(strlen($this->jsonData));
        $this->generating = false;
        $this->completed = true;
    }

    public function download()
    {
        return response()->streamDownload(function () {
            echo $this->jsonData;
        }, 'teams.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    private function formatBytes($bytes)
    {
        if ($bytes < 1024) return $bytes . 'B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . 'KB';
        return round($bytes / 1048576, 1) . 'MB';
    }
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 生成ボタン | プログレス表示 → JSON生成 → プレビュー表示 |
| ダウンロードボタン | teams.json をダウンロード |

## セキュリティ注意事項

- このファイルにはAPIキー（平文）が含まれる
- ダウンロード後は安全な場所に保管
- アクセスログを記録
- 管理者のみアクセス可能

## 復旧手順（参考）

1. このエクスポート機能で `teams.json` を取得
2. 新環境の `database/seeders/data/teams.json` に配置
3. `php artisan db:seed --class=OnPremiseSetupSeeder` を実行
4. 拠点・ユーザー・APIキーが復元される

## エクスポート仕様

- **対象データ:** 全拠点・全ユーザー・全APIキー（選択不可、全件エクスポート）
- **APIキー形式:** 平文（復号化済み）で出力
- **エクスポート履歴:** 記録なし（災害復旧用のため、履歴管理は不要）
