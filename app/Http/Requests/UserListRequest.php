<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserListRequest extends FormRequest
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
        return [
            'users_action' => ['required', 'string', 'regex:(delete)'],
            'users' => 'array|required',
        ];
    }

    public function messages()
    {
        return [
            'users_action.regex' => 'You should use action "Delete"',
            'users.required' => 'You should choose at least 1 user to delete',
        ];
    }
}
