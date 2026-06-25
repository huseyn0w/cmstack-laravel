<?php

namespace Tests\Feature\Auth;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Models\CPanel\CPanelGeneralSettings;
use App\Http\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

/**
 * P8: the membership toggle and email-verification flow must stay coherent with
 * social login. New social accounts count as signups (blocked when membership is
 * off) while existing linked accounts can still log in; and a social account is
 * created already email-verified (the provider vouches for the address).
 */
class SocialMembershipAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function fakeSocialUser(string $email, string $id, string $name = 'Social User', bool $emailVerified = true): void
    {
        $socialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);
        $socialUser->shouldReceive('getId')->andReturn($id);
        $socialUser->id = $id;
        $socialUser->email = $email;
        $socialUser->name = $name;
        // Mimic the provider's raw payload, where the verified-email flag lives.
        $socialUser->user = ['email_verified' => $emailVerified];

        $provider = Mockery::mock();
        $provider->shouldReceive('user')->andReturn($socialUser);

        Socialite::shouldReceive('driver')->andReturn($provider);
    }

    private function setMembership(bool $on): void
    {
        CPanelGeneralSettings::query()->update(['membership' => $on ? 1 : 0]);
    }

    public function test_new_social_account_blocked_when_membership_off(): void
    {
        $this->setMembership(false);
        $countBefore = User::count();
        $this->fakeSocialUser('newsocial@example.com', 'gh-77777', 'New Social');

        $response = app(LoginController::class)->handleProviderCallback('github');

        $this->assertSame($countBefore, User::count(), 'No new social account when membership off');
        $this->assertDatabaseMissing('users', ['email' => 'newsocial@example.com']);
        $this->assertGuest();
        $this->assertStringContainsString('login', $response->getTargetUrl());
    }

    public function test_existing_social_user_can_login_when_membership_off(): void
    {
        $this->setMembership(false);
        $existing = User::factory()->create([
            'email' => 'returning@example.com', 'provider' => null, 'provider_id' => null,
        ]);
        $this->fakeSocialUser('returning@example.com', 'gh-88888');

        app(LoginController::class)->handleProviderCallback('github');

        $this->assertTrue(auth()->check());
        $this->assertSame($existing->id, auth()->id());
    }

    public function test_social_login_refuses_link_when_provider_email_unverified(): void
    {
        // Pre-auth account-takeover guard: a provider profile carrying a victim's
        // email but WITHOUT a provider-verified flag must NOT be linked/logged in.
        $victim = User::factory()->create([
            'email' => 'victim@example.com', 'provider' => null, 'provider_id' => null,
        ]);
        $this->fakeSocialUser('victim@example.com', 'gh-evil', 'Attacker', emailVerified: false);

        $response = app(LoginController::class)->handleProviderCallback('github');

        $this->assertGuest();
        $this->assertStringContainsString('login', $response->getTargetUrl());

        $victim->refresh();
        $this->assertNull($victim->provider, 'Account must not be linked to an unverified social identity');
        $this->assertNull($victim->provider_id);
    }

    public function test_new_social_user_is_marked_email_verified(): void
    {
        $this->setMembership(true);
        $this->fakeSocialUser('verified-social@example.com', 'gh-66666');

        app(LoginController::class)->handleProviderCallback('github');

        $created = User::where('email', 'verified-social@example.com')->firstOrFail();
        $this->assertTrue($created->hasVerifiedEmail(), 'Provider-verified social accounts are pre-verified');
    }

    public function test_new_social_user_with_unverified_email_is_not_pre_verified(): void
    {
        // A new account from a provider that did NOT verify the email must not be
        // forged as verified (would bypass the email-verification requirement).
        $this->setMembership(true);
        $this->fakeSocialUser('unverified-new@example.com', 'gh-44444', emailVerified: false);

        app(LoginController::class)->handleProviderCallback('github');

        $created = User::where('email', 'unverified-new@example.com')->firstOrFail();
        $this->assertFalse($created->hasVerifiedEmail(), 'Unverified provider email must not pre-verify a new account');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
