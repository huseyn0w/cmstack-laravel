<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CategoryListRequest extends FormRequest
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
            'categories_action' => ['required', 'string', 'regex:(delete)'],
            'categories' => 'array|required',
        ];
    }

    public function messages()
    {
        return [
            'categories_action.regex' => 'You should use action "Delete"',
            'categories.required' => 'You should choose at least 1 category to delete',
        ];
    }
}
