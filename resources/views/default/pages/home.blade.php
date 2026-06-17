<?php
/**
 * LaraPress CMS
 * File: home.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 21.07.2019
 * Template Name: "Home Page";
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */
?>

@extends(config('app.template_name').'/index')

@section('content')

    @php
        $headline = get_field('headline', $custom_fields);
        $headline_background = get_field('headline-image', $custom_fields);

        $posts_from_category_headline = get_field('posts-from-category-headline', $custom_fields);
        $posts_from_category_description = get_field('posts-from-category-description', $custom_fields);
        $posts_from_category_category_id = get_field('posts-from-category-cat-id', $custom_fields);

        $about_headline = get_field('about-headline', $custom_fields);
        $about_description = get_field('about-description', $custom_fields);
        $about_big_description = get_field('about-big-text', $custom_fields);
        $authors = get_field('authors', $custom_fields);
    @endphp

<!-- start banner Area -->
<section class="relative overflow-hidden" id="home">
    @if($headline_background)
        <div class="absolute inset-0 -z-10 bg-ink-900">
            <img src="{{$headline_background}}" {!! image_fallback() !!} alt="" class="h-full w-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-b from-ink-950/55 via-ink-950/35 to-ink-950/80"></div>
        </div>
    @else
        <div class="absolute inset-0 -z-10 bg-ink-900"></div>
    @endif

    <div class="mx-auto flex min-h-[72vh] max-w-7xl items-center px-5 py-28 sm:px-8 sm:py-32">
        <div class="max-w-3xl">
            <p class="mb-5 inline-flex items-center gap-2 text-sm font-medium tracking-wide text-paper/70">
                <span class="h-px w-8 bg-brand-400"></span>
                {{ $home_page_data->title ?? 'LaraPress' }}
            </p>
            <div class="font-serif text-4xl font-medium leading-[1.06] tracking-tight text-paper sm:text-5xl lg:text-6xl [text-wrap:balance]">
                {!! $headline !!}
            </div>
        </div>
    </div>
</section>
<!-- End banner Area -->


@php
    $fields = ['post_translations.title', 'post_translations.slug', 'post_translations.preview','post_translations.updated_at','post_translations.thumbnail','post_translations.likes'];
    $args = ['fields' => $fields, 'category_id' => $posts_from_category_category_id, 'count' => 4];
    $category_posts = get_category_posts($args);
@endphp
@if(!empty($category_posts))
<!-- Start latest posts Area -->
<section class="mx-auto max-w-7xl px-5 py-20 sm:px-8 sm:py-24" id="travel">
    <div class="mb-12 max-w-2xl">
        <h2 class="text-3xl font-medium tracking-tight text-ink-900 sm:text-4xl">{{$posts_from_category_headline}}</h2>
        @if($posts_from_category_description)
            <p class="mt-4 text-lg leading-relaxed text-ink-500">{{$posts_from_category_description}}</p>
        @endif
    </div>

    <div class="grid gap-x-8 gap-y-12 sm:grid-cols-2">
        @foreach($category_posts as $post)
            @php
                $post_thumbnail = image_src($post->thumbnail);
            @endphp
            <article x-data="reveal({{ $loop->index * 70 }})" class="reveal-init group">
                <a href="{{env('APP_URL')}}/posts/{{$post->slug}}" class="block overflow-hidden rounded-2xl bg-ink-100 shadow-card">
                    <img src="{{$post_thumbnail}}" {!! image_fallback() !!} alt="{{$post->title}}" width="640" height="400" loading="lazy"
                         class="aspect-[16/10] w-full object-cover transition duration-700 ease-out-expo group-hover:scale-[1.03]">
                </a>
                <div class="mt-5 flex items-start gap-5">
                    <div class="shrink-0 text-center">
                        <div class="font-serif text-3xl font-medium leading-none text-ink-900">{{Carbon\Carbon::parse($post->updated_at)->format('d')}}</div>
                        <div class="mt-1 text-xs font-medium uppercase tracking-wider text-ink-400">{{Carbon\Carbon::parse($post->updated_at)->format('M')}}</div>
                    </div>
                    <div class="min-w-0 border-l border-ink-100 pl-5">
                        <h3 class="font-serif text-xl font-medium leading-snug text-ink-900 transition-colors group-hover:text-brand-700">
                            <a href="{{env('APP_URL')}}/posts/{{$post->slug}}">{{$post->title}}</a>
                        </h3>
                        <div class="mt-2 line-clamp-3 text-base text-ink-500">{!! $post->preview !!}</div>
                        <p class="mt-3 inline-flex items-center gap-1.5 text-sm text-ink-400">
                            <svg class="h-4 w-4 text-brand-500" viewBox="0 0 20 20" fill="currentColor"><path d="M10 17.5 3.5 11a4 4 0 1 1 6.5-4.6A4 4 0 1 1 16.5 11z"/></svg>
                            {{$post->likes}} {{trans('default/category.likes')}}
                        </p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
<!-- End latest posts Area -->
@endif

<!-- Start about / team Area -->
<section class="border-t border-ink-100 bg-ink-50/60" id="team">
    <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 sm:py-24">
        <div class="mb-14 max-w-2xl">
            <h2 class="text-3xl font-medium tracking-tight text-ink-900 sm:text-4xl">{{$about_headline}}</h2>
            @if($about_description)
                <p class="mt-4 text-lg leading-relaxed text-ink-500">{{$about_description}}</p>
            @endif
        </div>

        <div class="grid gap-12 lg:grid-cols-[1.1fr_1fr] lg:items-start lg:gap-16">
            <div class="article-prose max-w-prose">
                {!! $about_big_description !!}
            </div>

            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 lg:grid-cols-2">
                @foreach($authors as $author)
                    @php
                        $author_name = get_field('author-name', $author);
                        $author_image = get_field('author-image', $author);
                        $author_position = get_field('author-position', $author);
                        $author_linkedin = get_field('author-linkedin', $author);
                    @endphp
                    <figure class="group text-center">
                        <div class="relative mx-auto aspect-square w-full max-w-[180px] overflow-hidden rounded-2xl bg-ink-100 shadow-card">
                            <img src="{{ image_src($author_image, true) }}" {!! image_fallback(true) !!} alt="{{$author_name}}"
                                 class="h-full w-full object-cover transition duration-700 ease-out-expo group-hover:scale-105">
                            @if(!empty($author_linkedin['url']))
                                <a href="{{$author_linkedin['url']}}" target="{{$author_linkedin['target'] === "1" ? '_blank' : '_self'}}" rel="noopener"
                                   class="absolute bottom-3 right-3 inline-flex h-9 w-9 translate-y-1 items-center justify-center rounded-full bg-paper/90 text-ink-700 opacity-0 shadow-card backdrop-blur transition duration-300 ease-out-expo hover:text-brand-700 group-hover:translate-y-0 group-hover:opacity-100"
                                   aria-label="{{$author_name}} on LinkedIn">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM3 9h4v12H3zM10 9h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05C21.4 8.65 22 11 22 14.1V21h-4v-6.1c0-1.45-.03-3.3-2-3.3s-2.3 1.57-2.3 3.2V21h-4z"/></svg>
                                </a>
                            @endif
                        </div>
                        <figcaption class="mt-4">
                            <div class="font-serif text-lg font-medium text-ink-900">{{$author_name}}</div>
                            <div class="mt-0.5 text-sm text-ink-500">{{$author_position}}</div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </div>
</section>
<!-- End about / team Area -->

@endsection
