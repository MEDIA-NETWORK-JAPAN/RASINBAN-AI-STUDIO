<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmptyStateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-ES-001: Colspan の適用
     * AC-ES-001: `<td>` の `colspan="5"` が適用される
     */
    public function test_applies_colspan_attribute(): void
    {
        $this->markTestIncomplete('EmptyStateコンポーネント実装後に有効化');

        $view = $this->blade('<x-empty-state :colspan="5" message="データがありません" />');

        $view->assertSee('colspan="5"', false);
    }

    /**
     * TC-ES-002: メッセージの表示
     * AC-ES-002: メッセージが表示される
     */
    public function test_displays_message(): void
    {
        $this->markTestIncomplete('EmptyStateコンポーネント実装後に有効化');

        $view = $this->blade('<x-empty-state message="データがありません" />');

        $view->assertSee('データがありません', false);
    }

    /**
     * TC-ES-003: カスタムアイコンの表示
     * AC-ES-003: `fa-inbox` アイコンが `text-2xl text-gray-300` で表示される
     */
    public function test_displays_custom_icon(): void
    {
        $this->markTestIncomplete('EmptyStateコンポーネント実装後に有効化');

        $view = $this->blade('<x-empty-state message="データがありません" icon="inbox" />');

        $view->assertSee('fa-inbox', false);
        $view->assertSee('text-2xl', false);
        $view->assertSee('text-gray-300', false);
    }

    /**
     * TC-ES-004: デフォルトアイコンの表示
     * AC-ES-004: `fa-search` が表示される
     */
    public function test_displays_default_icon(): void
    {
        $this->markTestIncomplete('EmptyStateコンポーネント実装後に有効化');

        $view = $this->blade('<x-empty-state message="データがありません" />');

        $view->assertSee('fa-search', false);
    }
}
