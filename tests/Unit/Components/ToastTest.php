<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToastTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-T-001: Success タイプの表示
     * AC-T-001: `bg-green-600 text-white` が適用され、アイコンが `fa-check-circle` になる
     */
    public function test_success_type_displays_correctly(): void
    {
        $view = $this->blade('<x-toast type="success" message="成功しました" />');

        $view->assertSee('bg-green-600', false);
        $view->assertSee('text-white', false);
        $view->assertSee('fa-check-circle', false);
        $view->assertSee('成功しました', false);
    }

    /**
     * TC-T-002: Error タイプの表示
     * AC-T-002: `bg-red-600 text-white` が適用され、アイコンが `fa-exclamation-circle` になる
     */
    public function test_error_type_displays_correctly(): void
    {
        $view = $this->blade('<x-toast type="error" message="エラーが発生しました" />');

        $view->assertSee('bg-red-600', false);
        $view->assertSee('text-white', false);
        $view->assertSee('fa-exclamation-circle', false);
        $view->assertSee('エラーが発生しました', false);
    }

    /**
     * TC-T-003: Livewire イベント受信
     * AC-T-003: トーストが表示される
     *
     * Note: This requires Livewire event system implementation.
     * Skipped as it requires Livewire implementation.
     */
    public function test_receives_livewire_event(): void
    {
        $this->markTestSkipped('Livewire event test - requires Livewire implementation');
    }

    /**
     * TC-T-004: 自動非表示
     * AC-T-004: トーストが自動的に非表示になる
     *
     * Note: This is a frontend (Alpine.js setTimeout) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_auto_dismisses_after_delay(): void
    {
        $this->markTestSkipped('Frontend auto-dismiss test - requires Dusk');
    }
}
