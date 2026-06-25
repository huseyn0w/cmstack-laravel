<?php
/**
 * Cmstack-Laravel
 * File: header.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 19.07.2019
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */

// Phase 7: page-level SEO meta is now produced by partials/seo-meta.blade.php
// (title, description, canonical, robots, Open Graph, Twitter, hreflang,
// JSON-LD). The legacy ad-hoc title/description block here was removed.
$author = null;
if(isset($data) && isset($data->author)):
    $author = $data->author->name. ' '.$data->author->surname;
endif;

$logo_url = get_site_options('logo_url');

$current_lang = get_current_lang_prefix();


?>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-js">
<head>
    <meta charset="UTF-8">
    <!-- Mobile Specific Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="msapplication-TileColor" content="#b0322b">
    <meta name="theme-color" content="#fbfbf9">

    {{-- Phase 7 SEO/GEO head: title, description, canonical, robots, Open Graph,
         Twitter Card, hreflang alternates, verification tags and JSON-LD. --}}
    @include(config('app.template_name').'.partials.seo-meta')

    @if($author)<meta name="author" content="{{$author}}">@endif

    {{-- CSRF token for AJAX (like / comment). Needed for all visitors, not just auth. --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon-->
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('front/'.config('app.template_name').'/img/apple-touch-icon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('front/'.config('app.template_name').'/img/favicon-32x32.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('front/'.config('app.template_name').'/img/favicon-16x16.png')}}">

    {{-- Editorial type pairing: Newsreader (optical serif display) + Inter Tight (grotesque UI).
         preconnect for fast handshake; display=swap to avoid render-blocking FOIT. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;0,6..72,600;1,6..72,400&display=swap" rel="stylesheet">

    @stack('extrastyles')

    {{-- Tailwind + Alpine (Vite). Preflight is off globally; the public theme's
         scoped reset lives under .theme-default (see resources/css/app.css).
         This is the ONLY bundle the public theme loads — no jQuery, no legacy
         plugin JS/CSS from public/front/**. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Optional analytics (GA4 / GTM) — rendered ONLY when configured in the
         admin SEO settings, loaded async/deferred. Default = no script at all. --}}
    @include(config('app.template_name').'.partials.analytics')
</head>
<body class="theme-default min-h-[100dvh] bg-paper text-ink-800 antialiased">

@php
    $menu_params = [
        'menu_type'                    => "list",
        'menu_class'                   => "nav-menu",
        'item_class'                   => "",
        'link_class'                   => "nav-link",
        "item_class_with_submenu"      => 'dropdown',
        "item_link_class_with_submenu" => 'dropdown-toggle inline-flex items-center gap-1',
        "submenu_class"                => "dropdown-menu",
        "sublink_class"                => "dropdown-item",
    ];
    $header_menu = get_menu_data("header_menu", $menu_params);
@endphp

<!-- Start Header Area -->
<header
    x-data="{ open: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 8"
    :class="scrolled ? 'border-ink-100 bg-paper/85 shadow-card backdrop-blur-md' : 'border-transparent bg-paper/60 backdrop-blur-sm'"
    class="sticky top-0 z-40 border-b transition-colors duration-300"
>
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 py-3.5 sm:px-8">
        <a href="{{env('APP_URL')}}" class="flex shrink-0 items-center gap-2.5" aria-label="@lang('default/header.homepage_title')">
            @if($logo_url)
                <img src="{{$logo_url}}" alt="" class="h-9 w-auto">
            @else
                <span class="font-serif text-2xl font-semibold tracking-tightest text-ink-900">Cmstack-Laravel</span>
            @endif
        </a>

        {{-- Desktop navigation --}}
        <nav class="hidden items-center gap-1 lg:flex" aria-label="@lang('default/header.homepage_title')">
            {!! $header_menu !!}
        </nav>

        {{-- Desktop user / utility panel --}}
        <div class="hidden items-center gap-1 lg:flex">
            <a href="{{route('get_search_page')}}" class="nav-link inline-flex items-center gap-1.5" aria-label="@lang('default/header.search')">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="9" r="6"/><path d="m18 18-4-4" stroke-linecap="round"/></svg>
                <span>@lang('default/header.search')</span>
            </a>
            @auth
                @if (Auth::user()->can('see_admin_panel', 'App\Http\Models\UserRoles'))
                    <a href="{{route('cpanel_home')}}" class="nav-link">@lang('default/header.cpanel')</a>
                @endif
                <a href="{{route('get_user_info')}}" class="nav-link">@lang('default/header.profile')</a>
                <a href="{{route('logout')}}" class="btn-ghost ml-1 px-4 py-2">@lang('default/header.logout')</a>
            @else
                <a href="{{route('login')}}" class="nav-link">@lang('default/header.login')</a>
                @if(get_general_settings('membership'))
                <a href="{{route('register')}}" class="btn-primary ml-1 px-5 py-2.5">@lang('default/header.register')</a>
                @endif
            @endauth
        </div>

        {{-- Mobile toggle --}}
        <button
            type="button"
            @click="open = !open"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ink-200 text-ink-700 transition active:scale-95 lg:hidden"
            :aria-expanded="open"
            aria-label="Toggle navigation"
        >
            <svg x-show="!open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            <svg x-show="open" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
        </button>
    </div>

    {{-- Mobile drawer --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out-expo duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="border-t border-ink-100 bg-paper px-5 pb-6 pt-2 sm:px-8 lg:hidden"
    >
        <nav class="flex flex-col gap-0.5 py-2" aria-label="Mobile">
            {!! $header_menu !!}
        </nav>
        <div class="mt-2 flex flex-col gap-0.5 border-t border-ink-100 pt-3">
            <a href="{{route('get_search_page')}}" class="nav-link">@lang('default/header.search')</a>
            @auth
                @if (Auth::user()->can('see_admin_panel', 'App\Http\Models\UserRoles'))
                    <a href="{{route('cpanel_home')}}" class="nav-link">@lang('default/header.cpanel')</a>
                @endif
                <a href="{{route('get_user_info')}}" class="nav-link">@lang('default/header.profile')</a>
                <a href="{{route('logout')}}" class="nav-link">@lang('default/header.logout')</a>
            @else
                <a href="{{route('login')}}" class="nav-link">@lang('default/header.login')</a>
                @if(get_general_settings('membership'))
                <a href="{{route('register')}}" class="nav-link text-brand-700">@lang('default/header.register')</a>
                @endif
            @endauth
        </div>
    </div>
    @hook('header')
</header>
<!-- End Header Area -->

<main id="main">
