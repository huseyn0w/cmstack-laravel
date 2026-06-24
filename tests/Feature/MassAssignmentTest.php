<?php

namespace Tests\Feature;

use App\Http\Models\Post;
use App\Http\Models\User;
use App\Repositories\CPanelUserRepository;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fix #4: models must not be wide-open to mass assignment. In particular the
 * User model must not allow `provider`/`provider_id` to be mass assigned, and
 * the Post main row exposes no content columns.
 */
class MassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_provider_fields_are_not_mass_assignable(): void
    {
        $user = new User([
            'name' => 'Mallory',
            'email' => 'mallory@example.com',
            'username' => 'mallory',
            'provider' => 'github',
            'provider_id' => '999999',
        ]);

        $this->assertNull($user->provider, 'provider must not be mass assignable');
        $this->assertNull($user->provider_id, 'provider_id must not be mass assignable');
        $this->assertSame('Mallory', $user->name);
    }

    public function test_post_main_row_has_no_mass_assignable_columns(): void
    {
        $this->assertSame([], (new Post)->getFillable());
    }

    public function test_self_service_update_cannot_escalate_role(): void
    {
        // role_id 2 = standard user. Attempt to grant admin (1) via the repo.
        $user = User::factory()->create(['role_id' => 2]);

        $this->actingAs($user);

        app(CPanelUserRepository::class)->update($user->id, [
            'name' => 'Updated Name',
            'role_id' => 1,
        ]);

        $this->assertSame(2, (int) $user->fresh()->role_id, 'role_id must not be self-escalatable');
        $this->assertSame('Updated Name', $user->fresh()->name);
    }
}
