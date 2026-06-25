<?php

namespace App\Services\Auth;

use App\Http\Models\User;
use App\Repositories\UserRepository;

/**
 * Password-reset persistence, extracted from ResetPasswordController so the
 * controller stays a pure HTTP boundary. The service delegates persistence to
 * UserRepository (it never touches the ORM directly).
 *
 * The plaintext password is passed through and hashed once by User's
 * setPasswordAttribute mutator — the default ResetsPasswords trait also called
 * Hash::make() here, which double-hashed the password and silently broke login
 * after a reset; relying on the single hashing path in the model fixes that.
 */
class PasswordResetService
{
    public function __construct(private UserRepository $users) {}

    public function setPassword(User $user, string $password): void
    {
        $this->users->setPlainPassword($user, $password);
    }
}
