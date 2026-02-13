<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-AL-001: タイトルの表示
     * AC-AL-001: PageHeaderにタイトルが表示される
     */
    public function test_displays_page_title_in_header(): void
    {
        $this->markTestIncomplete('AdminLayoutコンポーネント実装後に有効化');

        $view = $this->blade('<x-admin-layout title="ダッシュボード" />');

        $view->assertSee('ダッシュボード', false);
    }

    /**
     * TC-AL-002: 説明文の表示
     * AC-AL-002: PageHeaderに説明文が表示される
     */
    public function test_displays_description_in_header(): void
    {
        $this->markTestIncomplete('AdminLayoutコンポーネント実装後に有効化');

        $view = $this->blade('<x-admin-layout title="ダッシュボード" description="システム概要" />');

        $view->assertSee('システム概要', false);
    }

    /**
     * TC-AL-003: メインコンテンツの表示
     * AC-AL-003: メインコンテンツエリアに内容が表示される
     */
    public function test_displays_main_content(): void
    {
        $this->markTestIncomplete('AdminLayoutコンポーネント実装後に有効化');

        $view = $this->blade('
            <x-admin-layout title="ダッシュボード">
                <div>メインコンテンツ</div>
            </x-admin-layout>
        ');

        $view->assertSee('メインコンテンツ', false);
    }
}
