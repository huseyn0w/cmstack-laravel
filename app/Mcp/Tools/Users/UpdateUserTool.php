<?php

namespace App\Mcp\Tools\Users;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelUserRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Validation\Rule;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update a user account by id. Only fields you pass are changed; pass password to reset it (it is re-hashed). Requires the manage_users permission.')]
class UpdateUserTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelUserRepository $users) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The user id to update.')->required(),
            'username' => $schema->string()->description('New unique username.'),
            'email' => $schema->string()->description('New unique email address.'),
            'password' => $schema->string()->description('New plain password (min 8 chars); stored hashed.'),
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

        $id = (int) $request->get('id');

        $validated = $request->validate([
            'id' => ['required', 'integer', 'exists:users,id'],
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8'],
            'name' => ['nullable', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'role_id' => ['nullable', 'integer', 'exists:user_roles,id'],
        ]);

        unset($validated['id']);
        $validated = array_filter($validated, fn ($v) => ! is_null($v));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one field besides id.');
        }

        $ok = $this->users->update($id, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'id' => $id, 'fields' => array_keys($validated)])
            : Response::error("Could not update user {$id}.");
    }
}
