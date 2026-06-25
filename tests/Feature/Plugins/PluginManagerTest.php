<?php

namespace Tests\Feature\Plugins;

use App\Repositories\CPanelPluginRepository;
use App\Support\Hooks;
use App\Support\PluginManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PluginManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_discover_finds_the_reading_time_plugin(): void
    {
        $manager = app(PluginManager::class);
        $this->assertArrayHasKey('reading-time', $manager->discover());
    }

    public function test_sync_registers_discovered_plugins_as_disabled(): void
    {
        app(PluginManager::class)->sync();
        $this->assertDatabaseHas('plugins', ['slug' => 'reading-time', 'enabled' => false]);
    }

    public function test_load_enabled_boots_only_enabled_plugins(): void
    {
        $manager = app(PluginManager::class);
        $manager->sync();

        // Disabled: the_content filter must NOT run.
        $hooks = new Hooks(Event::getFacadeRoot());
        $manager->loadEnabled($hooks);
        $this->assertSame('body', $hooks->filter('the_content', 'body'));

        // Enable, reload into a fresh Hooks: filter now runs.
        app(CPanelPluginRepository::class)->setEnabled('reading-time', true);
        $hooks2 = new Hooks(Event::getFacadeRoot());
        $manager->loadEnabled($hooks2);
        $this->assertStringContainsString('min read', $hooks2->filter('the_content', str_repeat('word ', 400)));
    }

    public function test_reading_time_filter_tolerates_null_content(): void
    {
        $manager = app(PluginManager::class);
        $manager->sync();
        app(CPanelPluginRepository::class)->setEnabled('reading-time', true);

        $hooks = new Hooks(Event::getFacadeRoot());
        $manager->loadEnabled($hooks);

        // A post with no body yields null content; the filter must not crash.
        $this->assertIsString($hooks->filter('the_content', null));
    }
}
