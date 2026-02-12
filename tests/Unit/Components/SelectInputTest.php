<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelectInputTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-SEL-001: ラベルの表示
     */
    public function test_renders_label(): void
    {
        $view = $this->blade(
            '<x-ui.select-input label="プラン" :options="[]" />'
        );

        $view->assertSee('プラン');
    }

    /**
     * TC-SEL-002: オプションの表示
     */
    public function test_renders_options(): void
    {
        $options = [
            ['value' => 1, 'label' => 'Basic'],
            ['value' => 2, 'label' => 'Pro'],
        ];

        $view = $this->blade(
            '<x-ui.select-input :options="$options" />',
            ['options' => $options]
        );

        $view->assertSee('Basic');
        $view->assertSee('Pro');
    }

    /**
     * TC-SEL-003: 選択状態の適用
     */
    public function test_applies_selected_option(): void
    {
        $options = [
            ['value' => 1, 'label' => 'Basic'],
            ['value' => 2, 'label' => 'Pro'],
        ];

        $view = $this->blade(
            '<x-ui.select-input :options="$options" selected="2" />',
            ['options' => $options]
        );

        $view->assertSee('selected', false);
    }

    /**
     * TC-SEL-004: Livewire双方向バインディング
     *
     * Note: Requires Livewire component implementation
     */
    public function test_livewire_two_way_binding(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }

    /**
     * TC-SEL-005: Livewire再レンダリング
     *
     * Note: Requires Livewire component implementation
     */
    public function test_livewire_re_rendering(): void
    {
        $this->markTestIncomplete('Livewire component test - requires implementation');
    }
}
