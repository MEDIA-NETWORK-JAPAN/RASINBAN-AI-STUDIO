<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertBannerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-AB-001: Warning タイプの表示
     * AC-AB-001: `bg-yellow-50 border-yellow-400` が適用され、アイコンが `fa-exclamation-triangle` になる
     */
    public function test_warning_type_displays_correctly(): void
    {
        $this->markTestIncomplete('AlertBannerコンポーネント実装後に有効化');

        $view = $this->blade('<x-alert-banner type="warning" message="注意が必要です" />');

        $view->assertSee('bg-yellow-50', false);
        $view->assertSee('border-yellow-400', false);
        $view->assertSee('fa-exclamation-triangle', false);
        $view->assertSee('注意が必要です', false);
    }

    /**
     * TC-AB-002: Error タイプの表示
     * AC-AB-002: `bg-red-50 border-red-400` が適用され、アイコンが `fa-exclamation-circle` になる
     */
    public function test_error_type_displays_correctly(): void
    {
        $this->markTestIncomplete('AlertBannerコンポーネント実装後に有効化');

        $view = $this->blade('<x-alert-banner type="error" message="エラーが発生しました" />');

        $view->assertSee('bg-red-50', false);
        $view->assertSee('border-red-400', false);
        $view->assertSee('fa-exclamation-circle', false);
        $view->assertSee('エラーが発生しました', false);
    }

    /**
     * TC-AB-003: Info タイプの表示
     * AC-AB-003: `bg-blue-50 border-blue-400` が適用され、アイコンが `fa-info-circle` になる
     */
    public function test_info_type_displays_correctly(): void
    {
        $this->markTestIncomplete('AlertBannerコンポーネント実装後に有効化');

        $view = $this->blade('<x-alert-banner type="info" message="情報があります" />');

        $view->assertSee('bg-blue-50', false);
        $view->assertSee('border-blue-400', false);
        $view->assertSee('fa-info-circle', false);
        $view->assertSee('情報があります', false);
    }

    /**
     * TC-AB-004: Success タイプの表示
     * AC-AB-004: `bg-green-50 border-green-400` が適用され、アイコンが `fa-check-circle` になる
     */
    public function test_success_type_displays_correctly(): void
    {
        $this->markTestIncomplete('AlertBannerコンポーネント実装後に有効化');

        $view = $this->blade('<x-alert-banner type="success" message="成功しました" />');

        $view->assertSee('bg-green-50', false);
        $view->assertSee('border-green-400', false);
        $view->assertSee('fa-check-circle', false);
        $view->assertSee('成功しました', false);
    }

    /**
     * TC-AB-005: タイトルの表示
     * AC-AB-005: タイトルが `font-bold` で表示される
     */
    public function test_displays_title_in_bold(): void
    {
        $this->markTestIncomplete('AlertBannerコンポーネント実装後に有効化');

        $view = $this->blade('<x-alert-banner type="warning" title="警告" message="注意が必要です" />');

        $view->assertSee('警告', false);
        $view->assertSee('font-bold', false);
    }

    /**
     * TC-AB-006: 閉じるボタンの表示
     * AC-AB-007: `dismissible=true` を設定すると閉じるボタンが表示される
     */
    public function test_displays_close_button_when_dismissible(): void
    {
        $this->markTestIncomplete('AlertBannerコンポーネント実装後に有効化');

        $view = $this->blade('<x-alert-banner type="info" message="情報" :dismissible="true" />');

        // 閉じるボタン（×アイコンまたはfa-timesアイコン）が表示される
        $view->assertSeeInOrder(['button', 'fa-times'], false);
    }

    /**
     * TC-AB-007: 閉じるボタンのクリック
     * AC-AB-101: 閉じるボタンをクリックするとバナーが非表示になる
     *
     * Note: This is a frontend (Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_close_button_hides_banner(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }
}
