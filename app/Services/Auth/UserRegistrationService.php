<?php

namespace App\Services\Auth;

use App\Http\Models\User;

/**
 * Form-registration user creation, extracted from RegisterController::create()
 * so the controller stays thin. Only the four self-service fields are ever
 * persisted; privileged fields (role_id) are left to the database default so a
 * caller cannot self-assign a higher role.
 *
 * NOTE: the plaintext password is passed through unchanged — User's
 * setPasswordAttribute() mutator hashes it. The original controller also called
 * Hash::make() here, which double-hashed the password and silently broke
 * password login for form-registered users; that bug is fixed by relying on the
 * single hashing path in the model.
 */
class UserRegistrationService
{
    /**
     * @param  array<string, mixed>  $data  Validated registration input.
     */
    public function register(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => $data['password'],
        ]);
    }
}
