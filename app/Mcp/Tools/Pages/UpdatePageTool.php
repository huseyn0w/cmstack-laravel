<?php

namespace App\Mcp\Tools\Pages;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelPageRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update an existing page by id, for a given locale. Only the fields you pass are changed. Requires the manage_pages permission.')]
class UpdatePageTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected CPanelPageRepository $pages) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The page id to update.')->required(),
            'locale' => $schema->string()->description('Language code of the translation to update, e.g. "en".'),
            'title' => $schema->string()->description('New title.'),
            'content' => $schema->string()->description('New body (HTML allowed; sanitised server-side).'),
            'template' => $schema->string()->description('Template/view name.'),
            'slug' => $schema->string()->description('New URL slug.'),
            'status' => $schema->string()->description('Publication status, e.g. "draft" or "published".'),
            'meta_keywords' => $schema->string()->description('SEO meta keywords.'),
            'meta_description' => $schema->string()->description('SEO meta description.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_pages')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'template' => ['nullable', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
        ]);

        $id = $validated['id'];
        unset($validated['id'], $validated['locale']);

        $this->applyLocale($request->get('locale'));

        if (empty($validated)) {
            return Response::error('Nothing to update: provide at least one field besides id and locale.');
        }

        $this->hydrateRequest($validated);

        $ok = $this->pages->update($id, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'id' => $id, 'fields' => array_keys($validated)])
            : Response::error("Could not update page {$id} (it may not exist).");
    }
}
