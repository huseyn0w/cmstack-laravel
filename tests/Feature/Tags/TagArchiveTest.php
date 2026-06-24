<?php

namespace Tests\Feature\Tags;

use App\Http\Models\Post;
use App\Repositories\TagRepository;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Parity §2: a browseable public tag archive at /tag/{slug} listing the posts
 * carrying that tag.
 */
class TagArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        app()->setLocale('en');
    }

    public function test_tag_archive_lists_its_posts(): void
    {
        $post = Post::findOrFail(1);
        app(TagRepository::class)->syncToPost($post, ['Laravel']);

        $response = $this->get('/tag/laravel');

        $response->assertStatus(200);
        $response->assertSee('Laravel', false);
        $response->assertSee($post->title, false);
    }

    public function test_unknown_tag_returns_404(): void
    {
        $this->get('/tag/does-not-exist')->assertNotFound();
    }

    public function test_post_detail_shows_its_tags_linking_to_the_archive(): void
    {
        $post = Post::findOrFail(1);
        app(TagRepository::class)->syncToPost($post, ['Laravel']);

        $response = $this->get('/posts/'.$post->slug);

        $response->assertStatus(200);
        $response->assertSee('Laravel', false);
        $response->assertSee('tag/laravel', false);
    }
}
