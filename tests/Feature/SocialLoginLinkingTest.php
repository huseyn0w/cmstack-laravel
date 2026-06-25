<?php

namespace Tests\Feature;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

/**
 * Fix #7: social-login hardening. A returning provider user is linked to the
 * existing account that shares their email instead of creating a duplicate,
 * and the provider fields are persisted via the explicit (non mass assigned)
 * path.
 */
class SocialLoginLinkingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function fakeSocialUser(string $email, string $id, string $name = 'Social User'): void
    {
        $socialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $socialUser->shouldReceive('getId')->andReturn($id);
        $socialUser->id = $id;
        $socialUser->email = $email;
        $socialUser->name = $name;
        // Provider-verified email so linking onto an existing account is allowed.
        $socialUser->user = ['email_verified' => true];

        $provider = Mockery::mock();
        $provider->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->andReturn($provider);
    }

    public function test_existing_email_account_is_linked_not_duplicated(): void
    {
        $existing = User::factory()->create([
            'email' => 'linkme@example.com',
            'provider' => null,
            'provider_id' => null,
        ]);

        $countBefore = User::count();

        $this->fakeSocialUser('linkme@example.com', 'gh-12345');

        $controller = app(LoginController::class);
        $controller->handleProviderCallback('github');

        $this->assertSame($countBefore, User::count(), 'No duplicate user should be created');

        $existing->refresh();
        $this->assertSame('github', $existing->provider);
        $this->assertSame('gh-12345', $existing->provider_id);
        $this->assertTrue(auth()->check());
        $this->assertSame($existing->id, auth()->id());
    }

    public function test_new_social_user_is_created_with_provider_fields(): void
    {
        $this->fakeSocialUser('brandnew@example.com', 'gh-99999', 'Brand New');

        $controller = app(LoginController::class);
        $controller->handleProviderCallback('github');

        $created = User::where('email', 'brandnew@example.com')->first();

        $this->assertNotNull($created);
        $this->assertSame('github', $created->provider);
        $this->assertSame('gh-99999', $created->provider_id);
        // Privileged role must fall back to the default (standard user = 2).
        $this->assertSame(2, (int) $created->role_id);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
