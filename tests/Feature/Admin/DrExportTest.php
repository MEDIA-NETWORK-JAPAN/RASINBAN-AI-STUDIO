<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;
use Tests\Traits\CreatesUserWithTeam;

class DrExportTest extends TestCase
{
    use CreatesAdminUser;
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-A09-001: 未ログインでのアクセス - /login へリダイレクト
     */
    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/admin/dr/export');

        $response->assertRedirect('/login');
    }

    /**
     * TC-A09-002: 一般ユーザーでのアクセス - 403エラー
     */
    public function test_forbids_access_for_regular_users(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/dr/export');

        $response->assertForbidden();
    }

    /**
     * TC-A09-003: 管理者でのアクセス - 200ステータス、エクスポート画面表示
     */
    public function test_admin_can_access_dr_export(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/dr/export');

        $response->assertStatus(200);
        $response->assertSee('災害復旧', false);
    }

    /**
     * TC-A09-004: 警告バナー表示 - APIキーが含まれることの注意喚起が表示される
     */
    public function test_displays_warning_banner(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/dr/export');

        $response->assertStatus(200);
        $response->assertSee('APIキー', false);
    }

    /**
     * TC-A09-005: 生成ボタン表示 - 「バックアップデータを生成」ボタンが表示される
     */
    public function test_displays_generate_button(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/dr/export');

        $response->assertStatus(200);
        $response->assertSee('バックアップ', false);
    }

    /**
     * TC-A09-006: エクスポート実行 - JSONプレビューとダウンロードボタンが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_generates_backup_data(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A09-007: JSONダウンロード - teams.jsonファイルがダウンロードされる
     *
     * Note: Requires Livewire component implementation
     */
    public function test_downloads_json_file(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A09-008: 全データエクスポート - 全拠点、全ユーザー、全APIキーが含まれる
     *
     * Note: Requires Livewire component implementation
     */
    public function test_exports_all_data(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A09-009: APIキー平文エクスポート - APIキーが平文（復号化済み）で含まれる
     *
     * Note: Requires Livewire component implementation
     */
    public function test_exports_plaintext_api_keys(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }
}
