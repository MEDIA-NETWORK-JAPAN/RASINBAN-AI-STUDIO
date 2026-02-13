<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        // ロゴの表示確認（imgタグまたはロゴ要素）
        // Note: 実際のロゴ実装に応じて、適切なセレクタやクラス名に変更してください
        $response->assertSee('logo', false); // または適切なロゴ識別子
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
        $this->markTestIncomplete('カスタム二段階認証ロジック（OTP生成・メール送信・セッション保存・ログアウト）実装後に有効化');

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

        // 期待値の検証:
        // 1. OTPコード生成（セッションまたはDBに保存）
        $this->assertTrue(session()->has('otp_code'));

        // 2. ユーザーID=1へメール送信（キューまたは即時送信）
        // Mail::assertSent() または Queue::assertPushed() で検証

        // 3. セッション保存（login.id など）
        $this->assertTrue(session()->has('login.id'));

        // 4. ログアウト（認証されていない状態）
        $this->assertGuest();

        // 5. G02（二段階認証画面）へリダイレクト
        $response->assertRedirect('/two-factor-challenge');
    }

    /**
     * TC-G01-003: 一般ユーザーログイン
     * AC-G01-203: 一般ユーザー（is_admin=false）がログインした場合、U01（拠点ダッシュボード）へリダイレクトされる
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
     */
    public function test_login_fails_with_incorrect_credentials(): void
    {
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

        // 期待値: 認証エラーメッセージが表示される
        // Note: エラーメッセージの具体的な文言はlocaleに依存するため、キーの存在のみ検証
    }

    /**
     * TC-G01-005: メールアドレス空エラー
     * AC-G01-301: メールアドレスが空の場合、バリデーションエラーが表示される
     */
    public function test_email_is_required(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // 期待値: emailフィールドのバリデーションエラーが存在
        // Note: エラーメッセージの具体的な文言はlocaleに依存するため、キーの存在のみ検証
    }

    /**
     * TC-G01-006: メールアドレス形式エラー
     * AC-G01-302: メールアドレス形式が不正な場合、バリデーションエラーが表示される
     */
    public function test_email_must_be_valid_format(): void
    {
        $response = $this->post('/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // 期待値: emailフィールドのバリデーションエラーが存在
        // Note: エラーメッセージの具体的な文言はlocaleに依存するため、キーの存在のみ検証
    }

    /**
     * TC-G01-007: パスワード空エラー
     * AC-G01-303: パスワードが空の場合、バリデーションエラーが表示される
     */
    public function test_password_is_required(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();

        // 期待値: passwordフィールドのバリデーションエラーが存在
        // Note: エラーメッセージの具体的な文言はlocaleに依存するため、キーの存在のみ検証
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
        $this->markTestIncomplete('カスタムログイン処理（スーパー管理者チェック）実装後に有効化');

        // Ensure user ID=1 exists but is NOT a super admin (to reserve the ID)
        \App\Models\User::factory()->create([
            'id' => 1,
            'is_admin' => false,
        ]);

        // Create admin user with ID != 1
        $admin = $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Admin's ID should not be 1 (since ID=1 is already taken)
        $this->assertNotEquals(1, $admin->id);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // 期待値: システムエラーが表示される
        // Note: エラーメッセージの具体的な文言はlocaleに依存するため、キーの存在のみ検証
        $response->assertSessionHasErrors('email'); // エラーキーを明示
    }
}
