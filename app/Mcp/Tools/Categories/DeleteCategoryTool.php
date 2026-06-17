<?php

namespace App\Mcp\Tools\Categories;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelCategoryRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Destructive. Delete a category by id. Requires the manage_post_categories permission. Confirm the id with list/get first.')]
class DeleteCategoryTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelCategoryRepository $categories) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The category id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_post_categories')) {
            return $denied;
        }

        $validated = $request->validate(['id' => ['required', 'integer']]);

        $ok = $this->categories->delete($validated['id']);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $validated['id']])
            : Response::error("Could not delete category {$validated['id']} (it may not exist).");
    }
}
