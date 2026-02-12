<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgressBarTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-PB-001: パーセンテージの表示
     */
    public function test_displays_percentage(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="75" />'
        );

        $view->assertSee('75%');
    }

    /**
     * TC-PB-002: バー幅の適用
     */
    public function test_applies_bar_width(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="50" />'
        );

        $view->assertSee('width: 50%', false);
    }

    /**
     * TC-PB-003: 通常時のテキスト色
     */
    public function test_applies_normal_text_color(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="50" />'
        );

        $view->assertSee('text-gray-600', false);
    }

    /**
     * TC-PB-004: 超過時のテキスト色
     */
    public function test_applies_exceeded_text_color(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="95" />'
        );

        $view->assertSee('text-red-600', false);
    }

    /**
     * TC-PB-005: 通常時のバー色
     */
    public function test_applies_normal_bar_color(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="50" />'
        );

        $view->assertSee('bg-blue-500', false);
    }

    /**
     * TC-PB-006: 警告時のバー色
     */
    public function test_applies_warning_bar_color(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="95" />'
        );

        $view->assertSee('bg-yellow-500', false);
    }

    /**
     * TC-PB-007: 超過時のバー色
     */
    public function test_applies_exceeded_bar_color(): void
    {
        $view = $this->blade(
            '<x-ui.progress-bar percentage="105" />'
        );

        $view->assertSee('bg-red-500', false);
    }
}
