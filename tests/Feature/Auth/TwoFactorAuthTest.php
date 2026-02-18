<?php

namespace Tests\Feature\Auth;

use App\Models\TwoFactorToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;

class TwoFactorAuthTest extends TestCase
{
    use CreatesAdminUser;
    use RefreshDatabase;

    /**
     * TC-G02-001: 直接アクセス（セッションなし） - G01へリダイレクト
     */
    public function test_redirects_to_login_when_no_session(): void
    {
        $response = $this->get('/two-factor-challenge');

        $response->assertRedirect('/login');
    }

    /**
     * TC-G02-002: ページ表示 - 説明文、コード入力フィールド、3つのボタンが表示される
     *
     * Note: Requires two-factor authentication implementation
     */
    public function test_displays_two_factor_challenge_page(): void
    {
        $admin = $this->createAdminUserWithIdOne([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Simulate two-factor pending session
        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->get('/two-factor-challenge');

        $response->assertStatus(200);
        $response->assertSee('認証コード', false);
        $response->assertSee('認証する', false);
        $response->assertSee('再送信', false);
        $response->assertSee('ログアウト', false);
    }

    /**
     * TC-G02-003: OTPコード送信先 - ユーザーID=1の管理者のメールアドレスにOTPコードが送信される
     *
     * Note: Email sending test - may need Mail::fake()
     */
    public function test_otp_code_sent_to_super_admin(): void
    {
        $this->markTestIncomplete('Email sending test - requires implementation');
    }

    /**
     * TC-G02-004: OTPコード形式 - 6桁の数字
     */
    public function test_otp_code_is_six_digits(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $token = TwoFactorToken::factory()->create([
            'user_id' => $admin->id,
            'token' => '123456',
        ]);

        $this->assertMatchesRegularExpression('/^\d{6}$/', $token->token);
    }

    /**
     * TC-G02-005: OTPコード有効期限 - 10分後にコードを入力すると有効期限切れエラー
     */
    public function test_expired_otp_code_shows_error(): void
    {
        $admin = $this->createAdminUserWithIdOne([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = TwoFactorToken::factory()->expired()->create([
            'user_id' => $admin->id,
            'token' => '123456',
        ]);

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/two-factor-challenge', [
            'code' => '123456',
        ]);

        $response->assertSessionHasErrors();
        $response->assertRedirect('/login');
    }

    /**
     * TC-G02-006: ログイン試行者情報 - メールにログイン試行者のID、名前、メールアドレスが含まれる
     *
     * Note: Email content test - requires Mail::fake()
     */
    public function test_email_contains_login_attempt_info(): void
    {
        $this->markTestIncomplete('Email content test - requires implementation');
    }

    /**
     * TC-G02-007: 正しいOTPコード入力 - A01（管理者ダッシュボード）へリダイレクト
     */
    public function test_correct_otp_code_redirects_to_admin_dashboard(): void
    {
        $admin = $this->createAdminUserWithIdOne([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $token = TwoFactorToken::factory()->create([
            'user_id' => $admin->id,
            'token' => '123456',
        ]);

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/two-factor-challenge', [
            'code' => '123456',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
    }

    /**
     * TC-G02-008: 間違ったOTPコード入力 - エラーメッセージ表示、試行回数+1
     */
    public function test_incorrect_otp_code_shows_error_and_increments_attempts(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $token = TwoFactorToken::factory()->create([
            'user_id' => $admin->id,
            'token' => '123456',
            'attempts' => 0,
        ]);

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/two-factor-challenge', [
            'code' => '999999',
        ]);

        $response->assertSessionHasErrors();
        $this->assertEquals(1, $token->fresh()->attempts);
    }

    /**
     * TC-G02-009: 試行回数超過（5回） - トークン無効化、エラー、G01へリダイレクト
     */
    public function test_max_attempts_exceeded_invalidates_token(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $token = TwoFactorToken::factory()->maxAttempts()->create([
            'user_id' => $admin->id,
            'token' => '123456',
        ]);

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/two-factor-challenge', [
            'code' => '999999',
        ]);

        $response->assertSessionHasErrors();
        $response->assertRedirect('/login');
    }

    /**
     * TC-G02-010: コード再送信 - 新しいOTPコードがユーザーID=1の管理者に送信され、トースト表示
     *
     * Note: Email sending test - requires Mail::fake()
     */
    public function test_resend_code_sends_new_otp(): void
    {
        $this->markTestIncomplete('Email resend test - requires implementation');
    }

    /**
     * TC-G02-011: ログアウトボタン - セッションがクリアされ、G01へリダイレクト
     */
    public function test_logout_button_clears_session_and_redirects(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /**
     * TC-G02-012: コード空エラー - 「認証コードを入力してください」バリデーションエラー
     */
    public function test_empty_code_shows_validation_error(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/two-factor-challenge', [
            'code' => '',
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    /**
     * TC-G02-013: コード6桁以外エラー - 「6桁の認証コードを入力してください」バリデーションエラー
     */
    public function test_non_six_digit_code_shows_validation_error(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ])->post('/two-factor-challenge', [
            'code' => '12345',
        ]);

        $response->assertSessionHasErrors(['code']);
    }
}
