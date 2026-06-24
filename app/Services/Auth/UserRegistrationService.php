<?php

namespace App\Services\Auth;

use App\Http\Models\User;
use App\Repositories\UserRepository;

/**
 * Form-registration user creation, extracted from RegisterController::create()
 * so the controller stays thin. The service delegates persistence to
 * UserRepository (it never touches the ORM directly).
 *
 * Only the four self-service fields are persisted; privileged fields (role_id)
 * are left to the database default so a caller cannot self-assign a higher
 * role. The plaintext password is passed through and hashed once by User's
 * setPasswordAttribute mutator — the original controller also called
 * Hash::make() here, which double-hashed the password and silently broke
 * password login; relying on the single hashing path in the model fixes that.
 */
class UserRegistrationService
{
    public function __construct(private UserRepository $users) {}

    /**
     * @param  array<string, mixed>  $data  Validated registration input.
     */
    public function register(array $data): User
    {
        return $this->users->createFromRegistration($data);
    }
}
