<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-AL-001: タイトルの表示
     * AC-AL-001: PageHeaderにタイトルが表示される
     */
    public function test_displays_page_title_in_header(): void
    {
        $view = $this->blade('<x-admin-layout title="ダッシュボード" />');

        $view->assertSee('ダッシュボード', false);
    }

    /**
     * TC-AL-002: 説明文の表示
     * AC-AL-002: PageHeaderに説明文が表示される
     */
    public function test_displays_description_in_header(): void
    {
        $view = $this->blade('<x-admin-layout title="ダッシュボード" description="システム概要" />');

        $view->assertSee('システム概要', false);
    }

    /**
     * TC-AL-003: メインコンテンツの表示
     * AC-AL-003: メインコンテンツエリアに内容が表示される
     */
    public function test_displays_main_content(): void
    {
        $view = $this->blade('
            <x-admin-layout title="ダッシュボード">
                <div>メインコンテンツ</div>
            </x-admin-layout>
        ');

        $view->assertSee('メインコンテンツ', false);
    }

    /**
     * TC-AL-004: Sidebarコンポーネントの包含
     * AC-AL-004: Sidebarコンポーネントが含まれている
     */
    public function test_includes_sidebar_component(): void
    {
        $view = $this->blade('<x-admin-layout title="ダッシュボード" />');

        // Sidebarコンポーネントまたは関連クラスが含まれていることを確認
        $view->assertSee('Sidebar', false);
    }

    /**
     * TC-AL-005: MobileHeaderコンポーネントの包含
     * AC-AL-005: MobileHeaderコンポーネントが含まれている
     */
    public function test_includes_mobile_header_component(): void
    {
        $view = $this->blade('<x-admin-layout title="ダッシュボード" />');

        // MobileHeaderコンポーネントまたは関連クラスが含まれていることを確認
        $view->assertSee('MobileHeader', false);
    }

    /**
     * TC-AL-006: デスクトップでのSidebar常時表示
     * AC-AL-501: デスクトップ（lg以上）では、Sidebarが常時表示される
     */
    public function test_sidebar_always_visible_on_desktop(): void
    {
        $view = $this->blade('<x-admin-layout title="ダッシュボード" />');

        // デスクトップでの常時表示を示すクラスが適用されている
        $view->assertSee('lg:static', false);
    }

    /**
     * TC-AL-007: モバイルでのMobileHeader表示
     * AC-AL-502: モバイル（lg未満）では、MobileHeaderが表示される
     */
    public function test_mobile_header_visible_on_mobile(): void
    {
        $view = $this->blade('<x-admin-layout title="ダッシュボード" />');

        // モバイルでの表示を示すクラスが適用されている
        $view->assertSee('lg:hidden', false);
    }
}
