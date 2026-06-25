<?php

namespace App\Plugins\ReadingTime;

use App\Plugins\Contracts\PluginInterface;
use App\Support\Hooks;

/**
 * Sample plugin: prepend an estimated reading time to the post body via the
 * `the_content` filter. Demonstrates the filter mechanism end-to-end.
 */
class ReadingTimePlugin implements PluginInterface
{
    private const WORDS_PER_MINUTE = 200;

    public function slug(): string
    {
        return 'reading-time';
    }

    public function name(): string
    {
        return 'Reading time';
    }

    public function description(): string
    {
        return 'Adds an estimated reading time badge to the top of each post.';
    }

    public function boot(Hooks $hooks): void
    {
        $hooks->onFilter('the_content', function (?string $html): string {
            // Posts may have no body (nullable content); treat null as empty.
            $html = (string) $html;
            $words = str_word_count(strip_tags($html));
            $minutes = max(1, (int) ceil($words / self::WORDS_PER_MINUTE));
            $badge = '<p class="reading-time">'.$minutes.' min read</p>';

            return $badge.$html;
        });
    }
}
