<?php

namespace App\Mcp\Tools\Posts;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelPostRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Read-only. List blog posts for a given locale, paginated. Returns id, slug, status, author and timestamps. Use this to discover post ids/slugs before getting, updating or deleting a post.')]
class ListPostsTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelPostRepository $posts) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'locale' => $schema->string()
                ->description('Language code to list posts for, e.g. "en". Defaults to the site default.'),
            'per_page' => $schema->integer()
                ->description('How many posts per page (1-100). Defaults to 20.'),
            'page' => $schema->integer()
                ->description('1-based page number. Defaults to 1.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'locale' => ['nullable', 'string', 'max:10'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $perPage = $validated['per_page'] ?? 20;
        $page = $validated['page'] ?? 1;

        $paginator = $this->posts->only($perPage, $page);

        return Response::structured([
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'posts' => collect($paginator->items())->map(fn ($p) => [
                'id' => $p->id,
                'slug' => $p->slug ?? null,
                'title' => $p->title ?? null,
                'status' => $p->status ?? null,
                'author_id' => $p->author_id ?? null,
                'updated_at' => (string) ($p->updated_at ?? ''),
            ])->all(),
        ]);
    }
}
