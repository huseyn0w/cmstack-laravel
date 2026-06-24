<?php

namespace Tests\Feature;

use App\Http\Models\Comments;
use App\Http\Models\User;
use App\Repositories\PostCommentsRepository;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Fix #8: comment update authorization. An administrator may edit any comment;
 * a regular user may edit only their own; everybody else is rejected.
 */
class CommentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function makeComment(int $userId): Comments
    {
        return Comments::create([
            'post_id' => 1,
            'parent_id' => null,
            'comment' => 'original',
            'user_id' => $userId,
            'status' => 1,
        ]);
    }

    private function updateRequest(int $commentId, string $text): Request
    {
        return new Request([
            'updated_comment_id' => $commentId,
            'comment' => $text,
        ]);
    }

    public function test_owner_can_edit_their_own_comment(): void
    {
        $owner = User::factory()->create(['role_id' => 2]);
        $comment = $this->makeComment($owner->id);

        $this->actingAs($owner);
        $result = app(PostCommentsRepository::class)
            ->update($this->updateRequest($comment->id, 'edited by owner'));

        $this->assertTrue($result);
        $this->assertSame('edited by owner', $comment->fresh()->comment);
    }

    public function test_other_user_cannot_edit_someone_elses_comment(): void
    {
        $owner = User::factory()->create(['role_id' => 2]);
        $other = User::factory()->create(['role_id' => 2]);
        $comment = $this->makeComment($owner->id);

        $this->actingAs($other);

        $this->expectException(HttpException::class);

        try {
            app(PostCommentsRepository::class)
                ->update($this->updateRequest($comment->id, 'hijack attempt'));
        } finally {
            $this->assertSame('original', $comment->fresh()->comment);
        }
    }

    public function test_admin_can_edit_any_comment(): void
    {
        $owner = User::factory()->create(['role_id' => 2]);
        $admin = User::where('username', 'admin')->firstOrFail();
        $comment = $this->makeComment($owner->id);

        $this->actingAs($admin);
        $result = app(PostCommentsRepository::class)
            ->update($this->updateRequest($comment->id, 'edited by admin'));

        $this->assertTrue($result);
        $this->assertSame('edited by admin', $comment->fresh()->comment);
    }
}
