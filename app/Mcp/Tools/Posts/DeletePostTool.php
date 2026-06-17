<?php

namespace App\Mcp\Tools\Posts;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelPostRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Destructive. Soft-delete a post by id (it can be restored from the admin trash). Requires the manage_posts permission. Confirm the id with list/get first.')]
class DeletePostTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelPostRepository $posts) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The post id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
        ]);

        $ok = $this->posts->delete($validated['id']);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $validated['id']])
            : Response::error("Could not delete post {$validated['id']} (it may not exist).");
    }
}
