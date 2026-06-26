@props(['href' => null, 'destructive' => false])

@php
$colorClasses = $destructive ? 'text-error' : 'text-fg';
$baseClasses = 'flex w-full items-center gap-2 px-4 py-2 text-sm font-sans text-left hover:bg-surface-2 focus:bg-surface-2 focus:outline-none transition-colors duration-[var(--dur-fast)] ' . $colorClasses;
$tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    role="menuitem"
    tabindex="-1"
    @if($href) href="{{ $href }}" @endif
    @if(!$href) type="button" @endif
    {{ $attributes->merge(['class' => $baseClasses]) }}
>
    {{ $slot }}
</{{ $tag }}>
