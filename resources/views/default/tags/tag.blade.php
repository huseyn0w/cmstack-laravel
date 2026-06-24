<?php
/**
 * Cmstack-Laravel
 * File: tag.blade.php
 * Public tag archive (parity §2) — mirrors the category archive, keyed on the
 * translatable tag name.
 */
?>

@php
    $tag_posts = $data->posts;
    $current_lang = get_current_lang_prefix();
@endphp

@extends(config('app.template_name').'/index')

@section('content')

@include(config('app.template_name').'.partials.banner', [
    'title'  => $data->name,
    'crumbs' => [
        ['label' => $home_page_data->title, 'url' => env('APP_URL')],
        ['label' => $data->name, 'url' => null],
    ],
])

<section class="mx-auto max-w-4xl px-5 py-16 sm:px-8 sm:py-20">
    @if(!empty($tag_posts) && count($tag_posts) > 0)
        <div class="divide-y divide-ink-100">
            @foreach($tag_posts as $post)
                @php
                    $comments_count = get_post_comments_count($post->id);
                    $post_thumbnail = image_src($post->thumbnail);
                    $post_url = env('APP_URL').'/'.$current_lang.'posts/'.$post->slug;
                @endphp
                <article x-data="reveal({{ $loop->index * 60 }})" class="reveal-init group flex flex-col gap-6 py-8 sm:flex-row">
                    <a href="{{$post_url}}" class="relative block shrink-0 overflow-hidden rounded-2xl bg-ink-100 shadow-card sm:w-56">
                        <img src="{{$post_thumbnail}}" {!! image_fallback() !!} alt="{{$post->title}}" width="448" height="280" loading="lazy"
                             class="aspect-[16/10] h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-[1.04]">
                        <div class="absolute left-3 top-3 rounded-lg bg-paper/90 px-2.5 py-1.5 text-center shadow-card backdrop-blur">
                            <div class="font-serif text-lg font-medium leading-none text-ink-900">{{Carbon\Carbon::parse($post->updated_at)->format('d')}}</div>
                            <div class="text-[10px] font-medium uppercase tracking-wider text-ink-500">{{Carbon\Carbon::parse($post->updated_at)->format('M')}}</div>
                        </div>
                    </a>
                    <div class="min-w-0 flex-1">
                        <h2 class="font-serif text-2xl font-medium leading-snug text-ink-900 transition-colors group-hover:text-brand-700">
                            <a href="{{$post_url}}">{{$post->title}}</a>
                        </h2>
                        <div class="mt-2 line-clamp-3 text-base text-ink-500">{!! $post->preview !!}</div>
                        <div class="mt-4 flex items-center gap-5 text-sm text-ink-400">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-brand-500" viewBox="0 0 20 20" fill="currentColor"><path d="M10 17.5 3.5 11a4 4 0 1 1 6.5-4.6A4 4 0 1 1 16.5 11z"/></svg>
                                {{$post->likes}} {{trans('default/category.likes')}}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 5h12v8H8l-4 3z" stroke-linejoin="round"/></svg>
                                {{$comments_count}} @lang('default/category.comments')
                            </span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-12">
            @php
                $links = $tag_posts->links();
                echo pretty_url($links);
            @endphp
        </div>
    @else
        <div class="py-20 text-center">
            <h2 class="text-2xl font-medium text-ink-500">@lang('default/category.not_found')</h2>
        </div>
    @endif
</section>

@endsection
