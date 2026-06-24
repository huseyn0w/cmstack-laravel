<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(private UserRepository $users)
    {
        $this->middleware('guest');
    }

    /**
     * Assign the new password to the user.
     *
     * The default trait implementation calls Hash::make() here, but User's
     * setPasswordAttribute mutator already hashes on assignment. Passing the
     * plaintext through keeps a single hashing path and avoids double-hashing
     * (which would silently break login after a reset).
     *
     * @param  User  $user
     * @param  string  $password
     * @return void
     */
    protected function setUserPassword($user, $password)
    {
        $this->users->setPlainPassword($user, $password);
    }
}
