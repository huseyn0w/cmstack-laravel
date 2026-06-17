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

#[Description('Update the site general settings (singleton). Only the fields you pass are changed. Requires the manage_general_settings permission.')]
class UpdateGeneralSettingsTool extends Tool
{
    use AuthorizesAccess;

    public function __construct(protected CPanelGeneralSettingRepository $settings) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'website_name' => $schema->string()->description('Public site name.'),
            'tagline' => $schema->string()->description('Site tagline/slogan.'),
            'contact_email' => $schema->string()->description('Contact email address.'),
            'posts_per_page' => $schema->integer()->description('Posts shown per listing page.'),
            'comments_per_page' => $schema->integer()->description('Comments shown per page.'),
            'membership' => $schema->boolean()->description('Whether public registration is open.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'website_name' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'posts_per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'comments_per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'membership' => ['nullable', 'boolean'],
        ]);

        $validated = array_filter($validated, fn ($v) => ! is_null($v));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one setting to change.');
        }

        $ok = $this->settings->update(1, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'fields' => array_keys($validated)])
            : Response::error('Could not update general settings.');
    }
}
