<?php

namespace Tests\Unit;

use App\Services\Captcha\CaptchaService;
use Tests\TestCase;

/**
 * Unit coverage for the captcha wrapper. When disabled (no keys), render()
 * returns an empty string and verify() always passes. When enabled with keys,
 * an empty token is rejected up front and the widget HTML is emitted.
 */
class CaptchaServiceTest extends TestCase
{
    public function test_service_is_disabled_without_keys(): void
    {
        $captcha = new CaptchaService(['enabled' => true]);

        $this->assertFalse($captcha->enabled());
        $this->assertSame('', $captcha->render());
        // Disabled -> verification always passes so forms keep working.
        $this->assertTrue($captcha->verify(null));
        $this->assertTrue($captcha->verify('anything'));
    }

    public function test_service_is_disabled_when_flag_off_even_with_keys(): void
    {
        $captcha = new CaptchaService([
            'enabled' => false,
            'site_key' => 'sk',
            'secret_key' => 'secret',
        ]);

        $this->assertFalse($captcha->enabled());
        $this->assertTrue($captcha->verify(null));
    }

    public function test_enabled_service_rejects_empty_token_without_network(): void
    {
        $captcha = new CaptchaService([
            'enabled' => true,
            'site_key' => 'dummy-site',
            'secret_key' => 'dummy-secret',
            'version' => 'v3',
        ]);

        $this->assertTrue($captcha->enabled());
        // An empty token short-circuits to false before any HTTP call.
        $this->assertFalse($captcha->verify(null));
        $this->assertFalse($captcha->verify(''));
    }

    public function test_enabled_service_renders_v3_widget_with_escaped_site_key(): void
    {
        $captcha = new CaptchaService([
            'enabled' => true,
            'site_key' => 'my"site',
            'secret_key' => 'dummy-secret',
            'version' => 'v3',
        ]);

        $html = $captcha->render();

        $this->assertStringContainsString('g-recaptcha-response', $html);
        $this->assertStringContainsString('recaptcha/api.js', $html);
        // The site key must be HTML-escaped in the output.
        $this->assertStringContainsString('my&quot;site', $html);
        $this->assertStringNotContainsString('my"site', $html);
    }

    public function test_enabled_service_renders_v2_checkbox_widget(): void
    {
        $captcha = new CaptchaService([
            'enabled' => true,
            'site_key' => 'site',
            'secret_key' => 'secret',
            'version' => 'v2',
        ]);

        $html = $captcha->render();

        $this->assertStringContainsString('g-recaptcha', $html);
        $this->assertStringContainsString('data-sitekey="site"', $html);
    }
}
