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

#[Description('Read-only. List user accounts, paginated. Returns id, username, email, name and role_id. Never returns passwords. Requires the manage_users permission.')]
class ListUsersTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelUserRepository $users) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'per_page' => $schema->integer()->description('Users per page (1-100). Defaults to 25.'),
            'page' => $schema->integer()->description('1-based page number. Defaults to 1.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_users')) {
            return $denied;
        }

        $validated = $request->validate([
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $paginator = $this->users->only($validated['per_page'] ?? 25, $validated['page'] ?? 1);

        return Response::structured([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'users' => collect($paginator->items())->map(fn ($u) => [
                'id' => $u->id,
                'username' => $u->username ?? null,
                'email' => $u->email ?? null,
                'name' => trim(($u->name ?? '').' '.($u->surname ?? '')),
                'role_id' => $u->role_id ?? null,
            ])->all(),
        ]);
    }
}
