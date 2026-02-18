<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthInputTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-MI-001: ラベルの表示
     * AC-MI-001: ラベルが表示される
     */
    public function test_displays_label(): void
    {
        $view = $this->blade('<x-month-input name="target_month" label="対象月" />');

        $view->assertSee('対象月', false);
    }

    /**
     * TC-MI-002: Month タイプの適用
     * AC-MI-002: `type="month"` の input 要素が生成される
     */
    public function test_generates_month_input_type(): void
    {
        $view = $this->blade('<x-month-input name="target_month" />');

        $view->assertSee('type="month"', false);
    }

    /**
     * TC-MI-003: 初期値の表示
     * AC-MI-003: 初期値が表示される
     */
    public function test_displays_initial_value(): void
    {
        $view = $this->blade('<x-month-input name="target_month" value="2024-01" />');

        $view->assertSee('value="2024-01"', false);
    }

    /**
     * TC-MI-004: Livewire双方向バインディング
     * AC-MI-201: Livewireプロパティと双方向バインディングされる
     *
     * Note: This requires Livewire component implementation.
     * Skipped as it requires Livewire component test.
     */
    public function test_livewire_two_way_binding(): void
    {
        $this->markTestSkipped('Livewire component test - requires Livewire implementation');
    }

    /**
     * TC-MI-005: フォーカス時のスタイル
     * AC-MI-101: フォーカス時、`focus:border-indigo-500` と `focus:ring-indigo-500` が適用される
     *
     * Note: This is a frontend focus state test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_focus_state_applies_indigo_styles(): void
    {
        $this->markTestSkipped('Frontend focus state test - requires Dusk');
    }
}
