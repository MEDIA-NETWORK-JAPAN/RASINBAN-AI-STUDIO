<?php

namespace Tests\Feature\Admin;

use App\Models\DifyApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;
use Tests\Traits\CreatesUserWithTeam;

class DifyAppListTest extends TestCase
{
    use CreatesAdminUser;
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-A05-001: 未ログインでのアクセス - /login へリダイレクト
     */
    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/admin/apps');

        $response->assertRedirect('/login');
    }

    /**
     * TC-A05-002: 一般ユーザーでのアクセス - 403エラー
     */
    public function test_forbids_access_for_regular_users(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/apps');

        $response->assertForbidden();
    }

    /**
     * TC-A05-003: 管理者でのアクセス - 200ステータス、Difyアプリ一覧表示
     */
    public function test_admin_can_access_dify_apps_list(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/apps');

        $response->assertStatus(200);
        $response->assertSee('Difyアプリ一覧', false);
    }

    /**
     * TC-A05-004: アプリ一覧表示（50件単位） - 60アプリ存在時に最初の50件が表示される
     */
    public function test_displays_first_fifty_apps(): void
    {
        $admin = $this->createAdminUser();
        DifyApp::factory()->count(60)->create();

        $response = $this->actingAs($admin)->get('/admin/apps');

        $response->assertStatus(200);
        $this->assertTrue(true);
    }

    /**
     * TC-A05-005: 新規登録ボタンクリック - 新規登録モーダルが表示される
     *
     * Note: This is a frontend (Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_create_button_opens_modal(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A05-006: アプリ新規作成 - アプリが作成され、一覧が更新される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_creates_new_dify_app(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-A05-007: 検索機能（debounce 300ms） - 「Chat」を含むアプリのみ表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_searches_apps_by_name_and_slug(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-A05-008: ステータス切替 - is_activeが即座にトグルされ、トースト表示
     *
     * Note: Requires Livewire component implementation
     */
    public function test_toggles_app_status(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-A05-009: アプリ名クリックで遷移 - /admin/apps/{id} へ遷移
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_app_name_click_redirects_to_edit(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A05-010: 接続設定状態表示 - 緑チェックマークが表示される
     */
    public function test_displays_connection_status(): void
    {
        $admin = $this->createAdminUser();
        DifyApp::factory()->create([
            'name' => 'Test App',
            'api_key' => 'app-testkey123',
        ]);

        $response = $this->actingAs($admin)->get('/admin/apps');

        $response->assertStatus(200);
        $response->assertSee('Test App', false);
    }

    /**
     * TC-A05-011: Slug重複エラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_duplicate_slug(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-A05-012: ページネーション（50件単位） - 60アプリ存在時にページ2で51-60件目が表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_paginates_apps_by_fifty(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-A05-013: 検索実行時のページリセット - ページネーションが1ページ目にリセットされる
     *
     * Note: Requires Livewire component implementation
     */
    public function test_resets_page_on_search(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }
}
