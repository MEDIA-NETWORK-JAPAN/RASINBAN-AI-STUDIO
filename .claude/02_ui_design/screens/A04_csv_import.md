# A04: CSV一括登録

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A04 |
| 画面名 | 拠点CSV一括登録 |
| URL | `/admin/teams/import` |
| モック | [mocks/A04_csv_import.html](../mocks/A04_csv_import.html) |

## 概要

CSVファイルをアップロードして複数の拠点・ユーザーを一括登録する画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `navigation/Breadcrumb`
- `forms/FileDropzone`
- `data-display/DataTable` (プレビュー)
- `data-display/ProgressBar`
- `buttons/Button`
- `feedback/ProcessingStatus`
- `feedback/CompletionStatus`
- `feedback/LogConsole`
- `feedback/Toast`

## 画面構成

### ステップ1: ファイルアップロード

- ドラッグ＆ドロップエリア
- CSVフォーマット説明
- テンプレートダウンロードリンク

### ステップ2: プレビュー確認

プレビューテーブル：
| カラム | 内容 |
|--------|------|
| # | 行番号 |
| 拠点名 | team_name |
| プラン | plan |
| オーナー名 | owner_name |
| オーナーメール | owner_email |
| ステータス | Valid/Error バッジ |

エラー行はハイライト表示 + エラー内容をツールチップで表示

### ステップ3: インポート実行

- 進捗プログレスバー
- 処理ログ表示（リアルタイム）

### ステップ4: 完了

- 成功/失敗サマリー
- エラーログダウンロード（エラーがある場合）

## CSVフォーマット

```csv
team_name,plan,owner_name,owner_email
東京本社 営業部,standard,田中 太郎,taro.tanaka@example.com
大阪支店 開発部,light,山田 花子,hanako.yamada@example.com
```

| カラム | 必須 | 説明 |
|--------|------|------|
| team_name | Yes | 拠点名 |
| plan | Yes | light/standard/pro |
| owner_name | Yes | オーナー名 |
| owner_email | Yes | オーナーメールアドレス |

## データ処理

```php
// Livewire Component
public $file;
public $preview = [];
public $importing = false;
public $progress = 0;
public $logs = [];
public $completed = false;
public $results = ['success' => 0, 'failed' => 0];

public function updatedFile()
{
    $this->validate(['file' => 'required|file|mimes:csv,txt|max:10240']);
    $this->parsePreview();
}

public function parsePreview()
{
    // CSVパース + バリデーション
    $rows = array_map('str_getcsv', file($this->file->getRealPath()));
    $headers = array_shift($rows);

    foreach ($rows as $index => $row) {
        $data = array_combine($headers, $row);
        $this->preview[] = [
            'row' => $index + 2,
            'data' => $data,
            'valid' => $this->validateRow($data),
            'errors' => $this->getRowErrors($data),
        ];
    }
}

public function import()
{
    $this->importing = true;

    foreach ($this->preview as $index => $item) {
        if (!$item['valid']) {
            $this->addLog('error', "行{$item['row']}: スキップ（バリデーションエラー）");
            $this->results['failed']++;
            continue;
        }

        try {
            // Team + User作成
            $this->createTeamWithOwner($item['data']);
            $this->addLog('success', "行{$item['row']}: {$item['data']['team_name']} - 登録完了");
            $this->results['success']++;
        } catch (\Exception $e) {
            $this->addLog('error', "行{$item['row']}: {$e->getMessage()}");
            $this->results['failed']++;
        }

        $this->progress = (($index + 1) / count($this->preview)) * 100;
    }

    $this->importing = false;
    $this->completed = true;
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| ファイルドロップ/選択 | CSVパース → プレビュー表示 |
| ファイル削除 | プレビュークリア |
| インポート開始 | 処理開始 → ログ表示 → 完了表示 |
| 完了後「一覧に戻る」 | A02へ遷移 |

## バリデーション

| フィールド | ルール |
|-----------|--------|
| team_name | required, string, max:255 |
| plan | required, in:light,standard,pro |
| owner_name | required, string, max:255 |
| owner_email | required, email, unique:users,email |
