<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PageListRequest extends FormRequest
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
            'pages_action' => ['required', 'string', Rule::in(['delete', 'destroy', 'restore'])],
            'pages' => 'array|required',
        ];
    }

    public function messages()
    {
        return [
            'pages_action.in' => 'You should use action "Delete", "Restore" or "Destroy"',
            'pages.required' => 'You should choose at least 1 page',
        ];
    }
}
