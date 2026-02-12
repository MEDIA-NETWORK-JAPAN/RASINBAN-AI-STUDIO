<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataTableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-TBL-001: タイトルの表示
     */
    public function test_renders_title(): void
    {
        $view = $this->blade(
            '<x-ui.data-table title="拠点一覧">
                <x-slot name="header"><th>ヘッダー</th></x-slot>
                <x-slot name="body"><td>ボディ</td></x-slot>
            </x-ui.data-table>'
        );

        $view->assertSee('拠点一覧');
    }

    /**
     * TC-TBL-002: ヘッダー行の表示
     */
    public function test_renders_header_row(): void
    {
        $view = $this->blade(
            '<x-ui.data-table>
                <x-slot name="header">
                    <th>拠点名</th>
                    <th>プラン</th>
                </x-slot>
                <x-slot name="body"><td>ボディ</td></x-slot>
            </x-ui.data-table>'
        );

        $view->assertSee('拠点名');
        $view->assertSee('プラン');
    }

    /**
     * TC-TBL-003: ボディ行の表示
     */
    public function test_renders_body_rows(): void
    {
        $view = $this->blade(
            '<x-ui.data-table>
                <x-slot name="header"><th>ヘッダー</th></x-slot>
                <x-slot name="body">
                    <tr><td>東京本社</td></tr>
                    <tr><td>大阪支店</td></tr>
                </x-slot>
            </x-ui.data-table>'
        );

        $view->assertSee('東京本社');
        $view->assertSee('大阪支店');
    }

    /**
     * TC-TBL-004: フッターの表示
     */
    public function test_renders_footer(): void
    {
        $view = $this->blade(
            '<x-ui.data-table>
                <x-slot name="header"><th>ヘッダー</th></x-slot>
                <x-slot name="body"><td>ボディ</td></x-slot>
                <x-slot name="footer">
                    <div>ページネーション</div>
                </x-slot>
            </x-ui.data-table>'
        );

        $view->assertSee('ページネーション');
    }

    /**
     * TC-TBL-005: ヘッダーアクションの表示
     */
    public function test_renders_header_actions(): void
    {
        $view = $this->blade(
            '<x-ui.data-table title="拠点一覧">
                <x-slot name="headerActions">
                    <button>新規作成</button>
                </x-slot>
                <x-slot name="header"><th>ヘッダー</th></x-slot>
                <x-slot name="body"><td>ボディ</td></x-slot>
            </x-ui.data-table>'
        );

        $view->assertSee('新規作成');
    }
}
