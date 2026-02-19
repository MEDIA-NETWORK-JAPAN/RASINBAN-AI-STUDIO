<?php

namespace Tests\Feature\Auth;

use App\Mail\TwoFactorOtpMail;
use App\Models\TwoFactorToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
        $response->assertSee($admin->name.' 宛に6桁の認証コードを送信しました', false);
        $response->assertSee('認証コード', false);
        $response->assertSee('認証する', false);
        $response->assertSee('再送信', false);
        $response->assertSee('ログアウト', false);
    }

    /**
     * TC-G02-003: OTPコード送信先 - ユーザーID=1の管理者のメールアドレスにOTPコードが送信される
     */
    public function test_otp_code_sent_to_super_admin(): void
    {
        Mail::fake();

        $superAdmin = $this->createAdminUserWithIdOne([
            'email' => 'super-admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/two-factor-challenge');

        Mail::assertSent(TwoFactorOtpMail::class, function (TwoFactorOtpMail $mail) use ($superAdmin): bool {
            return $mail->hasTo($superAdmin->email) && preg_match('/^\d{6}$/', $mail->otp) === 1;
        });

        Mail::assertSent(TwoFactorOtpMail::class, 1);
        $this->assertGuest();
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

        $this->assertDoesNotMatchRegularExpression('/^\d{6}$/', $token->token);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('123456', $token->token));
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
     */
    public function test_email_contains_login_attempt_info(): void
    {
        Mail::fake();

        $this->createAdminUserWithIdOne([
            'email' => 'super-admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $admin = $this->createAdminUser([
            'name' => 'ログイン管理者',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        Mail::assertSent(TwoFactorOtpMail::class, function (TwoFactorOtpMail $mail) use ($admin): bool {
            $html = $mail->render();

            return str_contains($html, (string) $admin->id)
                && str_contains($html, $admin->name)
                && str_contains($html, $admin->email);
        });
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
     */
    public function test_resend_code_sends_new_otp(): void
    {
        Mail::fake();

        $superAdmin = $this->createAdminUserWithIdOne([
            'email' => 'super-admin@example.com',
        ]);
        $loginAdmin = $this->createAdminUser([
            'email' => 'login-admin@example.com',
        ]);

        $token = TwoFactorToken::factory()->create([
            'user_id' => $loginAdmin->id,
            'token' => '111111',
            'attempts' => 3,
            'expires_at' => now()->addMinute(),
        ]);
        $oldExpiresAt = $token->expires_at;

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $loginAdmin->id,
        ])->post('/two-factor-challenge/resend');

        $response->assertSessionHas('status');

        $newToken = TwoFactorToken::where('user_id', $loginAdmin->id)->first();
        $this->assertNotNull($newToken);
        $this->assertNotSame('111111', $newToken->token);
        $this->assertSame(0, $newToken->attempts);
        $this->assertTrue($newToken->expires_at->greaterThan($oldExpiresAt));

        Mail::assertSent(TwoFactorOtpMail::class, function (TwoFactorOtpMail $mail) use ($superAdmin): bool {
            return $mail->hasTo($superAdmin->email);
        });
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
        ])->post(route('two-factor.cancel'));

        $response->assertRedirect('/login');
        $this->assertGuest();
        $response->assertSessionMissing('two_factor_pending');
        $response->assertSessionMissing('two_factor_user_id');
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

    /**
     * TC-G02-014: 正しいOTPコード入力後、セッションIDが再生成される
     */
    public function test_session_id_is_regenerated_after_successful_two_factor_authentication(): void
    {
        $admin = $this->createAdminUserWithIdOne([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        TwoFactorToken::factory()->create([
            'user_id' => $admin->id,
            'token' => '123456',
        ]);

        $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ]);

        $beforeId = session()->getId();
        $response = $this->post('/two-factor-challenge', ['code' => '123456']);
        $afterId = session()->getId();

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($admin);
        $this->assertNotSame($beforeId, $afterId);
    }

    /**
     * TC-G02-015: two_factor_user_id が非管理者の場合は認証拒否する
     */
    public function test_two_factor_auth_fails_when_session_user_is_not_admin(): void
    {
        $nonAdmin = User::factory()->create([
            'is_admin' => false,
        ]);

        TwoFactorToken::factory()->create([
            'user_id' => $nonAdmin->id,
            'token' => '123456',
        ]);

        $response = $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $nonAdmin->id,
        ])->post('/two-factor-challenge', [
            'code' => '123456',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors(['code']);
        $this->assertGuest();
    }

    /**
     * TC-G02-018: OTP検証にレート制限が適用される
     */
    public function test_two_factor_challenge_is_rate_limited(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/two-factor-challenge', ['code' => 'abcde']);
        }

        $blocked = $this->post('/two-factor-challenge', ['code' => 'abcde']);
        $blocked->assertStatus(429);
    }

    /**
     * TC-G02-019: OTP再送信にレート制限が適用される
     */
    public function test_two_factor_resend_is_rate_limited(): void
    {
        $admin = $this->createAdminUserWithIdOne();

        $this->withSession([
            'two_factor_pending' => true,
            'two_factor_user_id' => $admin->id,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/two-factor-challenge/resend');
        }

        $blocked = $this->post('/two-factor-challenge/resend');
        $blocked->assertStatus(429);
    }

    /**
     * TC-G02-020: OTPは平文で保存されない
     */
    public function test_otp_is_not_stored_in_plain_text(): void
    {
        Mail::fake();

        $this->createAdminUserWithIdOne([
            'email' => 'super-admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->createAdminUser([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/two-factor-challenge');

        $token = TwoFactorToken::query()->first();
        $this->assertNotNull($token);
        $this->assertDoesNotMatchRegularExpression('/^\d{6}$/', (string) $token->token);
    }
}
