<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TextareaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-TA-001: ラベルの表示
     * AC-TA-001: ラベルが表示される
     */
    public function test_displays_label(): void
    {
        $this->markTestIncomplete('Textareaコンポーネント実装後に有効化');

        $view = $this->blade('<x-textarea name="reason" label="修正理由" />');

        $view->assertSee('修正理由', false);
    }

    /**
     * TC-TA-002: 行数の設定
     * AC-TA-002: `rows="5"` が適用される
     */
    public function test_applies_rows_attribute(): void
    {
        $this->markTestIncomplete('Textareaコンポーネント実装後に有効化');

        $view = $this->blade('<x-textarea name="reason" :rows="5" />');

        $view->assertSee('rows="5"', false);
    }

    /**
     * TC-TA-003: プレースホルダーの表示
     * AC-TA-003: プレースホルダーが表示される
     */
    public function test_displays_placeholder(): void
    {
        $this->markTestIncomplete('Textareaコンポーネント実装後に有効化');

        $view = $this->blade('<x-textarea name="reason" placeholder="詳細を入力" />');

        $view->assertSee('placeholder="詳細を入力"', false);
    }

    /**
     * TC-TA-004: 必須表示
     * AC-TA-004: ラベルに `*必須` が表示される
     */
    public function test_displays_required_indicator(): void
    {
        $this->markTestIncomplete('Textareaコンポーネント実装後に有効化');

        $view = $this->blade('<x-textarea name="reason" label="修正理由" :required="true" />');

        $view->assertSee('*必須', false);
    }

    /**
     * TC-TA-005: Livewire双方向バインディング
     * AC-TA-201: Livewireプロパティと双方向バインディングされる
     *
     * Note: This requires Livewire component implementation.
     * Skipped as it requires Livewire component test.
     */
    public function test_livewire_two_way_binding(): void
    {
        $this->markTestSkipped('Livewire component test - requires Livewire implementation');
    }

    /**
     * TC-TA-006: デフォルト行数の適用
     * AC-TA-005: デフォルトでは `rows="3"` が適用される
     */
    public function test_default_rows_is_three(): void
    {
        $this->markTestIncomplete('Textareaコンポーネント実装後に有効化');

        $view = $this->blade('<x-textarea name="reason" />');

        $view->assertSee('rows="3"', false);
    }

    /**
     * TC-TA-007: フォーカス時のスタイル
     * AC-TA-101: フォーカス時、`focus:ring-indigo-500` と `focus:border-indigo-500` が適用される
     *
     * Note: This is a frontend focus state test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_focus_state_applies_indigo_styles(): void
    {
        $this->markTestSkipped('Frontend focus state test - requires Dusk');
    }
}
