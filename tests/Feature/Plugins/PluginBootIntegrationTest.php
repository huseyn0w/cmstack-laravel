<?php

namespace Tests\Feature\Plugins;

use App\Repositories\CPanelPluginRepository;
use App\Support\PluginManager;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The PluginServiceProvider primes the shared Hooks instance with enabled
 * plugins the first time Hooks is resolved.
 */
class PluginBootIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        app(PluginManager::class)->sync();
    }

    public function test_enabled_plugin_filter_is_active_on_first_hooks_resolution(): void
    {
        app(CPanelPluginRepository::class)->setEnabled('reading-time', true);

        // First resolution of the hooks singleton triggers the lazy primer.
        $filtered = app('hooks')->filter('the_content', str_repeat('word ', 400));

        $this->assertStringContainsString('min read', $filtered);
    }

    public function test_disabled_plugin_filter_is_inactive(): void
    {
        $this->assertSame('body', app('hooks')->filter('the_content', 'body'));
    }
}
