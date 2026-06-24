<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
            'pages_action' => ['required', 'string', 'regex:(delete)'],
            'pages' => 'array|required',
        ];
    }

    public function messages()
    {
        return [
            'pages_action.regex' => 'You should use action "Delete"',
            'pages.required' => 'You should choose at least 1 page to delete',
        ];
    }
}
