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

#[Description('Read-only. Fetch a single user account by id (no password). Requires the manage_users permission.')]
class GetUserTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelUserRepository $users) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The user id.')->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_users')) {
            return $denied;
        }

        $validated = $request->validate(['id' => ['required', 'integer']]);

        $user = $this->users->getBy('id', $validated['id']);

        if (is_null($user)) {
            return Response::error("No user found with id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $user->id,
            'username' => $user->username ?? null,
            'email' => $user->email ?? null,
            'name' => $user->name ?? null,
            'surname' => $user->surname ?? null,
            'role_id' => $user->role_id ?? null,
            'city' => $user->city ?? null,
            'country' => $user->country ?? null,
            'about_me' => $user->about_me ?? null,
        ]);
    }
}
