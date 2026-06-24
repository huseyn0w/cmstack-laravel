<?php

namespace Tests\Feature\Front;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Front-end self-service profile flows (all behind the auth middleware):
 * viewing the profile, updating it, and the change-password endpoints.
 */
class ProfileFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
        $this->user = User::factory()->create([
            'role_id' => 2,
            'password' => 'current123',
        ]);
    }

    public function test_profile_routes_require_authentication(): void
    {
        $this->get('/profile/edit')->assertRedirect('/login');
        $this->get('/profile/change_password')->assertRedirect('/login');
    }

    public function test_user_can_view_their_profile(): void
    {
        $this->actingAs($this->user)
            ->get('/profile/edit')
            ->assertStatus(200);
    }

    public function test_user_can_update_their_profile(): void
    {
        $this->actingAs($this->user)
            ->put('/profile/update', [
                'username' => $this->user->username,
                'email' => $this->user->email,
                'name' => 'Updated Name',
                'about_me' => 'Hello world',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Updated Name', $this->user->fresh()->name);
    }

    public function test_change_password_page_renders(): void
    {
        $this->actingAs($this->user)
            ->get('/profile/change_password')
            ->assertStatus(200);
    }

    public function test_user_can_change_password_with_correct_current_password(): void
    {
        $this->actingAs($this->user)
            ->put('/profile/change_password', [
                'current_password' => 'current123',
                'password' => 'brandnew123',
                'password_confirmation' => 'brandnew123',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('brandnew123', $this->user->fresh()->password));
    }

    public function test_change_password_rejects_wrong_current_password(): void
    {
        $this->actingAs($this->user)
            ->from('/profile/change_password')
            ->put('/profile/change_password', [
                'current_password' => 'wrong-current',
                'password' => 'brandnew123',
                'password_confirmation' => 'brandnew123',
            ])
            ->assertSessionHasErrors();

        $this->assertTrue(Hash::check('current123', $this->user->fresh()->password));
    }
}
