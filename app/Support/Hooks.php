<?php

namespace App\Support;

use Illuminate\Contracts\Events\Dispatcher;

/**
 * P9 hook engine: a thin, Laravel-idiomatic wrapper over the event dispatcher.
 * Hooks are string-named events namespaced by kind so a plugin can subscribe to
 * a specific hook by name:
 *   - actions: fire-and-forget side effects.
 *   - filters: a value is passed through listeners (each may return a new value).
 *   - regions: listeners return HTML fragments concatenated for a named template
 *     region (rendered via the @hook Blade directive).
 */
class Hooks
{
    public function __construct(private Dispatcher $events) {}

    public function onAction(string $name, callable $callback): void
    {
        $this->events->listen($this->key('action', $name), $callback);
    }

    public function action(string $name, mixed ...$args): void
    {
        $this->events->dispatch($this->key('action', $name), $args);
    }

    public function onFilter(string $name, callable $callback): void
    {
        $this->events->listen($this->key('filter', $name), function (...$payload) use ($callback) {
            // Laravel spreads the dispatched payload array into listener args:
            // $payload[0] is the mutable container; the rest are extra filter args.
            $container = $payload[0];
            $extra = array_slice($payload, 1);
            $container->value = $callback($container->value, ...$extra);
        });
    }

    public function filter(string $name, mixed $value, mixed ...$args): mixed
    {
        $container = new HookValue($value);
        $this->events->dispatch($this->key('filter', $name), array_merge([$container], $args));

        return $container->value;
    }

    public function onRegion(string $name, callable $callback): void
    {
        $this->events->listen($this->key('region', $name), $callback);
    }

    public function region(string $name, array $context = []): string
    {
        $fragments = (array) $this->events->dispatch($this->key('region', $name), [$context]);

        $html = '';
        foreach ($fragments as $fragment) {
            if ($fragment === null) {
                continue; // a listener with no opinion returns null
            }
            // Tolerate a listener that returns multiple fragments as an array.
            $html .= is_array($fragment)
                ? implode('', array_map(fn ($part) => (string) $part, $fragment))
                : (string) $fragment;
        }

        return $html;
    }

    private function key(string $kind, string $name): string
    {
        return "hook.{$kind}.{$name}";
    }
}
