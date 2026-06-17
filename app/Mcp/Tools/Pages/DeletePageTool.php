<?php

namespace App\Mcp\Tools\Pages;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelPageRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Destructive. Delete a page by id. Requires the manage_pages permission. Confirm the id with list/get first.')]
class DeletePageTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelPageRepository $pages) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The page id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_pages')) {
            return $denied;
        }

        $validated = $request->validate(['id' => ['required', 'integer']]);

        $ok = $this->pages->delete($validated['id']);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $validated['id']])
            : Response::error("Could not delete page {$validated['id']} (it may not exist).");
    }
}
