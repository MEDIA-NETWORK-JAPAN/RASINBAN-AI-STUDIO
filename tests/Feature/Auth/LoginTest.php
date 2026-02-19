<?php

namespace Tests\Feature\Auth;

use App\Mail\TwoFactorOtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;
use Tests\Traits\CreatesUserWithTeam;

class LoginTest extends TestCase
{
    use CreatesAdminUser;
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-G01-001: ページ表示
     * AC-G01-101: ページにロゴ、「Dify Gateway」タイトル、メールアドレス入力欄、パスワード入力欄、ログインボタンが表示される
     */
    public function test_login_page_displays_required_elements(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        // ロゴの表示確認（SVGにaria-label属性）
        $response->assertSee('aria-label="Dify Gateway Logo"', false);
        $response->assertSee('Dify Gateway', false);
        $response->assertSee('email', false);
        $response->assertSee('password', false);
        $response->assertSee('ログイン', false);
    }

    /**
     * AC-G01-201: 正しいメールアドレスとパスワードを入力してログインした場合、Fortify認証が成功する
     */
    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = $this->createUserWithTeam([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect();
    }

    /**
     * TC-G01-002: 管理者ログイン（二段階認証フロー）
     * AC-G01-202: 管理者ユーザー（is_admin=true）がログインした場合、二段階認証（G02）へリダイレクトされる
     *
     * 期待値: OTPコード生成 → ユーザーID=1へメール送信 → セッション保存 → ログアウト → G02へリダイレクト
     *
     * @requires カスタム二段階認証ロジック実装完了
     */
    public function test_admin_user_redirected_to_two_factor_auth(): void
    {
        Mail::fake();

        $superAdmin = $this->createAdminUserWithIdOne([
            'email' => 'super@example.com',
            'password' => bcrypt('password123'),
        ]);

        $admin = $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // 1. 二段階認証待ちフラグの保存
        $this->assertTrue(session()->has('two_factor_pending'));

        // 2. OTPメールがスーパー管理者へ送信されたことを確認
        Mail::assertSent(TwoFactorOtpMail::class, function ($mail) use ($superAdmin) {
            return $mail->hasTo($superAdmin->email);
        });

        // 3. 認証対象ユーザーIDの保存
        $this->assertEquals($admin->id, session()->get('two_factor_user_id'));

        // 4. ログアウト（認証されていない状態）
        $this->assertGuest();

        // 5. G02（二段階認証画面）へリダイレクト
        $response->assertRedirect('/two-factor-challenge');
    }

    /**
     * TC-G01-003: 一般ユーザーログイン
     * AC-G01-203: 一般ユーザー（is_admin=false）がログインした場合、U01（ユーザーダッシュボード）へリダイレクトされる
     */
    public function test_regular_user_redirected_to_dashboard(): void
    {
        $user = $this->createUserWithTeam([
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    /**
     * TC-G01-004: ログイン失敗（認証エラー）
     * AC-G01-204: メールアドレスまたはパスワードが間違っている場合、エラーメッセージが表示される
     *
     * 期待メッセージ（日本語）: 「メールアドレスまたはパスワードが正しくありません。」
     */
    public function test_login_fails_with_incorrect_credentials(): void
    {
        app()->setLocale('ja');

        $user = $this->createUserWithTeam([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // エラーメッセージが仕様の日本語文言と完全一致することを確認
        $errors = session('errors')->get('email');
        $this->assertNotEmpty($errors);
        // AC-G01-204: 「メールアドレスまたはパスワードが正しくありません。」
        $this->assertSame('メールアドレスまたはパスワードが正しくありません。', $errors[0]);
    }

    /**
     * TC-G01-005: メールアドレス空エラー
     * AC-G01-301: メールアドレスが空の場合、バリデーションエラーが表示される
     *
     * 期待メッセージ（日本語）: 「メールアドレスを入力してください」
     */
    public function test_email_is_required(): void
    {
        app()->setLocale('ja');

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // エラーメッセージが仕様の日本語文言と完全一致することを確認
        $errors = session('errors')->get('email');
        $this->assertNotEmpty($errors);
        // AC-G01-301: 「メールアドレスを入力してください」
        $this->assertSame('メールアドレスを入力してください', $errors[0]);
    }

    /**
     * TC-G01-006: メールアドレス形式エラー
     * AC-G01-302: メールアドレス形式が不正な場合、バリデーションエラーが表示される
     *
     * 期待メッセージ（日本語）: 「正しいメールアドレスを入力してください」
     */
    public function test_email_must_be_valid_format(): void
    {
        app()->setLocale('ja');

        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // エラーメッセージが仕様の日本語文言と完全一致することを確認
        $errors = session('errors')->get('email');
        $this->assertNotEmpty($errors);
        // AC-G01-302: 「正しいメールアドレスを入力してください」
        $this->assertSame('正しいメールアドレスを入力してください', $errors[0]);
    }

    /**
     * TC-G01-007: パスワード空エラー
     * AC-G01-303: パスワードが空の場合、バリデーションエラーが表示される
     *
     * 期待メッセージ（日本語）: 「パスワードを入力してください」
     */
    public function test_password_is_required(): void
    {
        app()->setLocale('ja');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();

        // エラーメッセージが仕様の日本語文言と完全一致することを確認
        $errors = session('errors')->get('password');
        $this->assertNotEmpty($errors);
        // AC-G01-303: 「パスワードを入力してください」
        $this->assertSame('パスワードを入力してください', $errors[0]);
    }

    /**
     * TC-G01-008: Enterキー押下でログイン
     *
     * Note: This is a frontend keyboard interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_enter_key_submits_login_form(): void
    {
        $this->markTestSkipped('Frontend keyboard interaction test - requires Dusk');
    }

    /**
     * TC-G01-009: ログイン処理中のボタン無効化
     *
     * Note: This is a frontend (Livewire/Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_login_button_disabled_during_processing(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-G01-010: スーパー管理者不在エラー
     *
     * @requires カスタムログイン処理実装完了
     */
    public function test_admin_login_fails_when_super_admin_not_exists(): void
    {
        Mail::fake();

        // ID=1 のユーザーを非管理者として作成（スーパー管理者不在を再現）
        \App\Models\User::factory()->create([
            'id' => 1,
            'is_admin' => false,
        ]);

        // PostgreSQL シーケンスリセット（ID=1 直接挿入後の衝突防止）
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("SELECT setval(pg_get_serial_sequence('users', 'id'), (SELECT MAX(id) FROM users))");
        } elseif (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
        }

        // ID != 1 の管理者を作成
        $admin = $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->assertNotEquals(1, $admin->id);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // スーパー管理者不在のためシステムエラー → /login へリダイレクト
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
