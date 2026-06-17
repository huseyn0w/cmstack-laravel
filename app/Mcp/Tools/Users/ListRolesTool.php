<?php

namespace App\Mcp\Tools\Users;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelUserRolesRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. List the permission roles (id + name) available to assign to users. Requires the manage_users permission.')]
class ListRolesTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelUserRolesRepository $roles) {}

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_users')) {
            return $denied;
        }

        $roles = $this->roles->all();

        return Response::structured([
            'roles' => collect($roles)->map(fn ($r) => [
                'id' => $r->id,
                'name' => $r->name ?? null,
            ])->all(),
        ]);
    }
}
