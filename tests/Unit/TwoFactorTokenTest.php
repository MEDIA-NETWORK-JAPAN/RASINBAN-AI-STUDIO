<?php

namespace Tests\Unit;

use App\Models\TwoFactorToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;

class TwoFactorTokenTest extends TestCase
{
    use CreatesAdminUser;
    use RefreshDatabase;

    /**
     * TC-G02-021-01: 正しいOTPで検証成功し、トークンは削除される
     */
    public function test_verify_succeeds_with_correct_token_and_deletes_record(): void
    {
        $admin = $this->createAdminUserWithIdOne();
        $token = TwoFactorToken::factory()->create([
            'user_id' => $admin->id,
            'token' => '123456',
            'attempts' => 0,
        ]);

        $result = $token->verify('123456');

        $this->assertTrue($result);
        $this->assertDatabaseMissing('two_factor_tokens', ['id' => $token->id]);
    }

    /**
     * TC-G02-021-02: 誤ったOTPで試行回数が1増加する
     */
    public function test_verify_increments_attempts_with_incorrect_token(): void
    {
        $admin = $this->createAdminUserWithIdOne();
        $token = TwoFactorToken::factory()->create([
            'user_id' => $admin->id,
            'token' => '123456',
            'attempts' => 0,
        ]);

        $result = $token->verify('654321');

        $this->assertFalse($result);
        $this->assertSame(1, $token->fresh()->attempts);
    }

    /**
     * TC-G02-021-03: 期限切れトークンは常に失敗し、試行回数は増加しない
     */
    public function test_verify_fails_for_expired_token_without_incrementing_attempts(): void
    {
        $admin = $this->createAdminUserWithIdOne();
        $token = TwoFactorToken::factory()->expired()->create([
            'user_id' => $admin->id,
            'token' => '123456',
            'attempts' => 2,
        ]);

        $result = $token->verify('123456');

        $this->assertFalse($result);
        $this->assertSame(2, $token->fresh()->attempts);
    }

    /**
     * TC-G02-021-04: 試行上限到達トークンは常に失敗し、試行回数は増加しない
     */
    public function test_verify_fails_for_max_attempts_token_without_incrementing_attempts(): void
    {
        $admin = $this->createAdminUserWithIdOne();
        $token = TwoFactorToken::factory()->maxAttempts()->create([
            'user_id' => $admin->id,
            'token' => '123456',
        ]);

        $result = $token->verify('123456');

        $this->assertFalse($result);
        $this->assertSame(5, $token->fresh()->attempts);
    }
}
