<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\Page;
use App\Http\Models\PageTranslation;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pages mirror the posts soft-delete/trash/restore/permanent-delete flow
 * (FEATURE_MATRIX §1). Soft-deleting a page hides it from normal listings but
 * keeps the row (and its translations) for restore.
 */
class PageSoftDeleteTest extends TestCase
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

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Trashable Page',
            'slug' => 'trashable-page',
            'author_id' => (string) $this->admin->id,
            'content' => 'page body',
            'meta_keywords' => 'kw',
            'meta_description' => 'md',
            'template' => 'default',
            'status' => 1,
        ], $overrides);
    }

    private function createPage(): int
    {
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/pages/new', $this->payload());

        return PageTranslation::where('slug', 'trashable-page')->firstOrFail()->page_id;
    }

    public function test_admin_can_soft_delete_a_page(): void
    {
        $pageId = $this->createPage();

        $this->actingAs($this->admin)
            ->delete('/cmstack-laravel-admin/pages/'.$pageId.'/delete')
            ->assertOk();

        $this->assertNull(Page::find($pageId), 'Page should be soft deleted.');
        $this->assertNotNull(Page::withTrashed()->find($pageId), 'Soft deleted page row should remain.');
        // Translations are preserved so the page can be restored intact.
        $this->assertSame(1, PageTranslation::where('page_id', $pageId)->count());
    }

    public function test_trashed_pages_listing_shows_only_soft_deleted_pages(): void
    {
        $deletedId = $this->createPage();
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/pages/new', $this->payload([
            'title' => 'Live Page', 'slug' => 'live-page',
        ]));
        $this->actingAs($this->admin)->delete('/cmstack-laravel-admin/pages/'.$deletedId.'/delete');

        $this->actingAs($this->admin)
            ->get('/cmstack-laravel-admin/pages/trashed')
            ->assertOk()
            ->assertSee('Trashable Page')
            ->assertDontSee('Live Page');
    }

    public function test_trashed_tab_renders_restore_and_destroy_affordances(): void
    {
        $pageId = $this->createPage();
        $this->actingAs($this->admin)->delete('/cmstack-laravel-admin/pages/'.$pageId.'/delete');

        $this->actingAs($this->admin)
            ->get('/cmstack-laravel-admin/pages/trashed')
            ->assertOk()
            ->assertSee(__('cpanel/pages.restore_page'))
            ->assertSee(__('cpanel/pages.destroy_page'))
            ->assertSee(route('cpanel_restore_page', $pageId), false);
    }

    public function test_admin_can_restore_a_soft_deleted_page(): void
    {
        $pageId = $this->createPage();
        $this->actingAs($this->admin)->delete('/cmstack-laravel-admin/pages/'.$pageId.'/delete');

        $this->actingAs($this->admin)
            ->get('/cmstack-laravel-admin/pages/'.$pageId.'/restore')
            ->assertRedirect();

        $this->assertNotNull(Page::find($pageId), 'Page should be restored (no longer trashed).');
    }

    public function test_admin_can_permanently_destroy_a_trashed_page(): void
    {
        $pageId = $this->createPage();
        $this->actingAs($this->admin)->delete('/cmstack-laravel-admin/pages/'.$pageId.'/delete');

        $this->actingAs($this->admin)
            ->delete('/cmstack-laravel-admin/pages/'.$pageId.'/destroy')
            ->assertOk();

        $this->assertNull(Page::withTrashed()->find($pageId), 'Page row should be gone.');
        // The FK cascade removes the translations too.
        $this->assertSame(0, PageTranslation::where('page_id', $pageId)->count());
    }

    public function test_admin_can_bulk_restore_trashed_pages(): void
    {
        $a = $this->createPage();
        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/pages/new', $this->payload([
            'title' => 'Second Page', 'slug' => 'second-page',
        ]));
        $b = PageTranslation::where('slug', 'second-page')->firstOrFail()->page_id;
        $this->actingAs($this->admin)->delete('/cmstack-laravel-admin/pages/'.$a.'/delete');
        $this->actingAs($this->admin)->delete('/cmstack-laravel-admin/pages/'.$b.'/delete');

        $this->actingAs($this->admin)->post('/cmstack-laravel-admin/pages/multiple', [
            'pages_action' => 'restore',
            'pages' => [$a, $b],
        ])->assertRedirect();

        $this->assertNotNull(Page::find($a));
        $this->assertNotNull(Page::find($b));
    }
}
