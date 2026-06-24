<?php

namespace App\Services\Auth;

use App\Http\Models\User;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * Social-login business logic, extracted from LoginController so the
 * controller stays thin (resolve socialite user -> service -> Auth::login).
 *
 * Behaviour is preserved exactly from the original controller:
 *  - match first on (provider_id, provider),
 *  - then link by the provider-supplied email (no duplicate accounts),
 *  - lookup + linking and creation each run in a transaction,
 *  - provider fields are set explicitly (never mass assigned),
 *  - role_id is left to the database default for new users.
 */
class SocialAuthService
{
    /**
     * Resolve an existing account for the social user, linking by email when
     * needed. Returns null when no account can be resolved (caller then
     * validates + creates a fresh user).
     */
    public function findOrLink(object $socialUser, string $provider): ?User
    {
        return DB::transaction(function () use ($socialUser, $provider) {
            $authUser = User::where('provider_id', $socialUser->id)
                ->where('provider', $provider)
                ->first();

            if ($authUser) {
                return $authUser;
            }

            if (empty($socialUser->email)) {
                return null;
            }

            $existing = User::where('email', $socialUser->email)->first();

            if ($existing) {
                // Link the social identity to the existing account. Provider
                // fields are set explicitly (they are not mass assignable).
                $existing->provider = $provider;
                $existing->provider_id = $socialUser->id;
                $existing->save();

                return $existing;
            }

            return null;
        });
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
        return DB::transaction(function () use ($socialUser, $provider) {
            $newUser = new User([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'username' => $this->usernameFromEmail((string) $socialUser->email),
            ]);

            $newUser->provider = $provider;
            $newUser->provider_id = $socialUser->id;
            $newUser->save();

            return $newUser;
        });
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
