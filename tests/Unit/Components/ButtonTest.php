<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ButtonTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-BTN-001: Primary バリアントの表示
     */
    public function test_renders_primary_variant(): void
    {
        $view = $this->blade(
            '<x-ui.button variant="primary">保存</x-ui.button>'
        );

        $view->assertSee('保存');
        $view->assertSee('bg-indigo-600', false);
    }

    /**
     * TC-BTN-002: Secondary バリアントの表示
     */
    public function test_renders_secondary_variant(): void
    {
        $view = $this->blade(
            '<x-ui.button variant="secondary">キャンセル</x-ui.button>'
        );

        $view->assertSee('キャンセル');
        $view->assertSee('border-gray-300', false);
        $view->assertSee('bg-white', false);
    }

    /**
     * TC-BTN-003: Danger バリアントの表示
     */
    public function test_renders_danger_variant(): void
    {
        $view = $this->blade(
            '<x-ui.button variant="danger">削除</x-ui.button>'
        );

        $view->assertSee('削除');
        $view->assertSee('bg-red-600', false);
    }

    /**
     * TC-BTN-004: Ghost バリアントの表示
     */
    public function test_renders_ghost_variant(): void
    {
        $view = $this->blade(
            '<x-ui.button variant="ghost">詳細</x-ui.button>'
        );

        $view->assertSee('詳細');
        $view->assertSee('text-indigo-600', false);
    }

    /**
     * TC-BTN-005: Small サイズの表示
     */
    public function test_renders_small_size(): void
    {
        $view = $this->blade(
            '<x-ui.button size="sm">保存</x-ui.button>'
        );

        $view->assertSee('保存');
        $view->assertSee('px-3', false);
        $view->assertSee('py-1.5', false);
        $view->assertSee('text-xs', false);
    }

    /**
     * TC-BTN-006: Medium サイズの表示
     */
    public function test_renders_medium_size(): void
    {
        $view = $this->blade(
            '<x-ui.button size="md">保存</x-ui.button>'
        );

        $view->assertSee('保存');
        $view->assertSee('px-4', false);
        $view->assertSee('py-2', false);
        $view->assertSee('text-sm', false);
    }

    /**
     * TC-BTN-007: Large サイズの表示
     */
    public function test_renders_large_size(): void
    {
        $view = $this->blade(
            '<x-ui.button size="lg">保存</x-ui.button>'
        );

        $view->assertSee('保存');
        $view->assertSee('px-6', false);
        $view->assertSee('py-3', false);
        $view->assertSee('text-base', false);
    }

    /**
     * TC-BTN-008: アイコンの表示
     */
    public function test_renders_with_icon(): void
    {
        $view = $this->blade(
            '<x-ui.button icon="fa-plus">追加</x-ui.button>'
        );

        $view->assertSee('追加');
        $view->assertSee('fa-plus', false);
    }

    /**
     * TC-BTN-009: アイコン位置（右）の表示
     */
    public function test_renders_icon_on_right(): void
    {
        $view = $this->blade(
            '<x-ui.button icon="fa-arrow-right" iconPosition="right">次へ</x-ui.button>'
        );

        $view->assertSee('次へ');
        $view->assertSee('fa-arrow-right', false);
    }

    /**
     * TC-BTN-010: クリックイベントの発火
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_click_event_fires(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-BTN-011: 無効化状態の表示
     */
    public function test_renders_disabled_state(): void
    {
        $view = $this->blade(
            '<x-ui.button disabled="true">保存</x-ui.button>'
        );

        $view->assertSee('保存');
        $view->assertSee('opacity-50', false);
        $view->assertSee('cursor-not-allowed', false);
    }

    /**
     * TC-BTN-012: 無効化状態のクリック防止
     *
     * Note: This is a frontend (Alpine.js/Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_disabled_button_prevents_click(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-BTN-013: ローディング状態の表示
     */
    public function test_renders_loading_state(): void
    {
        $view = $this->blade(
            '<x-ui.button loading="true">保存中...</x-ui.button>'
        );

        $view->assertSee('fa-circle-notch', false);
        $view->assertSee('fa-spin', false);
    }
}
