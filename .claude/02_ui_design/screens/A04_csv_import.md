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
- **アップロード後の表示:** ファイル選択エリアの下にアップロードファイル名とファイルサイズを表示（HTMLモック参照）

### ステップ2: プレビュー確認

プレビューテーブル：
| カラム | 内容 |
|--------|------|
| No | 行番号 (1から開始) |
| 拠点名 | チーム名 |
| 契約プラン | プラン名 (バッジ表示、任意登録可能) |
| 管理者名 | オーナー名 |
| 管理者Email | オーナーメールアドレス |

注記: ※APIキーとユーザーパスワードは自動生成されます

### ステップ3: インポート実行

- 進捗プログレスバー
- 処理ログ表示（リアルタイム）

### ステップ4: 完了

- 成功/失敗サマリー
- エラーログダウンロード（エラーがある場合）
- **アクションボタン:**
  - 「一覧に戻る」ボタン: A02拠点一覧へ遷移
  - 「続けてインポート」ボタン: フォームをリセットして再度インポート可能にする

## CSVフォーマット

```csv
name,plan,owner_name,email
札幌支店 営業部,standard,山田太郎,sapporo.mgr@example.com
仙台支店 総務課,light,佐藤花子,sendai.admin@example.com
横浜開発センター,pro,鈴木一郎,yokohama.dev@example.com
```

| カラム | 必須 | 説明 |
|--------|------|------|
| name | Yes | 拠点名 |
| plan | No | プラン名（任意、plansテーブルに存在する名前） |
| owner_name | Yes | 管理者名 |
| email | Yes | 管理者メールアドレス |

**自動生成される項目:**
- APIキー: `Str::random(64)` で生成され、SHA-256ハッシュと暗号化で保存
- ユーザーパスワード: 10文字以上のランダム文字列（小文字・大文字・数字・記号を全て含む）で生成

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

    try {
        DB::transaction(function () {
            foreach ($this->preview as $index => $item) {
                if (!$item['valid']) {
                    $this->addLog('error', "行{$item['row']}: スキップ（バリデーションエラー）");
                    $this->results['failed']++;
                    continue;
                }

                try {
                    // Team + User作成（パスワード自動生成: Str::random(12)）
                    $this->createTeamWithOwner($item['data']);
                    $this->addLog('success', "行{$item['row']}: {$item['data']['name']} - 登録完了");
                    $this->results['success']++;
                } catch (\Exception $e) {
                    $this->addLog('error', "行{$item['row']}: {$e->getMessage()}");
                    $this->results['failed']++;
                    // エラー発生時はロールバック
                    throw $e;
                }

                $this->progress = (($index + 1) / count($this->preview)) * 100;
            }
        });
    } catch (\Exception $e) {
        $this->addLog('error', 'インポート処理中にエラーが発生したため、すべての変更をロールバックしました。');
        $this->results['success'] = 0;
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
| name | required, string, max:255 |
| plan | required, in:light,standard,pro |
| email | required, email, unique:teams,* (重複チェック) |
