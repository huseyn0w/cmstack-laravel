<?php

namespace Tests\Feature\Auth;

use App\Http\Models\User;
use App\Services\Auth\UserRegistrationService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Characterizes form-registration user creation now extracted out of
 * RegisterController::create() into a service. Password must be hashed and the
 * privileged role_id left to the database default.
 */
class UserRegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_register_creates_user_with_hashed_password(): void
    {
        $service = app(UserRegistrationService::class);

        $user = $service->register([
            'name' => 'Jane Doe',
            'username' => 'jane',
            'email' => 'jane@example.com',
            'password' => 'secret-password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com', 'username' => 'jane']);
        $this->assertNotSame('secret-password', $user->password);
        $this->assertTrue(Hash::check('secret-password', $user->password));
    }

    public function test_register_leaves_role_id_at_database_default(): void
    {
        $service = app(UserRegistrationService::class);

        $user = $service->register([
            'name' => 'John Doe',
            'username' => 'john',
            'email' => 'john@example.com',
            'password' => 'another-secret',
        ]);

        $this->assertSame(2, (int) $user->fresh()->role_id);
    }

    public function test_register_ignores_injected_privileged_role(): void
    {
        $service = app(UserRegistrationService::class);

        $user = $service->register([
            'name' => 'Mallory',
            'username' => 'mallory',
            'email' => 'mallory@example.com',
            'password' => 'pw',
            'role_id' => 1, // attempt to self-assign administrator
        ]);

        $this->assertSame(2, (int) $user->fresh()->role_id);
    }

    /**
     * Regression for the double-hash bug: a user registered through the HTTP
     * endpoint must be able to log in with their plaintext password afterwards.
     */
    public function test_registered_user_can_login_with_their_password(): void
    {
        $this->post('/register', [
            'name' => 'Real User',
            'username' => 'realuser',
            'email' => 'real@example.com',
            'password' => 'correct-horse',
            'password_confirmation' => 'correct-horse',
        ]);

        auth()->logout();

        $this->assertTrue(
            auth()->attempt(['email' => 'real@example.com', 'password' => 'correct-horse']),
            'Registered user must authenticate with their plaintext password (no double hashing).'
        );
    }
}
