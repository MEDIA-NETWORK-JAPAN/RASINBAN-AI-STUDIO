<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusBadgeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-SB-001: Active ステータスの表示
     */
    public function test_renders_active_status(): void
    {
        $view = $this->blade(
            '<x-ui.status-badge status="active" label="Active" />'
        );

        $view->assertSee('Active');
        $view->assertSee('bg-green-100', false);
        $view->assertSee('text-green-800', false);
        $view->assertSee('bg-green-500', false);
    }

    /**
     * TC-SB-002: Inactive ステータスの表示
     */
    public function test_renders_inactive_status(): void
    {
        $view = $this->blade(
            '<x-ui.status-badge status="inactive" label="Inactive" />'
        );

        $view->assertSee('Inactive');
        $view->assertSee('bg-gray-100', false);
        $view->assertSee('text-gray-600', false);
        $view->assertSee('bg-gray-400', false);
    }

    /**
     * TC-SB-003: Warning ステータスの表示
     */
    public function test_renders_warning_status(): void
    {
        $view = $this->blade(
            '<x-ui.status-badge status="warning" label="Warning" />'
        );

        $view->assertSee('Warning');
        $view->assertSee('bg-yellow-100', false);
        $view->assertSee('text-yellow-800', false);
        $view->assertSee('bg-yellow-500', false);
    }

    /**
     * TC-SB-004: Error ステータスの表示
     */
    public function test_renders_error_status(): void
    {
        $view = $this->blade(
            '<x-ui.status-badge status="error" label="Error" />'
        );

        $view->assertSee('Error');
        $view->assertSee('bg-red-100', false);
        $view->assertSee('text-red-800', false);
        $view->assertSee('bg-red-500', false);
    }

    /**
     * TC-SB-005: ドットアイコンの表示
     * AC-SB-006: ドットアイコンが `w-1.5 h-1.5 rounded-full` で表示される
     */
    public function test_displays_dot_icon_with_correct_styles(): void
    {
        $view = $this->blade(
            '<x-ui.status-badge status="active" label="Active" />'
        );

        $view->assertSee('w-1.5', false);
        $view->assertSee('h-1.5', false);
        $view->assertSee('rounded-full', false);
    }
}
