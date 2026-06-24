<?php
/**
 * Cmstack-Laravel
 * File: edit_post.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 01.08.2019
 */
?>

@extends('cpanel.core.index')

@push('extrastyles')
    <link rel="stylesheet" href="{{asset('admin')}}/css/datepicker.min.css">
@endpush

@section('content')
    @php
        $categories_ids = [];
        foreach($entity->categories as $category) $categories_ids[] = $category->id;
        $tags_value = $entity->tags->pluck('name')->implode(', ');
    @endphp

    <div class="mx-auto max-w-6xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-ink-900">@lang('cpanel/posts.edit_headline')</h1>
            <p class="mt-1 text-sm text-ink-500">
                @lang('cpanel/posts.url_preview')
                <a href="{{env('APP_URL')}}/posts/{{ old('slug',$entity->slug) }}" class="font-medium text-brand-700 hover:text-brand-800">{{env('APP_URL')}}/posts/{{ old('slug',$entity->slug) }}</a>
            </p>
        </div>

        @include('cpanel.core.flash')
        @if (($update_message = Session::get('message')) !== null)
            <div class="alert {{ $update_message ? 'alert-success' : 'alert-danger' }}"><strong>{{ $update_message ? __('cpanel/posts.updated_success') : __('cpanel/posts.updated_error') }}</strong></div>
        @endif

        <form action="{{ route('cpanel_update_post', ['id' => $entity->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method("PUT")
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-x-5 md:grid-cols-2">
                                <div class="field">
                                    <label for="cpanel_title" class="field-label">@lang('cpanel/posts.title')</label>
                                    <input type="text" id="cpanel_title" required class="form-control" name="title" value="{{ old('title', $entity->title) }}">
                                </div>
                                <div class="field">
                                    <label for="cpanel_slug" class="field-label">@lang('cpanel/posts.slug')</label>
                                    <input type="text" id="cpanel_slug" required class="form-control" name="slug" value="{{ old('slug',$entity->slug) }}">
                                </div>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.preview')</label>
                                <textarea name="preview" id="editor" class="my-editor form-control">{{old('preview',$entity->preview)}}</textarea>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.content')</label>
                                <textarea name="content" id="editor" class="my-editor form-control">{{old('content',$entity->content)}}</textarea>
                            </div>
                            @include('cpanel.core.seo')
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-5">
                    <div class="card">
                        <div class="card-body">
                            @include('cpanel.core.translation')
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.category')</label>
                                <select name="category[]" multiple class="form-control category_list multiple_list" id="post_category">
                                    @foreach($categories_list as $category)
                                        <option value="{{$category->category_id}}" {{ in_array($category->category_id, $categories_ids) ? 'selected': null}}>{{$category->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.tags')</label>
                                <input type="text" name="tags" class="form-control" id="post_tags" value="{{ old('tags', $tags_value) }}" placeholder="@lang('cpanel/posts.tags_hint')">
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.author')</label>
                                <select name="author_id" id="author_id" class="form-control">
                                    @foreach($users_list as $user)
                                        <option value="{{$user->id}}" {{$user->id === $entity->author_id ? 'selected' : ''}}>{{$user->username}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.publish_date')</label>
                                <input class="form-control" value="{{old('updated_at', $entity->updated_at)}}" autocomplete="off" name="updated_at" required id="date_time_picker" type="text" />
                            </div>
                            <div class="field">
                                <label class="field-label">@lang('cpanel/posts.status')</label>
                                <select name="status" id="user_role" class="form-control">
                                    <option value="0" {{$entity->status === 0 ? 'selected' :null}}>@lang('cpanel/posts.status_private')</option>
                                    <option value="1" {{$entity->status === 1 ? 'selected' :null}}>@lang('cpanel/posts.status_published')</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="custom_input_image" class="field-label">@lang('cpanel/posts.thumbnail')</label>
                                <span class="input-group-btn">
                                    <a id="lfm" data-input="thumbnail" data-preview="holder" class="choose-image">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="8.5" cy="10" r="1.5"/><path d="m21 16-5-5L5 19"/></svg>
                                        @lang('cpanel/posts.thumbnail_label')
                                    </a>
                                </span>
                                <input id="thumbnail" class="form-control mt-2" type="hidden" name="thumbnail" value="{{ old('thumbnail', $entity->thumbnail) }}">
                                <div class="post-thumbnail relative mt-3 inline-block" {{ empty($entity->thumbnail) ? "style=display:none;" : null}}>
                                    <button type="button" class="remove_thumbnail">X</button>
                                    <img src="{{ old('logo_url', $entity->thumbnail) }}" id="post-thumbnail" class="max-h-40 rounded-lg border border-ink-200" alt="Post Thumbnail">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end border-t border-ink-100 px-5 py-4">
                            <button type="submit" class="btn btn-info">@lang('cpanel/posts.update_button_label')</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('extrascripts')
    <script src="https://cdn.tiny.cloud/1/4vyoa49f4irghhao6v5lpc7z5z2hvhgau8wsjj1y9g65ovse/tinymce/4/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{asset('admin')}}/js/datepicker.min.js"></script>
    <script src="{{asset('admin')}}/js/i18n/datepicker.en.js"></script>
    <script src="{{asset('')}}/vendor/laravel-filemanager/js/lfm.js"></script>
@endpush

@push('finalscripts')
    <script src="{{asset('admin')}}/js/post.js"></script>
    <script>
        var site_url = "<?php echo env('APP_URL'); ?>/";
    </script>
    <script src="{{asset('admin')}}/js/thumbnail.js"></script>
@endpush
