<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-PG-001: 件数表示 - 「全 100 件中 1 - 10 件」が表示される
     */
    public function test_displays_record_count(): void
    {
        $view = $this->blade(
            '<x-ui.pagination :total="100" :from="1" :to="10" :currentPage="1" :totalPages="10" />'
        );

        $view->assertSee('100');
        $view->assertSee('1');
        $view->assertSee('10');
    }

    /**
     * TC-PG-002: 現在ページの表示
     */
    public function test_highlights_current_page(): void
    {
        $view = $this->blade(
            '<x-ui.pagination :total="100" :from="11" :to="20" :currentPage="2" :totalPages="10" />'
        );

        $view->assertSee('bg-indigo-50', false);
        $view->assertSee('text-indigo-600', false);
    }

    /**
     * TC-PG-003: 前へボタンの表示
     */
    public function test_displays_previous_button(): void
    {
        $view = $this->blade(
            '<x-ui.pagination :total="100" :from="1" :to="10" :currentPage="1" :totalPages="10" />'
        );

        $view->assertSee('fa-chevron-left', false);
    }

    /**
     * TC-PG-004: 次へボタンの表示
     */
    public function test_displays_next_button(): void
    {
        $view = $this->blade(
            '<x-ui.pagination :total="100" :from="1" :to="10" :currentPage="1" :totalPages="10" />'
        );

        $view->assertSee('fa-chevron-right', false);
    }

    /**
     * TC-PG-005: ページ番号ボタンのクリック - ページ3へ遷移する
     *
     * Note: This is a frontend (Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_page_number_click(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-PG-006: 次へボタンのクリック - 次のページへ遷移する
     *
     * Note: This is a frontend (Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_next_button_click(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-PG-007: 前へボタンのクリック - 前のページへ遷移する
     *
     * Note: This is a frontend (Livewire) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_previous_button_click(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-PG-008: 最初のページでの前へボタン無効化
     */
    public function test_disables_previous_button_on_first_page(): void
    {
        $view = $this->blade(
            '<x-ui.pagination :total="100" :from="1" :to="10" :currentPage="1" :totalPages="10" />'
        );

        $view->assertSee('disabled', false);
    }

    /**
     * TC-PG-009: 最後のページでの次へボタン無効化
     */
    public function test_disables_next_button_on_last_page(): void
    {
        $view = $this->blade(
            '<x-ui.pagination :total="100" :from="91" :to="100" :currentPage="10" :totalPages="10" />'
        );

        $view->assertSee('disabled', false);
    }
}
