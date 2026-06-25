<?php
/**
 * Cmstack-Laravel
 * File: post.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 30.10.2019
 * Template Name: "Standart";
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 *           Like/comment interactivity reimplemented with Alpine.js + fetch.
 */
?>

@php
    $category_title = $data->categories[0]->title;
    $category_slug = $data->categories[0]->slug;

    $author = $data->author->name .' '.$data->author->surname;

    $post_liked = check_if_post_liked_by_current_user($data->id);

    $post_comments_count = count($data->comments);

    if(is_logged_in()) $user_id = \Auth()->user()->id;

    $current_lang = get_current_lang_prefix();
@endphp

@extends(config('app.template_name').'/index')

@section('content')

@include(config('app.template_name').'.partials.banner', [
    'title'  => $data->title,
    'crumbs' => [
        ['label' => $home_page_data->title, 'url' => env('APP_URL')],
        ['label' => $category_title, 'url' => env('APP_URL').'/'.$current_lang.'category/'.$category_slug],
        ['label' => $data->title, 'url' => null],
    ],
])

<article class="mx-auto max-w-3xl px-5 py-14 sm:px-8 sm:py-16">

    {{-- Byline --}}
    <div class="flex items-center gap-3 border-b border-ink-100 pb-8">
        <img src="{{ image_src($data->author->avatar, true) }}" {!! image_fallback(true) !!} alt="{{$author}}" width="44" height="44" loading="lazy" class="h-11 w-11 rounded-full object-cover ring-1 ring-ink-100">
        <div>
            <a href="{{route('show_user',['username' => $data->author->username])}}" class="font-serif text-base font-medium text-ink-900 hover:text-brand-700">{{$author}}</a>
            <div class="text-sm text-ink-400">{{Carbon\Carbon::parse($data->updated_at)->format('d.m.Y')}}</div>
        </div>
    </div>

    @if(!empty($data->thumbnail))
        <figure class="mt-10 overflow-hidden rounded-2xl bg-ink-100 shadow-card">
            <img src="{{$data->thumbnail}}" {!! image_fallback() !!} alt="{{$data->title}}" width="1280" height="720" loading="eager" class="aspect-[16/9] w-full object-cover">
        </figure>
    @endif

    {{-- Post body (passed through the `the_content` plugin filter) --}}
    <div class="article-prose mt-12">
        {!! app('hooks')->filter('the_content', $data->content) !!}
    </div>

    {{-- Tags --}}
    @if(!empty($data->tags) && count($data->tags) > 0)
        <div class="mt-10 flex flex-wrap items-center gap-2">
            <span class="mr-1 text-xs font-medium uppercase tracking-wider text-ink-400">@lang('default/post.tags')</span>
            @foreach($data->tags as $tag)
                <a href="{{ env('APP_URL').'/'.$current_lang.'tag/'.$tag->slug }}"
                   class="inline-flex items-center rounded-full border border-ink-200 px-3 py-1 text-sm text-ink-600 transition hover:border-brand-300 hover:text-brand-700">{{ $tag->name }}</a>
            @endforeach
        </div>
    @endif

    {{-- Like bar --}}
    <div class="mt-14 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-ink-100 bg-ink-50/60 px-6 py-5">
        @if(is_logged_in())
            <div
                x-data="postLike({
                    url: '{{ route('handle_post_likes', ['id' => $data->id]) }}',
                    postId: {{ $data->id }},
                    userId: {{ \Auth::user()->id }},
                    liked: {{ $post_liked ? 'true' : 'false' }},
                    likes: {{ (int) $data->likes }},
                    lang: {
                        like: @json(__('default/post.like')),
                        dislike: @json(__('default/post.dislike')),
                        likeAdded: @json(__('default/post.like_added')),
                        likeDeleted: @json(__('default/post.like_deleted')),
                        youOnly: @json(__('default/post.you_only_liked')),
                        youAndPre: @json(__('default/post.you_and_multiple_like_pre')),
                        youAndAfter: @json(__('default/post.you_and_multiple_like_after')),
                        othersAfter: @json(__('default/post.multiple_like_after')),
                        nobody: @json(__('default/post.nobody_likes'))
                    }
                })"
                class="flex flex-wrap items-center gap-4"
            >
                <button
                    type="button"
                    @click="toggle()"
                    :disabled="busy"
                    :class="liked ? 'border-brand-600 bg-brand-600 text-white' : 'border-ink-200 bg-surface text-ink-700 hover:border-brand-300 hover:text-brand-700'"
                    class="inline-flex items-center gap-2 rounded-full border px-5 py-2.5 text-sm font-semibold transition duration-200 ease-out-expo active:scale-[0.97] disabled:opacity-60"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 17.5 3.5 11a4 4 0 1 1 6.5-4.6A4 4 0 1 1 16.5 11z"/></svg>
                    <span x-text="label">@lang($post_liked ? 'default/post.dislike' : 'default/post.like')</span>
                </button>
                <p class="text-sm text-ink-500" x-text="summary"></p>
            </div>
        @else
            <div class="flex items-center gap-2.5 text-sm text-ink-500">
                <svg class="h-5 w-5 text-brand-500" viewBox="0 0 20 20" fill="currentColor"><path d="M10 17.5 3.5 11a4 4 0 1 1 6.5-4.6A4 4 0 1 1 16.5 11z"/></svg>
                @if($data->likes > 0)
                    <span><span class="font-semibold text-ink-700">{{$data->likes}}</span> @lang('default/post.multiple_like_after')</span>
                @else
                    <span>@lang('default/post.nobody_likes')</span>
                @endif
            </div>
        @endif
    </div>

    {{-- Comments --}}
    <section class="mt-16" x-data="commentThread({
        deleteUrl: '{{ route('delete_post_comments', ['id' => $data->id]) }}',
        confirmText: @json(__('default/post.delete') . '?')
    })">
        <h2 class="text-2xl font-medium text-ink-900">{{$post_comments_count}} @lang('default/post.comments')</h2>

        @if($post_comments_count > 0)
            <div class="mt-8 space-y-8">
                @foreach($data->comments as $comment)
                    @include(config('app.template_name').'.partials.comment', ['comment' => $comment, 'user_id' => $user_id ?? null, 'is_reply' => false])

                    @if(count($comment->replies) > 0)
                        <div data-comment-replies class="ml-6 space-y-8 border-l border-ink-100 pl-6 sm:ml-10 sm:pl-8">
                            @foreach($comment->replies as $child_comment)
                                @include(config('app.template_name').'.partials.comment', ['comment' => $child_comment, 'user_id' => $user_id ?? null, 'is_reply' => true])
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-10">
                {!! pretty_url($data->comments->links()) !!}
            </div>
        @else
            <p class="mt-6 text-ink-500">@lang('default/post.no_comments')</p>
        @endif
    </section>

    {{-- Comment form --}}
    <section class="mt-16 scroll-mt-24" id="comment-area">
        @auth
            <h2 class="text-2xl font-medium text-ink-900">@lang('default/post.leave_reply')</h2>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3.5 text-sm text-brand-800" role="alert">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if ($update_message = Session::get('comment_added'))
                <div class="mt-6 flex items-center gap-2.5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3.5 text-sm font-medium text-emerald-800" role="status">
                    <svg class="h-5 w-5 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10" cy="10" r="8"/><path d="m6.5 10 2.5 2.5 4.5-5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    @if (Auth::user()->can('manage_comments', 'App\Http\Models\UserRoles'))
                        @lang('default/post.comment_added')
                    @else
                        @lang('default/post.comment_send_to_approve')
                    @endif
                </div>
            @endif

            <form action="{{route('store_post_comments', ['id' => $data->id])}}" method="POST" class="mt-6">
                @csrf
                <textarea id="comment-field" name="comment" rows="5" required
                          placeholder="@lang('default/post.comment')"
                          class="field-input resize-y"></textarea>
                <input type="hidden" name="parent_id" id="comment_parent_id" value="">
                <input type="hidden" name="post_id" value="{{$data->id}}">
                <button type="submit" class="btn-primary mt-4">@lang('default/post.comment')</button>
            </form>
        @else
            <div class="rounded-2xl border border-ink-100 bg-ink-50/60 px-6 py-8 text-center">
                <p class="text-ink-600">@lang('default/post.comment_auth')</p>
                <a href="{{route('login')}}" class="btn-primary mt-4">@lang('default/header.login')</a>
            </div>
        @endauth
    </section>
</article>

@auth
    @include('default.posts.modal')
@endauth

@endsection

{{-- Phase 7: removed the legacy AddThis third-party share widget (render-blocking,
     tracker-heavy). The public theme now loads only the Vite bundle. --}}
