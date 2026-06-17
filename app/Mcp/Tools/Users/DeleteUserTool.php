<?php

namespace App\Mcp\Tools\Users;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelUserRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Destructive. Delete a user account by id. The primary admin (id 1) and your own account cannot be deleted. Requires the manage_users permission.')]
class DeleteUserTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelUserRepository $users) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The user id to delete.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_users')) {
            return $denied;
        }

        $validated = $request->validate(['id' => ['required', 'integer']]);
        $id = $validated['id'];

        if ($id === 1) {
            return Response::error('Refusing to delete the primary admin account (id 1).');
        }

        if ($id === (int) $request->user()->id) {
            return Response::error('You cannot delete the account you are currently authenticated as.');
        }

        $ok = $this->users->delete($id);

        return $ok
            ? Response::structured(['deleted' => true, 'id' => $id])
            : Response::error("Could not delete user {$id} (it may not exist).");
    }
}
