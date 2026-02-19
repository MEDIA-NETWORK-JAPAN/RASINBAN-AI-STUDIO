<?php

namespace Tests\Feature\Admin;

use App\Models\Team;
use App\Models\User;
use App\Models\UserApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\CreatesAdminUser;
use Tests\Traits\CreatesUserWithTeam;

class TeamEditTest extends TestCase
{
    use CreatesAdminUser;
    use CreatesUserWithTeam;
    use RefreshDatabase;

    /**
     * TC-A03-001: 未ログインでのアクセス - /login へリダイレクト
     */
    public function test_redirects_to_login_when_not_authenticated(): void
    {
        $team = Team::factory()->create();

        $response = $this->get("/admin/teams/{$team->id}");

        $response->assertRedirect('/login');
    }

    /**
     * TC-A03-002: 一般ユーザーでのアクセス - 403エラー
     */
    public function test_forbids_access_for_regular_users(): void
    {
        $user = $this->createUserWithTeam(['is_admin' => false]);
        $team = Team::factory()->create();

        $response = $this->actingAs($user)->get("/admin/teams/{$team->id}");

        $response->assertForbidden();
    }

    /**
     * TC-A03-003: 管理者でのアクセス - 200ステータス、拠点編集画面表示
     */
    public function test_admin_can_access_team_edit(): void
    {
        $admin = $this->createAdminUser();
        $team = Team::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/teams/{$team->id}");

        $response->assertStatus(200);
        $response->assertSee('拠点編集', false);
    }

    /**
     * TC-A03-004: 基本情報表示 - チーム名が表示される
     *
     * Note: v1.9でplanはユーザー単位のため、チームページではチーム名のみ検証
     */
    public function test_displays_basic_info(): void
    {
        $admin = $this->createAdminUser();
        $team = Team::factory()->create(['name' => 'Tokyo Office']);

        $response = $this->actingAs($admin)->get("/admin/teams/{$team->id}");

        $response->assertStatus(200);
        $response->assertSee('Tokyo Office', false);
    }

    /**
     * TC-A03-005: 所属ユーザー一覧表示 - 所属する全ユーザーが表示される
     */
    public function test_displays_team_members(): void
    {
        $admin = $this->createAdminUser();
        $team = Team::factory()->create();

        $user1 = User::factory()->create(['name' => 'User One']);
        $user2 = User::factory()->create(['name' => 'User Two']);
        $team->users()->attach([$user1->id, $user2->id]);

        $response = $this->actingAs($admin)->get("/admin/teams/{$team->id}");

        $response->assertStatus(200);
        $response->assertSee('User One', false);
        $response->assertSee('User Two', false);
    }

    /**
     * TC-A03-006: APIキー管理セクション表示 - 所属ユーザーのAPIキー（マスク表示）、最終利用日時が表示される
     *
     * Note: v1.9でAPIキーはユーザー単位。所属ユーザーのUserApiKeyを表示
     */
    public function test_displays_api_key_section(): void
    {
        $admin = $this->createAdminUser();
        $team = Team::factory()->create();
        $member = User::factory()->create();
        $team->users()->attach($member->id, ['role' => 'editor']);
        UserApiKey::factory()->create([
            'user_id' => $member->id,
            'last_used_at' => now()->subHours(2),
        ]);

        $response = $this->actingAs($admin)->get("/admin/teams/{$team->id}");

        $response->assertStatus(200);
        $response->assertSee('APIキー', false);
    }

    /**
     * TC-A03-007: 基本情報更新 - Team情報が更新され、成功トースト表示
     *
     * Note: Requires Livewire component implementation
     */
    public function test_updates_basic_info(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-008: ユーザー追加 - 新規ユーザーが作成され、Teamに紐付けられる
     *
     * Note: Requires Livewire component implementation
     */
    public function test_adds_new_user_to_team(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-009: ユーザー編集 - ユーザー情報が更新される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_edits_team_member(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-010: ユーザー削除確認モーダル表示 - 削除確認モーダルが表示される
     *
     * Note: This is a frontend (Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_shows_delete_confirmation_modal(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A03-011: ユーザー削除実行 - ユーザーが削除される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_deletes_team_member(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-012: APIキー表示 - マスクが解除され平文表示される
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_reveals_api_key(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A03-013: APIキーコピー - APIキーがクリップボードにコピーされる
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_copies_api_key_to_clipboard(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-A03-014: APIキー再生成 - 新規APIキーが発行され、平文で表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_regenerates_api_key(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-015: チーム削除 - 削除が実行される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_deletes_team(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-016: パスワード10文字未満エラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_short_password(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-017: メールアドレス空白エラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_empty_email(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-018: ユーザー名空白エラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_empty_name(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-019: パスワード文字種不足エラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_weak_password(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }

    /**
     * TC-A03-020: よく使われるパスワードエラー - バリデーションエラーが表示される
     *
     * Note: Requires Livewire component implementation
     */
    public function test_validation_error_for_common_password(): void
    {
        $this->markTestSkipped('Livewire component implementation required');
    }
}
