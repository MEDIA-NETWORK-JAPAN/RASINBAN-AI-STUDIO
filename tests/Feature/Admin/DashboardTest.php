<?php

namespace Tests\Feature\Admin;

use App\Models\DifyApp;
use App\Models\MonthlyApiUsage;
use App\Models\Plan;
use App\Models\PlanLimit;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;
use Tests\Traits\CreatesUserWithTeam;

class DashboardTest extends TestCase
{
    use CreatesAdminUser;
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-A01-001: 未ログインでのアクセス - /login へリダイレクト
     */
    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    /**
     * TC-A01-002: 一般ユーザーでのアクセス - 403エラー
     */
    public function test_forbids_access_for_regular_users(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    /**
     * TC-A01-003: 管理者でのアクセス - 200ステータス、ダッシュボード表示
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('ダッシュボード', false);
    }

    /**
     * TC-A01-004: KPIカード表示 - 契約拠点数、総リクエスト、稼働アプリが表示される
     */
    public function test_displays_kpi_cards(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('契約拠点数', false);
        $response->assertSee('総リクエスト', false);
        $response->assertSee('稼働アプリ', false);
    }

    /**
     * TC-A01-005: 契約拠点数の表示 - 5拠点存在時に「5」が表示される
     */
    public function test_displays_total_teams_count(): void
    {
        $admin = $this->createAdminUser();
        Team::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('5', false);
    }

    /**
     * TC-A01-006: 今月の総リクエスト表示 - 今月の利用実績が表示される
     */
    public function test_displays_total_requests_this_month(): void
    {
        $admin = $this->createAdminUser();
        $user = User::factory()->withPersonalTeam()->create();

        MonthlyApiUsage::factory()->forUser($user)->create([
            'usage_month' => now()->format('Y-m'),
            'request_count' => 150,
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('150', false);
    }

    /**
     * TC-A01-007: 稼働Difyアプリ表示 - アクティブアプリ3件時に「3」が表示される
     */
    public function test_displays_active_dify_apps_count(): void
    {
        $admin = $this->createAdminUser();

        DifyApp::factory()->count(3)->create(['is_active' => true]);
        DifyApp::factory()->count(2)->create(['is_active' => false]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('3', false);
    }

    /**
     * TC-A01-008: 利用率上位5件表示 - 10ユーザー存在時に上位5ユーザーのみ表示される
     *
     * Note: 管理者ダッシュボードの上位ユーザー表示コンポーネント実装後に有効化
     */
    public function test_displays_top_five_users_by_usage(): void
    {
        $this->markTestIncomplete('管理者ダッシュボード上位5ユーザー表示コンポーネントの実装後に有効化');

        $admin = $this->createAdminUser();

        // Create a plan with monthly limit
        $plan = Plan::factory()->create();
        PlanLimit::factory()->create([
            'plan_id' => $plan->id,
            'endpoint' => '/v1/chat-messages',
            'monthly_limit' => 100,
        ]);

        // Create 10 users with plan and different usage rates
        for ($i = 1; $i <= 10; $i++) {
            $user = User::factory()->withPersonalTeam()->withPlan($plan)->create(['name' => "User {$i}"]);
            MonthlyApiUsage::factory()->forUser($user)->create([
                'endpoint' => '/v1/chat-messages',
                'usage_month' => now()->format('Y-m'),
                'request_count' => $i * 10,
            ]);
        }

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('User 10', false);
        $response->assertSee('User 9', false);
        $response->assertSee('User 8', false);
        $response->assertSee('User 7', false);
        $response->assertSee('User 6', false);
        $response->assertDontSee('User 1', false);
    }

    /**
     * TC-A01-009: 利用率プログレスバー表示 - 利用率75%の拠点でプログレスバーが75%表示
     *
     * @requires 管理者ダッシュボード実装完了
     */
    public function test_displays_usage_progress_bar(): void
    {
        $this->markTestIncomplete('管理者ダッシュボード（/admin）とProgressBarコンポーネント実装後に有効化');

        $admin = $this->createAdminUser();

        // 利用率75%のテストデータ作成
        $plan = Plan::factory()->create();
        $team = Team::factory()->create(['plan_id' => $plan->id]);
        $planLimit = PlanLimit::factory()->create([
            'plan_id' => $plan->id,
            'endpoint' => '/v1/chat-messages',
            'monthly_limit' => 100,
        ]);
        MonthlyApiUsage::factory()->create([
            'team_id' => $team->id,
            'endpoint' => '/v1/chat-messages',
            'request_count' => 75, // 75%使用
            'usage_month' => now()->format('Y-m'),
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        // プログレスバーが75%で表示されることを検証
        $response->assertSee('75', false); // 数値として75が表示される
        // または、プログレスバーのwidth属性やaria-valuenowを検証
        // $response->assertSee('aria-valuenow="75"', false);
        // $response->assertSee('style="width: 75%"', false);
    }

    /**
     * TC-A01-010: 編集ボタンクリック - /admin/teams/{id} へ遷移
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_edit_button_redirects_to_team_edit(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A01-011: すべて見るリンククリック - /admin/teams へ遷移
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_view_all_link_redirects_to_teams_list(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A01-012: 利用率90%以上の表示 - 利用率95%の拠点でプログレスバーが赤色表示
     */
    public function test_displays_red_progress_bar_for_high_usage(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        // This test verifies the rendering logic for high usage warning
        // Actual color verification requires Dusk
        $this->markTestIncomplete('Color verification requires browser testing');
    }
}
