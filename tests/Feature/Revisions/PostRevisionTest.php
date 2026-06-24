<?php

namespace Tests\Feature\Revisions;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\PostTranslation;
use App\Http\Models\Revision;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Revisions are immutable snapshots of a post translation taken *before* each
 * admin update. Creating a post takes no snapshot (nothing to preserve); the
 * first edit snapshots the pre-edit state, and so on.
 */
class PostRevisionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('username', 'admin')->firstOrFail();
    }

    private function postPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Round Trip Post',
            'slug' => 'round-trip-post',
            'content' => 'original body',
            'preview' => 'original preview',
            'author_id' => $this->admin->id,
            'meta_keywords' => 'kw',
            'meta_description' => 'md',
            'category' => [1],
            'status' => 1,
        ], $overrides);
    }

    public function test_creating_a_post_takes_no_revision(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());

        $this->assertSame(0, Revision::count(), 'Creating a post must not snapshot.');
    }

    public function test_updating_a_post_snapshots_the_previous_translation(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();

        $this->actingAs($this->admin)->put(
            '/cmstack-laravel-admin/posts/'.$translation->post_id.'/update',
            $this->postPayload(['content' => 'edited body'])
        );

        $this->assertSame(1, Revision::count(), 'One revision after the first edit.');

        $revision = Revision::firstOrFail();
        $this->assertSame(PostTranslation::class, $revision->revisionable_type);
        $this->assertSame($translation->id, $revision->revisionable_id);
        $this->assertSame($this->admin->id, $revision->user_id);
        // The snapshot preserves the PRE-edit content, not the new value.
        $this->assertStringContainsString('original body', $revision->data['content']);
        $this->assertSame('Round Trip Post', $revision->data['title']);
    }

    public function test_each_edit_appends_a_new_revision(): void
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/posts/new', $this->postPayload());
        $translation = PostTranslation::where('slug', 'round-trip-post')->firstOrFail();

        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$translation->post_id.'/update', $this->postPayload(['content' => 'edit one']));
        $this->actingAs($this->admin)->put('/cmstack-laravel-admin/posts/'.$translation->post_id.'/update', $this->postPayload(['content' => 'edit two']));

        $this->assertSame(2, Revision::count(), 'Each edit appends one revision.');
    }
}
