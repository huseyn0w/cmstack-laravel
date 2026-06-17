<?php
/**
 * Laravella CMS
 * File: footer.blade.php
 * Created by Elman (https://linkedin.com/in/huseyn0w)
 * Date: 19.07.2019
 * Phase 4: rewritten from Bootstrap 4 to Tailwind CSS (editorial theme).
 */

$site_options = get_site_options();

$copyright = $site_options->copyright;
$linkedin_url = $site_options->linkedin_url;
$github_url = $site_options->github_url;

$languages = get_translation_links();

?>
</main>
<!-- start footer Area -->
<footer class="mt-24 border-t border-ink-100 bg-paper">
    <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
        <div class="flex flex-col gap-10 sm:flex-row sm:items-start sm:justify-between">
            <div class="max-w-md">
                <a href="{{env('APP_URL')}}" class="font-serif text-2xl font-semibold tracking-tightest text-ink-900">Laravella</a>
                <div class="mt-3 text-sm leading-relaxed text-ink-500">{!! $copyright !!}</div>
                <p class="mt-2 text-xs text-ink-400">
                    Developed by
                    <a href="https://elman.group" target="_blank" rel="noopener"
                       class="font-medium text-ink-600 underline-offset-2 transition hover:text-ink-900 hover:underline">Elman Group</a>
                </p>
            </div>

            <div class="flex items-center gap-3">
                @if($linkedin_url)
                <a href="{{$linkedin_url}}" target="_blank" rel="noopener" aria-label="LinkedIn"
                   class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ink-200 text-ink-600 transition hover:border-ink-300 hover:bg-ink-50 hover:text-ink-900 active:scale-95">
                    <svg class="h-4.5 w-4.5" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M4.98 3.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM3 9h4v12H3zM10 9h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05C21.4 8.65 22 11 22 14.1V21h-4v-6.1c0-1.45-.03-3.3-2-3.3s-2.3 1.57-2.3 3.2V21h-4z"/></svg>
                </a>
                @endif
                @if($github_url)
                <a href="{{$github_url}}" target="_blank" rel="noopener" aria-label="GitHub"
                   class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-ink-200 text-ink-600 transition hover:border-ink-300 hover:bg-ink-50 hover:text-ink-900 active:scale-95">
                    <svg class="h-4.5 w-4.5" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.58 2 12.25c0 4.53 2.87 8.37 6.84 9.73.5.1.68-.22.68-.49v-1.7c-2.78.62-3.37-1.21-3.37-1.21-.45-1.18-1.11-1.5-1.11-1.5-.91-.64.07-.62.07-.62 1 .07 1.53 1.06 1.53 1.06.9 1.57 2.36 1.12 2.94.86.09-.67.35-1.12.63-1.38-2.22-.26-4.56-1.14-4.56-5.06 0-1.12.39-2.03 1.03-2.75-.1-.26-.45-1.3.1-2.7 0 0 .84-.28 2.75 1.05a9.4 9.4 0 0 1 5 0c1.91-1.33 2.75-1.05 2.75-1.05.55 1.4.2 2.44.1 2.7.64.72 1.03 1.63 1.03 2.75 0 3.93-2.35 4.79-4.58 5.05.36.32.68.94.68 1.9v2.82c0 .27.18.59.69.49A10.26 10.26 0 0 0 22 12.25C22 6.58 17.52 2 12 2z"/></svg>
                </a>
                @endif
            </div>
        </div>

        {{-- Language switcher --}}
        @if(!empty($languages))
        <div class="mt-12 flex flex-col gap-3 border-t border-ink-100 pt-6 sm:flex-row sm:items-center">
            <span class="text-xs font-medium uppercase tracking-wider text-ink-400">Language</span>
            <ul class="flex flex-wrap items-center gap-2">
                @foreach($languages as $code => $language)
                    <li>
                        @if($code === get_current_lang())
                            <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3.5 py-1.5 text-sm font-medium text-brand-800">
                                <img src="{{$language['icon']}}" alt="" class="h-4 w-4 rounded-sm">
                                {{$language['title']}}
                            </span>
                        @else
                            <a href="{{$language['url']}}"
                               class="inline-flex items-center gap-2 rounded-full border border-ink-200 px-3.5 py-1.5 text-sm text-ink-600 transition hover:border-ink-300 hover:bg-ink-50 hover:text-ink-900 active:scale-95">
                                <img src="{{$language['icon']}}" alt="{{$language['title']}}" class="h-4 w-4 rounded-sm">
                                {{$language['title']}}
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</footer>
<!-- End footer Area -->

@stack('extrascripts')
</body>
</html>
