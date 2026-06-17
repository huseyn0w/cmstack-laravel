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

#[Description('Read-only. Return the global SEO/GEO settings: title separator, default meta description and OG image, site verification tokens, analytics ids, and indexing/sitemap flags. Requires the manage_general_settings permission.')]
class GetSeoSettingsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelSeoSettingsRepository $seo) {}

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $row = $this->seo->firstOrNew();

        return Response::structured([
            'title_separator' => $row->title_separator ?? null,
            'default_meta_description' => $row->default_meta_description ?? null,
            'default_og_image' => $row->default_og_image ?? null,
            'og_site_name' => $row->og_site_name ?? null,
            'twitter_handle' => $row->twitter_handle ?? null,
            'google_site_verification' => $row->google_site_verification ?? null,
            'bing_site_verification' => $row->bing_site_verification ?? null,
            'ga4_measurement_id' => $row->ga4_measurement_id ?? null,
            'gtm_container_id' => $row->gtm_container_id ?? null,
            'discourage_search_engines' => (bool) ($row->discourage_search_engines ?? false),
            'sitemap_enabled' => (bool) ($row->sitemap_enabled ?? false),
            'robots_extra' => $row->robots_extra ?? null,
        ]);
    }
}
