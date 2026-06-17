<?php

namespace App\Mcp\Tools\Posts;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\HydratesRequest;
use App\Mcp\Concerns\ResolvesLocale;
use App\Repositories\CPanelPostRepository;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Create a new blog post in the given locale. Provide a title and content; slug is derived from the title when omitted. Author defaults to the connected account. Optionally attach category ids. Requires the manage_posts permission.')]
class CreatePostTool extends Tool
{
    use AuthorizesAccess;
    use HydratesRequest;
    use ResolvesLocale;

    public function __construct(protected CPanelPostRepository $posts) {}

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Post title.')->required(),
            'content' => $schema->string()->description('Post body (HTML allowed; it is sanitised server-side).')->required(),
            'locale' => $schema->string()->description('Language code, e.g. "en". Defaults to the site default.'),
            'slug' => $schema->string()->description('URL slug. Auto-generated from the title when omitted.'),
            'preview' => $schema->string()->description('Short excerpt/preview text.'),
            'status' => $schema->string()->description('Publication status, e.g. "draft" or "published".'),
            'meta_keywords' => $schema->string()->description('SEO meta keywords.'),
            'meta_description' => $schema->string()->description('SEO meta description.'),
            'category' => $schema->array()->description('Category ids to attach to the post.'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_posts')) {
            return $denied;
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'locale' => ['nullable', 'string', 'max:10'],
            'slug' => ['nullable', 'string', 'max:255'],
            'preview' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'array'],
            'category.*' => ['integer'],
        ]);

        $this->applyLocale($validated['locale'] ?? null);
        unset($validated['locale']);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['author_id'] = $request->user()->id;

        // Observers read content/preview/category off the global request.
        $this->hydrateRequest($validated);

        $post = $this->posts->create($validated);

        return Response::structured([
            'created' => true,
            'id' => $post->id ?? null,
            'slug' => $validated['slug'],
        ]);
    }
}
