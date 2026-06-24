<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PostListRequest extends FormRequest
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
            'posts_action' => ['required', 'string', Rule::in(['delete', 'destroy', 'restore'])],
            'posts' => 'array|required',
        ];
    }

    public function messages()
    {
        return [
            'posts_action.regex' => 'You should use action "Delete"',
            'posts.required' => 'You should choose at least 1 post to delete',
        ];
    }
}
