<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Authentication flows wired through Laravel's standard Auth::routes():
 * login (valid/invalid), logout, registration and the password-reset request.
 * Login authenticates by email (the default RegistersUsers username field).
 */
class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role_id' => 2,
            'password' => 'secret123',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'role_id' => 2,
            'password' => 'secret123',
        ]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['role_id' => 2]);

        $this->actingAs($user)->get('/logout');

        $this->assertGuest();
    }

    public function test_registration_page_renders(): void
    {
        $this->get('/register')->assertStatus(200);
    }

    public function test_user_can_register(): void
    {
        $this->post('/register', [
            'name' => 'New Person',
            'username' => 'newp',
            'email' => 'newperson@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'username' => 'newp',
            'email' => 'newperson@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_registration_validation_blocks_bad_input(): void
    {
        $this->from('/register')->post('/register', [
            'name' => '',
            'username' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ])->assertSessionHasErrors(['name', 'username', 'email', 'password']);

        $this->assertGuest();
    }

    public function test_password_reset_request_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['role_id' => 2]);

        $this->get('/password/reset')->assertStatus(200);

        $this->post('/password/email', ['email' => $user->email])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
