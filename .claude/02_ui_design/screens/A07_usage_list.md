# A07: 利用状況一覧

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A07 |
| 画面名 | 利用状況・実績管理 |
| URL | `/admin/usages` |
| モック | [mocks/A07_usage_list.html](../mocks/A07_usage_list.html) |

## 概要

月次のAPI利用実績データを一覧表示し、検索・フィルタリングを行う画面。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `forms/MonthInput`
- `forms/TextInput`
- `forms/SelectInput`
- `forms/ToggleSwitch`
- `data-display/DataTable`
- `data-display/Pagination`
- `data-display/ProgressBar`
- `buttons/Button`

## 画面構成

### 1. ヘッダーエリア

- タイトル: 「利用状況・実績管理」
- 説明: 「各ユーザーの月次API利用回数を確認・修正します。」
- アクション: 再読込ボタン、CSV出力ボタン

### 2. フィルタバー

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| 対象年月 | MonthInput | YYYY-MM形式 |
| ユーザー名/拠点名 | TextInput | 部分一致検索（`users.name` または `teams.name` で検索） |
| Difyアプリ | SelectInput | 全て/各アプリ |
| 制限超過のみ | ToggleSwitch | 超過データのみ表示 |

### 3. 利用実績テーブル

| カラム | 内容 | 説明 |
|--------|------|------|
| 対象月 | `usage_month` | YYYY-MM形式 |
| ユーザー/プラン | `user.name`（太字）+ `team.name`（サブテキスト）+ `user.plan.name` | 超過時はバッジ表示 |
| 利用アプリ | `dify_app.name` | アイコン付き |
| 利用状況 | `request_count`/上限 + ProgressBar | 色分け表示 |
| 最終リクエスト | `last_request_at` | YYYY-MM-DD HH:mm（NULLの場合は「-」表示） |
| 操作 | 修正ボタン | A08モーダルを開く |

### 行スタイル

| 条件 | 背景色 |
|------|--------|
| 超過なし | 白 |
| 超過あり | `bg-red-50` |

## データ取得

```php
// Livewire Component
public $month;
public $userSearch = '';  // ユーザー名または拠点名で検索
public $appFilter = '';
public $overLimitOnly = false;

public function mount(): void
{
    $this->month = now()->format('Y-m');
}

public function getUsagesProperty()
{
    return MonthlyApiUsage::query()
        ->when($this->month, fn($q) => $q->where('usage_month', $this->month))
        ->when($this->userSearch, fn($q) => $q
            ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->userSearch}%"))
            ->orWhereHas('team', fn($q) => $q->where('name', 'like', "%{$this->userSearch}%"))
        )
        ->when($this->appFilter, fn($q) => $q->where('dify_app_id', $this->appFilter))
        ->when($this->overLimitOnly, fn($q) => $q->whereRaw('request_count > monthly_limit'))
        ->with(['user.plan', 'user.currentTeam', 'team', 'difyApp'])
        ->orderByDesc('request_count')
        ->paginate(20);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 年月変更 | フィルタリング更新 |
| ユーザー名/拠点名入力 | リアルタイム検索 (debounce 300ms) |
| アプリ選択 | フィルタリング更新 |
| 超過のみトグル | フィルタリング更新（利用率100%超過のレコードのみ表示） |
| CSV出力ボタン | 現在のフィルタ条件でCSVダウンロード（`usages_{年月}.csv`） |
| 修正ボタン | A08モーダルを開く |

## CSV出力のビジネスロジック

**ファイル名:** `usages_{YYYY-MM}.csv`（例: `usages_2024-01.csv`）

**文字エンコーディング:** UTF-8 BOM付き（Excel対応）

**CSVヘッダー:**
```
対象月,ユーザー名,拠点名,プラン,アプリ名,利用回数,月間上限,利用率(%),最終リクエスト
```

**データ取得:**
- 現在のフィルタ条件（年月、ユーザー名/拠点名、アプリ、制限超過のみ）をすべて適用
- ページネーションなし（全件出力）
- 利用率は小数点第1位まで表示（例: 95.3）
- 削除されたアプリは「削除されたアプリ」と表示

## Livewire実装

```php
// app/Livewire/Admin/UsageList.php
class UsageList extends Component
{
    use WithPagination;

    public $month;
    public $userSearch = '';  // ユーザー名または拠点名で検索
    public $appFilter = '';
    public $overLimitOnly = false;

    public $selectedUsage = null;
    public $showEditModal = false;

    protected $queryString = ['month', 'userSearch', 'appFilter', 'overLimitOnly'];

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
    }

    public function openEditModal(MonthlyApiUsage $usage): void
    {
        $this->selectedUsage = $usage;
        $this->showEditModal = true;
    }

    public function exportCsv()
    {
        // 現在のフィルタ条件を適用したデータを取得
        $usages = MonthlyApiUsage::query()
            ->when($this->month, fn($q) => $q->where('usage_month', $this->month))
            ->when($this->userSearch, fn($q) => $q
                ->whereHas('user', fn($q) => $q->where('name', 'like', "%{$this->userSearch}%"))
                ->orWhereHas('team', fn($q) => $q->where('name', 'like', "%{$this->userSearch}%"))
            )
            ->when($this->appFilter, fn($q) => $q->where('dify_app_id', $this->appFilter))
            ->when($this->overLimitOnly, fn($q) => $q->whereRaw('request_count > monthly_limit'))
            ->with(['user.plan', 'user.currentTeam', 'team', 'difyApp'])
            ->orderByDesc('request_count')
            ->get();

        return response()->streamDownload(function () use ($usages) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM（Excel対応）
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSVヘッダー
            fputcsv($handle, [
                '対象月',
                'ユーザー名',
                '拠点名',
                'プラン',
                'アプリ名',
                '利用回数',
                '月間上限',
                '利用率(%)',
                '最終リクエスト',
            ]);

            // データ行
            foreach ($usages as $usage) {
                $percentage = $usage->monthly_limit > 0
                    ? round(($usage->request_count / $usage->monthly_limit) * 100, 1)
                    : 0;

                fputcsv($handle, [
                    $usage->usage_month,
                    $usage->user->name ?? '',
                    $usage->team->name ?? '',
                    $usage->user?->plan->name ?? '',
                    $usage->difyApp->name ?? '削除されたアプリ',
                    $usage->request_count,
                    $usage->monthly_limit,
                    $percentage,
                    $usage->last_request_at?->format('Y-m-d H:i:s') ?? '',
                ]);
            }

            fclose($handle);
        }, "usages_{$this->month}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
```

## プログレスバーの色ルール

```php
public function getProgressColor($count, $limit)
{
    $percentage = ($count / $limit) * 100;

    if ($percentage > 100) return 'bg-red-600';
    if ($percentage > 90) return 'bg-yellow-500';
    return 'bg-blue-500';
}
```
