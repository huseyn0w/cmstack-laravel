<?php

namespace Tests\Feature;

use App\Http\Models\Comments;
use App\Http\Models\User;
use App\Repositories\PostCommentsRepository;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Fix #5: repositories must persist only whitelisted, server-controlled data.
 * A user must not be able to inject privileged fields (e.g. spoof another
 * user_id or force an approved status) through the request payload.
 */
class RepositoryWhitelistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_comment_create_ignores_injected_privileged_fields(): void
    {
        $user = User::factory()->create(['role_id' => 2]);
        $victim = User::factory()->create(['role_id' => 2]);

        $this->actingAs($user);

        // Attacker tries to spoof another user_id and auto-approve the comment.
        $request = new Request([
            'post_id' => 1,
            'comment' => 'injected',
            'user_id' => $victim->id,
            'status' => 1,
            'id' => 999,
        ]);

        app(PostCommentsRepository::class)->create($request);

        $comment = Comments::where('comment', 'injected')->firstOrFail();

        // user_id must be the authenticated user, not the injected victim id.
        $this->assertSame($user->id, (int) $comment->user_id);
        // A non-admin's comment must remain unapproved regardless of input.
        $this->assertSame(0, (int) $comment->status);
    }
}
