<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-SB-001: ロゴの表示
     * AC-SB-001: ロゴエリアに「Gateway Admin」が表示される
     */
    public function test_displays_logo_text(): void
    {
        $this->markTestIncomplete('Sidebarコンポーネント実装後に有効化');

        $view = $this->blade('<x-sidebar />');

        $view->assertSee('Gateway Admin', false);
    }

    /**
     * TC-SB-002: ロゴアイコンの表示
     * AC-SB-002: ロゴアイコン（fa-network-wired）が表示される
     */
    public function test_displays_logo_icon(): void
    {
        $this->markTestIncomplete('Sidebarコンポーネント実装後に有効化');

        $view = $this->blade('<x-sidebar />');

        $view->assertSee('fa-network-wired', false);
    }

    /**
     * TC-SB-003: ナビゲーション項目の表示
     * AC-SB-003: ナビゲーション項目が表示される
     */
    public function test_displays_navigation_items(): void
    {
        $this->markTestIncomplete('Sidebarコンポーネント実装後に有効化');

        $view = $this->blade('
            <x-sidebar>
                <x-slot name="navigation">
                    <a href="/admin">ダッシュボード</a>
                </x-slot>
            </x-sidebar>
        ');

        $view->assertSee('ダッシュボード', false);
    }

    /**
     * TC-SB-004: モバイルでのオーバーレイクリック
     * AC-SB-101: オーバーレイをクリックするとサイドバーが閉じる
     *
     * Note: This is a frontend (Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_mobile_overlay_click_closes_sidebar(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-SB-005: デスクトップでの常時表示
     * AC-SB-501: サイドバーが常時表示される（`lg:static lg:translate-x-0`）
     */
    public function test_desktop_always_visible(): void
    {
        $this->markTestIncomplete('Sidebarコンポーネント実装後に有効化');

        $view = $this->blade('<x-sidebar />');

        $view->assertSee('lg:static', false);
        $view->assertSee('lg:translate-x-0', false);
    }

    /**
     * TC-SB-006: モバイルでの非表示
     * AC-SB-502: サイドバーが画面外に隠れる（`-translate-x-full`）
     */
    public function test_mobile_hidden_by_default(): void
    {
        $this->markTestIncomplete('Sidebarコンポーネント実装後に有効化');

        $view = $this->blade('<x-sidebar />');

        $view->assertSee('-translate-x-full', false);
    }

    /**
     * TC-SB-007: モバイルでのスライドイン
     * AC-SB-503: サイドバーがスライドインする（`translate-x-0`）
     *
     * Note: This is a frontend (Alpine.js) state test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_mobile_slides_in_when_open(): void
    {
        $this->markTestSkipped('Frontend state test - requires Dusk');
    }
}
