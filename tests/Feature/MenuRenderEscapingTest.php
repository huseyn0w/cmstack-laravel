<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Fix #6: render_menu() must escape user-controlled menu titles/slugs so a
 * malicious title cannot inject script into the rendered (and {!! !!}-printed)
 * markup.
 */
class MenuRenderEscapingTest extends TestCase
{
    public function test_script_title_is_escaped(): void
    {
        $menu = [
            (object) [
                'type' => 'custom',
                'title' => '<script>alert(1)</script>',
                'slug' => '/',
            ],
        ];

        $html = render_menu($menu, ['menu_type' => 'list']);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_malicious_slug_attribute_is_escaped(): void
    {
        $menu = [
            (object) [
                'type' => "custom' onmouseover='alert(1)",
                'title' => 'evil',
                'slug' => "x' onclick='alert(1)",
            ],
        ];

        $html = render_menu($menu, ['menu_type' => 'list']);

        // The raw attribute-breaking quote sequence must not survive verbatim.
        $this->assertStringNotContainsString("onmouseover='alert(1)'", $html);
    }
}
