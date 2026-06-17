<?php
/**
 * LaraPress CMS
 * File: geo-settings.blade.php
 * GEO (Generative Engine Optimization) admin settings page.
 */
?>

@extends('cpanel.core.index')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/settings.geo_settings_headline')</h1>
            <p class="mt-2 text-sm leading-relaxed text-ink-500">@lang('cpanel/settings.geo_intro')</p>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}">
                <strong>{{ $update_message ? __('cpanel/settings.geo_settings_updates_success') : $update_message }}</strong>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cpanel_update_geo_settings') }}" method="POST">
            @csrf

            {{-- Identity --}}
            <div class="card">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.geo_identity_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_business_name')</label>
                        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $geo_settings->business_name) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_business_type')</label>
                        @php $type = old('business_type', $geo_settings->business_type ?? 'Organization'); @endphp
                        <select name="business_type" class="form-control">
                            <option value="Organization" @selected($type === 'Organization')>@lang('cpanel/settings.geo_type_organization')</option>
                            <option value="LocalBusiness" @selected($type === 'LocalBusiness')>@lang('cpanel/settings.geo_type_localbusiness')</option>
                            <option value="ProfessionalService" @selected($type === 'ProfessionalService')>@lang('cpanel/settings.geo_type_professionalservice')</option>
                            <option value="Person" @selected($type === 'Person')>@lang('cpanel/settings.geo_type_person')</option>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_description')</label>
                        <textarea rows="3" name="description" class="form-control">{{ old('description', $geo_settings->description) }}</textarea>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_founder_name')</label>
                        <input type="text" name="founder_name" class="form-control" value="{{ old('founder_name', $geo_settings->founder_name) }}">
                    </div>
                </div>
            </div>

            {{-- Services & reach --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.geo_services_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_services')</label>
                        <textarea rows="5" name="services" class="form-control" placeholder="Laravel development&#10;Custom CMS&#10;AI / MCP integration">{{ old('services', $geo_settings->services) }}</textarea>
                        <div class="field-desc"><p>@lang('cpanel/settings.geo_services_help')</p></div>
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_service_area')</label>
                        <input type="text" name="service_area" class="form-control" value="{{ old('service_area', $geo_settings->service_area) }}">
                        <div class="field-desc"><p>@lang('cpanel/settings.geo_service_area_help')</p></div>
                    </div>
                </div>
            </div>

            {{-- Contact --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.geo_contact_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_contact_email')</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $geo_settings->contact_email) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_contact_phone')</label>
                        <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $geo_settings->contact_phone) }}">
                    </div>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_address')</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address', $geo_settings->address) }}">
                    </div>
                </div>
            </div>

            {{-- Authority / citations --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.geo_authority_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_same_as')</label>
                        <textarea rows="4" name="same_as" class="form-control" placeholder="https://linkedin.com/in/...&#10;https://github.com/...">{{ old('same_as', $geo_settings->same_as) }}</textarea>
                        <div class="field-desc"><p>@lang('cpanel/settings.geo_same_as_help')</p></div>
                    </div>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.geo_faq_section')</h2>
                    <div class="field">
                        <label class="field-label">@lang('cpanel/settings.geo_faq')</label>
                        <textarea rows="5" name="faq" class="form-control" placeholder="Do you work remotely? | Yes, with clients across the EU and worldwide.">{{ old('faq', $geo_settings->faq) }}</textarea>
                        <div class="field-desc"><p>@lang('cpanel/settings.geo_faq_help')</p></div>
                    </div>
                </div>
            </div>

            {{-- Output toggles --}}
            <div class="card mt-6">
                <div class="card-body space-y-1">
                    <h2 class="mb-2 text-sm font-semibold uppercase tracking-wide text-ink-400">@lang('cpanel/settings.geo_output_section')</h2>
                    <div class="field">
                        <label for="emit_jsonld" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                            <input class="form-check-input" id="emit_jsonld" name="emit_jsonld" type="checkbox" value="1" {{ old('emit_jsonld', $geo_settings->emit_jsonld ?? true) ? 'checked' : '' }}>
                            @lang('cpanel/settings.geo_emit_jsonld')
                        </label>
                    </div>
                    <div class="field">
                        <label for="include_in_llms" class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-700">
                            <input class="form-check-input" id="include_in_llms" name="include_in_llms" type="checkbox" value="1" {{ old('include_in_llms', $geo_settings->include_in_llms ?? true) ? 'checked' : '' }}>
                            @lang('cpanel/settings.geo_include_in_llms')
                        </label>
                    </div>
                </div>
                <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                    <button type="submit" class="btn btn-info">@lang('cpanel/settings.update_button_label')</button>
                </div>
            </div>
        </form>
    </div>
@endsection
