# Repository Guidelines

## 役割と最優先参照先（レビュー/リファクタリング）
- このリポジトリは **rasinban-ai-studio**（Dify 中継・管理プラットフォーム）。既存コードと設計資料を調査・解析して、レビュー/リファクタリングを行う。\n+- 仕様の最優先参照は `.claude/01_development_docs/03_テストケース定義書.md` と `.claude/01_development_docs/04_画面テストケース定義書.md`（受入条件とテストケースが実質仕様）。\n+- 画面/コンポーネント/UI の詳細は `.claude/02_ui_design/`、セキュリティ/コーディング規約は `.claude/01_development_docs/05_セキュリティ・コーディング規約.md` を参照。\n+- Git 運用は `.claude/03_git_guidelines.md` に従う（AI 署名禁止）。\n+
## プロジェクト構成とモジュール配置
- `app/` にアプリケーションの中核（Actions/Models/Policies/Providers）。
- `routes/` に `web.php` / `api.php` / `console.php` / `channels.php`。
- `resources/views/` が Blade/Livewire の画面、`resources/js/` と `resources/css/` が Vite でビルドするフロント資産。
- `database/migrations/` / `database/factories/` / `database/seeders/` が DB スキーマとテストデータ。
- `tests/Feature/` と `tests/Unit/` に PHPUnit テスト。
- `.claude/` に要件・UI設計・Git運用の仕様書。

## ビルド・テスト・開発コマンド
全て Laravel Sail 経由で実行します。
- `vendor/bin/sail up -d` サービス起動（App/Postgres/Redis/Mailpit）。
- `vendor/bin/sail composer install` PHP 依存解決。
- `vendor/bin/sail npm install` フロント依存解決。
- `vendor/bin/sail artisan migrate` マイグレーション。
- `vendor/bin/sail npm run dev` Vite 開発サーバ。
- `vendor/bin/sail npm run build` 本番ビルド。
- `vendor/bin/sail artisan test --compact` テスト実行。

## コーディング規約と命名
- PHP は Laravel/PSR-12 準拠。整形は `vendor/bin/sail bin pint`。
- クラスは StudlyCase、メソッド/変数は camelCase、テストは `*Test.php`。
- Tailwind CSS の統一ルールは `.claude/02_ui_design/_design_tokens.md` を参照。

## テスト指針
- PHPUnit を使用。挙動確認は Feature、部品検証は Unit を優先。
- 配置は `tests/Feature/` / `tests/Unit/`。対象機能に合わせた命名にする。
- 可能なら最小範囲で実行例: `vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php`。

## コミット/PR ガイドライン
- コミットは `prefix: title` 形式。プレフィックスと本文形式は `.claude/03_git_guidelines.md` に準拠。
- 変更が大きい場合はカテゴリ別の箇条書きを本文に追加。
- AI 署名（`Co-Authored-By`）は禁止。
- PR には概要、関連チケット/Issue、UI 変更のスクリーンショット、実行したテストを記載。

## セキュリティと設定
- ローカル設定は `.env` を使用（必要なら `.env.example` から作成）。秘密情報の直書きは禁止。
- 管理者 URL は `ADMIN_PATH` で変更可能。API キーは暗号化保存。

## 仕様書の参照先
- 受入条件・テスト仕様は `.claude/01_development_docs/` を参照。
- UI 仕様と HTML モックは `.claude/02_ui_design/` に配置。

## レビュー観点（最小セット）
- 仕様適合: 受入条件/テストケースと実装の齟齬がないか。
- セキュリティ: 権限境界、データアクセス制御、ログに機密が出ないか。
- 変更影響: ルーティング、認可、モデル関係、UI 共通部品への影響。
- テスト: 既存テストの妥当性と不足ケース（失敗パス/境界値）。

## リファクタ方針
- 機能変更を避け、意図が明確な小さな差分で行う。
- 既存の命名・構造に揃え、重複の削減は共通化を優先。
- 仕様書/テストの期待に反しないことを先に確認する。

## 変更影響の確認手順
- 関連する仕様書/テストケースを特定し、該当テストを最小範囲で実行。
- UI 変更は画面遷移と共通コンポーネントへの影響を確認。
- DB 変更はマイグレーションと既存データの整合性を確認。
