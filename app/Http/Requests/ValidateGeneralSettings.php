<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ValidateGeneralSettings extends FormRequest
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
        $membership = $this->request->get('membership');

        if ($membership === 'on') {
            $this->request->add(['membership' => '1']);
        } else {
            $this->request->add(['membership' => '0']);
        }

        return [
            'website_name' => 'required|string',
            'tagline' => 'required|string',
            'posts_per_page' => 'required|integer',
            'comments_per_page' => 'required|integer',
            'contact_email' => 'required|email',
            'membership' => 'required|in:0,1',
            'active_template_name' => 'nullable|string',
        ];

    }
}
