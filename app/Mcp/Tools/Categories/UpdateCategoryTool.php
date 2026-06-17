<?php

namespace App\Mcp\Tools\Categories;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelCategoryRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a category by id for a given locale. Only the fields you pass are changed. Requires the manage_post_categories permission.')]
class UpdateCategoryTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected CPanelCategoryRepository $categories) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The category id to update.')->required(),
            'locale' => $schema->string()->description('Language code of the translation to update, e.g. "en".'),
            'title' => $schema->string()->description('New title.'),
            'slug' => $schema->string()->description('New URL slug.'),
            'description' => $schema->string()->description('New description.'),
            'parent_category_id' => $schema->integer()->description('New parent category id.'),
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
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parent_category_id' => ['nullable', 'integer'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ]);

        $id = $validated['id'];
        unset($validated['id'], $validated['locale']);

        $this->applyLocale($request->get('locale'));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one field besides id and locale.');
        }

        $this->hydrateRequest($validated);

        $ok = $this->categories->update($id, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'id' => $id, 'fields' => array_keys($validated)])
            : Response::error("Could not update category {$id} (it may not exist).");
    }
}
