<?php

namespace App\Mcp\Tools\Pages;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelPageRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new page in the given locale. The template name selects which Blade view under the theme renders it (see the theme-file tools). Requires the manage_pages permission.')]
class CreatePageTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected CPanelPageRepository $pages) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Page title.')->required(),
            'content' => $schema->string()->description('Page body (HTML allowed; sanitised server-side).')->required(),
            'template' => $schema->string()->description('Template/view name, e.g. "default". Must match a Blade file under resources/views/<theme>/pages.'),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'slug' => $schema->string()->description('URL slug. Auto-generated from the title when omitted.'),
            'status' => $schema->string()->description('Publication status, e.g. "draft" or "published".'),
            'meta_keywords' => $schema->string()->description('SEO meta keywords.'),
            'meta_description' => $schema->string()->description('SEO meta description.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_pages')) {
            return $denied;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'template' => ['nullable', 'string', 'max:100'],
            'locale' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);
        unset($validated['locale']);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['template'] = $validated['template'] ?? 'default';
        $validated['author_id'] = $request->user()->id;

        $this->hydrateRequest($validated);

        $page = $this->pages->create($validated);

        return Response::structured([
            'created' => true,
            'id' => $page->id ?? null,
            'slug' => $validated['slug'],
            'template' => $validated['template'],
        ]);
    }
}
