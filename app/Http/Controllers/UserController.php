<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\FrontEndUserRequest;
use App\Services\Front\ProfileService;

class UserController extends BaseController
{
    public function __construct(ProfileService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    public function yourProfile()
    {
        $username = get_logged_user_username();
        $user = $this->service->byUsername($username);

        return view('default.users.yourprofile', compact('user'));
    }

    public function update(FrontEndUserRequest $request)
    {
        $user_id = get_logged_user_id();
        $this->service->update($user_id, $request);

        return back()->with('message', ' ');
    }

    public function password()
    {
        return view('default.users.change_password');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $result = $this->service->changePassword($request);

        if (! $result) {
            return redirect()->back()->withErrors(trans('cpanel/controller.password_match'));
        }

        return back()->with('message', ' ');
    }

    public function show($username)
    {
        $user = $this->service->byUsername($username);

        return view('default.users.profile', compact('user'));
    }
}
