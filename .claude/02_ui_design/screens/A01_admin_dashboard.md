# A01: 管理者ダッシュボード

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | A01 |
| 画面名 | 管理者ダッシュボード |
| URL | `/admin` |
| モック | [mocks/A01_admin_dashboard.html](../mocks/A01_admin_dashboard.html) |

## 概要

管理者がログイン後に最初にアクセスする画面。システム全体の稼働状況を俯瞰できます。

## 使用コンポーネント

- `layout/AppLayout`
- `layout/Sidebar`
- `layout/PageHeader`
- `data-display/KpiCard`
- `data-display/DataTable`
- `data-display/ProgressBar`
- `buttons/IconButton`

## 画面構成

### 1. KPIカードエリア

3カラムのグリッドレイアウトで以下を表示：

| カード | データ | アイコン | 色 |
|--------|--------|---------|-----|
| 契約拠点数 | `teams.count()` | `fa-building` | indigo |
| 今月の総リクエスト | `monthly_api_usages.sum('request_count')` | `fa-exchange-alt` | blue |
| 稼働Difyアプリ | `dify_apps.where('is_active', true).count()` | `fa-robot` | green |

### 2. 利用率の高いユーザー一覧

テーブルカラム：
| カラム | 内容 |
|--------|------|
| ユーザー名 | `user.name` |
| 拠点名 | `user.currentTeam.name` |
| プラン | `user.plan.name` |
| 利用率 | プログレスバー + パーセンテージ |
| 操作 | 編集ボタン → A03（ユーザーの所属拠点）へ遷移 |

表示条件：利用率上位5件（`monthly_api_usages` の `user_id` 集計）

## データ取得

```php
// Controller
public function index()
{
    return view('admin.dashboard', [
        'teamCount' => Team::count(),
        'teamCountDiff' => Team::whereMonth('created_at', now()->subMonth())->count(),
        'totalRequests' => MonthlyApiUsage::currentMonth()->sum('request_count'),
        'requestLimit' => config('app.system_request_limit'),
        'activeApps' => DifyApp::where('is_active', true)->count(),
        'topUsageUsers' => User::withCurrentMonthUsage()
            ->orderByDesc('usage_percentage')
            ->take(5)
            ->get(),
    ]);
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 編集ボタンクリック | A03 拠点編集画面へ遷移（ユーザーの所属拠点） |
| 「すべて見る」クリック | A07 利用状況一覧へ遷移 |

## レスポンシブ対応

- KPIカード: `grid-cols-1 md:grid-cols-3`
- テーブル: 横スクロール対応

## Livewire実装方針

この画面は静的なデータ表示が主なので、通常のBladeビュー + Controllerで実装。
リアルタイム更新が必要な場合はLivewireコンポーネント化を検討。
