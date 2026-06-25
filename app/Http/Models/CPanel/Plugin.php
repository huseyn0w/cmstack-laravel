<?php

namespace App\Http\Models\CPanel;

use Illuminate\Database\Eloquent\Model;

/**
 * Runtime state of a discovered in-repo plugin (the `plugins` table). Plugin
 * behaviour lives in the App\Plugins classes; this row only records whether a
 * plugin is enabled. See App\Support\PluginManager.
 */
class Plugin extends Model
{
    protected $table = 'plugins';

    protected $fillable = ['slug', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];
}
