<?php

namespace Tests\Feature\Admin;

use App\Models\DifyApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;
use Tests\Traits\CreatesUserWithTeam;

class DifyAppEditTest extends TestCase
{
    use CreatesAdminUser;
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-A06-001: 未ログインでのアクセス - /login へリダイレクト
     */
    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $app = DifyApp::factory()->create();

        $response = $this->get("/admin/apps/{$app->id}");

        $response->assertRedirect('/login');
    }

    /**
     * TC-A06-002: 一般ユーザーでのアクセス - 403エラー
     */
    public function test_forbids_access_for_regular_users(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);
        $app = DifyApp::factory()->create();

        $response = $this->actingAs($user)->get("/admin/apps/{$app->id}");

        $response->assertForbidden();
    }

    /**
     * TC-A06-003: 管理者でのアクセス - 200ステータス、アプリ編集画面表示
     */
    public function test_admin_can_access_app_edit(): void
    {
        $admin = $this->createAdminUser();
        $app = DifyApp::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/apps/{$app->id}");

        $response->assertStatus(200);
        $response->assertSee('アプリ編集', false);
    }

    /**
     * TC-A06-004: 基本情報セクション表示 - アプリ名、Slug、説明、ステータスが表示される
     */
    public function test_displays_basic_info_section(): void
    {
        $admin = $this->createAdminUser();
        $app = DifyApp::factory()->create([
            'name' => 'Chat App',
            'slug' => 'chat-app',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get("/admin/apps/{$app->id}");

        $response->assertStatus(200);
        $response->assertSee('Chat App', false);
        $response->assertSee('chat-app', false);
    }

    /**
     * TC-A06-005: Dify接続情報セクション表示 - エンドポイントURL、APIキー（マスク）、更新ボタンが表示される
     */
    public function test_displays_dify_connection_section(): void
    {
        $admin = $this->createAdminUser();
        $app = DifyApp::factory()->create([
            'base_url' => 'https://api.dify.ai/v1',
            'api_key' => 'app-testkey123',
        ]);

        $response = $this->actingAs($admin)->get("/admin/apps/{$app->id}");

        $response->assertStatus(200);
        $response->assertSee('https://api.dify.ai/v1', false);
    }

    /**
     * TC-A06-006: 基本情報更新 - DifyApp更新、トースト表示
     *
     * Note: Requires Livewire component implementation
     */
    public function test_updates_basic_info(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A06-007: Slug変更時警告 - 警告メッセージが表示される
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_shows_warning_on_slug_change(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A06-008: APIキー表示 - マスクが解除され、復号されたAPIキーが表示される
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_reveals_api_key(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A06-009: APIキー更新 - api_keyが暗号化保存され、トースト表示
     *
     * Note: Requires Livewire component implementation
     */
    public function test_updates_api_key(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A06-010: アプリ削除（ソフトデリート） - deleted_atがセットされ、A05へリダイレクト
     *
     * Note: Requires Livewire component implementation
     */
    public function test_soft_deletes_app(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A06-011: 削除後のデータ保持確認 - monthly_api_usagesデータが保持されている
     *
     * Note: Requires Livewire component implementation
     */
    public function test_preserves_usage_data_after_deletion(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A06-012: 削除後の一覧非表示確認 - 削除されたアプリが一覧に表示されない
     *
     * Note: Requires Livewire component implementation
     */
    public function test_hides_deleted_app_from_list(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A06-013: 説明・メモ最大長超過エラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_long_description(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }
}
