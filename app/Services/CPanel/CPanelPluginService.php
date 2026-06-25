<?php

namespace App\Services\CPanel;

use App\Repositories\CPanelPluginRepository;
use App\Support\PluginManager;
use Illuminate\Support\Collection;

/**
 * Admin-facing plugin management: merges the filesystem-discovered plugin
 * metadata (via PluginManager) with the enabled state from the repository, and
 * toggles a plugin on/off. Data access stays in the repository.
 */
class CPanelPluginService
{
    public function __construct(
        private PluginManager $manager,
        private CPanelPluginRepository $repository,
    ) {}

    /**
     * @return Collection<int, array{slug:string,name:string,description:string,enabled:bool}>
     */
    public function listForAdmin(): Collection
    {
        $this->manager->sync();
        $rows = $this->repository->allKeyedBySlug();

        return collect($this->manager->discover())
            ->map(fn ($plugin) => [
                'slug' => $plugin->slug(),
                'name' => $plugin->name(),
                'description' => $plugin->description(),
                'enabled' => (bool) ($rows[$plugin->slug()]->enabled ?? false),
            ])
            ->values();
    }

    public function toggle(string $slug, bool $enabled): void
    {
        $this->repository->ensureExists($slug);
        $this->repository->setEnabled($slug, $enabled);
    }
}
