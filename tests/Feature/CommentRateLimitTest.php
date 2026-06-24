<?php

namespace Tests\Feature;

use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Parity FEATURE_MATRIX §3 (Submit rate limiting) + hardening of the
 * comment-notification fan-out: the comment submit route is throttled so a
 * logged-in user cannot flood post authors/moderators with notification email.
 */
class CommentRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        Mail::fake();
    }

    public function test_comment_submission_is_rate_limited(): void
    {
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user);

        // The canonical limit is 8/min; the 9th submit in the window is blocked.
        for ($i = 0; $i < 8; $i++) {
            $response = $this->post('/posts/handlecomment/1', [
                'post_id' => 1,
                'parent_id' => null,
                'comment' => "comment number {$i}",
            ]);
            $this->assertNotSame(429, $response->getStatusCode(), "Request {$i} should not be throttled");
        }

        $blocked = $this->post('/posts/handlecomment/1', [
            'post_id' => 1,
            'parent_id' => null,
            'comment' => 'one too many',
        ]);

        $blocked->assertStatus(429);
    }
}
