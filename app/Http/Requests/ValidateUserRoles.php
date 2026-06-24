<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

class ValidateUserRoles extends FormRequest
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

        $rules = [];

        $role_id = (int) $this->route('id');

        $route_name = Route::currentRouteName();

        $rules['name'] = 'string|required|unique:user_roles';

        if ($route_name === 'cpanel_update_user_role') {
            $rules['name'] = ['required',
                'string',
                'max:100',
                Rule::unique('user_roles')->ignore($role_id),
            ];
        }

        $rules['permissions'] = 'array';

        return $rules;
    }

    public function messages()
    {
        return [
            'name.unique' => 'You already have Role with this name',
            'permissions.required' => 'You should choose at least 1 role management operation',
        ];
    }
}
