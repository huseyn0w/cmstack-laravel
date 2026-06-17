<?php

namespace App\Mcp\Tools\Settings;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelSeoSettingsRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update the global SEO/GEO settings singleton. Only the fields you pass are changed. Useful for tuning how the site presents itself to search engines and AI crawlers. Requires the manage_general_settings permission.')]
class UpdateSeoSettingsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelSeoSettingsRepository $seo) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'title_separator' => $schema->string()->description('Separator between page title and site name, e.g. "|".'),
            'default_meta_description' => $schema->string()->description('Fallback meta description for pages without their own.'),
            'default_og_image' => $schema->string()->description('Default Open Graph image URL.'),
            'og_site_name' => $schema->string()->description('Open Graph site name.'),
            'twitter_handle' => $schema->string()->description('Twitter/X @handle for Twitter cards.'),
            'google_site_verification' => $schema->string()->description('Google Search Console verification token.'),
            'bing_site_verification' => $schema->string()->description('Bing verification token.'),
            'ga4_measurement_id' => $schema->string()->description('GA4 measurement id, e.g. "G-XXXX".'),
            'gtm_container_id' => $schema->string()->description('Google Tag Manager container id.'),
            'discourage_search_engines' => $schema->boolean()->description('When true, ask engines not to index the site.'),
            'sitemap_enabled' => $schema->boolean()->description('Whether /sitemap.xml is served.'),
            'robots_extra' => $schema->string()->description('Extra lines appended to robots.txt.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'title_separator' => ['nullable', 'string', 'max:10'],
            'default_meta_description' => ['nullable', 'string', 'max:500'],
            'default_og_image' => ['nullable', 'string', 'max:2048'],
            'og_site_name' => ['nullable', 'string', 'max:255'],
            'twitter_handle' => ['nullable', 'string', 'max:255'],
            'google_site_verification' => ['nullable', 'string', 'max:255'],
            'bing_site_verification' => ['nullable', 'string', 'max:255'],
            'ga4_measurement_id' => ['nullable', 'string', 'max:50'],
            'gtm_container_id' => ['nullable', 'string', 'max:50'],
            'discourage_search_engines' => ['nullable', 'boolean'],
            'sitemap_enabled' => ['nullable', 'boolean'],
            'robots_extra' => ['nullable', 'string', 'max:5000'],
        ]);

        $validated = array_filter($validated, fn ($v) => ! is_null($v));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one SEO setting to change.');
        }

        $instance = $this->seo->firstOrNew();
        $instance->fill($validated);
        $ok = $instance->save();

        return $ok
            ? Response::structured(['updated' => true, 'fields' => array_keys($validated)])
            : Response::error('Could not update SEO settings.');
    }
}
