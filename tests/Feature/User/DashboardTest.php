<?php

namespace Tests\Feature\User;

use App\Models\MonthlyApiUsage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesUserWithTeam;

class DashboardTest extends TestCase
{
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-U01-001: 未ログインでのアクセス - /login へリダイレクト
     */
    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * TC-U01-002: 一般ユーザーでのアクセス - 200ステータス、ユーザーダッシュボード表示
     */
    public function test_regular_user_can_access_dashboard(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('ユーザーダッシュボード', false);
    }

    /**
     * TC-U01-003: 利用状況表示 - 自ユーザーの今月の利用状況が表示される
     */
    public function test_displays_current_team_usage(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);

        // Create usage data for current user
        MonthlyApiUsage::factory()->forUser($user)->create([
            'usage_month' => now()->format('Y-m'),
            'request_count' => 150,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('150', false);
    }

    /**
     * TC-U01-004: 他ユーザーデータ非表示 - 自ユーザーのデータのみ表示される
     */
    public function test_does_not_display_other_team_data(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);
        $otherUser = User::factory()->withPersonalTeam()->create();

        // Create usage for other user
        MonthlyApiUsage::factory()->forUser($otherUser)->create([
            'usage_month' => now()->format('Y-m'),
            'request_count' => 999,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('999', false);
    }

    /**
     * TC-U01-005: 編集ボタン非表示 - 編集・削除ボタンが表示されない
     */
    public function test_edit_and_delete_buttons_not_displayed(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('編集', false);
        $response->assertDontSee('削除', false);
    }

    /**
     * TC-U01-006: user_idパラメータ無視 - /dashboard?user_id=999 へアクセスしても自ユーザーのデータのみ表示
     */
    public function test_ignores_user_id_parameter(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);
        $otherUser = User::factory()->withPersonalTeam()->create();

        // Create usage data for other user (should not appear)
        MonthlyApiUsage::factory()->forUser($otherUser)->create([
            'usage_month' => now()->format('Y-m'),
            'request_count' => 999,
        ]);

        // Try to access dashboard with another user's ID
        $response = $this->actingAs($user)->get('/dashboard?user_id='.$otherUser->id);

        $response->assertStatus(200);
        $response->assertDontSee('999', false);
    }

    /**
     * TC-U01-007: auth()->user()でのデータ取得 - ログインユーザー自身の利用実績のみ表示される
     */
    public function test_uses_authenticated_user_for_data_retrieval(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);
        $otherUser = User::factory()->withPersonalTeam()->create();

        // Create usage for current user
        MonthlyApiUsage::factory()->forUser($user)->create([
            'usage_month' => now()->format('Y-m'),
            'request_count' => 200,
        ]);

        // Create usage for other user (should not appear)
        MonthlyApiUsage::factory()->forUser($otherUser)->create([
            'usage_month' => now()->format('Y-m'),
            'request_count' => 999,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('999', false);
    }
}
