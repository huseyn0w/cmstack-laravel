<?php

namespace App\Http\Controllers;

use App\Http\Models\Post;
use App\Http\Models\Page;
use App\Http\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

/**
 * Phase 7 (SEO/GEO): public SEO endpoints — sitemap.xml, robots.txt, llms.txt.
 *
 * All read-only, guest-accessible, and lightly cached. The sitemap lists
 * published pages/posts/categories with hreflang <xhtml:link> alternates for
 * en/ru; robots.txt honours the global "discourage" toggle and admin extra
 * lines; llms.txt is a minimal GEO markdown index.
 */
class SeoController extends Controller
{
    /**
     * Dynamic XML sitemap with hreflang alternates.
     */
    public function sitemap()
    {
        $seo = get_seo_settings();

        // Sitemap can be disabled from the admin SEO settings.
        if ($seo && isset($seo->sitemap_enabled) && !$seo->sitemap_enabled) {
            abort(404);
        }

        $xml = Cache::remember('larapress.sitemap.xml', now()->addHour(), function () {
            return $this->buildSitemap();
        });

        return Response::make($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    protected function buildSitemap(): string
    {
        $base    = rtrim(config('app.url'), '/');
        $default = config('app.locale');         // 'en'

        $urls = [];

        // --- Pages (slug "/" => homepage) ---
        $pages = Page::join('page_translations', 'pages.id', '=', 'page_translations.page_id')
            ->select('pages.id', 'page_translations.slug', 'page_translations.locale', 'page_translations.updated_at')
            ->get();
        $urls = array_merge($urls, $this->groupBy($pages, 'page_id', '', $base, $default, true));

        // --- Posts ---
        $posts = Post::join('post_translations', 'posts.id', '=', 'post_translations.post_id')
            ->select('posts.id', 'post_translations.slug', 'post_translations.locale', 'post_translations.updated_at', 'post_translations.post_id')
            ->get();
        $urls = array_merge($urls, $this->groupBy($posts, 'post_id', 'posts/', $base, $default));

        // --- Categories (category_translations has no timestamps) ---
        $categories = Category::join('category_translations', 'categories.id', '=', 'category_translations.category_id')
            ->select('categories.id', 'category_translations.slug', 'category_translations.locale', 'category_translations.category_id')
            ->get();
        $urls = array_merge($urls, $this->groupBy($categories, 'category_id', 'category/', $base, $default));

        return $this->renderSitemap($urls);
    }

    /**
     * Build one <url> entry per entity, with one <xhtml:link> alternate per
     * available locale translation.
     */
    protected function groupBy($rows, $key, $prefix, $base, $default, bool $isPage = false): array
    {
        $byId = [];
        foreach ($rows as $row) {
            $byId[$row->$key][$row->locale] = $row;
        }

        $urls = [];
        foreach ($byId as $id => $translations) {
            // Canonical loc uses the default locale if present, else any.
            $canonicalRow = $translations[$default] ?? reset($translations);
            $loc          = $this->urlFor($base, $prefix, $canonicalRow->slug, $default, $default, $isPage);

            $alternates = [];
            foreach ($translations as $locale => $row) {
                $alternates[$locale] = $this->urlFor($base, $prefix, $row->slug, $locale, $default, $isPage);
            }

            $urls[] = [
                'loc'        => $loc,
                'lastmod'    => optional($canonicalRow->updated_at)->toAtomString(),
                'alternates' => $alternates,
            ];
        }

        return $urls;
    }

    protected function urlFor($base, $prefix, $slug, $locale, $default, bool $isPage): string
    {
        $localePart = ($locale === $default) ? '' : $locale . '/';

        // Home page (slug "/") => site root (per-locale root for non-default).
        if ($isPage && $slug === '/') {
            return $locale === $default ? $base : $base . '/' . $locale;
        }

        return $base . '/' . $localePart . $prefix . ltrim($slug, '/');
    }

    protected function renderSitemap(array $urls): string
    {
        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" '
              . 'xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1) . "</loc>\n";
            if (!empty($url['lastmod'])) {
                $xml .= '    <lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1) . "</lastmod>\n";
            }
            foreach ($url['alternates'] as $locale => $href) {
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($locale, ENT_XML1)
                      . '" href="' . htmlspecialchars($href, ENT_XML1) . "\"/>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Dynamic robots.txt honouring the "discourage search engines" toggle and
     * the admin "extra lines", and pointing at the sitemap.
     */
    public function robots()
    {
        $seo  = get_seo_settings();
        $base = rtrim(config('app.url'), '/');

        $lines = ['User-agent: *'];

        if ($seo && !empty($seo->discourage_search_engines)) {
            $lines[] = 'Disallow: /';
        } else {
            $lines[] = 'Disallow: /larapress-admin';
            $lines[] = 'Disallow: /search';
            $lines[] = 'Allow: /';
        }

        if (!$seo || $seo->sitemap_enabled) {
            $lines[] = '';
            $lines[] = 'Sitemap: ' . $base . '/sitemap.xml';
        }

        if ($seo && !empty($seo->robots_extra)) {
            $lines[] = '';
            // Normalise CRLF and append admin-provided extra lines verbatim.
            foreach (preg_split('/\r\n|\r|\n/', trim($seo->robots_extra)) as $extra) {
                $lines[] = $extra;
            }
        }

        return Response::make(implode("\n", $lines) . "\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Minimal llms.txt (emerging GEO standard): a short markdown index of the
     * site and key links for generative engines.
     */
    public function llms()
    {
        $base     = rtrim(config('app.url'), '/');
        $name     = get_general_settings('website_name');
        $tagline  = get_general_settings('tagline');

        $geo     = get_geo_settings();
        $emitGeo = $geo && $geo->include_in_llms;

        $lines   = [];
        $lines[] = '# ' . (($emitGeo && !empty($geo->business_name)) ? $geo->business_name : $name);
        $lines[] = '';
        if (!empty($tagline)) {
            $lines[] = '> ' . $tagline;
            $lines[] = '';
        }

        // GEO: machine-readable summary for generative engines.
        if ($emitGeo) {
            if (!empty($geo->description)) {
                $lines[] = '## About';
                $lines[] = '';
                $lines[] = $geo->description;
                $lines[] = '';
            }

            $services = $geo->servicesList();
            if (!empty($services)) {
                $lines[] = '## Services';
                $lines[] = '';
                foreach ($services as $service) {
                    $lines[] = '- ' . $service;
                }
                if (!empty($geo->service_area)) {
                    $lines[] = '';
                    $lines[] = 'Service area: ' . $geo->service_area;
                }
                $lines[] = '';
            }

            $faq = $geo->faqList();
            if (!empty($faq)) {
                $lines[] = '## FAQ';
                $lines[] = '';
                foreach ($faq as $qa) {
                    $lines[] = '### ' . $qa['question'];
                    $lines[] = $qa['answer'];
                    $lines[] = '';
                }
            }
        }

        $lines[] = '## Key links';
        $lines[] = '';
        $lines[] = '- [Home](' . $base . ')';
        $lines[] = '- [Sitemap](' . $base . '/sitemap.xml)';

        $categories = Category::join('category_translations', 'categories.id', '=', 'category_translations.category_id')
            ->where('category_translations.locale', config('app.locale'))
            ->select('category_translations.title', 'category_translations.slug')
            ->limit(20)->get();

        if ($categories->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '## Categories';
            $lines[] = '';
            foreach ($categories as $category) {
                $lines[] = '- [' . $category->title . '](' . $base . '/category/' . $category->slug . ')';
            }
        }

        return Response::make(implode("\n", $lines) . "\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}
