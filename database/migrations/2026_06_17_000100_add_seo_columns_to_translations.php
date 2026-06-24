<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7 (SEO/GEO): per-entity SEO overrides.
 *
 * Adds an optional canonical URL override and a per-entity noindex flag to the
 * translation tables (post/page/category). hasColumn guards keep the migration
 * idempotent and safe to run on both MySQL and SQLite.
 */
class AddSeoColumnsToTranslations extends Migration
{
    private array $tables = [
        'post_translations',
        'page_translations',
        'category_translations',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (! Schema::hasColumn($table, 'canonical_url')) {
                    $blueprint->string('canonical_url')->nullable();
                }
                if (! Schema::hasColumn($table, 'meta_noindex')) {
                    $blueprint->boolean('meta_noindex')->default(false);
                }
            });
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                if (Schema::hasColumn($table, 'canonical_url')) {
                    $blueprint->dropColumn('canonical_url');
                }
                if (Schema::hasColumn($table, 'meta_noindex')) {
                    $blueprint->dropColumn('meta_noindex');
                }
            });
        }
    }
}
