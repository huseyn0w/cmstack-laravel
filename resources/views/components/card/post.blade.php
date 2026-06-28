@props([
    'title',
    'url',
    'excerpt'  => null,
    'category' => null,
    'date'     => null,
    'author'   => null,
    'image'    => null,
])

<article {{ $attributes->merge(['class' => 'group flex flex-col gap-4']) }}>
    @if($image)
        <div class="aspect-video overflow-hidden rounded-md">
            <img
                src="{{ $image }}"
                alt="{{ $title }}"
                loading="lazy"
                decoding="async"
                width="800"
                height="450"
                class="rounded-md w-full h-full object-cover transition group-hover:scale-[1.02] motion-reduce:transition-none"
            />
        </div>
    @endif

    <div class="flex flex-col gap-2">
        @if($category)
            <x-eyebrow>{{ $category }}</x-eyebrow>
        @endif

        <h3 class="font-serif text-xl leading-snug">
            <a href="{{ $url }}" class="hover:text-primary transition-colors">{{ $title }}</a>
        </h3>

        @if($excerpt)
            <p class="text-muted text-sm line-clamp-3">{{ $excerpt }}</p>
        @endif

        @if($date || $author)
            <div class="flex items-center gap-2 font-mono text-xs text-subtle">
                @if($date)
                    <time datetime="{{ $date }}">{{ $date }}</time>
                @endif
                @if($date && $author)
                    <span aria-hidden="true">·</span>
                @endif
                @if($author)
                    <span>{{ $author }}</span>
                @endif
            </div>
        @endif
    </div>
</article>
