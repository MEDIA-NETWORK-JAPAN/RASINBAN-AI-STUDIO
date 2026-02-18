<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageHeaderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-PH-001: タイトルの表示
     * AC-PH-001: ページタイトルが `text-xl font-bold` で表示される
     */
    public function test_displays_page_title_with_bold_style(): void
    {
        $view = $this->blade('<x-page-header title="拠点一覧" />');

        $view->assertSee('拠点一覧', false);
        $view->assertSee('text-xl', false);
        $view->assertSee('font-bold', false);
    }

    /**
     * TC-PH-002: 説明文の表示
     * AC-PH-002: 説明文が `text-xs text-gray-500` で表示される
     */
    public function test_displays_description_with_small_gray_style(): void
    {
        $view = $this->blade('<x-page-header title="拠点一覧" description="登録拠点の一覧" />');

        $view->assertSee('登録拠点の一覧', false);
        $view->assertSee('text-xs', false);
        $view->assertSee('text-gray-500', false);
    }

    /**
     * TC-PH-003: アクションボタンの表示
     * AC-PH-003: 右側にアクションボタン群が表示される
     */
    public function test_displays_action_buttons_on_right(): void
    {
        $view = $this->blade('
            <x-page-header title="拠点一覧">
                <x-slot name="actions">
                    <button>新規登録</button>
                </x-slot>
            </x-page-header>
        ');

        $view->assertSee('新規登録', false);
    }

    /**
     * TC-PH-004: 説明文未設定時の非表示
     * AC-PH-401: Props `description` が未設定の場合、説明文は表示されない
     */
    public function test_description_not_displayed_when_not_provided(): void
    {
        $view = $this->blade('<x-page-header title="拠点一覧" />');

        // 説明文用のクラスが存在しないことを確認
        $view->assertDontSee('text-xs text-gray-500', false);
    }
}
