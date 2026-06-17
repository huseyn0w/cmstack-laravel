<?php

namespace App\Mcp\Tools\Pages;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelPageRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. Fetch a single page by id with its translated fields for the requested locale.')]
class GetPageTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelPageRepository $pages) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->description('The page id.')->required(),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
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
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $page = $this->pages->getBy('id', $validated['id']);

        if (is_null($page)) {
            return Response::error("No page found with id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $page->id,
            'slug' => $page->slug ?? null,
            'title' => $page->title ?? null,
            'template' => $page->template ?? null,
            'content' => $page->content ?? null,
            'status' => $page->status ?? null,
            'custom_fields' => $page->custom_fields ?? null,
            'meta_keywords' => $page->meta_keywords ?? null,
            'meta_description' => $page->meta_description ?? null,
        ]);
    }
}
