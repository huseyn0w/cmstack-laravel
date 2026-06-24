<?php

namespace Tests\Unit;

use App\Http\Models\Page;
use App\Http\Models\PageTranslation;
use App\Http\Models\Post;
use App\Http\Models\PostTranslation;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Unit coverage for the model observers. The observers read directly off the
 * current request, so each test binds a request carrying the relevant inputs.
 *
 *  - PostObserver: attaches the chosen categories to the new post (pivot).
 *  - PageObserver: json-encodes custom_fields and sanitises content.
 *  - PostTranslationObserver: sanitises preview/content on save.
 */
class ObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function bindRequest(array $input): void
    {
        $request = Request::create('/test', 'POST', $input);
        $this->app->instance('request', $request);
    }

    public function test_post_observer_attaches_category_on_create(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->bindRequest(['category' => [1], 'content' => 'c', 'preview' => 'p']);

        $post = Post::create([
            'title' => 'ObsPost',
            'slug' => 'obs-post',
            'content' => 'c',
            'preview' => 'p',
            'author_id' => $admin->id,
            'meta_keywords' => 'k',
            'meta_description' => 'd',
            'status' => 1,
        ]);

        $this->assertSame(1, $post->categories()->count());
    }

    public function test_post_translation_observer_sanitises_content(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->bindRequest([
            'content' => '<script>alert(1)</script><p>safe</p>',
            'preview' => '<p>preview</p>',
            'category' => [1],
        ]);

        Post::create([
            'title' => 'CleanPost',
            'slug' => 'clean-post',
            'content' => '<script>alert(1)</script><p>safe</p>',
            'preview' => '<p>preview</p>',
            'author_id' => $admin->id,
            'meta_keywords' => 'k',
            'meta_description' => 'd',
            'status' => 1,
        ]);

        $translation = PostTranslation::where('slug', 'clean-post')->firstOrFail();
        $this->assertStringNotContainsString('<script>', $translation->content);
        $this->assertStringContainsString('safe', $translation->content);
    }

    public function test_page_observer_json_encodes_custom_fields(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->bindRequest([
            'custom_fields' => ['headline' => 'Hello'],
            'content' => '<p>page</p>',
        ]);

        Page::create([
            'title' => 'ObsPage',
            'slug' => 'obs-page',
            'author_id' => $admin->id,
            'content' => '<p>page</p>',
            'meta_keywords' => 'k',
            'meta_description' => 'd',
            'template' => 'default',
            'status' => 1,
        ]);

        $translation = PageTranslation::where('slug', 'obs-page')->firstOrFail();
        $decoded = json_decode($translation->custom_fields, true);
        $this->assertSame('Hello', $decoded['headline']);
    }

    public function test_page_observer_sanitises_content(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $this->bindRequest([
            'custom_fields' => [],
            'content' => '<script>evil()</script><p>ok</p>',
        ]);

        Page::create([
            'title' => 'CleanPage',
            'slug' => 'clean-page',
            'author_id' => $admin->id,
            'content' => '<script>evil()</script><p>ok</p>',
            'meta_keywords' => 'k',
            'meta_description' => 'd',
            'template' => 'default',
            'status' => 1,
        ]);

        $translation = PageTranslation::where('slug', 'clean-page')->firstOrFail();
        $this->assertStringNotContainsString('<script>', $translation->content);
        $this->assertStringContainsString('ok', $translation->content);
    }
}
