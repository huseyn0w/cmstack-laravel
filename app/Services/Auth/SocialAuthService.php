<?php

namespace App\Services\Auth;

use App\Http\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\Validator;

/**
 * Social-login business logic, extracted from LoginController so the
 * controller stays thin (resolve socialite user -> service -> Auth::login).
 *
 * The service owns the flow (resolve-or-link, validate, create) but never
 * touches the ORM directly — all persistence goes through UserRepository:
 *  - match first on (provider_id, provider),
 *  - then link by the provider-supplied email (no duplicate accounts),
 *  - lookup + linking and creation each run in a transaction,
 *  - provider fields are set explicitly (never mass assigned),
 *  - role_id is left to the database default for new users.
 */
class SocialAuthService
{
    public function __construct(private UserRepository $users) {}

    /**
     * Resolve an existing account for the social user. Matches first on the
     * social identity (provider_id, provider); otherwise links the identity onto
     * an account that already owns the email — but ONLY when the provider has
     * verified that email, to prevent an account takeover via an attacker's
     * unverified provider email. Returns null when no account can be resolved
     * (caller then validates + creates a fresh user).
     *
     * @throws SocialEmailNotVerifiedException when an account exists for the
     *                                         email but the provider email is unverified.
     */
    public function findOrLink(object $socialUser, string $provider): ?User
    {
        $linked = $this->users->findBySocialIdentity($socialUser, $provider);

        if ($linked) {
            return $linked;
        }

        if (empty($socialUser->email)) {
            return null;
        }

        $existing = $this->users->findByEmail((string) $socialUser->email);

        if (! $existing) {
            return null;
        }

        if (! $this->providerEmailVerified($socialUser)) {
            throw new SocialEmailNotVerifiedException;
        }

        return $this->users->linkSocialIdentity($existing, $socialUser, $provider);
    }

    /**
     * Whether the provider asserts the social profile's email is verified. The
     * flag lives in the provider's raw payload under one of a few common keys;
     * when it is absent we treat the email as UNVERIFIED (secure default), so a
     * provider that does not vouch for the address cannot link to an account.
     */
    private function providerEmailVerified(object $socialUser): bool
    {
        $raw = [];

        if (method_exists($socialUser, 'getRaw')) {
            $raw = (array) $socialUser->getRaw();
        } elseif (isset($socialUser->user) && is_array($socialUser->user)) {
            $raw = $socialUser->user;
        }

        foreach (['email_verified', 'verified_email'] as $key) {
            if (array_key_exists($key, $raw)) {
                return filter_var($raw[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return false;
    }

    /**
     * Validate a brand-new social profile before creating an account.
     *
     * @return true|ValidatorContract True when valid, otherwise the failed validator.
     */
    public function validateNew(object $socialUser): bool|ValidatorContract
    {
        $username = $this->usernameFromEmail((string) $socialUser->email);

        $validator = Validator::make(
            [
                'email' => $socialUser->email,
                'name' => $socialUser->name,
                'username' => $username,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
            ]
        );

        if ($validator->fails()) {
            return $validator;
        }

        return true;
    }

    /**
     * Create a brand-new user from the social profile. Provider identity
     * fields are assigned explicitly rather than mass assigned, and privileged
     * fields (role_id) are left to their database default.
     */
    public function create(object $socialUser, string $provider): User
    {
        return $this->users->createFromSocial(
            $socialUser,
            $provider,
            $this->usernameFromEmail((string) $socialUser->email),
            $this->providerEmailVerified($socialUser)
        );
    }

    /**
     * Derive a username from the local part of the email. For a normal address
     * this matches the original controller exactly; a malformed `@`-less value
     * (which never passes the email validation in validateNew) returns the whole
     * string rather than the original's empty string — a harmless hardening.
     */
    private function usernameFromEmail(string $email): string
    {
        $position = strpos($email, '@');

        return $position === false ? $email : substr($email, 0, $position);
    }
}
