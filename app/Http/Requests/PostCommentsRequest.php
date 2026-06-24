<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class PostCommentsRequest extends FormRequest
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

        $route_name = Route::currentRouteName();

        if ($route_name === 'store_post_comments') {
            $rules = [
                'post_id' => 'required|integer',
                'parent_id' => 'nullable|integer',
                'comment' => 'required|string|max:5000',
            ];
        } elseif ($route_name === 'update_post_comment') {
            $rules = [
                'updated_comment_id' => 'required|integer',
                'comment' => 'required|string|max:5000',
            ];
        } elseif ($route_name === 'delete_post_comments') {
            $rules = [
                'commentId' => 'required|integer',
                'username' => 'required|string',
            ];
        }

        return $rules;
    }
}
