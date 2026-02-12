<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IconButtonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-IBTN-001: アイコンの表示
     */
    public function test_renders_icon(): void
    {
        $view = $this->blade(
            '<x-ui.icon-button icon="fa-edit" />'
        );

        $view->assertSee('fa-edit', false);
    }

    /**
     * TC-IBTN-002: カラー（indigo）の適用
     */
    public function test_applies_indigo_color(): void
    {
        $view = $this->blade(
            '<x-ui.icon-button icon="fa-edit" color="indigo" />'
        );

        $view->assertSee('hover:text-indigo-600', false);
    }

    /**
     * TC-IBTN-003: カラー（red）の適用
     */
    public function test_applies_red_color(): void
    {
        $view = $this->blade(
            '<x-ui.icon-button icon="fa-trash" color="red" />'
        );

        $view->assertSee('hover:text-red-600', false);
    }

    /**
     * TC-IBTN-004: カラー（gray）の適用
     */
    public function test_applies_gray_color(): void
    {
        $view = $this->blade(
            '<x-ui.icon-button icon="fa-info" color="gray" />'
        );

        $view->assertSee('hover:text-gray-600', false);
    }

    /**
     * TC-IBTN-005: デフォルト色の適用
     */
    public function test_applies_default_color(): void
    {
        $view = $this->blade(
            '<x-ui.icon-button icon="fa-edit" />'
        );

        $view->assertSee('text-gray-400', false);
    }

    /**
     * TC-IBTN-006: クリックイベントの発火
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_click_event_fires(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-IBTN-007: ツールチップの表示
     */
    public function test_renders_tooltip(): void
    {
        $view = $this->blade(
            '<x-ui.icon-button icon="fa-edit" title="編集" />'
        );

        $view->assertSee('title="編集"', false);
    }
}
