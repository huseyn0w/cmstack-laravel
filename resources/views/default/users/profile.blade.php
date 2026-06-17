<?php
/**
 * LaraPress CMS
 * File: profile.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 14.11.2019
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */
?>
@extends(config('app.template_name').'/index')

@section('content')

@php
    $home_page_data = get_data(1, 'page', ['slug', 'title']);
    $logged_user_name = get_logged_user_username();

    $socials = array_filter([
        'Facebook'  => $user->facebook_url,
        'Google'    => $user->google_url,
        'Twitter'   => $user->twitter_url,
        'Instagram' => $user->instagram_url,
        'LinkedIn'  => $user->linkedin_url,
        'Xing'      => $user->xing_url,
    ]);
@endphp

@include(config('app.template_name').'.partials.banner', [
    'title'  => __('default/profile.profile'),
    'crumbs' => [
        ['label' => $home_page_data->title, 'url' => env('APP_URL')],
        ['label' => __('default/profile.profile_info'), 'url' => null],
    ],
])

<section class="mx-auto max-w-4xl px-5 py-16 sm:px-8 sm:py-20">
    <div class="flex flex-col items-start gap-8 sm:flex-row sm:items-center">
        <img
            src="{{ image_src($user->avatar, true) }}" {!! image_fallback(true) !!}
            alt="{{$user->username}}"
            class="h-28 w-28 shrink-0 rounded-2xl object-cover shadow-card ring-1 ring-ink-100">
        <div class="min-w-0">
            <h2 class="font-serif text-3xl font-medium text-ink-900">
                {{ trim($user->name.' '.$user->surname) ?: $user->username }}
            </h2>
            <p class="mt-1 text-ink-500">@@{{$user->username}}</p>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-brand-50 px-3 py-1 text-sm font-medium text-brand-800">{{$user->role->name}}</span>
                @if($user->gender)
                    <span class="inline-flex items-center rounded-full border border-ink-200 px-3 py-1 text-sm text-ink-600 capitalize">{{$user->gender}}</span>
                @endif
            </div>
        </div>
        @if($user->username === $logged_user_name)
            <a href="{{route('get_user_info')}}" class="btn-ghost sm:ml-auto">@lang('default/profile.edit')</a>
        @endif
    </div>

    @if($user->about_me)
        <p class="mt-10 max-w-prose border-l-2 border-brand-300 pl-5 font-serif text-xl leading-relaxed text-ink-700">{{$user->about_me}}</p>
    @endif

    <dl class="mt-12 grid gap-x-12 gap-y-7 border-t border-ink-100 pt-10 sm:grid-cols-2">
        <div>
            <dt class="text-xs font-medium uppercase tracking-wider text-ink-400">@lang('default/profile.email')</dt>
            <dd class="mt-1 text-base text-ink-800">{{$user->email}}</dd>
        </div>
        @if($user->country)
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-ink-400">@lang('default/profile.country')</dt>
                <dd class="mt-1 text-base text-ink-800">{{$user->country}}</dd>
            </div>
        @endif
        @if($user->city)
            <div>
                <dt class="text-xs font-medium uppercase tracking-wider text-ink-400">@lang('default/profile.city')</dt>
                <dd class="mt-1 text-base text-ink-800">{{$user->city}}</dd>
            </div>
        @endif
    </dl>

    @if(!empty($socials))
        <div class="mt-10 border-t border-ink-100 pt-10">
            <h3 class="text-xs font-medium uppercase tracking-wider text-ink-400">Links</h3>
            <div class="mt-4 flex flex-wrap gap-2.5">
                @foreach($socials as $label => $url)
                    <a href="{{$url}}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 rounded-full border border-ink-200 px-4 py-2 text-sm font-medium text-ink-700 transition hover:border-ink-300 hover:bg-ink-50 active:scale-95">
                        {{$label}}
                        <svg class="h-3.5 w-3.5 text-ink-400" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 13 13 7M8 7h5v5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>

@endsection
