<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FrontEndUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userId = get_logged_user_id();

        $rules = [
            'name' => 'nullable|string',
            'surname' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'about_me' => 'nullable|string',
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'google_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'xing_url' => 'nullable|url',
            // NOTE: role_id is intentionally NOT accepted here. A front-end
            // user must never be able to change their own role; the repository
            // strips it as a second line of defence.
            'gender' => 'nullable|in:male,female',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        $email = ['required',
            'string',
            'email',
            'max:100',
            Rule::unique('users')->ignore($userId),
        ];

        $username = [
            'string',
            'min:5',
            'max:20',
            Rule::unique('users')->ignore($userId),
        ];

        $rules['email'] = $email;
        $rules['username'] = $username;

        return $rules;
    }
}
