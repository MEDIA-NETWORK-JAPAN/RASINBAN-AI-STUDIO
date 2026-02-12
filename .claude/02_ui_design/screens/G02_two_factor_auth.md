# G02: 二段階認証コード入力

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | G02 |
| 画面名 | 二段階認証コード入力 |
| URL | `/two-factor-challenge` |
| モック | [mocks/G02_two_factor_auth.html](../mocks/G02_two_factor_auth.html) |

## 概要

管理者（is_admin=true）がログインした際、メールアドレス・パスワード認証後に表示される二段階認証コード入力画面。ユーザーID=1の管理者のメールアドレスに送信された6桁のOTPコードを入力して認証を完了する。

**対象ユーザー:** 管理者のみ（一般ユーザーはこの画面をスキップ）

**セキュリティ仕様:** どの管理者がログインしようとした場合でも、OTPコードは常にユーザーID=1（スーパー管理者）のメールアドレスにのみ送信される。

## 使用コンポーネント

- `layout/GuestLayout`
- `forms/TextInput` (number, 6桁)
- `buttons/Button`
- `feedback/AlertBanner`
- `feedback/Toast`

## 画面構成

### 1. ヘッダーエリア

- ロゴ
- タイトル: 「二段階認証」
- 説明: 「システム管理者（ユーザーID=1）のメールアドレスに6桁の認証コードを送信しました。」

### 2. コード入力エリア

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| 認証コード | TextInput (number) | 6桁の数字、中央揃え、大きめフォント |

### 3. アクションボタン

| ボタン | 説明 |
|--------|------|
| 認証する | コードを検証してログイン完了 |
| コードを再送信 | 新しいOTPコードをメール送信（ユーザーID=1へ） |
| ログアウト | 認証をキャンセルしてログイン画面へ戻る |

### 4. 注記

- 有効期限: 「このコードは10分間有効です。」
- セキュリティ注意: 「コードを他人に教えないでください。」
- メール受信者: 「※コードはユーザーID=1の管理者に送信されています。受信していない場合は管理者にお問い合わせください。」

## データ取得

```php
// Livewire Component
public $code = '';
public $attempts = 0;
public $maxAttempts = 5;

public function mount()
{
    // セッションに「二段階認証待ち」フラグがない場合はログイン画面へリダイレクト
    if (!session('two_factor_pending')) {
        return redirect()->route('login');
    }
}

public function getTokenProperty()
{
    return TwoFactorToken::where('user_id', session('two_factor_user_id'))
        ->where('expires_at', '>', now())
        ->first();
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| 認証コード入力 | 6桁の数字のみ受付、自動フォーマット |
| 「認証する」ボタンクリック | コード検証 → 成功時: A01へリダイレクト、失敗時: エラー表示 + 試行回数カウント |
| 「コードを再送信」ボタンクリック | 既存トークンを削除 → 新しいOTPコード生成 → ユーザーID=1へメール送信 → トースト表示「新しいコードを送信しました」 |
| 「ログアウト」ボタンクリック | セッションをクリア → G01へリダイレクト |
| 5回連続失敗 | トークン無効化 → セッションクリア → G01へリダイレクト + エラーメッセージ |

## Livewire実装

```php
// app/Livewire/Auth/TwoFactorChallenge.php
class TwoFactorChallenge extends Component
{
    public $code = '';

    protected $rules = [
        'code' => 'required|digits:6',
    ];

    public function mount()
    {
        // 二段階認証待ち状態でない場合はログイン画面へ
        if (!session('two_factor_pending')) {
            return redirect()->route('login');
        }
    }

    public function verify()
    {
        $this->validate();

        $loginUserId = session('two_factor_user_id');
        $token = TwoFactorToken::where('user_id', $loginUserId)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            session()->flash('error', '認証コードの有効期限が切れています。再度ログインしてください。');
            return $this->logout();
        }

        // 試行回数チェック
        if ($token->attempts >= 5) {
            session()->flash('error', '試行回数の上限に達しました。再度ログインしてください。');
            $token->delete();
            return $this->logout();
        }

        // コード検証
        if ($token->token !== $this->code) {
            $token->increment('attempts');
            $this->addError('code', '認証コードが正しくありません。');
            $this->code = '';
            return;
        }

        // 検証成功
        $token->delete();
        session()->forget(['two_factor_pending', 'two_factor_user_id']);

        // ユーザーをログイン状態にする
        $user = User::find($loginUserId);
        Auth::login($user);

        return redirect()->intended(route('admin.dashboard'));
    }

    public function resend()
    {
        $loginUserId = session('two_factor_user_id');
        $loginUser = User::find($loginUserId);

        // ユーザーID=1の管理者を取得
        $superAdmin = User::find(1);

        if (!$superAdmin) {
            $this->addError('code', 'システムエラー: スーパー管理者が見つかりません。');
            return;
        }

        // 既存トークンを削除
        TwoFactorToken::where('user_id', $loginUserId)->delete();

        // 新しいトークン生成
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        TwoFactorToken::create([
            'user_id' => $loginUserId,
            'token' => $code,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        // ユーザーID=1の管理者へメール送信
        Mail::to($superAdmin->email)->send(new TwoFactorCodeMail(
            code: $code,
            superAdminName: $superAdmin->name,
            loginUser: $loginUser
        ));

        $this->dispatch('toast', type: 'success', message: '新しいコードをメールに送信しました');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| 認証コード | required, digits:6 |

## エラーハンドリング

| エラー条件 | メッセージ | 動作 |
|-----------|----------|------|
| コードが空 | 「認証コードを入力してください」 | バリデーションエラー表示 |
| コードが6桁でない | 「6桁の認証コードを入力してください」 | バリデーションエラー表示 |
| コードが間違っている | 「認証コードが正しくありません」 | エラー表示 + 試行回数カウント |
| 有効期限切れ | 「認証コードの有効期限が切れています。再度ログインしてください。」 | G01へリダイレクト |
| 試行回数超過（5回） | 「試行回数の上限に達しました。再度ログインしてください。」 | トークン削除 + G01へリダイレクト |
| スーパー管理者不在 | 「システムエラー: スーパー管理者が見つかりません。」 | エラー表示 |

## セキュリティ仕様

- **送信先固定:** OTPコードは常にユーザーID=1のメールアドレスにのみ送信。
- **セッション管理:** 二段階認証待ち状態は`two_factor_pending`と`two_factor_user_id`をセッションに保存。
- **CSRF保護:** Livewireが自動的にCSRFトークンを検証。
- **試行回数制限:** 最大5回まで。超過時はトークンを無効化してログアウト。
- **有効期限:** トークンは10分間のみ有効。
- **トークン再利用防止:** 検証成功時にトークンを削除。
- **ログイン試行者追跡:** メールにログイン試行者の情報（ID、名前、メールアドレス）を含める。

## メール送信仕様

**メールクラス:** `App\Mail\TwoFactorCodeMail`

**送信先:** ユーザーID=1の管理者のメールアドレス（固定）

**件名:** `[rasinban-ai-studio] 管理者ログイン - 二段階認証コード`

**本文:**
```
{ユーザーID=1の管理者名} 様

管理者ログインの二段階認証コードをお送りします。

【ログイン試行者】
ユーザーID: {ログイン試行者のID}
ユーザー名: {ログイン試行者の名前}
メールアドレス: {ログイン試行者のメールアドレス}

【認証コード】
{OTPコード}

このコードは10分間有効です。
ログイン画面でコードを入力してログインを完了してください。

※このログイン試行に心当たりがない場合は、不正アクセスの可能性があります。速やかにパスワードを変更してください。
```

## 認証フロー全体

1. **G01: ログイン画面** - メールアドレス・パスワード入力
2. **Fortify認証** - ユーザー認証成功
3. **is_admin チェック:**
   - `is_admin = true` → OTPコード生成 → **ユーザーID=1へメール送信** → セッションに「二段階認証待ち」フラグをセット → **G02へリダイレクト**
   - `is_admin = false` → U01へリダイレクト（二段階認証スキップ）
4. **G02: 二段階認証コード入力** - OTPコード検証
5. **検証成功** → A01へリダイレクト
6. **検証失敗（5回）** → G01へリダイレクト

## 注意事項

- 一般ユーザー（is_admin=false）はこの画面を経由せず、ログイン成功後に直接U01へリダイレクト。
- トークンは`two_factor_tokens`テーブルで管理。
- セッションに「二段階認証待ち」フラグがない状態でG02にアクセスした場合は、G01へリダイレクト。
- 有効期限切れトークンの自動削除は、Artisanコマンドまたはスケジューラーで定期実行を推奨。
- **重要:** ユーザーID=1の管理者が存在しない場合、二段階認証は機能しない。システムセットアップ時に必ずユーザーID=1の管理者アカウントを作成すること。
