<?php
/**
 * LaraPress CMS
 * File: yourprofile.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 12.11.2019
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 *          Avatar live-preview reimplemented with Alpine (avatarUpload).
 */
?>
@extends(config('app.template_name').'/index')

@section('content')

@php
    $home_page_data = get_data(1, 'page', ['slug', 'title']);
    $countries = get_countries_array();
    $avatar_src = image_src($user->avatar, true);
@endphp

@include(config('app.template_name').'.partials.banner', [
    'title'  => __('default/profile.profile'),
    'crumbs' => [
        ['label' => $home_page_data->title, 'url' => env('APP_URL')],
        ['label' => __('default/profile.edit_profile'), 'url' => null],
    ],
])

<section class="mx-auto max-w-3xl px-5 py-16 sm:px-8 sm:py-20">
    @if ($errors->any())
        <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm text-brand-800" role="alert">
            <ul class="list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (Session::has('message'))
        @if (Session::get('message'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800" role="status">@lang('default/profile.user_updated')</div>
        @else
            <div class="mb-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm font-medium text-brand-800" role="alert">@lang('default/profile.user_update_error')</div>
        @endif
    @endif

    <form action="{{ route('update_user_info') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @method('PUT')
        @csrf

        {{-- Avatar --}}
        <div x-data="avatarUpload('{{ $avatar_src }}')" class="flex items-center gap-6">
            <img :src="preview" alt="{{$user->username}}" class="h-24 w-24 rounded-2xl object-cover shadow-card ring-1 ring-ink-100">
            <div>
                <label for="file-upload" class="btn-ghost cursor-pointer">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M10 13V4m0 0L6.5 7.5M10 4l3.5 3.5M4 13v2a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @lang('default/profile.edit')
                </label>
                <input id="file-upload" type="file" name="avatar" accept="image/*" class="sr-only" @change="pick">
                <p class="mt-2 text-sm text-ink-400">PNG, JPG, GIF, WEBP</p>
            </div>
        </div>

        {{-- Account --}}
        <div class="space-y-5 border-t border-ink-100 pt-8">
            <a href="{{route('get_change_password_interface')}}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-700 hover:underline">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="9" width="12" height="8" rx="1.5"/><path d="M7 9V6.5a3 3 0 0 1 6 0V9" stroke-linecap="round"/></svg>
                @lang('default/profile.change_password')
            </a>

            <div class="grid gap-5 sm:grid-cols-2">
                @if($user->username)
                    <div>
                        <span class="field-label">@lang('default/profile.username')</span>
                        <p class="rounded-xl border border-ink-100 bg-ink-50 px-4 py-3 text-base text-ink-600">{{$user->username}}</p>
                    </div>
                @endif
                <div @class(['sm:col-span-2' => !$user->username])>
                    <label for="email" class="field-label">@lang('default/profile.email')</label>
                    <input type="email" id="email" name="email" class="field-input" value="{{ old('email', $user->email) }}">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="field-label">@lang('default/profile.name')</label>
                    <input type="text" id="name" name="name" class="field-input" placeholder="@lang('default/profile.name')" value="{{ old('name', $user->name) }}">
                </div>
                <div>
                    <label for="surname" class="field-label">@lang('default/profile.surname')</label>
                    <input type="text" id="surname" name="surname" class="field-input" placeholder="@lang('default/profile.surname')" value="{{ old('surname', $user->surname) }}">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="country" class="field-label">@lang('default/profile.country')</label>
                    <select name="country" id="country" class="field-input">
                        @foreach($countries as $country)
                            <option value="{{$country['name']}}" @selected($country['name'] === $user->country)>{{$country['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="city" class="field-label">@lang('default/profile.city')</label>
                    <input type="text" id="city" name="city" class="field-input" value="{{ old('city', $user->city) }}">
                </div>
            </div>

            <div>
                <label for="about_me" class="field-label">@lang('default/profile.about')</label>
                <textarea id="about_me" name="about_me" rows="4" class="field-input resize-y">{{ old('about_me', $user->about_me) }}</textarea>
            </div>
        </div>

        {{-- Social links --}}
        <div class="space-y-5 border-t border-ink-100 pt-8">
            <h3 class="text-xs font-medium uppercase tracking-wider text-ink-400">Links</h3>
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach([
                    'facebook_url' => 'Facebook', 'google_url' => 'Google',
                    'twitter_url'  => 'Twitter',  'instagram_url' => 'Instagram',
                    'linkedin_url' => 'Linkedin', 'xing_url' => 'Xing',
                ] as $field => $label)
                    <div>
                        <label for="{{$field}}" class="field-label">{{$label}}</label>
                        <input type="text" id="{{$field}}" name="{{$field}}" class="field-input" placeholder="https://" value="{{ old($field, $user->$field) }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Gender --}}
        <div class="space-y-3 border-t border-ink-100 pt-8">
            <span class="field-label">@lang('default/profile.gender')</span>
            <div class="flex flex-wrap gap-3">
                <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-xl border border-ink-200 px-4 py-3 transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                    <input type="radio" name="gender" value="male" id="male" @checked($user->gender === 'male') class="text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-ink-700">@lang('default/profile.gender_male')</span>
                </label>
                <label class="flex flex-1 cursor-pointer items-center gap-3 rounded-xl border border-ink-200 px-4 py-3 transition has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50">
                    <input type="radio" name="gender" value="female" id="female" @checked($user->gender === 'female') class="text-brand-600 focus:ring-brand-500">
                    <span class="text-sm font-medium text-ink-700">@lang('default/profile.gender_female')</span>
                </label>
            </div>
        </div>

        <div class="border-t border-ink-100 pt-8">
            <button type="submit" class="btn-primary">@lang('default/profile.updated_profile')</button>
        </div>
    </form>
</section>

@endsection
