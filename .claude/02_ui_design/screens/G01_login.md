# G01: ログイン

## 基本情報

| 項目 | 値 |
|------|-----|
| 画面ID | G01 |
| 画面名 | ログイン |
| URL | `/login` |
| モック | [mocks/G01_login.html](../mocks/G01_login.html) |

## 概要

システムへのログイン画面。メールアドレスとパスワードで認証を行う。

**対象ユーザー:** 全ユーザー（管理者・一般ユーザー共通）

**認証フロー:**
1. メールアドレス・パスワード入力 → Fortify認証
2. 認証成功 → `is_admin` チェック
   - `is_admin = true` → OTPコード生成 → ユーザーID=1へメール送信 → **G02へリダイレクト**
   - `is_admin = false` → **U01へリダイレクト**
3. 認証失敗 → エラーメッセージ表示

## 使用コンポーネント

- `layout/GuestLayout`
- `forms/TextInput` (email)
- `forms/PasswordInput`
- `buttons/Button` (primary)
- `feedback/AlertBanner`

## 画面構成

### 1. ヘッダーエリア

- ロゴ（アイコン）
- タイトル: 「Dify Gateway」
- サブタイトル: 「rasinban-ai-studio」

### 2. ログインフォーム

| フィールド | タイプ | 説明 |
|-----------|--------|------|
| メールアドレス | TextInput (email) | ユーザーのメールアドレス |
| パスワード | PasswordInput | ユーザーのパスワード |

### 3. アクションボタン

| ボタン | 説明 |
|--------|------|
| ログイン | 認証を実行してシステムへログイン |

### 4. エラーメッセージ

認証失敗時に表示：
- 「メールアドレスまたはパスワードが正しくありません。」

## データ取得

```php
// Livewire Component (Laravel Fortify使用)
public $email = '';
public $password = '';
public $errorMessage = '';

public function authenticate()
{
    $this->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
        $user = Auth::user();

        if ($user->is_admin) {
            // 管理者の場合：二段階認証
            $this->generateAndSendOtp($user);
            session(['two_factor_pending' => true, 'two_factor_user_id' => $user->id]);
            Auth::logout(); // 一旦ログアウト
            return redirect()->route('two-factor.challenge');
        } else {
            // 一般ユーザーの場合：直接ダッシュボードへ
            return redirect()->intended(route('user.dashboard'));
        }
    } else {
        $this->errorMessage = 'メールアドレスまたはパスワードが正しくありません。';
    }
}

protected function generateAndSendOtp($loginUser)
{
    $superAdmin = User::find(1);
    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    TwoFactorToken::create([
        'user_id' => $loginUser->id,
        'token' => $code,
        'expires_at' => now()->addMinutes(10),
        'attempts' => 0,
    ]);

    Mail::to($superAdmin->email)->send(new TwoFactorCodeMail(
        code: $code,
        superAdminName: $superAdmin->name,
        loginUser: $loginUser
    ));
}
```

## インタラクション

| 操作 | 動作 |
|------|------|
| メールアドレス入力 | バリデーション: email形式チェック |
| パスワード入力 | マスク表示（type=password） |
| 「ログイン」ボタンクリック | 認証実行 → 成功時: G02またはU01へリダイレクト、失敗時: エラー表示 |
| Enterキー押下 | ログインボタンと同じ動作 |

## Livewire実装

```php
// app/Livewire/Auth/Login.php
class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    public function authenticate()
    {
        $this->validate();

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $user = Auth::user();

            if ($user->is_admin) {
                // 管理者：二段階認証フローへ
                $this->generateAndSendOtp($user);
                session(['two_factor_pending' => true, 'two_factor_user_id' => $user->id]);
                Auth::logout();
                return redirect()->route('two-factor.challenge');
            } else {
                // 一般ユーザー：直接ダッシュボードへ
                return redirect()->intended(route('user.dashboard'));
            }
        }

        $this->addError('email', 'メールアドレスまたはパスワードが正しくありません。');
    }

    protected function generateAndSendOtp($loginUser)
    {
        $superAdmin = User::find(1);

        if (!$superAdmin) {
            $this->addError('email', 'システムエラーが発生しました。管理者にお問い合わせください。');
            return;
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        TwoFactorToken::create([
            'user_id' => $loginUser->id,
            'token' => $code,
            'expires_at' => now()->addMinutes(10),
            'attempts' => 0,
        ]);

        Mail::to($superAdmin->email)->send(new TwoFactorCodeMail(
            code: $code,
            superAdminName: $superAdmin->name,
            loginUser: $loginUser
        ));
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.guest');
    }
}
```

## バリデーション

| フィールド | ルール |
|-----------|--------|
| メールアドレス | required, email |
| パスワード | required |

## エラーハンドリング

| エラー条件 | メッセージ | 動作 |
|-----------|----------|------|
| メールアドレスが空 | 「メールアドレスを入力してください」 | バリデーションエラー表示 |
| メールアドレス形式が不正 | 「正しいメールアドレスを入力してください」 | バリデーションエラー表示 |
| パスワードが空 | 「パスワードを入力してください」 | バリデーションエラー表示 |
| 認証失敗 | 「メールアドレスまたはパスワードが正しくありません。」 | エラーメッセージ表示 |
| スーパー管理者不在（管理者ログイン時） | 「システムエラーが発生しました。管理者にお問い合わせください。」 | エラーメッセージ表示 |

## セキュリティ仕様

- **CSRF保護**: Livewireが自動的にCSRFトークンを検証
- **パスワードマスク**: type=passwordで入力内容を隠蔽
- **レート制限**: Laravel Fortifyのスロットリング機能（5回失敗で1分間ロック）
- **ログイン試行記録**: 失敗時のIPアドレス記録（オプション）

## 認証フロー全体

```
G01 (ログイン画面)
  ↓ メール・パスワード入力
Fortify認証
  ↓
[is_admin チェック]
  ├─ is_admin = true
  │   ↓ OTPコード生成
  │   ↓ ユーザーID=1へメール送信
  │   ↓ セッションに二段階認証待ちフラグをセット
  │   → G02 (二段階認証画面)
  │       ↓ OTPコード検証成功
  │       → A01 (管理者ダッシュボード)
  │
  └─ is_admin = false
      → U01 (拠点ダッシュボード)
```

## 注意事項

- Laravel Fortifyを使用するため、`config/fortify.php`で設定が必要
- 管理者ログイン時は必ず二段階認証を経由
- OTPコードは常にユーザーID=1のメールアドレスに送信される
- 一般ユーザーは二段階認証をスキップ
- パスワードリセット機能は今回の仕様には含まない（将来的に追加可能）
