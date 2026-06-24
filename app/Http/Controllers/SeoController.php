<?php

namespace App\Http\Controllers;

use App\Services\Front\SeoFeedService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

/**
 * Phase 7 (SEO/GEO): public SEO endpoints — sitemap.xml, robots.txt, llms.txt.
 *
 * All read-only, guest-accessible, and lightly cached. This controller is a pure
 * HTTP boundary: it reads the global SEO toggle, delegates all composition (and
 * the underlying data access via repositories) to SeoFeedService, and wraps the
 * result in a Response / Cache layer. No Eloquent or query building lives here.
 */
class SeoController extends Controller
{
    public function __construct(private SeoFeedService $service) {}

    /**
     * Dynamic XML sitemap with hreflang alternates.
     */
    public function sitemap()
    {
        $seo = get_seo_settings();

        // Sitemap can be disabled from the admin SEO settings.
        if ($seo && isset($seo->sitemap_enabled) && ! $seo->sitemap_enabled) {
            abort(404);
        }

        $xml = Cache::remember('cmstack_laravel.sitemap.xml', now()->addHour(), function () {
            return $this->service->buildSitemap();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * Dynamic robots.txt honouring the "discourage search engines" toggle and
     * the admin "extra lines", and pointing at the sitemap.
     */
    public function robots()
    {
        return Response::make($this->service->buildRobots(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Minimal llms.txt (emerging GEO standard): a short markdown index of the
     * site and key links for generative engines.
     */
    public function llms()
    {
        return Response::make($this->service->buildLlms(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
