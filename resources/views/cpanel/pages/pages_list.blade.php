<?php
/**
 * Cmstack-Laravel
 * File: pages_list.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 16.08.2019
 */
?>

@extends('cpanel.core.index')
@push('extrastyles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@php
$route_name = Route::current()->getName();
$is_trash = $route_name == "cpanel_trashed_pages_list";
@endphp

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/pages.list_headline')</h1>
            <a href="{{route('cpanel_add_new_page')}}" class="btn btn-info">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 4a1 1 0 0 1 1 1v4h4a1 1 0 1 1 0 2h-4v4a1 1 0 1 1-2 0v-4H5a1 1 0 1 1 0-2h4V5a1 1 0 0 1 1-1Z"/></svg>
                @lang('cpanel/pages.add_new_page')
            </a>
        </div>

        @include('cpanel.core.flash')
        @if (Session::get('page_added'))
            <div class="alert alert-success"><strong>@lang('cpanel/pages.page_added')</strong></div>
        @endif
        @if (($update_message = Session::get('deleted')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/pages.bulky_deleted_message') : __('cpanel/pages.bulky_error_message') }}</strong></div>
        @endif
        @if (($update_message = Session::get('restored')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/pages.bulky_restored_message') : __('cpanel/pages.bulky_error_message') }}</strong></div>
        @endif
        @if (($update_message = Session::get('destroyed')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/pages.bulky_destroyed_message') : __('cpanel/pages.bulky_error_message') }}</strong></div>
        @endif

        {{-- Tab switch: published vs trashed --}}
        <div class="mb-4 inline-flex rounded-lg border border-ink-200 bg-surface p-1 shadow-sm">
            <a href="{{route('cpanel_pages_list')}}" class="rounded-md px-3.5 py-1.5 text-sm font-medium transition {{ !$is_trash ? 'bg-brand-600 text-white shadow-sm' : 'text-ink-600 hover:text-ink-900' }}">@lang('cpanel/pages.general_pages')</a>
            <a href="{{route('cpanel_trashed_pages_list')}}" class="rounded-md px-3.5 py-1.5 text-sm font-medium transition {{ $is_trash ? 'bg-brand-600 text-white shadow-sm' : 'text-ink-600 hover:text-ink-900' }}">@lang('cpanel/pages.trashed_pages')</a>
        </div>

        <div class="card overflow-hidden">
            <form method="POST" action="{{ $is_trash ? route('cpanel_pages_bulk_action') : route('cpanel_pages_bulk_delete') }}">
                @csrf
                @if($is_trash) @method('POST') @else @method('DELETE') @endif
                <div class="border-b border-ink-100 px-5 py-4">
                    <div class="select-cover mb-0">
                        <select id="inputState" name="pages_action" required class="form-control">
                            <option selected="selected">@lang('cpanel/pages.bulk_action_label')</option>
                            @if($is_trash)
                                <option value="destroy">@lang('cpanel/pages.bulk_action_destroy_label')</option>
                                <option value="restore">@lang('cpanel/pages.bulk_action_restore_label')</option>
                            @else
                                <option value="delete">@lang('cpanel/pages.bulk_action_delete_label')</option>
                            @endif
                        </select>
                        <button type="submit" class="btn btn-ghost">@lang('cpanel/pages.bulk_action_apply')</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table users-table">
                        <thead>
                            <tr>
                                <th class="w-10"><input class="form-check-input" id="selectAll" name="allusers" type="checkbox" aria-label="Select all"></th>
                                <th class="w-12">№</th>
                                <th>@lang('cpanel/pages.table_name')</th>
                                <th>@lang('cpanel/pages.table_author')</th>
                                <th>@lang('cpanel/pages.table_publish_date')</th>
                                <th>@lang('cpanel/pages.table_status')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($pages_count = 0)
                        @forelse($pages_list as $page)
                            @php($pages_count++)
                            <tr>
                                <td><input class="form-check-input pages-checkbox-input" id="page_{{$page->id}}" name="pages[]" type="checkbox" value="{{$page->id}}" aria-label="Select page"></td>
                                <td class="text-ink-400">{{$pages_count}}</td>
                                <td>
                                    <span class="font-medium text-ink-900">{{$page->title}}</span>
                                    <span class="user_actions">
                                        @if (Auth::user()->can('manage_pages', 'App\Http\Models\UserRoles'))
                                            <a href="{{route('cpanel_edit_page', ['id' => $page->id, 'lang' => get_current_lang()])}}" target="_blank">@lang('cpanel/pages.edit_page')</a>
                                            <input type="hidden" class="deleted_page_id" value="{{$page->id}}" name="deleted_page_id">
                                            @if(!$is_trash)
                                                <button type="button" class="delete_page">@lang('cpanel/pages.delete_page')</button>
                                            @else
                                                <button type="button" class="destroy_page">@lang('cpanel/pages.destroy_page')</button>
                                                <a href="{{route('cpanel_restore_page', $page->id)}}" class="restore_page">@lang('cpanel/pages.restore_page')</a>
                                            @endif
                                        @endif
                                    </span>
                                </td>
                                <td>{{$page->author->username}}</td>
                                <td class="whitespace-nowrap text-ink-600">{{ Carbon\Carbon::parse($page->created_at)->format('d.m.Y')}}</td>
                                <td>
                                    @if($page->status == 1)
                                        <span class="badge badge-success">@lang('cpanel/pages.page_published')</span>
                                    @else
                                        <span class="badge badge-muted">@lang('cpanel/pages.page_pending')</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-10 text-center text-ink-400">@lang('cpanel/pages.not_found')</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="border-t border-ink-100 px-5 py-4">
                {{ $pages_list->links() }}
            </div>
        </div>
    </div>
@endsection

@push('finalscripts')
    <script>
        var delete_confirmation = '@lang('cpanel/pages.js_delete_confirmation')',
            delete_success = '@lang('cpanel/pages.js_delete_success')',
            destroy_confirmation = '@lang('cpanel/pages.js_destroy_confirmation')',
            destroy_success = '@lang('cpanel/pages.js_destroy_success')',
            error_message = '@lang('cpanel/pages.js_delete_error')';
    </script>
    <script src="{{asset('admin')}}/js/page.js"></script>
@endpush
