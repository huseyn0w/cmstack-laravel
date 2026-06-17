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

#[Description('Read-only. Fetch a single post by id, including its translated fields (title, content, status, meta) for the requested locale.')]
class GetPostTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesLocale;

    public function __construct(protected CPanelPostRepository $posts) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()
                ->description('The post id.')
                ->required(),
            'locale' => $schema->string()
                ->description('Language code for the translation to read, e.g. "en". Defaults to the site default.'),
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
        ]);

        $this->applyLocale($validated['locale'] ?? null);

        $post = $this->posts->getBy('id', $validated['id']);

        if (is_null($post)) {
            return Response::error("No post found with id {$validated['id']}.");
        }

        return Response::structured([
            'id' => $post->id,
            'slug' => $post->slug ?? null,
            'title' => $post->title ?? null,
            'content' => $post->content ?? null,
            'preview' => $post->preview ?? null,
            'status' => $post->status ?? null,
            'author_id' => $post->author_id ?? null,
            'meta_keywords' => $post->meta_keywords ?? null,
            'meta_description' => $post->meta_description ?? null,
        ]);
    }
}
