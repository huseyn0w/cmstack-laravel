<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\SocialAuthService;
use App\Services\Auth\SocialEmailNotVerifiedException;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    public function __construct(private SocialAuthService $socialAuth)
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return SymfonyRedirectResponse
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return RedirectResponse
     */
    public function handleProviderCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->user();

        try {
            $authUser = $this->socialAuth->findOrLink($socialUser, $provider);
        } catch (SocialEmailNotVerifiedException $e) {
            return redirect()->route('login')
                ->with('status', trans('default/auth.social_email_unverified'));
        }

        if ($authUser) {
            Auth::login($authUser, true);

            return redirect($this->redirectTo);
        }

        // Creating a brand-new account via a provider is a signup, so it is
        // gated by the same membership toggle as the register form. Linking an
        // existing account above is a login and stays allowed.
        if (! get_general_settings('membership')) {
            return redirect()->route('login')
                ->with('status', trans('default/auth.registration_disabled'));
        }

        $validator = $this->socialAuth->validateNew($socialUser);

        if ($validator !== true) {
            return redirect('login')
                ->withErrors($validator)
                ->withInput();
        }

        $registered_user = $this->socialAuth->create($socialUser, $provider);
        Auth::login($registered_user, true);

        return redirect($this->redirectTo);
    }

    protected function credentials(Request $request)
    {
        $field = filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)
            ? $this->username()
            : 'username';

        return [
            $field => $request->get($this->username()),
            'password' => $request->password,
        ];
    }
}
