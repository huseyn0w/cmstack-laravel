<?php

namespace Tests\Feature\Plugins;

use App\Repositories\CPanelPluginRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PluginRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private function repo(): CPanelPluginRepository
    {
        return app(CPanelPluginRepository::class);
    }

    public function test_ensure_exists_inserts_disabled_then_is_idempotent(): void
    {
        $repo = $this->repo();
        $repo->ensureExists('reading-time');
        $repo->ensureExists('reading-time');

        $this->assertDatabaseCount('plugins', 1);
        $this->assertDatabaseHas('plugins', ['slug' => 'reading-time', 'enabled' => false]);
    }

    public function test_set_enabled_and_enabled_slugs(): void
    {
        $repo = $this->repo();
        $repo->ensureExists('reading-time');
        $repo->ensureExists('other');
        $repo->setEnabled('reading-time', true);

        $this->assertSame(['reading-time'], $repo->enabledSlugs());
    }

    public function test_all_keyed_by_slug(): void
    {
        $repo = $this->repo();
        $repo->ensureExists('reading-time');

        $all = $repo->allKeyedBySlug();
        $this->assertTrue($all->has('reading-time'));
        $this->assertFalse((bool) $all['reading-time']->enabled);
    }
}
