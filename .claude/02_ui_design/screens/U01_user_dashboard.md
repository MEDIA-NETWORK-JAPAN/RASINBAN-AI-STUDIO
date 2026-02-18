# U01: ユーザーダッシュボード

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | U01 |
| 画面名 | ユーザーダッシュボード |
| URL | `/dashboard` |
| モック | [mocks/U01_user_dashboard.html](../mocks/U01_user_dashboard.html) |

## 概要

一般ユーザーが自分のAPI利用状況を確認する画面。プランの月間上限に対する利用率をリアルタイムで把握できる閲覧専用ダッシュボード。

**設計原則:**
- `auth()->id()` のデータのみ表示（URLパラメータは使用しない）
- 編集・削除操作なし（閲覧専用）
- `monthly_api_usages WHERE user_id = auth()->id()` で集計

## 使用コンポーネント

- `layout/AppLayout`
- `layout/PageHeader`
- `data-display/KpiCard`
- `data-display/DataTable`
- `data-display/ProgressBar`
- `data-display/StatusBadge`

## 画面構成

### 1. ユーザー情報サマリー

| 項目 | 内容 |
|------|------|
| ユーザー名 | `auth()->user()->name` |
| 拠点名 | `auth()->user()->currentTeam->name` |
| プラン名 | `auth()->user()->plan->name` |
| APIキー状態 | `StatusBadge`（Active/Inactive）|

### 2. KPIカードエリア

3カラムのグリッドレイアウトで以下を表示：

| カード | データ | アイコン | 色 |
|--------|--------|---------|-----|
| 今月の総利用回数 | `monthly_api_usages WHERE user_id = auth()->id()` の今月合計 | `fa-exchange-alt` | blue |
| プラン月間上限 | `user.plan.plan_limits` の合計上限 | `fa-chart-bar` | indigo |
| 今月の利用率 | 総利用回数 / 月間上限 × 100% + ProgressBar | `fa-percent` | 利用率に応じた色分け |

### 3. アプリ別利用状況テーブル

| カラム | 内容 |
|--------|------|
| アプリ名 | `dify_app.name`（アイコン付き） |
| 利用状況 | 今月の利用回数 / 月間上限 + ProgressBar |
| 利用率 | パーセンテージ表示 |
| 最終更新 | `updated_at`（相対時刻） |

### 行スタイル

| 条件 | 背景色 |
|------|--------|
| 利用率 < 90% | 白 |
| 利用率 ≥ 90% | `bg-yellow-50` |
| 利用率 > 100% | `bg-red-50` |

## データ取得

```php
// Livewire Component
public function mount(): void
{
    // URLパラメータは使用しない - 常にログインユーザーのデータのみ
}

public function getCurrentMonthUsagesProperty()
{
    return MonthlyApiUsage::query()
        ->where('user_id', auth()->id())  // 必ずログインユーザーのIDで絞り込む
        ->where('year_month', now()->format('Y-m'))
        ->with(['difyApp'])
        ->get();
}

public function getTotalUsageProperty(): int
{
    return $this->currentMonthUsages->sum('count');
}

public function getPlanLimitProperty(): int
{
    return auth()->user()->plan?->plan_limits->sum('monthly_limit') ?? 0;
}

public function getUsagePercentageProperty(): float
{
    if ($this->planLimit === 0) {
        return 0;
    }
    return min(100, round(($this->totalUsage / $this->planLimit) * 100, 1));
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| ページ表示 | ログインユーザーの今月の利用状況を表示（読み取り専用） |

## セキュリティ

- **URLパラメータを使用しない**: リクエストパラメータから `user_id` や `team_id` を取得するコードは実装しない
- **必ず `auth()->id()` を使用**: `WHERE user_id = auth()->id()` を必須条件として使用する
- **他ユーザーへのアクセス不可**: ミドルウェアで `is_admin=false` を確認済み

```php
// 禁止パターン（セキュリティ違反）
$userId = request('user_id');  // NG
MonthlyApiUsage::where('user_id', $userId);  // NG

// 必須パターン
MonthlyApiUsage::where('user_id', auth()->id());  // OK
```

## プログレスバーの色ルール

```php
public function getProgressColorProperty(): string
{
    if ($this->usagePercentage > 100) {
        return 'bg-red-600';
    }
    if ($this->usagePercentage > 90) {
        return 'bg-yellow-500';
    }
    return 'bg-blue-500';
}
```

## Livewire実装方針

Livewireコンポーネントで実装。データはすべて `auth()->id()` 固定取得。リアルタイム更新は不要（静的表示）。
