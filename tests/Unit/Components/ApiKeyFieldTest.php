<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiKeyFieldTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-AK-001: デフォルトマスク表示
     * AC-AK-001: `type="password"` でAPIキーがマスクされる
     */
    public function test_default_state_masks_api_key(): void
    {
        $view = $this->blade('<x-api-key-field value="sk-test1234567890" label="APIキー" />');

        $view->assertSee('type="password"', false);
        $view->assertSee('sk-test1234567890', false);
    }

    /**
     * TC-AK-002: ラベルの表示
     * AC-AK-002: ラベルが表示される
     */
    public function test_displays_label(): void
    {
        $view = $this->blade('<x-api-key-field value="sk-test" label="APIキー" />');

        $view->assertSee('APIキー', false);
    }

    /**
     * TC-AK-003: 表示切替ボタンの表示
     * AC-AK-003: 表示切替ボタン（fa-eye / fa-eye-slash）が表示される
     */
    public function test_displays_toggle_visibility_button(): void
    {
        $view = $this->blade('<x-api-key-field value="sk-test" label="APIキー" />');

        // デフォルトはマスク状態なので fa-eye が表示される
        $view->assertSee('fa-eye', false);
    }

    /**
     * TC-AK-004: コピーボタンの表示
     * AC-AK-004: コピーボタン（fa-copy）が表示される
     */
    public function test_displays_copy_button(): void
    {
        $view = $this->blade('<x-api-key-field value="sk-test" label="APIキー" />');

        $view->assertSee('fa-copy', false);
    }

    /**
     * TC-AK-005: 読み取り専用の確認
     * AC-AK-005: 入力フィールドが `readonly` で編集不可
     */
    public function test_input_field_is_readonly(): void
    {
        $view = $this->blade('<x-api-key-field value="sk-test" label="APIキー" />');

        $view->assertSee('readonly', false);
    }

    /**
     * TC-AK-006: 表示切替（表示）
     * AC-AK-101: 表示切替ボタンをクリックすると `type="text"` に切り替わりAPIキーが表示される
     *
     * Note: This is a frontend (Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_toggle_visibility_shows_api_key(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-AK-007: 表示切替（非表示）
     * AC-AK-102: 再度表示切替ボタンをクリックすると `type="password"` に戻る
     *
     * Note: This is a frontend (Alpine.js) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_toggle_visibility_hides_api_key(): void
    {
        $this->markTestSkipped('Frontend interaction test - requires Dusk');
    }

    /**
     * TC-AK-008: コピー機能
     * AC-AK-103: コピーボタンをクリックするとAPIキーがクリップボードにコピーされる
     *
     * Note: This is a frontend (JavaScript Clipboard API) interaction test.
     * Skipped as it requires browser testing (Dusk).
     */
    public function test_copy_button_copies_api_key_to_clipboard(): void
    {
        $this->markTestSkipped('Frontend clipboard interaction test - requires Dusk');
    }

    /**
     * TC-AK-009: フィールドスタイルの適用
     * AC-AK-006: フィールドに `font-mono` と `bg-gray-50` が適用される
     */
    public function test_field_applies_mono_font_and_gray_background(): void
    {
        $view = $this->blade('<x-api-key-field value="sk-test" label="APIキー" />');

        $view->assertSee('font-mono', false);
        $view->assertSee('bg-gray-50', false);
    }
}
