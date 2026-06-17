<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Validation for the admin global GEO settings form.
 *
 * The route is already gated by manage_general_settings; we re-assert it here.
 * Checkbox fields are normalised to real booleans so unchecked boxes persist
 * as false. business_type is constrained to the schema.org types we render.
 */
class ValidateGeoSettings extends FormRequest
{
    public function authorize()
    {
        return Auth::check()
            && Auth::user()->can('manage_general_settings', 'App\Http\Models\UserRoles');
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'emit_jsonld'     => $this->boolean('emit_jsonld'),
            'include_in_llms' => $this->boolean('include_in_llms'),
        ]);
    }

    public function rules()
    {
        return [
            'business_name'   => 'nullable|string|max:255',
            'business_type'   => 'required|in:Organization,LocalBusiness,ProfessionalService,Person',
            'description'     => 'nullable|string|max:1000',
            'founder_name'    => 'nullable|string|max:255',
            'services'        => 'nullable|string|max:2000',
            'service_area'    => 'nullable|string|max:255',
            'contact_email'   => 'nullable|email|max:255',
            'contact_phone'   => 'nullable|string|max:50',
            'address'         => 'nullable|string|max:255',
            'same_as'         => 'nullable|string|max:2000',
            'faq'             => 'nullable|string|max:5000',
            'emit_jsonld'     => 'boolean',
            'include_in_llms' => 'boolean',
        ];
    }
}
