<?php

namespace App\Mcp\Tools\Categories;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelCategoryRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a post category in the given locale. Optionally nest it under a parent category. Requires the manage_post_categories permission.')]
class CreateCategoryTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected CPanelCategoryRepository $categories) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Category title.')->required(),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'slug' => $schema->string()->description('URL slug. Auto-generated from the title when omitted.'),
            'description' => $schema->string()->description('Category description.'),
            'parent_category_id' => $schema->integer()->description('Id of the parent category, for nesting.'),
            'meta_keywords' => $schema->string()->description('SEO meta keywords.'),
            'meta_description' => $schema->string()->description('SEO meta description.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_post_categories')) {
            return $denied;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_category_id' => ['nullable', 'integer'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);
        unset($validated['locale']);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['author_id'] = $request->user()->id;

        $this->hydrateRequest($validated);

        $category = $this->categories->create($validated);

        return Response::structured([
            'created' => true,
            'id' => $category->id ?? null,
            'slug' => $validated['slug'],
        ]);
    }
}
