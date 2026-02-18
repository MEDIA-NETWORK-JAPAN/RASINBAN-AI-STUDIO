# APIキー設計変更提案：拠点単位 → ユーザー単位

## 📄 文書情報

| 項目 | 内容 |
|------|------|
| ドキュメント名 | APIキー設計変更提案書 |
| バージョン | 1.9 |
| 作成日 | 2026-02-13 |
| 最終更新日 | 2026-02-18 |
| ステータス | **提案中（レビュー待ち）** |
| 優先度 | 🔴 High（実装開始前に確定必須） |

---

## 1. 課題の背景

### 1.1 現状のAPIキー設計

現在、拠点（Team）がDifyにアクセスする際の認証は **拠点単位のAPIキー（`team_api_keys`テーブル）** によって行われています。

```
【現状フロー】
オンプレシステム → X-Api-Key: <チームキー>
    → team_api_keys.key_hash で照合
    → チームのOwnerとしてログイン
    → /relay/{slug}/... へプロキシ
    → monthly_api_usages に team_id で記録
```

**現在のテーブル構造（`team_api_keys`）:**

| カラム | 型 | 説明 |
|--------|-----|------|
| id | BIGSERIAL | PK |
| team_id | FK | 拠点ID |
| name | VARCHAR | キー識別名（例: "Gateway_01"）|
| key_hash | VARCHAR(64) | SHA-256ハッシュ（認証用） |
| key_encrypted | TEXT | Laravel暗号化（表示/復元用） |
| is_active | BOOLEAN | 有効/無効フラグ |
| last_used_at | TIMESTAMP | 最終利用日時 |
| expires_at | TIMESTAMP | 有効期限（任意） |

### 1.2 課題

> **「将来的に拠点に紐づくユーザーが複数できた場合、管理できなくなってしまう」**

| 問題 | 詳細 |
|------|------|
| **誰が使ったか不明** | 1つのAPIキーを複数ユーザーで共有するため、どのユーザーが利用したか追跡不能 |
| **権限管理が困難** | 特定ユーザーのアクセスを無効化したい場合、拠点全体のキーを変更せざるを得ない |
| **セキュリティリスク** | 退職者・異動者のアクセスを即座に無効化できない |
| **利用ログの粗さ** | 拠点レベルの集計しか取れず、ユーザー別の利用状況が把握できない |

---

## 2. 変更の方向性

### 2.1 基本方針

**「ユーザーをシステムの基本単位とする」**

- 認証はユーザー単位（`user_api_keys`）
- プラン（クォータ）もユーザー単位（`users.plan_id`）
- 利用量集計もユーザーが主体（`user_id` を基準に記録）
- 拠点（Team）は「ユーザーをグルーピングするための名前空間」として維持
- 拠点集計は副次的に維持（A07 画面での拠点別表示用）
- ユーザー内のロール区分（オーナー・一般等）はなし（`is_admin` は管理画面専用であり、relay API とは無関係）

**拠点（Team）の役割の変化:**

| | 変更前 | 変更後 |
|-|--------|--------|
| 拠点の役割 | 認証・課金・クォートの単位 | **ユーザーをグルーピングする名前空間** |
| 認証単位 | 拠点 | **ユーザー** |
| プラン/クォータ単位 | 拠点 | **ユーザー** |
| 利用量集計の主体 | 拠点 | **ユーザー**（拠点別集計は副次） |

### 2.2 変更後のフロー

```
【変更後フロー】
オンプレシステム → X-Api-Key: <ユーザーキー>
    → user_api_keys.key_hash で照合
    → そのユーザーとして認証
    → user.currentTeam で拠点を特定
    → /relay/{slug}/... へプロキシ
    → monthly_api_usages に user_id（主）+ team_id（副）で記録
```

---

## 3. データベース変更

### 3.1 新設テーブル：`user_api_keys`

```sql
CREATE TABLE user_api_keys (
    id            BIGSERIAL PRIMARY KEY,
    user_id       BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    name          VARCHAR(255) NOT NULL DEFAULT 'default',
    key_hash      VARCHAR(64)  NOT NULL UNIQUE,   -- SHA-256（認証用高速照合）
    key_encrypted TEXT         NOT NULL,           -- Laravel Encrypt（表示/復元用）
    is_active     BOOLEAN      NOT NULL DEFAULT true,
    last_used_at  TIMESTAMP    NULL,
    expires_at    TIMESTAMP    NULL,
    created_at    TIMESTAMP    NOT NULL,
    updated_at    TIMESTAMP    NOT NULL
);

CREATE INDEX idx_user_api_keys_key_hash ON user_api_keys (key_hash);
CREATE INDEX idx_user_api_keys_user_id  ON user_api_keys (user_id);
```

### 3.2 `monthly_api_usages` テーブルの変更

利用量集計をユーザー主体に変更します。

**変更内容:**

| 変更 | 詳細 |
|------|------|
| `user_id` カラム追加 | NOT NULL（認証済みユーザーが必ず特定できるため） |
| `team_id` カラム | 維持（将来の拠点合計集計・A07の拠点名フィルタ用途） |
| UNIQUE 制約の変更 | `(team_id, dify_app_id, usage_month, endpoint)` → `(user_id, dify_app_id, usage_month, endpoint)` |

```sql
-- user_id カラム追加
ALTER TABLE monthly_api_usages
    ADD COLUMN user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE;

-- UNIQUE 制約を変更（ユーザー単位に）
ALTER TABLE monthly_api_usages
    DROP CONSTRAINT monthly_api_usages_team_id_dify_app_id_usage_month_endpoint_unique;

ALTER TABLE monthly_api_usages
    ADD CONSTRAINT monthly_api_usages_user_id_dify_app_id_usage_month_endpoint_unique
    UNIQUE (user_id, dify_app_id, usage_month, endpoint);

-- team_id でのクエリ用インデックス（将来の拠点合計集計・A07の拠点名フィルタ用途）
CREATE INDEX idx_monthly_api_usages_team_id ON monthly_api_usages (team_id);
CREATE INDEX idx_monthly_api_usages_user_id ON monthly_api_usages (user_id);
```

**集計ロジックのイメージ:**

```
A07画面（管理者: 利用状況一覧）
  → monthly_api_usages WHERE user_id IN (...) GROUP BY user_id, dify_app_id, usage_month
      → ユーザー単位の利用量を一覧表示
      → フィルタで拠点名検索する場合: monthly_api_usages.team_id JOIN teams WHERE name LIKE ? で絞り込み
        （team_id インデックスを活用・記録時点の拠点で絞れるため履歴精度が高い）

U01画面（一般ユーザー: ユーザーダッシュボード）
  → monthly_api_usages WHERE user_id = currentUser->id
      → ログインユーザー自身の利用量を表示

【将来拡張】拠点合計集計
  → monthly_api_usages WHERE team_id = ? GROUP BY team_id, usage_month
      → 同一拠点の全ユーザー分を合算して表示
```

### 3.3 `users` テーブルへの `plan_id` 追加

```sql
ALTER TABLE users
    ADD COLUMN plan_id BIGINT NULL REFERENCES plans(id) ON DELETE SET NULL;

CREATE INDEX idx_users_plan_id ON users (plan_id);
```

### 3.4 `teams` テーブルから `plan_id` 削除

```sql
ALTER TABLE teams DROP COLUMN plan_id;
```

> **注意:** `plan_id` は teams テーブルのマイグレーションではまだ実装されていない（DB定義書では「要追加」状態）。そのため今回の変更で「teamsに追加せず、最初からusersに追加する」という形で実装する。

### 3.5 廃止テーブル：`team_api_keys`

段階的に廃止します。

| フェーズ | タイミング | 内容 |
|---------|-----------|------|
| **フェーズ1**（本変更） | 今回の実装 | `user_api_keys` 新設、`team_api_keys` は参照停止 |
| **フェーズ2**（後日） | 動作確認後 | `team_api_keys` テーブル削除マイグレーション |

---

## 4. 認証フロー変更

### 4.1 `AuthenticateHybrid` ミドルウェア

```php
// 【変更前】
$keyHash = hash('sha256', $request->header('X-Api-Key'));
$apiKey  = TeamApiKey::where('key_hash', $keyHash)
                     ->where('is_active', true)
                     ->with('team')
                     ->first();
if ($apiKey) {
    // チームのOwnerとしてログイン（ユーザー単位の追跡なし）
    Auth::login($apiKey->team->owner());
    $apiKey->touch('last_used_at');
}

// 【変更後】
$keyHash = hash('sha256', $request->header('X-Api-Key'));
$apiKey  = UserApiKey::where('key_hash', $keyHash)
                     ->where('is_active', true)
                     ->with('user')
                     ->first();
if ($apiKey) {
    // そのユーザー自身としてログイン
    Auth::login($apiKey->user);
    $apiKey->touch('last_used_at');
}
```

### 4.2 `CheckMonthlyQuota` ミドルウェアの変更

クォータチェックがユーザーのプランを参照するようになります。

```php
// 【変更前】（チームのプランで判定）
$team      = auth()->user()->currentTeam;
$planLimit = $team->plan->planLimits()
                   ->where('endpoint', $endpoint)->first();
$usage = MonthlyApiUsage::where('team_id', $team->id)
                         ->where('endpoint', $endpoint)
                         ->where('usage_month', now()->format('Y-m'))
                         ->first();

// 【変更後】（ユーザーのプランで判定）
$user      = auth()->user();
$planLimit = $user->plan->planLimits()
                   ->where('endpoint', $endpoint)->first();
$usage = MonthlyApiUsage::where('user_id', $user->id)
                         ->where('endpoint', $endpoint)
                         ->where('usage_month', now()->format('Y-m'))
                         ->first();
```

### 4.3 `DifyProxyController` の変更

```php
// 【変更前】
// user フィールドをチーム名に置換（Dify側ではチーム単位でしか識別できなかった）
$body['user'] = auth()->user()->currentTeam->name;

// 【変更後】
// user フィールドをユーザーメールアドレスに変更（Dify側でユーザー単位の追跡が可能）
$body['user'] = auth()->user()->email;
```

```php
// 【変更前】
// 利用量をチームに記録
MonthlyApiUsage::incrementForTeam(
    teamId: auth()->user()->currentTeam->id,
    difyAppId: $difyApp->id,
    endpoint: $request->path(),
);

// 【変更後】
// 利用量をユーザーに記録（team_id も同時に記録して拠点集計に使う）
MonthlyApiUsage::incrementForUser(
    userId: auth()->user()->id,
    teamId: auth()->user()->currentTeam->id,
    difyAppId: $difyApp->id,
    endpoint: $request->path(),
);
```

---

## 5. 画面変更

### 5.1 A02画面（拠点一覧 → 新規拠点登録モーダル）

**変更: プラン選択がユーザーに移動、TeamApiKey → UserApiKey**

#### 変更前のモーダルフォーム

```
[新規拠点登録モーダル]
├ 拠点名
├ 契約プラン  ← チームに紐づく
├ 管理者名
├ 管理者メール
└ APIキー（自動生成）← team_api_keys
```

#### 変更後のモーダルフォーム

```
[新規拠点登録モーダル]
├ 拠点名
├ 管理者名
├ 管理者メール
├ 契約プラン  ← ユーザーに紐づく（概念は同じ、保存先が変わる）
└ APIキー（自動生成）← user_api_keys
```

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| プランの保存先 | `teams.plan_id` | `users.plan_id` |
| APIキー生成対象 | `team_api_keys` | `user_api_keys` |
| UIラベル・フォーム項目 | 変更なし | 変更なし（順序が入れ替わる程度） |

**処理フロー（変更後）:**

```
1. フォーム入力: 拠点名 + 管理者名 + メール + 契約プラン + APIキー（自動生成）
2. Team レコード作成（plan_id なし）
3. User レコード作成（plan_id あり、current_team_id を紐づけ）
4. UserApiKey レコード作成（そのユーザーに紐づけ）
   ※ TeamApiKey は作成しない
```

### 5.2 A03画面（拠点編集）

**変更: APIキー管理セクションをページ独立セクションからユーザー編集モーダル内へ移動**

#### 変更前の画面構成

```
[Section 2] 基本情報（拠点名、プラン）
[Section 3] 所属ユーザー一覧
    → [編集] → ユーザー編集モーダル
        ├ お名前
        └ メールアドレス
[Section 4] APIキー管理 ← ← ← ページ独立セクション（拠点に1つ）
    ├ マスク表示 + 表示 + コピー + 再生成
    └ 最終利用日時
[Section 5] Danger Zone
```

#### 変更後の画面構成

```
[Section 2] 基本情報（拠点名のみ）← プランは廃止
[Section 3] 所属ユーザー一覧
    → [ユーザー追加] → ユーザー追加モーダル
        ├ お名前
        ├ メールアドレス
        ├ 初期パスワード
        ├ 契約プラン（SelectInput）← ユーザーに紐づくプランをここで設定
        └ APIキー（自動生成 + 表示 + コピー）← 新規追加時はここで発行
    → [編集] → ユーザー編集モーダル ← ← ← APIキー管理 + プランもここへ移動
        ├ お名前
        ├ メールアドレス
        ├ 契約プラン（SelectInput）← プランの変更もここで行う
        └ APIキー管理セクション
            ├ マスク表示 + 表示ボタン + コピーボタン
            ├ 最終利用日時（YYYY-MM-DD HH:MM:SS）
            └ 再生成ボタン → 確認モーダル → 新規キー発行
[Section 4] Danger Zone  ← 旧 Section 5（Section 4 は廃止）
```

#### 変更内容の詳細

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| 基本情報セクションの内容 | 拠点名 + プラン | **拠点名のみ** |
| プランの管理場所 | 基本情報セクション（拠点レベル） | **ユーザー編集/追加モーダル内** |
| APIキー管理の場所 | ページ独立セクション（Section 4） | **ユーザー編集モーダル内** |
| APIキーの所有者 | 拠点（`team_api_keys`） | ユーザー（`user_api_keys`） |
| 表示するキー | `$team->teamApiKeys()->first()` | `$editingUser->userApiKeys()->first()` |
| プランの参照 | `$team->plan` | `$editingUser->plan` |
| 再生成対象 | `TeamApiKey` | `UserApiKey`（編集中ユーザーの） |
| 新規ユーザー追加時 | APIキー生成なし（拠点に1つあるため） | ユーザー追加モーダルで自動生成し表示 |
| Livewire の状態管理 | `$showApiKey`, `$newApiKey`, `$plan_id` がコンポーネント直下 | `$editUserShowApiKey`, `$editUserNewApiKey`, `$editUserPlanId` をユーザーモーダルスコープに移動 |

#### Livewire コンポーネントの変更イメージ

```php
// 変更前
public $showApiKey = false;    // ページレベル
public $newApiKey  = null;     // ページレベル

// 変更後
public $editUserShowApiKey = false;   // ユーザー編集モーダルスコープ
public $editUserNewApiKey  = null;    // ユーザー編集モーダルスコープ
public $newUserApiKey      = null;    // ユーザー追加モーダルスコープ（初回表示用）
```

#### 使用コンポーネントの変更

| コンポーネント | 変更前 | 変更後 |
|-------------|--------|--------|
| `ApiKeyField` | Section 4（ページ独立） | ユーザー編集モーダル内 |
| `ConfirmModal`（再生成確認） | ページレベルで表示 | ユーザー編集モーダル内でネスト |

### 5.3 A04画面（CSV一括登録）

**変更: CSV の `plan` カラムがユーザーへの紐づけに変わる（CSVフォーマット・UIは変更なし）**

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| CSVカラム | `name, plan, owner_name, email`（4列） | **変更なし** |
| `plan` カラムの意味 | 拠点のプラン（`teams.plan_id`） | **ユーザーのプラン**（`users.plan_id`） |
| APIキー生成先 | `TeamApiKey::create(['team_id' => $team->id])` | `UserApiKey::create(['user_id' => $user->id])` |
| プラン保存先 | `$team->plan_id = $plan->id` | `$user->plan_id = $plan->id` |
| 画面上の注記 | 「※APIキーとパスワードは自動生成されます」 | **変更なし** |

**インポート処理フロー（変更後）:**

```
CSVの1行 = name, plan, owner_name, email
  ↓
1. Team::create(['name' => $name])          // plan_id なし
2. User::create([                           // plan_id あり
       'name'    => $owner_name,
       'email'   => $email,
       'plan_id' => $plan->id,
   ])
3. UserApiKey::create(['user_id' => $user->id, ...])  // ユーザーに紐づけ
```

### 5.4 A07画面（利用状況一覧）

**変更: テーブル構造は維持しつつ、行の主体がユーザーに変わる**

#### 現在のモックのテーブル構造（変更前）

```
[フィルタ] 対象年月 | 拠点名 | Difyアプリ | 制限超過のみ

| 年月    | 拠点名/プラン         | 利用アプリ | 利用状況              | 最終更新   | 操作 |
|---------|----------------------|------------|----------------------|------------|------|
| 2026-02 | 札幌支店 / Standard  | 翻訳Bot    | 200/1000 [████░░░░] | 2026-02-13 | 修正 |
         ↑ team.name / team.plan.name       ↑ team.plan.plan_limits から上限取得
```

#### 変更後のテーブル構造

```
[フィルタ] 対象年月 | ユーザー名（追加） | 拠点名（維持） | Difyアプリ | 制限超過のみ

| 年月    | ユーザー / プラン             | 利用アプリ | 利用状況              | 最終更新   | 操作 |
|---------|------------------------------|------------|----------------------|------------|------|
| 2026-02 | 山田太郎（札幌支店）/ Standard | 翻訳Bot   | 200/1000 [████░░░░] | 2026-02-13 | 修正 |
         ↑ user.name（team.name）/ user.plan.name  ↑ user.plan.plan_limits から上限取得
```

#### 変更内容の詳細

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| テーブルのカラム構成 | 年月/拠点名・プラン/アプリ/利用状況/最終更新/操作 | **同じ**（カラム数・順序変更なし） |
| 「拠点名/プラン」カラムの内容 | `team.name` / `team.plan.name` | **`user.name`（`team.name`）/ `user.plan.name`** |
| フィルタ「拠点名」検索 | `team.name` 部分一致 | **維持**（`team.name` 部分一致） |
| フィルタ「ユーザー名」検索 | なし | **新規追加**（`user.name` 部分一致） |
| 両フィルタの組み合わせ | - | AND 条件（両方入力時は絞り込み） |
| プラン上限の参照 | `team.plan.plan_limits` | **`user.plan.plan_limits`** |
| データクエリの集計キー | `monthly_api_usages.team_id` | **`monthly_api_usages.user_id`** |
| ProgressBar の上限値 | チームのプラン上限 | **ユーザーのプラン上限** |
| CSV出力ヘッダー | 年月, 拠点名, プラン, アプリ名, 利用回数, 月間上限, 利用率, 最終更新 | 年月, **ユーザー名**, 拠点名, プラン, アプリ名, 利用回数, 月間上限, 利用率, 最終更新 |

**クエリのイメージ（変更後）:**
```php
MonthlyApiUsage::query()
    ->when($this->month, fn($q) => $q->where('usage_month', $this->month))
    ->when($this->userSearch, fn($q) => $q->whereHas('user', fn($q) =>    // 新規追加
        $q->where('name', 'like', "%{$this->userSearch}%")
    ))
    ->when($this->teamSearch, fn($q) => $q->whereHas('team', fn($q) =>  // team_id 起点（インデックス活用・記録時点の拠点で絞れる）
        $q->where('name', 'like', "%{$this->teamSearch}%")
    ))
    ->when($this->appFilter, fn($q) => $q->where('dify_app_id', $this->appFilter))
    ->when($this->overLimitOnly, ...)
    ->with(['user.plan', 'user.currentTeam', 'difyApp'])
    ->paginate(20);
```

**将来対応（拠点合計集計）:**
- フィルタに「拠点別集計」トグルを追加し、`team_id` でGROUP BY した合計行を表示できるビューを追加

### 5.5 U01画面（ユーザーダッシュボード）

**変更: 「拠点ダッシュボード」→「ユーザーダッシュボード」に性格が変わる**

#### 変更前

```
画面名: 拠点ダッシュボード
表示内容: 自チームの利用状況（team_id で集計）
          → 同じ拠点の全ユーザーの合算利用量
          → team.plan の上限と比較
```

#### 変更後

```
画面名: ユーザーダッシュボード
表示内容: 自分自身の利用状況（user_id で集計）
          → 自分のリクエスト数
          → user.plan の上限と比較
```

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| 画面名（ID） | 拠点ダッシュボード（U01） | **ユーザーダッシュボード（U01）** |
| URL | `/dashboard` | 変更なし |
| 表示する利用量 | チーム全体の合算 | **ログインユーザー自身の利用量** |
| プラン上限参照 | `team.plan.plan_limits` | **`user.plan.plan_limits`** |
| 集計キー | `monthly_api_usages WHERE team_id = ?` | **`monthly_api_usages WHERE user_id = ?`** |
| 所属拠点の表示 | 主役 | ユーザー情報の付帯情報として表示（「〇〇支店 所属」等） |

### 5.6 A01画面（管理者ダッシュボード）への影響

A01 の「利用率の高い拠点一覧」テーブルをユーザーベースに変更します。

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| セクション名 | 「利用率の高い拠点一覧」 | **「利用率の高いユーザー一覧」** |
| 一覧の行単位 | 拠点（`team.name` + `team.plan.name`） | **ユーザー**（`user.name` + `user.plan.name`） |
| テーブル列 | 拠点名 / プラン / 利用率 / 操作 | **ユーザー名 / 拠点名 / プラン / 利用率 / 操作** |
| KPI「契約拠点数」 | `teams.count()` | **変更なし**（拠点数のカウントは継続） |
| 利用率の計算 | `team_id` ベース | **`user_id` ベース** |

### 5.7 A08画面（利用回数修正モーダル）

**変更: 対象の「拠点」表示 → 「ユーザー」表示に変更**

#### 変更前の対象データ情報表示

```
[対象データ確認エリア]
├ 対象年月: 2023-10
├ 契約プラン: Standard Plan (100,000 limit)
└ 拠点 (Team): 東京本社 営業部  |  アプリ (App): カスタマーサポートBot
```

#### 変更後の対象データ情報表示

```
[対象データ確認エリア]
├ 対象年月: 2023-10
├ 契約プラン (ユーザー): Standard Plan (100,000 limit)  ← ユーザーのプランであることを明示
└ ユーザー (User): 田中 太郎    |  アプリ (App): カスタマーサポートBot
                  東京本社 営業部  ← 拠点名はサブテキストとして残す
```

#### 変更内容の詳細

| 項目 | 変更前 | 変更後 |
|------|--------|--------|
| data モデル | `{ team, plan, app, limit, currentCount }` | `{ user, team, plan, app, limit, currentCount }` （`user` フィールド追加） |
| 「対象拠点」ラベル | 「拠点 (Team)」 | **「ユーザー (User)」** |
| 拠点名の表示位置 | 主表示 | ユーザー名の下のサブテキスト（グレー小文字） |
| 「契約プラン」ラベル | 「契約プラン」 | **「契約プラン (ユーザー)」** |
| プランの参照先 | `team.plan.plan_limits` | **`user.plan.plan_limits`** |
| プラン上限の計算 | チームのプラン上限 | **ユーザーのプラン上限** |
| 修正対象レコード | `monthly_api_usages WHERE team_id = ?` | **`monthly_api_usages WHERE user_id = ?`** |
| 機能（修正・差分・理由） | 変更なし | **変更なし** |

#### Livewire コンポーネントの変更イメージ

```php
// 変更前
public $usage;  // monthly_api_usages レコード（team_id ベース）

// 変更後
// usage->user_id を使って対象ユーザーを取得
public $usage;  // monthly_api_usages レコード（user_id ベース）

// 対象データの表示プロパティ
public function getTargetUserProperty(): User
{
    return $this->usage->user;  // user リレーション経由
}

public function getTargetTeamProperty(): Team
{
    return $this->usage->user->currentTeam;  // ユーザーの所属拠点
}
```

#### 補足：修正処理への影響

A08は「利用回数の手動補正」モーダルのため、修正ロジック自体への影響は軽微です。

```php
// 変更前
MonthlyApiUsage::where('team_id', $teamId)
    ->where('dify_app_id', $difyAppId)
    ->where('usage_month', $usageMonth)
    ->update(['request_count' => $newCount]);

// 変更後
MonthlyApiUsage::where('user_id', $userId)    // user_id に変更
    ->where('dify_app_id', $difyAppId)
    ->where('usage_month', $usageMonth)
    ->update(['request_count' => $newCount]);
```

---

### 5.8 A09画面（災害復旧エクスポート）

**変更: エクスポートJSONのAPIキーデータ構造を変更**

```json
// 【変更前】
{
  "teams": [
    {
      "name": "札幌支店",
      "plan": "standard",
      "api_keys": [
        { "name": "Gateway_01", "key": "sk_live_xxxx..." }
      ],
      "users": [
        { "email": "sapporo@example.com" }
      ]
    }
  ]
}

// 【変更後】
// ※ teams.plan_id 廃止のため team レベルの "plan" は削除し、user レベルに移動
{
  "teams": [
    {
      "name": "札幌支店",
      "users": [
        {
          "email": "sapporo@example.com",
          "plan": "standard",
          "api_keys": [
            { "name": "default", "key": "sk_live_xxxx..." }
          ]
        }
      ]
    }
  ]
}
```

---

## 6. モデル変更

### 6.1 新規モデル：`UserApiKey`

```php
// app/Models/UserApiKey.php
class UserApiKey extends Model
{
    protected $fillable = [
        'user_id', 'name', 'key_hash', 'key_encrypted',
        'is_active', 'last_used_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'last_used_at' => 'datetime',
            'expires_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### 6.2 `User` モデルへのリレーション追加

```php
// app/Models/User.php に追加
public function userApiKeys(): HasMany
{
    return $this->hasMany(UserApiKey::class);
}

public function plan(): BelongsTo
{
    return $this->belongsTo(Plan::class);
}
```

### 6.3 `Team` モデルの変更

```php
// plan() リレーション削除（またはdeprecated）
// app/Models/Team.php
// public function plan(): BelongsTo  ← 削除
```

### 6.4 `MonthlyApiUsage` モデルの変更

```php
protected $fillable = [
    'user_id',   // 追加
    'team_id',
    'dify_app_id',
    'usage_month',
    'endpoint',
    'request_count',
    'last_request_at',
];

// ユーザー単位でインクリメントするメソッド
public static function incrementForUser(
    int $userId,
    int $teamId,
    int $difyAppId,
    string $endpoint,
): void {
    static::updateOrCreate(
        [
            'user_id'     => $userId,
            'dify_app_id' => $difyAppId,
            'usage_month' => now()->format('Y-m'),
            'endpoint'    => $endpoint,
        ],
        ['team_id' => $teamId]  // 拠点集計用に記録
    )->increment('request_count');
}
```

---

## 7. テスト変更への影響

### 7.1 影響を受けるテストファイル

| テストファイル | 影響度 | 変更内容 |
|------------|-------|---------|
| `tests/Feature/Admin/TeamListTest.php` | 🔴 High | TeamApiKey → UserApiKey、生成先ユーザーに変更 |
| `tests/Feature/Admin/TeamEditTest.php` | 🔴 High | APIキー操作がUserApiKey参照に変更 |
| `tests/Feature/Admin/CsvImportTest.php` | 🟠 Medium | UserApiKey生成ロジックに変更 |
| `tests/Feature/Admin/DrExportTest.php` | 🟠 Medium | エクスポートJSON構造の変更 |
| `tests/Feature/Admin/UsageListTest.php` | 🟠 Medium | monthly_api_usages に user_id が加わる |
| `tests/Feature/Admin/UsageEditModalTest.php` | 🟠 Medium | 修正対象が user_id ベースに変更 |
| `tests/Feature/Admin/DashboardTest.php` | 🟠 Medium | A01: 利用率テーブルがユーザー行に変更 |
| `tests/Feature/User/DashboardTest.php` | 🟠 Medium | U01: user_id 集計・user.plan 参照に変更 |
| `tests/Unit/Components/ApiKeyFieldTest.php` | 🟡 Low | 参照先のみ変更（UIは同じ） |

### 7.2 テストケース定義書（04_画面テストケース定義書.md）の更新箇所

| 箇所 | 変更前 | 変更後 |
|------|--------|--------|
| AC-A02-204 | `TeamApiKey` が作成される | `UserApiKey` が作成される |
| AC-A02-504 | SHA-256 + 暗号化保存（`team_api_keys`） | SHA-256 + 暗号化保存（`user_api_keys`） |
| AC-A03-501 | 古い `TeamApiKey` が無効化 | 古い `UserApiKey` が無効化 |
| AC-A03-502 | `key_hash`/`key_encrypted` 保存（チーム） | `key_hash`/`key_encrypted` 保存（ユーザー） |
| AC-A04-502 | `TeamApiKey` 自動生成 | `UserApiKey` 自動生成 |
| AC-A08-xxx | 修正対象: `team_id` で monthly_api_usages を特定 | 修正対象: `user_id` で monthly_api_usages を特定 |
| AC-A09-xxx | エクスポートJSON: `api_keys` at team level | エクスポートJSON: `api_keys` at user level, team の `plan` 削除 |
| AC-A01-xxx | 「利用率の高い拠点一覧」拠点行表示 | 「利用率の高いユーザー一覧」ユーザー行表示（ユーザー名・拠点名・プラン） |
| AC-A07-xxx | `team_id` ベース集計・フィルタは拠点名のみ | `user_id` ベース集計・フィルタにユーザー名追加（AND条件）、プラン上限を `user.plan` 参照 |
| AC-U01-xxx | 拠点ダッシュボード・`team_id` ベース集計 | ユーザーダッシュボード・`user_id` ベース集計・`user.plan` 上限参照 |

---

## 8. マイグレーション実装計画

### 8.1 実装順序

```
Step 1: user_api_keys テーブル作成マイグレーション
        vendor/bin/sail artisan make:migration create_user_api_keys_table

Step 2: users テーブルに plan_id 追加マイグレーション
        vendor/bin/sail artisan make:migration add_plan_id_to_users_table
        ※ teams への plan_id 追加は行わない（最初からユーザーに持たせる）

Step 3: monthly_api_usages に user_id 追加（UNIQUE制約変更含む）
        vendor/bin/sail artisan make:migration add_user_id_to_monthly_api_usages_table

Step 4: UserApiKey モデル + Factory 作成
        vendor/bin/sail artisan make:model UserApiKey --factory

Step 5: User モデルに userApiKeys() / plan() リレーション追加

Step 6: Team モデルから plan() リレーション削除

Step 7: MonthlyApiUsage モデルに user_id 追加 + incrementForUser() 実装

Step 8: AuthenticateHybrid ミドルウェア
        - user_api_keys に照合先を変更

Step 9: CheckMonthlyQuota ミドルウェア
        - user.plan.planLimits を参照するように変更

Step 10: DifyProxyController
        - user フィールド置換: team.name → user.email
        - incrementForUser() に切り替え

Step 11: A02/A03/A04/A09 画面・ロジック変更

Step 12: テスト修正・実行
```

### 8.2 実装計画マスタープラン（v1.4）への影響

| Phase | 影響 | 対応 |
|-------|------|------|
| Phase 0（UIコンポーネント） | 影響なし | 変更不要 |
| Phase 1（認証機能 G01/G02） | 影響なし | 変更不要 |
| Phase 2（A01+U01画面） | U01の集計クエリが変わる | 軽微な変更 |
| Phase 3（A04用コンポーネント） | `TeamApiKey` → `UserApiKey` | Factory更新 |
| Phase 4（A04画面） | CSVインポートロジック変更 | 仕様更新 |
| Phase 8（A03用コンポーネント） | `ApiKeyField` 参照先変更 | 軽微な変更 |
| Phase 9（A03画面） | APIキー管理セクション参照先 | 仕様更新 |
| Phase 10（A02画面） | 新規登録モーダルのキー生成 | 仕様更新 |
| Phase 12（A09画面） | エクスポートJSON構造変更 | 仕様更新 |

---

## 9. 変更による効果

| 効果 | 説明 |
|------|------|
| **個人認証の実現** | 誰がAPIを呼んだか特定可能 |
| **細粒度のアクセス制御** | 特定ユーザーのキーのみ無効化可能 |
| **スケーラビリティ** | 拠点に複数ユーザーを追加しても管理可能 |
| **セキュリティ向上** | 退職者・異動者のアクセスを即座に無効化 |
| **Dify側のトレーサビリティ** | `user` フィールドをユーザーメールにすることでDify側でも誰の利用か把握可能 |
| **将来の機能拡張** | ユーザー別利用量レポート（A07拡張）が容易になる |

---

## 📝 変更履歴

| 日付 | バージョン | 変更内容 |
|------|----------|---------|
| 2026-02-13 | 1.0 | 初版作成 |
| 2026-02-13 | 1.1 | ユーザーをシステムの基本単位に変更。monthly_api_usages の UNIQUE 制約を user_id ベースに変更。Dify の user フィールドをメールアドレスに確定。ロール区分なしの方針を明記。 |
| 2026-02-13 | 1.2 | A03画面のAPIキー管理セクション（Section 4）を廃止し、ユーザー編集モーダル内へ移動。ユーザー追加モーダルでもAPIキー発行・表示に対応。Livewireの状態管理変更を追記。 |
| 2026-02-13 | 1.3 | plan_id を teams から users へ移動。CheckMonthlyQuota のクォータ参照をユーザープランに変更。A02/A03/A04 の画面変更にプラン移動を反映。拠点はユーザーのグルーピング名前空間と位置づけを明確化。 |
| 2026-02-13 | 1.4 | A07画面の変更を正確に記述。既存モックのテーブル構造（年月/拠点名・プラン/アプリ/利用状況/操作）を維持したまま、行の主体をユーザーに変更。フィルタ「拠点名」→「ユーザー名」検索に変更。プラン上限参照を user.plan.plan_limits に変更。 |
| 2026-02-13 | 1.5 | U01画面を「ユーザーダッシュボード」に変更（拠点合算→ユーザー個人の利用量表示）。A01管理者ダッシュボードの「利用率の高い拠点一覧」もユーザーベースへの変更を追記。 |
| 2026-02-13 | 1.6 | A07フィルタ修正。「拠点名」検索を維持しつつ「ユーザー名」検索を新規追加（AND条件）。CSVヘッダーにユーザー名列を追加。 |
| 2026-02-13 | 1.7 | A08画面（利用回数修正モーダル）の変更を追記。対象表示を「拠点」→「ユーザー（拠点名）」に変更、契約プランラベルに「(ユーザー)」を明示。重複していたセクション番号 5.6 を 5.7 に修正（A09）。 |
| 2026-02-18 | 1.8 | 確定方針への整合修正。①3.2集計ロジックをuser_id集計に統一（A07/U01）。②A01をユーザー行確定表記に変更（「または拠点合計（将来対応）」を削除）。③A09 JSONの変更後からteamレベルの"plan"を削除しuserレベルへ移動（teams.plan_id廃止と整合）。④テスト一覧のUsageEditModalTest/DashboardTest追加・誤記修正。⑤セクション番号重複を完全修正（A08=5.7、A09=5.8）。⑥最終更新日を2026-02-18に更新。 |
| 2026-02-18 | 1.9 | 軽微な整合修正。①SQLインデックスコメントを「将来の拠点合計集計・A07の拠点名フィルタ用途」に更新。②3.2集計ロジックのA07拠点名フィルタを `user.currentTeam` 起点から `monthly_api_usages.team_id JOIN teams` 起点に統一（team_id インデックス活用・記録時点の拠点で絞れる点を明記）。③5.4 A07のクエリ例も同方針に統一（whereHas('user.currentTeam') → whereHas('team')）。 |

---

**⚠️ 本提案の確認・承認をお願いします。承認後、設計資料（テストケース定義書・UI設計書）への反映と、実装計画マスタープランへの追記を行います。**
