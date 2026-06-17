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

#[Description('Create a user account. The password is hashed automatically by the model. role_id assigns the permission role (use list-roles to see available roles). Requires the manage_users permission.')]
class CreateUserTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelUserRepository $users) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'username' => $schema->string()->description('Unique username.')->required(),
            'email' => $schema->string()->description('Unique email address.')->required(),
            'password' => $schema->string()->description('Plain password (min 8 chars); stored hashed.')->required(),
            'name' => $schema->string()->description('First name.'),
            'surname' => $schema->string()->description('Last name.'),
            'role_id' => $schema->integer()->description('Permission role id to assign.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_users')) {
            return $denied;
        }

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'role_id' => ['nullable', 'integer', 'exists:user_roles,id'],
        ]);

        $user = $this->users->create($validated);

        return Response::structured([
            'created' => true,
            'id' => $user->id ?? null,
            'username' => $validated['username'],
        ]);
    }
}
