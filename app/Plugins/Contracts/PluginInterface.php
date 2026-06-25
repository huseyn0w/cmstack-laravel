<?php

namespace App\Plugins\Contracts;

use App\Support\Hooks;

/**
 * An in-repo plugin: stable metadata plus a boot() that registers hook listeners.
 * Discovered by App\Support\PluginManager and booted only when enabled.
 */
interface PluginInterface
{
    public function slug(): string;

    public function name(): string;

    public function description(): string;

    public function boot(Hooks $hooks): void;
}
