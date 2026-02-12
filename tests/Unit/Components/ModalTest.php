<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-MDL-001: タイトルの表示
     */
    public function test_renders_title(): void
    {
        $view = $this->blade(
            '<x-ui.modal title="拠点登録">本文</x-ui.modal>'
        );

        $view->assertSee('拠点登録');
    }

    /**
     * TC-MDL-002: アイコンの表示
     */
    public function test_renders_icon(): void
    {
        $view = $this->blade(
            '<x-ui.modal title="拠点登録" icon="building">本文</x-ui.modal>'
        );

        $view->assertSee('building', false);
    }

    /**
     * TC-MDL-003: 本文の表示
     */
    public function test_renders_content(): void
    {
        $view = $this->blade(
            '<x-ui.modal title="拠点登録">
                <p>フォーム内容がここに表示されます</p>
            </x-ui.modal>'
        );

        $view->assertSee('フォーム内容がここに表示されます');
    }

    /**
     * TC-MDL-004: フッターの表示
     */
    public function test_renders_footer(): void
    {
        $view = $this->blade(
            '<x-ui.modal title="拠点登録">
                本文
                <x-slot name="footer">
                    <button>保存</button>
                </x-slot>
            </x-ui.modal>'
        );

        $view->assertSee('保存');
    }

    /**
     * TC-MDL-005: 閉じるボタンのクリック
     *
     * Note: This is a frontend (Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_close_button_click(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-MDL-006: オーバーレイのクリック
     *
     * Note: This is a frontend (Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_overlay_click(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }
}
