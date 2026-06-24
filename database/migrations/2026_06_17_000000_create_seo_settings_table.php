<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 7 (SEO/GEO): global SEO settings singleton.
 *
 * One row (id = 1) holds site-wide SEO/social/verification/analytics defaults
 * that drive the public <head> meta partial, sitemap.xml and robots.txt.
 * Portable across MySQL and SQLite (no DB-specific types). Nullable strings +
 * boolean defaults keep existing seeders / fresh installs working.
 */
class CreateSeoSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Meta defaults
            $table->string('title_separator', 8)->default('—');
            $table->text('default_meta_description')->nullable();
            $table->string('default_og_image')->nullable();

            // Social
            $table->string('og_site_name')->nullable();
            $table->string('twitter_handle')->nullable();

            // Search-engine verification meta tags
            $table->string('google_site_verification')->nullable();
            $table->string('bing_site_verification')->nullable();

            // Optional analytics (rendered only when set, async/deferred)
            $table->string('ga4_measurement_id')->nullable();
            $table->string('gtm_container_id')->nullable();

            // Indexing / robots / sitemap
            $table->boolean('discourage_search_engines')->default(false);
            $table->boolean('sitemap_enabled')->default(true);
            $table->text('robots_extra')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo_settings');
    }
}
