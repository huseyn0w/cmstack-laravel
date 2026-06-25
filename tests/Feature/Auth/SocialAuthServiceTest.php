<?php

namespace Tests\Feature\Auth;

use App\Http\Models\User;
use App\Services\Auth\SocialAuthService;
use App\Services\Auth\SocialEmailNotVerifiedException;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/**
 * Characterizes the social-login business logic now that it lives in a
 * dedicated service (extracted out of LoginController). Behaviour must be
 * byte-for-byte identical to the pre-refactor controller.
 */
class SocialAuthServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function socialUser(string $email, string $id, string $name = 'Social User', bool $emailVerified = true): object
    {
        $user = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $user->shouldReceive('getId')->andReturn($id);
        $user->id = $id;
        $user->email = $email;
        $user->name = $name;
        $user->user = ['email_verified' => $emailVerified];

        return $user;
    }

    public function test_find_or_link_returns_match_by_provider_id(): void
    {
        $service = app(SocialAuthService::class);

        $existing = User::factory()->create([
            'email' => 'byid@example.com',
            'provider' => 'github',
            'provider_id' => 'gh-1',
        ]);

        $resolved = $service->findOrLink($this->socialUser('different@example.com', 'gh-1'), 'github');

        $this->assertNotNull($resolved);
        $this->assertSame($existing->id, $resolved->id);
    }

    public function test_find_or_link_links_existing_email_account_without_duplicating(): void
    {
        $service = app(SocialAuthService::class);

        $existing = User::factory()->create([
            'email' => 'linkme@example.com',
            'provider' => null,
            'provider_id' => null,
        ]);
        $before = User::count();

        $resolved = $service->findOrLink($this->socialUser('linkme@example.com', 'gh-2'), 'github');

        $this->assertSame($before, User::count());
        $this->assertNotNull($resolved);
        $this->assertSame($existing->id, $resolved->id);
        $resolved->refresh();
        $this->assertSame('github', $resolved->provider);
        $this->assertSame('gh-2', $resolved->provider_id);
    }

    public function test_find_or_link_returns_null_for_unknown_user(): void
    {
        $service = app(SocialAuthService::class);

        $resolved = $service->findOrLink($this->socialUser('nobody@example.com', 'gh-3'), 'github');

        $this->assertNull($resolved);
    }

    public function test_find_or_link_refuses_to_link_when_provider_email_unverified(): void
    {
        $service = app(SocialAuthService::class);

        $existing = User::factory()->create([
            'email' => 'protected@example.com', 'provider' => null, 'provider_id' => null,
        ]);

        $this->expectException(SocialEmailNotVerifiedException::class);

        try {
            $service->findOrLink($this->socialUser('protected@example.com', 'gh-x', emailVerified: false), 'github');
        } finally {
            $existing->refresh();
            $this->assertNull($existing->provider, 'Unverified social email must not link onto the account');
        }
    }

    public function test_find_or_link_returns_null_when_email_is_empty(): void
    {
        $service = app(SocialAuthService::class);

        $resolved = $service->findOrLink($this->socialUser('', 'gh-4'), 'github');

        $this->assertNull($resolved);
    }

    public function test_validate_new_returns_true_for_valid_social_user(): void
    {
        $service = app(SocialAuthService::class);

        $this->assertTrue($service->validateNew($this->socialUser('fresh@example.com', 'gh-5')));
    }

    public function test_validate_new_returns_validator_when_email_taken(): void
    {
        $service = app(SocialAuthService::class);
        User::factory()->create(['email' => 'taken@example.com']);

        $result = $service->validateNew($this->socialUser('taken@example.com', 'gh-6'));

        $this->assertInstanceOf(ValidatorContract::class, $result);
        $this->assertTrue($result->fails());
    }

    public function test_create_persists_user_with_provider_fields_and_default_role(): void
    {
        $service = app(SocialAuthService::class);

        $created = $service->create($this->socialUser('brandnew@example.com', 'gh-7', 'Brand New'), 'github');

        $this->assertSame('github', $created->provider);
        $this->assertSame('gh-7', $created->provider_id);
        $this->assertSame('brandnew', $created->username);
        // role_id is left to the DB default (2) — visible after a fresh read.
        $this->assertSame(2, (int) $created->fresh()->role_id);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
