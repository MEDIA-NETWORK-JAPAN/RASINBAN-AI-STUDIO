<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-BC-001: アイテムの表示
     * AC-BC-001: すべてのアイテムが表示される
     */
    public function test_displays_all_breadcrumb_items(): void
    {
        $this->markTestIncomplete('Breadcrumbコンポーネント実装後に有効化');

        $items = [
            ['href' => '/admin', 'label' => 'ホーム'],
            ['href' => null, 'label' => '拠点'],
        ];

        $view = $this->blade('<x-breadcrumb :items="$items" />', ['items' => $items]);

        $view->assertSee('ホーム', false);
        $view->assertSee('拠点', false);
    }

    /**
     * TC-BC-002: 最初のアイテムのリンク
     * AC-BC-002: 最初のアイテムがリンクとして表示される
     */
    public function test_first_item_is_a_link(): void
    {
        $this->markTestIncomplete('Breadcrumbコンポーネント実装後に有効化');

        $items = [
            ['href' => '/admin', 'label' => 'ホーム'],
            ['href' => null, 'label' => '拠点'],
        ];

        $view = $this->blade('<x-breadcrumb :items="$items" />', ['items' => $items]);

        $view->assertSee('href="/admin"', false);
        $view->assertSee('ホーム', false);
    }

    /**
     * TC-BC-003: 最後のアイテムの現在地表示
     * AC-BC-003: 最後のアイテムが現在地（リンクなし）として表示される
     */
    public function test_last_item_is_not_a_link(): void
    {
        $this->markTestIncomplete('Breadcrumbコンポーネント実装後に有効化');

        $items = [
            ['href' => '/admin', 'label' => 'ホーム'],
            ['href' => null, 'label' => '拠点'],
        ];

        $view = $this->blade('<x-breadcrumb :items="$items" />', ['items' => $items]);

        // 最後のアイテムはリンクではない（spanやテキストのみ）
        $view->assertSee('拠点', false);

        // 最後のアイテムが <a> タグで囲まれていないことを確認
        // パターン: "拠点" の後に </a> が続かないことを検証
        $html = $view->getContent();
        $this->assertStringNotContainsString('<a', substr($html, strrpos($html, '拠点')));
    }

    /**
     * TC-BC-004: 区切り記号の表示
     * AC-BC-005: 区切り記号として `fa-chevron-right` が表示される
     */
    public function test_displays_separator_between_items(): void
    {
        $this->markTestIncomplete('Breadcrumbコンポーネント実装後に有効化');

        $items = [
            ['href' => '/admin', 'label' => 'ホーム'],
            ['href' => '/admin/teams', 'label' => '拠点一覧'],
            ['href' => null, 'label' => '詳細'],
        ];

        $view = $this->blade('<x-breadcrumb :items="$items" />', ['items' => $items]);

        $view->assertSee('fa-chevron-right', false);
    }

    /**
     * TC-BC-005: 中間アイテムのリンク表示
     * AC-BC-004: 中間のアイテムがリンクとして表示される
     */
    public function test_middle_items_are_links(): void
    {
        $this->markTestIncomplete('Breadcrumbコンポーネント実装後に有効化');

        $items = [
            ['href' => '/admin', 'label' => 'ホーム'],
            ['href' => '/admin/teams', 'label' => '拠点一覧'],
            ['href' => null, 'label' => '詳細'],
        ];

        $view = $this->blade('<x-breadcrumb :items="$items" />', ['items' => $items]);

        // 中間アイテム「拠点一覧」がリンクとして表示される
        $view->assertSee('href="/admin/teams"', false);
        $view->assertSee('拠点一覧', false);
    }

    /**
     * TC-BC-006: 単一アイテム時の区切り記号非表示
     * AC-BC-401: Props `items` が1個のみの場合、区切り記号は表示されない
     */
    public function test_no_separator_for_single_item(): void
    {
        $this->markTestIncomplete('Breadcrumbコンポーネント実装後に有効化');

        $items = [
            ['href' => null, 'label' => 'ホーム'],
        ];

        $view = $this->blade('<x-breadcrumb :items="$items" />', ['items' => $items]);

        // 区切り記号が表示されない
        $view->assertDontSee('fa-chevron-right', false);
    }
}
