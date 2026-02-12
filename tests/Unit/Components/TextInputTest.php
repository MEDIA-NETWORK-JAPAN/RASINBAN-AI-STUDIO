<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextInputTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-TI-001: ラベルの表示
     */
    public function test_renders_label(): void
    {
        $view = $this->blade(
            '<x-ui.text-input label="拠点名" />'
        );

        $view->assertSee('拠点名');
    }

    /**
     * TC-TI-002: Email タイプの適用
     */
    public function test_applies_email_type(): void
    {
        $view = $this->blade(
            '<x-ui.text-input type="email" />'
        );

        $view->assertSee('type="email"', false);
    }

    /**
     * TC-TI-003: プレースホルダーの表示
     */
    public function test_renders_placeholder(): void
    {
        $view = $this->blade(
            '<x-ui.text-input placeholder="例: 東京本社" />'
        );

        $view->assertSee('placeholder="例: 東京本社"', false);
    }

    /**
     * TC-TI-004: 初期値の表示
     */
    public function test_renders_initial_value(): void
    {
        $view = $this->blade(
            '<x-ui.text-input value="東京本社" />'
        );

        $view->assertSee('value="東京本社"', false);
    }

    /**
     * TC-TI-005: 必須属性の付与
     */
    public function test_applies_required_attribute(): void
    {
        $view = $this->blade(
            '<x-ui.text-input required="true" />'
        );

        $view->assertSee('required', false);
    }

    /**
     * TC-TI-006: ヒントテキストの表示
     */
    public function test_renders_hint_text(): void
    {
        $view = $this->blade(
            '<x-ui.text-input hint="半角英数字のみ" />'
        );

        $view->assertSee('半角英数字のみ');
        $view->assertSee('text-xs', false);
        $view->assertSee('text-gray-400', false);
    }

    /**
     * TC-TI-007: デフォルトタイプの適用
     */
    public function test_applies_default_text_type(): void
    {
        $view = $this->blade(
            '<x-ui.text-input />'
        );

        $view->assertSee('type="text"', false);
    }

    /**
     * TC-TI-008: Livewire双方向バインディング
     *
     * Note: Requires Livewire component implementation
     */
    public function test_livewire_two_way_binding(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-TI-009: Livewire再レンダリング
     *
     * Note: Requires Livewire component implementation
     */
    public function test_livewire_re_rendering(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }
}
