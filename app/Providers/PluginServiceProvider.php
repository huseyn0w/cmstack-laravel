<?php

namespace App\Providers;

use App\Support\Hooks;
use App\Support\PluginManager;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * P9: prime the hook engine with the enabled plugins.
 *
 * Loading is lazy — it runs the first time the Hooks singleton is resolved
 * (once per request/app instance) rather than during provider boot. That keeps
 * the database read out of the bootstrap phase, so it always uses the final DB
 * connection and re-evaluates the enabled set on every request (enable/disable
 * with no restart). Guarded by Schema::hasTable for fresh installs / migrations.
 */
class PluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $primed = false;

        $this->app->afterResolving(Hooks::class, function (Hooks $hooks, $app) use (&$primed) {
            if ($primed) {
                return;
            }
            $primed = true;

            if (! Schema::hasTable('plugins')) {
                return;
            }

            $manager = $app->make(PluginManager::class);
            $manager->sync();
            $manager->loadEnabled($hooks);
        });
    }
}
