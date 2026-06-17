<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GEO (Generative Engine Optimization) settings singleton (row id = 1).
 *
 * Captures the business facts that generative engines (ChatGPT, Perplexity,
 * Gemini, Google AI Overviews) need to understand and cite the site. The admin
 * fills these in once; the front-end turns them into schema.org JSON-LD
 * (Organization / LocalBusiness / Service / FAQPage) and an enriched llms.txt.
 * Mirrors the seo_settings singleton convention: one row, no timestamps,
 * model-cached.
 */
class CreateGeoSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('geo_settings', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Identity
            $table->string('business_name')->nullable();
            // schema.org @type: Organization | LocalBusiness | ProfessionalService | Person
            $table->string('business_type', 50)->default('Organization');
            $table->text('description')->nullable();        // what you do / who you serve
            $table->string('founder_name')->nullable();     // expertise attribution

            // Services & reach (one item per line)
            $table->text('services')->nullable();           // services offered
            $table->string('service_area')->nullable();     // areaServed, e.g. "Baku, Azerbaijan; Remote, EU"

            // Contact
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('address')->nullable();

            // Authority / citation signals (one URL per line): profiles, socials, listings
            $table->text('same_as')->nullable();

            // FAQ — one "Question | Answer" pair per line -> FAQPage schema
            $table->text('faq')->nullable();

            // Output toggles
            $table->boolean('emit_jsonld')->default(true);
            $table->boolean('include_in_llms')->default(true);
        });
    }

    public function down()
    {
        Schema::dropIfExists('geo_settings');
    }
}
