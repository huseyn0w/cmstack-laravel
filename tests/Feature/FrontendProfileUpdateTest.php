<?php

namespace Tests\Feature;

use App\Http\Models\User;
use App\Http\Requests\FrontEndUserRequest;
use App\Repositories\UserRepository;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fix #4/#5: the front-end self-service profile update must not let a user
 * escalate their own role or hijack a social identity.
 */
class FrontendProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_cannot_escalate_role_via_profile_update(): void
    {
        $user = User::factory()->create(['role_id' => 2]);
        $this->actingAs($user);

        // Build a validated FormRequest carrying a malicious role_id.
        $request = FrontEndUserRequest::create('/profile/update', 'PUT', [
            'email' => $user->email,
            'username' => $user->username,
            'name' => 'New Name',
            'role_id' => 1,
        ]);
        $request->setContainer($this->app)->setRedirector($this->app['redirect']);
        $request->validateResolved();

        app(UserRepository::class)->update($user->id, $request);

        $fresh = $user->fresh();
        $this->assertSame(2, (int) $fresh->role_id, 'role_id must stay unchanged');
        $this->assertSame('New Name', $fresh->name);
    }
}
