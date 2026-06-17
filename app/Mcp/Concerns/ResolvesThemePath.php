<?php

namespace App\Mcp\Concerns;

/**
 * Path safety for the theme-file tools.
 *
 * Theme tools may only ever touch Blade templates inside the *active* theme
 * directory (resources/views/<template_name>). This trait centralises the
 * allow-listing: it rejects path traversal, absolute paths, non-.blade.php
 * files, and anything resolving outside the theme root. There is intentionally
 * no method here that executes a template — only locate/read/write of text.
 */
trait ResolvesThemePath
{
    /** Absolute path to the active theme's view directory. */
    protected function themeRoot(): string
    {
        $theme = config('app.template_name', 'default');

        return realpath(resource_path('views/'.$theme)) ?: resource_path('views/'.$theme);
    }

    /**
     * Resolve a caller-supplied relative path to an absolute path that is
     * guaranteed to live inside the theme root and end in .blade.php.
     *
     * @return string|null Absolute safe path, or null if the path is rejected.
     */
    protected function safeThemePath(string $relative, bool $mustExist = true): ?string
    {
        // No absolute paths, no traversal, no NUL bytes.
        if ($relative === '' || str_contains($relative, "\0") || str_contains($relative, '..')) {
            return null;
        }

        if (str_starts_with($relative, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $relative)) {
            return null;
        }

        if (! str_ends_with($relative, '.blade.php')) {
            return null;
        }

        $root = $this->themeRoot();
        $candidate = $root.DIRECTORY_SEPARATOR.ltrim($relative, '/\\');

        if ($mustExist) {
            $real = realpath($candidate);

            return ($real && str_starts_with($real, $root.DIRECTORY_SEPARATOR)) ? $real : null;
        }

        // For writes the file may not exist yet; verify the *parent* directory
        // resolves inside the theme root so a new file can't escape it.
        $parent = realpath(dirname($candidate));

        if (! $parent || ! ($parent === $root || str_starts_with($parent, $root.DIRECTORY_SEPARATOR))) {
            return null;
        }

        return $parent.DIRECTORY_SEPARATOR.basename($candidate);
    }
}
