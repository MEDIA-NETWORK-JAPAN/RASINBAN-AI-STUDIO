<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-NI-001: リンク先の設定
     * AC-NI-001: リンク先が設定される
     */
    public function test_sets_link_destination(): void
    {
        $this->markTestIncomplete('NavItemコンポーネント実装後に有効化');

        $view = $this->blade('<x-nav-item href="/admin/teams" label="拠点管理" icon="fa-building" />');

        $view->assertSee('href="/admin/teams"', false);
    }

    /**
     * TC-NI-002: アイコンの表示
     * AC-NI-002: FontAwesomeアイコンが表示される
     */
    public function test_displays_fontawesome_icon(): void
    {
        $this->markTestIncomplete('NavItemコンポーネント実装後に有効化');

        $view = $this->blade('<x-nav-item href="/admin/teams" label="拠点管理" icon="fa-building" />');

        $view->assertSee('fa-building', false);
    }

    /**
     * TC-NI-003: ラベルの表示
     * AC-NI-003: メニュー名が表示される
     */
    public function test_displays_menu_label(): void
    {
        $this->markTestIncomplete('NavItemコンポーネント実装後に有効化');

        $view = $this->blade('<x-nav-item href="/admin/teams" label="拠点・ユーザー管理" icon="fa-building" />');

        $view->assertSee('拠点・ユーザー管理', false);
    }

    /**
     * TC-NI-004: 非アクティブ状態の表示
     * AC-NI-004: `text-gray-600` と `hover:bg-gray-50` が適用される
     */
    public function test_inactive_state_applies_gray_styles(): void
    {
        $this->markTestIncomplete('NavItemコンポーネント実装後に有効化');

        $view = $this->blade('<x-nav-item href="/admin/teams" label="拠点管理" icon="fa-building" :active="false" />');

        $view->assertSee('text-gray-600', false);
        $view->assertSee('hover:bg-gray-50', false);
    }

    /**
     * TC-NI-005: アクティブ状態の表示
     * AC-NI-005: `bg-indigo-50 text-indigo-700` が適用される
     */
    public function test_active_state_applies_indigo_styles(): void
    {
        $this->markTestIncomplete('NavItemコンポーネント実装後に有効化');

        $view = $this->blade('<x-nav-item href="/admin/teams" label="拠点管理" icon="fa-building" :active="true" />');

        $view->assertSee('bg-indigo-50', false);
        $view->assertSee('text-indigo-700', false);
    }
}
