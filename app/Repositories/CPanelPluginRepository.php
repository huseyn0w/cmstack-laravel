<?php

namespace App\Repositories;

use App\Http\Models\CPanel\Plugin;
use Illuminate\Support\Collection;

/**
 * Data access for the `plugins` registry. The only layer that touches the
 * Plugin model; PluginManager / services / controllers go through here.
 */
class CPanelPluginRepository
{
    public function ensureExists(string $slug): void
    {
        Plugin::firstOrCreate(['slug' => $slug], ['enabled' => false]);
    }

    /** @return array<int, string> */
    public function enabledSlugs(): array
    {
        return Plugin::where('enabled', true)->pluck('slug')->all();
    }

    public function setEnabled(string $slug, bool $enabled): void
    {
        Plugin::where('slug', $slug)->update(['enabled' => $enabled]);
    }

    /** @return Collection<string, Plugin> */
    public function allKeyedBySlug(): Collection
    {
        return Plugin::all()->keyBy('slug');
    }
}
