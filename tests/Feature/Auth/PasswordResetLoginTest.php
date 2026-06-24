<?php

namespace Tests\Feature\Auth;

use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * Regression for the password-reset double-hash bug: the laravel/ui
 * ResetsPasswords trait assigns Hash::make($password) while User's
 * setPasswordAttribute mutator also hashes, double-hashing the stored value and
 * silently breaking login after a reset. ResetPasswordController overrides
 * setUserPassword to assign the plaintext and let the single mutator hash it.
 */
class PasswordResetLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_user_can_login_after_resetting_their_password(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);
        $token = Password::broker()->createToken($user);

        $this->post('/password/reset', [
            'token' => $token,
            'email' => 'reset@example.com',
            'password' => 'brand-new-pass',
            'password_confirmation' => 'brand-new-pass',
        ]);

        auth()->logout();

        $this->assertTrue(
            auth()->attempt(['email' => 'reset@example.com', 'password' => 'brand-new-pass']),
            'User must be able to authenticate with the new password after a reset (no double hashing).'
        );
    }
}
