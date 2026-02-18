<?php

namespace Tests\Unit\Components;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KpiCardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TC-KPI-001: ラベルの表示
     */
    public function test_renders_label(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" />'
        );

        $view->assertSee('総拠点数');
    }

    /**
     * TC-KPI-002: メイン数値の表示
     */
    public function test_renders_main_value(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" />'
        );

        $view->assertSee('42');
        $view->assertSee('text-3xl', false);
        $view->assertSee('font-bold', false);
    }

    /**
     * TC-KPI-003: アイコンの表示
     */
    public function test_renders_icon(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" icon="fa-building" />'
        );

        $view->assertSee('fa-building', false);
    }

    /**
     * TC-KPI-004: カラー（indigo）の適用
     */
    public function test_applies_indigo_color(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" color="indigo" />'
        );

        $view->assertSee('bg-indigo-50', false);
        $view->assertSee('text-indigo-600', false);
    }

    /**
     * TC-KPI-005: カラー（blue）の適用
     */
    public function test_applies_blue_color(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" color="blue" />'
        );

        $view->assertSee('bg-blue-50', false);
        $view->assertSee('text-blue-600', false);
    }

    /**
     * TC-KPI-006: カラー（green）の適用
     */
    public function test_applies_green_color(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" color="green" />'
        );

        $view->assertSee('bg-green-50', false);
        $view->assertSee('text-green-600', false);
    }

    /**
     * TC-KPI-007: サブ数値の表示
     */
    public function test_renders_sub_value(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" subValue="+12%" />'
        );

        $view->assertSee('+12%');
    }

    /**
     * TC-KPI-008: サブ数値カラー（green）の適用
     */
    public function test_applies_sub_value_color(): void
    {
        $view = $this->blade(
            '<x-ui.kpi-card label="総拠点数" value="42" subValue="+12%" subColor="green" />'
        );

        $view->assertSee('+12%');
        $view->assertSee('text-green-600', false);
    }
}
