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
     * AC-G01-101: ページにロゴ、「Dify Gateway」タイトル、メールアドレス入力欄、パスワード入力欄、ログインボタンが表示される
     */
    public function test_login_page_displays_required_elements(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
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
     * AC-G01-202: 管理者ユーザー（is_admin=true）がログインした場合、二段階認証（G02）へリダイレクトされる
     *
     * Note: This test assumes two-factor authentication logic is implemented.
     * If not yet implemented, this test will need to be adjusted.
     */
    public function test_admin_user_redirected_to_two_factor_auth(): void
    {
        $admin = $this->createAdminUserWithIdOne([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // After admin login, should redirect to two-factor auth page
        // This may need adjustment based on actual implementation
        $response->assertRedirect('/two-factor-challenge');
    }

    /**
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

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /**
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
    }

    /**
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
    }

    /**
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
    }
}
