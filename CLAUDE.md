# CLAUDE.md

このファイルは Claude Code がプロジェクトを理解するためのガイドです。

## プロジェクト概要

**rasinban-ai-studio** - Dify中継・管理プラットフォーム

オンプレミスシステムとDify（LLMプラットフォーム）の中間に位置し、リクエストの認証、中継、利用状況の管理を行うゲートウェイシステム。

## 技術スタック

- **Framework:** Laravel 12.x
- **Frontend:** Livewire 4.x (Jetstream Stack)
- **Auth Scaffolding:** Laravel Jetstream (Teams機能有効)
- **HTTP Client:** Laravel Http Facade (for Dify Proxy)
- **Database:** MySQL 8.0+
- **将来の拡張:** Laravel Sanctum (準備のみ)

## 主要機能

### 1. API中継 (Proxy)
- 拠点からのリクエストを認証し、Difyへ転送
- `X-Api-Key` による拠点認証 → Dify用 `Authorization: Bearer` に差し替え
- URLのSlug (`/relay/{slug}/{any}`) でDifyアプリを特定
- リクエストボディの `user` フィールドをTeam名に置換

### 2. 認証・権限モデル
- **管理者 (is_admin=true):** 全管理機能へのアクセス
- **一般ユーザー (is_admin=false):** 自チームのダッシュボードのみ閲覧
- **拠点 (Team):** `team_api_keys` の固定APIキーで認証
- **管理者URL:** 環境変数 `ADMIN_PATH` で推測されにくいパスに変更可能

### 3. 管理者機能
- 拠点・ユーザー管理 (CRUD, CSV一括登録)
- Difyアプリ管理 (Slug設定, APIキー暗号化保存)
- 利用状況管理 (月間リクエスト制限, 手動修正)
- 災害復旧データエクスポート (JSON)

### 4. 一般ユーザー機能
- ダッシュボードで自チームの利用状況確認（閲覧のみ）

## データベース設計

| テーブル | 用途 |
|---------|------|
| `users` | ユーザー管理 (`is_admin` フラグ追加) |
| `teams` | 拠点管理 (`plan_id` 追加) |
| `team_api_keys` | 拠点認証用固定キー (SHA-256ハッシュ + 暗号化) |
| `dify_apps` | Dify接続先設定 (slug, api_key暗号化) |
| `plans` | 契約プラン定義 |
| `plan_limits` | エンドポイントごとの月間上限 |
| `monthly_api_usages` | 月次利用実績ログ |

## 画面構成

### 共通
- **G01:** ログイン画面

### 一般ユーザーエリア
- **U01:** 拠点ダッシュボード（利用状況確認）

### 管理者エリア (Admin Guard)
- **A01:** 管理者ダッシュボード
- **A02:** 拠点一覧
- **A03:** 拠点登録・編集（APIキー管理含む）
- **A04:** CSV一括登録
- **A05:** Difyアプリ一覧
- **A06:** Difyアプリ登録・編集
- **A07:** 利用状況一覧
- **A08:** 利用回数修正（モーダル）
- **A09:** 復旧データエクスポート

## ディレクトリ構成

```
/
├── .claude/                           # プロジェクトドキュメント
│   ├── 01_development_docs/           # 開発仕様書
│   │   ├── 01_要件定義書.md
│   │   └── 02_画面一覧・機能一覧定義書.md
│   ├── 02_ui_design/                  # UI設計ドキュメント
│   │   ├── README.md                  # UI設計の目次・使い方
│   │   ├── _design_tokens.md          # Tailwind統一ルール
│   │   ├── components/                # 共通コンポーネント仕様
│   │   ├── screens/                   # 画面別仕様
│   │   └── mocks/                     # HTMLモック（参照用）
│   └── 03_git_guidelines.md           # Git運用ガイドライン
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/                # AuthenticateHybrid, AdminGuard, CheckMonthlyQuota
│   └── Models/
├── database/
│   ├── migrations/
│   └── seeders/
│       └── data/                      # DR用JSONファイル (teams.json)
├── resources/views/
├── routes/
└── CLAUDE.md
```

## 開発コマンド

```bash
# 依存関係インストール
composer install
npm install

# マイグレーション
php artisan migrate

# 開発サーバー起動
php artisan serve
npm run dev

# テスト
php artisan test
```

## セキュリティ注意事項

- APIキーは `key_hash` (SHA-256) と `key_encrypted` (Laravel Encrypt) の両方で保存
- Dify APIキーは暗号化して保存
- 管理者URLは環境変数で変更可能（推測防止）
- プロンプト内容はログに保存しない（メタデータのみ）

## ドキュメント参照ガイド（AI向け）

以下の指示があった場合は、対応するドキュメントを参照してください。

### 画面実装の指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「A02画面を実装して」 | `.claude/02_ui_design/screens/A02_team_list.md` + `mocks/A02_team_list.html` |
| 「拠点一覧画面を作って」 | `.claude/02_ui_design/screens/A02_team_list.md` |
| 「ダッシュボードを実装」 | `.claude/02_ui_design/screens/A01_admin_dashboard.md` |

### コンポーネント実装の指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「ボタンコンポーネントを作って」 | `.claude/02_ui_design/components/buttons.md` |
| 「モーダルを実装して」 | `.claude/02_ui_design/components/modals.md` |
| 「テーブルコンポーネントを作成」 | `.claude/02_ui_design/components/data-display.md` |
| 「フォーム部品を実装」 | `.claude/02_ui_design/components/forms.md` |

### スタイル・デザインの指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「Tailwindのルールを確認」 | `.claude/02_ui_design/_design_tokens.md` |
| 「デザイントークンに従って」 | `.claude/02_ui_design/_design_tokens.md` |
| 「UIの統一ルールは？」 | `.claude/02_ui_design/_design_tokens.md` |

### 仕様確認の指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「要件を確認して」 | `.claude/01_development_docs/01_要件定義書.md` |
| 「機能一覧を見せて」 | `.claude/01_development_docs/02_画面一覧・機能一覧定義書.md` |
| 「画面遷移を確認」 | `.claude/02_ui_design/screens/_index.md` |
| 「使用コンポーネント一覧」 | `.claude/02_ui_design/components/_index.md` |

### 複合的な指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「管理者画面をすべて実装」 | `.claude/02_ui_design/screens/_index.md` → 各A01〜A09.md |
| 「共通レイアウトを作成」 | `.claude/02_ui_design/components/layout.md` |
| 「拠点管理機能を実装」 | `screens/A02_team_list.md`, `A03_team_edit.md`, `A04_csv_import.md` |

### Git操作の指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「コミットして」 | `.claude/03_git_guidelines.md` を参照してメッセージ作成 |
| 「コミットメッセージを提案して」 | `.claude/03_git_guidelines.md` の規約に従う |
| 「ブランチを作成」 | `.claude/03_git_guidelines.md` の命名規則に従う |

### テスト・品質保証の指示

| 指示例 | 参照ドキュメント |
|--------|-----------------|
| 「画面の受入条件を確認」 | `.claude/01_development_docs/04_画面テストケース定義書.md` の「4. 受入条件」 |
| 「画面テストケースを確認」 | `.claude/01_development_docs/04_画面テストケース定義書.md` の「5. テストケース一覧」 |
| 「A01画面のテストを実装」 | `.claude/01_development_docs/04_画面テストケース定義書.md` で該当画面検索 |
| 「コンポーネントの受入条件を確認」 | `.claude/01_development_docs/03_テストケース定義書.md` の「4. 受入条件」 |
| 「コンポーネントテストケースを確認」 | `.claude/01_development_docs/03_テストケース定義書.md` の「5. テストケース一覧」 |
| 「TC-XXXのテストを実装」 | `.claude/01_development_docs/03_テストケース定義書.md` で該当テストID検索 |
| 「AC-XXXの受入条件は?」 | 画面AC: `04_画面テストケース定義書.md`、コンポーネントAC: `03_テストケース定義書.md` |
| 「テスト実装順序は?」 | `.claude/01_development_docs/04_画面テストケース定義書.md` の「6. テスト実装優先度」 |
| 「Feature Testの書き方」 | `.claude/01_development_docs/04_画面テストケース定義書.md` の「7. テスト実装ガイド」 |
| 「コンポーネントテストの書き方」 | `.claude/01_development_docs/03_テストケース定義書.md` の「7. テスト実装ガイド」 |

### HTMLモックの参照

実装時は対応するHTMLモックを必ず参照してください：
- モック配置先: `.claude/02_ui_design/mocks/`
- モックにはAlpine.js + Tailwind CSSで動作するサンプルが含まれています
- Livewire実装時はAlpine.jsの状態管理をLivewireに置き換えてください

## 開発ドキュメント一覧

| カテゴリ | ドキュメント | 内容 |
|---------|-------------|------|
| 要件定義 | `.claude/01_development_docs/01_要件定義書.md` | システム要件、機能要件 |
| 機能一覧 | `.claude/01_development_docs/02_画面一覧・機能一覧定義書.md` | 画面・機能の詳細定義 |
| コンポーネントテスト | `.claude/01_development_docs/03_テストケース定義書.md` | コンポーネント単体テストの受入条件とテストケース定義（約260ケース） |
| 画面テスト | `.claude/01_development_docs/04_画面テストケース定義書.md` | 画面単位（Feature Test）の受入条件とテストケース定義（約130ケース） |
| UI設計目次 | `.claude/02_ui_design/README.md` | UI設計ドキュメントの使い方 |
| デザイントークン | `.claude/02_ui_design/_design_tokens.md` | Tailwind統一ルール |
| コンポーネント | `.claude/02_ui_design/components/` | 共通UIコンポーネント仕様 |
| 画面仕様 | `.claude/02_ui_design/screens/` | 各画面の実装仕様 |
| HTMLモック | `.claude/02_ui_design/mocks/` | 参照用HTMLモック |
| Git運用 | `.claude/03_git_guidelines.md` | コミットメッセージ規約、ブランチ運用 |

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.5.2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- livewire/livewire (LIVEWIRE) - v3
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- phpunit/phpunit (PHPUNIT) - v11
- tailwindcss (TAILWINDCSS) - v3

## Skills Activation

This project has domain-specific skills available. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

- `livewire-development` — Develops reactive Livewire 3 components. Activates when creating, updating, or modifying Livewire components; working with wire:model, wire:click, wire:loading, or any wire: directives; adding real-time updates, loading states, or reactivity; debugging component behavior; writing Livewire tests; or when the user mentions Livewire, component, counter, or reactive UI.
- `tailwindcss-development` — Styles applications using Tailwind CSS v3 utilities. Activates when adding styles, restyling components, working with gradients, spacing, layout, flex, grid, responsive design, dark mode, colors, typography, or borders; or when the user mentions CSS, styling, classes, Tailwind, restyle, hero section, cards, buttons, or any visual/UI changes.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `vendor/bin/sail npm run build`, `vendor/bin/sail npm run dev`, or `vendor/bin/sail composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan

- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs

- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging

- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool

- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)

- Boost comes with a powerful `search-docs` tool you should use before trying other approaches when working with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries at once. For example: `['rate limiting', 'routing rate limiting', 'routing']`. The most relevant results will be returned first.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.

## Constructors

- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

## Type Declarations

- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Enums

- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

## Comments

- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless the logic is exceptionally complex.

## PHPDoc Blocks

- Add useful array shape type definitions when appropriate.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail npm run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `vendor/bin/sail artisan test --compact` with a specific filename or filter.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

## Database

- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `vendor/bin/sail artisan make:model`.

### APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## Controllers & Validation

- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

## Authentication & Authorization

- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Queues

- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

## Configuration

- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `vendor/bin/sail npm run build` or ask the user to run `vendor/bin/sail npm run dev` or `vendor/bin/sail composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== livewire/core rules ===

# Livewire

- Livewire allows you to build dynamic, reactive interfaces using only PHP — no JavaScript required.
- Instead of writing frontend code in JavaScript frameworks, you use Alpine.js to build the UI when client-side interactions are required.
- State lives on the server; the UI reflects it. Validate and authorize in actions (they're like HTTP requests).
- IMPORTANT: Activate `livewire-development` every time you're working with Livewire-related tasks.

=== pint/core rules ===

# Laravel Pint Code Formatter

- You must run `vendor/bin/sail bin pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test`, simply run `vendor/bin/sail bin pint` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This application uses PHPUnit for testing. All tests must be written as PHPUnit classes. Use `vendor/bin/sail artisan make:test --phpunit {name}` to create a new test.
- If you see a test using "Pest", convert it to PHPUnit.
- Every time a test has been updated, run that singular test.
- When the tests relating to your feature are passing, ask the user if they would like to also run the entire test suite to make sure everything is still passing.
- Tests should cover all happy paths, failure paths, and edge cases.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files; these are core to the application.

## Running Tests

- Run the minimal number of tests, using an appropriate filter, before finalizing.
- To run all tests: `vendor/bin/sail artisan test --compact`.
- To run all tests in a file: `vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `vendor/bin/sail artisan test --compact --filter=testName` (recommended after making a change to a related file).

=== tailwindcss/core rules ===

# Tailwind CSS

- Always use existing Tailwind conventions; check project patterns before adding new ones.
- IMPORTANT: Always use `search-docs` tool for version-specific Tailwind CSS documentation and updated code examples. Never rely on training data.
- IMPORTANT: Activate `tailwindcss-development` every time you're working with a Tailwind CSS or styling-related task.
</laravel-boost-guidelines>
