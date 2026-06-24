<?php

/**
 * Cmstack-Laravel
 * File: PageRepository.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 24.10.2019
 */

namespace App\Repositories;

use App\Http\Models\User;
use Hash;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Image;

class UserRepository extends BaseRepository
{
    private $logged_user_id;

    protected $select_fields = [
        'email',
        'username',
        'name',
        'surname',
        'gender',
        'country',
        'city',
        'role_id',
        'facebook_url',
        'twitter_url',
        'google_url',
        'instagram_url',
        'linkedin_url',
        'xing_url',
        'about_me',
        'created_at',
        'avatar',
    ];

    public function __construct(User $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    private function get_logged_user_id()
    {
        if (! is_logged_in()) {
            return false;
        }

        $this->logged_user_id = get_logged_user_id();
    }

    /**
     * Update the user's own profile from validated input.
     *
     * Only whitelisted (validated) fields are applied, and privileged columns
     * (role_id, provider, provider_id) are stripped so a front-end user can
     * never escalate their role or hijack a social identity through this path.
     *
     * @param  FormRequest  $request
     * @return bool
     */
    public function update(int $id, $request)
    {
        $data = $request->validated();

        unset($data['role_id'], $data['provider'], $data['provider_id']);

        // The validated avatar (when present) is an uploaded file; replace it
        // with the stored image path before persisting.
        if ($request->hasFile('avatar')) {
            $data['avatar'] = uploadImage($request->file('avatar'));
        } else {
            unset($data['avatar']);
        }

        $user = $this->model->findOrFail($id);

        return (bool) $user->update($data);
    }

    public function changePassword($request)
    {
        if (! is_logged_in() || ! (Hash::check($request->current_password, \Auth::user()->password))) {
            return false;
        }

        $this->get_logged_user_id();

        $user = $this->model->findOrFail($this->logged_user_id);
        $result = $user->update(['password' => $request->password]);

        return $result;
    }

    /**
     * Resolve an existing account for a social profile, linking by email when
     * needed. Matches first on (provider_id, provider), then falls back to
     * linking the social identity onto an account that already owns the
     * provider-supplied email (so no duplicate account is created). The whole
     * lookup + link runs in one transaction to avoid races. Returns null when
     * no account can be resolved (the caller then validates + creates one).
     *
     * @param  object  $socialUser  Socialite user (id, email, name)
     */
    public function findOrLinkSocialIdentity(object $socialUser, string $provider): ?User
    {
        return DB::transaction(function () use ($socialUser, $provider) {
            $authUser = $this->model->where('provider_id', $socialUser->id)
                ->where('provider', $provider)
                ->first();

            if ($authUser) {
                return $authUser;
            }

            if (empty($socialUser->email)) {
                return null;
            }

            $existing = $this->model->where('email', $socialUser->email)->first();

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
     * Create a brand-new user from a social profile. Provider identity fields
     * are assigned explicitly (not mass assigned); privileged fields (role_id)
     * are left to the database default.
     *
     * @param  object  $socialUser  Socialite user (email, name)
     */
    public function createFromSocial(object $socialUser, string $provider, string $username): User
    {
        return DB::transaction(function () use ($socialUser, $provider, $username) {
            $newUser = $this->model->newInstance([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'username' => $username,
            ]);

            $newUser->provider = $provider;
            $newUser->provider_id = $socialUser->id;
            $newUser->save();

            return $newUser;
        });
    }

    /**
     * Assign a new plaintext password to a user instance (the model's
     * setPasswordAttribute mutator hashes it once). The caller persists the
     * model; this keeps password mutation out of the controller layer.
     */
    public function setPlainPassword(User $user, string $password): void
    {
        $user->password = $password;
    }

    /**
     * Create a user from a self-service registration. Only the four whitelisted
     * fields are persisted; the plaintext password is passed through and hashed
     * once by the model's setPasswordAttribute mutator (privileged fields like
     * role_id are left to the database default).
     *
     * @param  array<string, mixed>  $data  Validated registration input.
     */
    public function createFromRegistration(array $data): User
    {
        return $this->model::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
        ]);
    }
}
