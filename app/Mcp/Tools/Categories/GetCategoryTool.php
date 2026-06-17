<?php

namespace App\Mcp\Tools\Categories;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelCategoryRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. Fetch a single category by id with its translated fields for the requested locale.')]
class GetCategoryTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelCategoryRepository $categories) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The category id.')->required(),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
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
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $category = $this->categories->getBy('id', $validated['id']);

        if (is_null($category)) {
            return Response::error("No category found with id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $category->id,
            'slug' => $category->slug ?? null,
            'title' => $category->title ?? null,
            'description' => $category->description ?? null,
            'parent_category_id' => $category->parent_category_id ?? null,
            'meta_keywords' => $category->meta_keywords ?? null,
            'meta_description' => $category->meta_description ?? null,
        ]);
    }
}
