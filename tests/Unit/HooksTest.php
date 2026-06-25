<?php

namespace Tests\Unit;

use App\Support\Hooks;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class HooksTest extends TestCase
{
    private function hooks(): Hooks
    {
        return new Hooks(Event::getFacadeRoot());
    }

    public function test_action_invokes_registered_listeners(): void
    {
        $hooks = $this->hooks();
        $seen = [];
        $hooks->onAction('post.viewed', function ($id) use (&$seen) {
            $seen[] = $id;
        });

        $hooks->action('post.viewed', 42);

        $this->assertSame([42], $seen);
    }

    public function test_filter_returns_mutated_value_in_order(): void
    {
        $hooks = $this->hooks();
        $hooks->onFilter('the_content', fn ($html) => $html.'-a');
        $hooks->onFilter('the_content', fn ($html) => $html.'-b');

        $this->assertSame('x-a-b', $hooks->filter('the_content', 'x'));
    }

    public function test_filter_without_listeners_returns_value_unchanged(): void
    {
        $this->assertSame('y', $this->hooks()->filter('untouched', 'y'));
    }

    public function test_filter_passes_extra_args_to_listeners(): void
    {
        $hooks = $this->hooks();
        $hooks->onFilter('greet', fn ($value, $name) => $value.$name);

        $this->assertSame('hi-bob', $hooks->filter('greet', 'hi-', 'bob'));
    }

    public function test_region_concatenates_listener_fragments(): void
    {
        $hooks = $this->hooks();
        $hooks->onRegion('footer', fn () => '<a>');
        $hooks->onRegion('footer', fn () => '<b>');

        $this->assertSame('<a><b>', $hooks->region('footer'));
    }

    public function test_region_without_listeners_returns_empty_string(): void
    {
        $this->assertSame('', $this->hooks()->region('nothing'));
    }

    public function test_region_flattens_array_fragments_instead_of_crashing(): void
    {
        $hooks = $this->hooks();
        $hooks->onRegion('multi', fn () => ['<a>', '<b>']);

        $this->assertSame('<a><b>', $hooks->region('multi'));
    }

    public function test_region_keeps_falsy_string_fragments(): void
    {
        $hooks = $this->hooks();
        $hooks->onRegion('zero', fn () => '0');

        $this->assertSame('0', $hooks->region('zero'));
    }
}
