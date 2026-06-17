<?php

namespace App\Mcp\Tools\Settings;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Repositories\CPanelGeneralSettingRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. Return the site general settings: website name, tagline, contact email, pagination and membership flags. Requires the manage_general_settings permission.')]
class GetGeneralSettingsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelGeneralSettingRepository $settings) {}

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $row = $this->settings->first();

        if (is_null($row)) {
            return Response::error('General settings have not been seeded yet.');
        }

        return Response::structured([
            'website_name' => $row->website_name ?? null,
            'tagline' => $row->tagline ?? null,
            'contact_email' => $row->contact_email ?? null,
            'posts_per_page' => $row->posts_per_page ?? null,
            'comments_per_page' => $row->comments_per_page ?? null,
            'membership' => $row->membership ?? null,
            'active_template_name' => $row->active_template_name ?? null,
        ]);
    }
}
