<?php

namespace App\Mcp\Tools\Posts;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelPostRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Update an existing post by id, for a given locale. Only the fields you pass are changed. Requires the manage_posts permission.')]
class UpdatePostTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected CPanelPostRepository $posts) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The post id to update.')->required(),
            'locale' => $schema->string()->description('Language code of the translation to update, e.g. "en".'),
            'title' => $schema->string()->description('New title.'),
            'content' => $schema->string()->description('New body (HTML allowed; sanitised server-side).'),
            'slug' => $schema->string()->description('New URL slug.'),
            'preview' => $schema->string()->description('New excerpt/preview text.'),
            'status' => $schema->string()->description('Publication status, e.g. "draft" or "published".'),
            'meta_keywords' => $schema->string()->description('SEO meta keywords.'),
            'meta_description' => $schema->string()->description('SEO meta description.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'id' => ['required', 'integer'],
            'locale' => ['nullable', 'string', 'max:10'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255'],
            'preview' => ['nullable', 'string'],
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

        $ok = $this->posts->update($id, $validated);

        return $ok
            ? Response::structured(['updated' => true, 'id' => $id, 'fields' => array_keys($validated)])
            : Response::error("Could not update post {$id} (it may not exist).");
    }
}
