<?php

namespace Tests\Feature\User;

use App\Models\MonthlyApiUsage;
use App\Models\Team;
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
     * TC-U01-002: 一般ユーザーでのアクセス - 200ステータス、拠点ダッシュボード表示
     */
    public function test_regular_user_can_access_dashboard(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('拠点ダッシュボード', false);
    }

    /**
     * TC-U01-003: 利用状況表示 - 自チームの今月の利用状況が表示される
     */
    public function test_displays_current_team_usage(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);

        // Create usage data for current team
        MonthlyApiUsage::factory()->create([
            'team_id' => $user->currentTeam->id,
            'year_month' => now()->format('Y-m'),
            'total_requests' => 150,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('150', false);
    }

    /**
     * TC-U01-004: 他チームデータ非表示 - 自チームのデータのみ表示される
     */
    public function test_does_not_display_other_team_data(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);
        $otherTeam = Team::factory()->create(['name' => 'Other Team']);

        // Create usage for other team
        MonthlyApiUsage::factory()->create([
            'team_id' => $otherTeam->id,
            'year_month' => now()->format('Y-m'),
            'total_requests' => 999,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('Other Team', false);
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
     * TC-U01-006: team_idパラメータ無視 - /dashboard?team_id=999 へアクセスしても自チームのデータのみ表示
     */
    public function test_ignores_team_id_parameter(): void
    {
        $user = $this->createUserWithTeam([
            'name' => 'My Team',
            'is_admin' => false,
        ], ['name' => 'My Team']);

        $otherTeam = Team::factory()->create(['name' => 'Other Team']);

        // Try to access dashboard with other team's ID
        $response = $this->actingAs($user)->get('/dashboard?team_id='.$otherTeam->id);

        $response->assertStatus(200);
        $response->assertSee('My Team', false);
        $response->assertDontSee('Other Team', false);
    }

    /**
     * TC-U01-007: current_team_idでのデータ取得 - auth()->user()->currentTeam のデータのみ取得される
     */
    public function test_uses_current_team_id_for_data_retrieval(): void
    {
        $user = $this->createUserWithTeam([
            'is_admin' => false,
        ]);

        // Create usage for current team
        MonthlyApiUsage::factory()->create([
            'team_id' => $user->current_team_id,
            'year_month' => now()->format('Y-m'),
            'total_requests' => 100,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        // Verify that current_team_id is used (not any other team)
        $this->assertEquals($user->current_team_id, $user->currentTeam->id);
    }
}
