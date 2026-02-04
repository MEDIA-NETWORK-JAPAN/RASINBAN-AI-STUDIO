# Screens Index

画面一覧と遷移図です。

## 画面一覧

### 管理者エリア (Admin)

| ID | 画面名 | URL | 説明 |
|----|--------|-----|------|
| A01 | [ダッシュボード](./A01_admin_dashboard.md) | `/admin` | KPI表示、利用率の高い拠点一覧 |
| A02 | [拠点一覧](./A02_team_list.md) | `/admin/teams` | 拠点検索・一覧、新規作成モーダル |
| A03 | [拠点編集](./A03_team_edit.md) | `/admin/teams/{id}` | 拠点詳細編集、ユーザー管理、APIキー管理 |
| A04 | [CSV一括登録](./A04_csv_import.md) | `/admin/teams/import` | CSVアップロード、プレビュー、一括登録 |
| A05 | [Difyアプリ一覧](./A05_dify_app_list.md) | `/admin/apps` | アプリ一覧、ステータス切替、新規登録モーダル |
| A06 | [Difyアプリ編集](./A06_dify_app_edit.md) | `/admin/apps/{id}` | アプリ詳細編集、削除 |
| A07 | [利用状況一覧](./A07_usage_list.md) | `/admin/usages` | 月次利用実績検索・一覧 |
| A08 | [利用回数修正](./A08_usage_edit_modal.md) | (モーダル) | 利用回数の手動修正 |
| A09 | [災害復旧エクスポート](./A09_export.md) | `/admin/dr/export` | 復旧用JSONエクスポート |

### 一般ユーザーエリア (User) - 今後追加予定

| ID | 画面名 | URL | 説明 |
|----|--------|-----|------|
| U01 | 拠点ダッシュボード | `/dashboard` | 自チームの利用状況確認 |

### 共通画面 (General) - 今後追加予定

| ID | 画面名 | URL | 説明 |
|----|--------|-----|------|
| G01 | ログイン | `/login` | Jetstream標準 |

---

## 画面遷移図

```
[G01 ログイン]
    │
    ├─→ [A01 ダッシュボード] ←────────────────┐
    │       │                                │
    │       ├─→ [A02 拠点一覧] ──→ [A03 拠点編集]
    │       │       │
    │       │       └─→ [A04 CSV一括登録]
    │       │
    │       ├─→ [A05 Difyアプリ一覧] ──→ [A06 アプリ編集]
    │       │
    │       ├─→ [A07 利用状況一覧] ──→ [A08 修正モーダル]
    │       │
    │       └─→ [A09 災害復旧エクスポート]
    │
    └─→ [U01 拠点ダッシュボード] (一般ユーザー)
```

---

## ルーティング定義

```php
// routes/web.php

// 管理者エリア
Route::prefix(config('app.admin_path', 'admin'))
    ->middleware(['auth', 'admin'])
    ->name('admin.')
    ->group(function () {

        // A01: ダッシュボード
        Route::get('/', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // A02-A04: 拠点管理
        Route::resource('teams', TeamController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::get('teams/import', [TeamController::class, 'importForm'])
            ->name('teams.import');
        Route::post('teams/import', [TeamController::class, 'import'])
            ->name('teams.import.store');

        // A05-A06: Difyアプリ管理
        Route::resource('apps', DifyAppController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        // A07-A08: 利用状況管理
        Route::resource('usages', UsageController::class)
            ->only(['index', 'update']);

        // A09: 災害復旧
        Route::get('dr/export', [DrController::class, 'export'])
            ->name('dr.export');
        Route::post('dr/export', [DrController::class, 'download'])
            ->name('dr.download');
    });
```

---

## 共通レイアウト適用

すべての管理者画面で使用するレイアウト：

```blade
{{-- resources/views/layouts/admin.blade.php --}}
<x-layout.app :title="$title ?? 'Admin'">
  {{ $slot }}
</x-layout.app>
```

各画面での使用：

```blade
{{-- resources/views/admin/dashboard.blade.php --}}
<x-layouts.admin title="ダッシュボード">
  {{-- 画面コンテンツ --}}
</x-layouts.admin>
```
