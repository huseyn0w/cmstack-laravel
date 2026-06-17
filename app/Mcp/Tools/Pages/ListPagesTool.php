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

#[Description('Read-only. List pages for a given locale, paginated. Use to discover page ids/slugs before get/update/delete.')]
class ListPagesTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelPageRepository $pages) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'per_page' => $schema->integer()->description('Pages per page (1-100). Defaults to 20.'),
            'page' => $schema->integer()->description('1-based page number. Defaults to 1.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_pages')) {
            return $denied;
        }

        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'max:10'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $paginator = $this->pages->only($validated['per_page'] ?? 20, $validated['page'] ?? 1);

        return Response::structured([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'pages' => collect($paginator->items())->map(fn ($p) => [
                'id' => $p->id,
                'slug' => $p->slug ?? null,
                'title' => $p->title ?? null,
                'template' => $p->template ?? null,
                'status' => $p->status ?? null,
            ])->all(),
        ]);
    }
}
