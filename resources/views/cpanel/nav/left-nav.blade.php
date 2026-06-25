<?php
/**
 * Cmstack-Laravel
 * File: left-nav.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 */

$current_route = \Route::currentRouteName();

// Icon set — inline, single stroke weight (1.5), consistent across the rail.
$icons = [
    'dashboard'  => '<path d="M3 13h8V3H3v10Zm0 8h8v-6H3v6Zm10 0h8V11h-8v10Zm0-18v6h8V3h-8Z"/>',
    'profile'    => '<path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5Z"/>',
    'media'      => '<path d="M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm1 11 4-5 3 4 2-2 4 5H5Zm3-6a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>',
    'pages'      => '<path d="M6 2h8l4 4v15a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1Zm7 1.5V7h3.5L13 3.5ZM8 11h8v1.5H8V11Zm0 4h8v1.5H8V15Z"/>',
    'posts'      => '<path d="M5 3h14a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm2 4v2h10V7H7Zm0 4v2h10v-2H7Zm0 4v2h6v-2H7Z"/>',
    'categories' => '<path d="M10 3 3 7l7 4 7-4-7-4Zm0 18-7-4v-3l7 4 7-4v3l-7 4Z"/>',
    'comments'   => '<path d="M4 4h16a1 1 0 0 1 1 1v11a1 1 0 0 1-1 1H9l-5 4V5a1 1 0 0 1 1-1Z"/>',
    'users'      => '<path d="M9 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm7 0a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM2 20c0-3 3.1-5 7-5s7 2 7 5v1H2v-1Zm15-5c2.7.4 5 2.1 5 5v1h-4v-1c0-1.9-.4-3.5-1-5Z"/>',
    'settings'   => '<path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm9 4-2 .9.5 2.1-1.6 1.6-2.1-.5-.9 2H12l-.9-2-2.1.5L7.4 16.6 5.3 16 4.4 13.9 3 13v-2l2-.9-.5-2.1L6.1 6.4l2.1.5L9.1 5 11 4h2l.9 2 2.1-.5 1.6 1.6-.5 2.1 2 .9v2Z"/>',
    'general'    => '<path d="M4 5h16v2H4V5Zm0 6h16v2H4v-2Zm0 6h10v2H4v-2Z"/>',
    'options'    => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm0 5a5 5 0 1 1 0 10 5 5 0 0 1 0-10Z"/>',
    'menus'      => '<path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z"/>',
    'roles'      => '<path d="M12 1 3 5v6c0 5 3.8 9.7 9 11 5.2-1.3 9-6 9-11V5l-9-4Zm0 6a2.5 2.5 0 0 1 2.5 2.5c0 1-.6 1.8-1.5 2.2V15h-2v-3.3c-.9-.4-1.5-1.2-1.5-2.2A2.5 2.5 0 0 1 12 7Z"/>',
];

// Helper: is a route the current one?
$isActive = fn ($route) => $current_route === $route;

// Are any children of a group active (to auto-open the submenu)?
$postsActive    = in_array($current_route, ['cpanel_category_list', 'cpanel_posts_list', 'cpanel_trashed_posts_list', 'cpanel_comments_list']);
$settingsActive = in_array($current_route, ['cpanel_general_settings', 'cpanel_site_options', 'cpanel_seo_settings', 'cpanel_geo_settings', 'cpanel_plugins_list', 'cpanel_menu_list', 'cpanel_user_roles']);
?>
<div class="flex h-full flex-col">
    {{-- Brand --}}
    <div class="flex h-16 shrink-0 items-center gap-2.5 border-b border-ink-800/60 px-5">
        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-bold text-white">L</span>
        <span class="text-sm font-semibold tracking-tight text-white">Cmstack-Laravel</span>
    </div>

    @php
        $linkBase   = 'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors';
        $linkIdle   = 'text-ink-300 hover:bg-ink-800/70 hover:text-white';
        $linkActive = 'bg-brand-600/15 text-white';
        $subBase    = 'flex items-center gap-2.5 rounded-md px-3 py-1.5 text-[0.8125rem] transition-colors';
        $subIdle    = 'text-ink-400 hover:text-white';
        $subActive  = 'text-white';
        $iconCls    = 'h-5 w-5 shrink-0';
    @endphp

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4" aria-label="Main">
        <a href="{{route('cpanel_home')}}" class="{{ $linkBase }} {{ $isActive('cpanel_home') ? $linkActive : $linkIdle }}">
            <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['dashboard'] !!}</svg>
            <span>@lang('cpanel/nav/left.dashboard')</span>
        </a>

        <a href="{{route('cpanel_myprofile')}}" class="{{ $linkBase }} {{ $isActive('cpanel_myprofile') ? $linkActive : $linkIdle }}">
            <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['profile'] !!}</svg>
            <span>@lang('cpanel/nav/left.edit_profile')</span>
        </a>

        <a href="{{route('cpanel_all_media')}}" class="{{ $linkBase }} {{ $isActive('cpanel_all_media') ? $linkActive : $linkIdle }}">
            <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['media'] !!}</svg>
            <span>@lang('cpanel/nav/left.media')</span>
        </a>

        @if (Auth::user()->can('manage_pages', 'App\Http\Models\UserRoles'))
            <a href="{{route('cpanel_pages_list')}}" class="{{ $linkBase }} {{ $isActive('cpanel_pages_list') ? $linkActive : $linkIdle }}">
                <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['pages'] !!}</svg>
                <span>@lang('cpanel/nav/left.pages')</span>
            </a>
        @endif

        {{-- Posts group --}}
        <div>
            <button
                type="button"
                data-toggle="collapse"
                data-target="#nav-posts"
                aria-expanded="{{ $postsActive ? 'true' : 'false' }}"
                class="{{ $linkBase }} w-full {{ $postsActive ? $linkActive : $linkIdle }}"
            >
                <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['posts'] !!}</svg>
                <span class="flex-1 text-left">@lang('cpanel/nav/left.posts')</span>
                <svg class="h-4 w-4 transition-transform {{ $postsActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
            <div id="nav-posts" class="collapse {{ $postsActive ? 'is-open' : '' }}">
                <div>
                    <div class="ml-4 mt-1 space-y-0.5 border-l border-ink-800/60 pl-3">
                        @if (Auth::user()->can('manage_post_categories', 'App\Http\Models\UserRoles'))
                            <a href="{{route('cpanel_category_list')}}" class="{{ $subBase }} {{ $isActive('cpanel_category_list') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.categories')
                            </a>
                        @endif
                        @if (Auth::user()->can('manage_posts', 'App\Http\Models\UserRoles'))
                            <a href="{{route('cpanel_posts_list')}}" class="{{ $subBase }} {{ $isActive('cpanel_posts_list') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.all_posts')
                            </a>
                        @endif
                        @if (Auth::user()->can('manage_comments', 'App\Http\Models\UserRoles'))
                            <a href="{{route('cpanel_comments_list')}}" class="{{ $subBase }} {{ $isActive('cpanel_comments_list') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.comments')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <a href="{{route('cpanel_all_users_list')}}" class="{{ $linkBase }} {{ $isActive('cpanel_all_users_list') ? $linkActive : $linkIdle }}">
            <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['users'] !!}</svg>
            <span>@lang('cpanel/nav/left.users')</span>
        </a>

        {{-- Settings group --}}
        <div>
            <button
                type="button"
                data-toggle="collapse"
                data-target="#nav-settings"
                aria-expanded="{{ $settingsActive ? 'true' : 'false' }}"
                class="{{ $linkBase }} w-full {{ $settingsActive ? $linkActive : $linkIdle }}"
            >
                <svg class="{{ $iconCls }}" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">{!! $icons['settings'] !!}</svg>
                <span class="flex-1 text-left">@lang('cpanel/nav/left.settings')</span>
                <svg class="h-4 w-4 transition-transform {{ $settingsActive ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" clip-rule="evenodd"/></svg>
            </button>
            <div id="nav-settings" class="collapse {{ $settingsActive ? 'is-open' : '' }}">
                <div>
                    <div class="ml-4 mt-1 space-y-0.5 border-l border-ink-800/60 pl-3">
                        @if (Auth::user()->can('manage_general_settings', 'App\Http\Models\UserRoles'))
                            <a href="{{route('cpanel_general_settings')}}" class="{{ $subBase }} {{ $isActive('cpanel_general_settings') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.general_settings')
                            </a>
                            <a href="{{route('cpanel_site_options')}}" class="{{ $subBase }} {{ $isActive('cpanel_site_options') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.site_options')
                            </a>
                            <a href="{{route('cpanel_seo_settings')}}" class="{{ $subBase }} {{ $isActive('cpanel_seo_settings') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.seo_settings')
                            </a>
                            <a href="{{route('cpanel_geo_settings')}}" class="{{ $subBase }} {{ $isActive('cpanel_geo_settings') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.geo_settings')
                            </a>
                            <a href="{{route('cpanel_plugins_list')}}" class="{{ $subBase }} {{ $isActive('cpanel_plugins_list') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.plugins')
                            </a>
                        @endif
                        @if (Auth::user()->can('manage_menus', 'App\Http\Models\UserRoles'))
                            <a href="{{route('cpanel_menu_list')}}" class="{{ $subBase }} {{ $isActive('cpanel_menu_list') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.menus')
                            </a>
                        @endif
                        @if (Auth::user()->can('manage_user_roles', 'App\Http\Models\UserRoles'))
                            <a href="{{route('cpanel_user_roles')}}" class="{{ $subBase }} {{ $isActive('cpanel_user_roles') ? $subActive : $subIdle }}">
                                <span class="h-1 w-1 rounded-full bg-current opacity-50"></span>@lang('cpanel/nav/left.user_roles')
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
