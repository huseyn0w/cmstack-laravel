<?php

namespace App\Mcp\Tools\Theme;

use App\Mcp\Concerns\AuthorizesAccess;
use App\Mcp\Concerns\ResolvesThemePath;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\ResponseFactory;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Symfony\Component\Finder\Finder;

#[Description('Read-only. List every Blade template (*.blade.php) in the active theme, as relative paths you can pass to read-theme-file / write-theme-file. Requires the manage_general_settings permission.')]
class ListThemeFilesTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesThemePath;

    public function schema(JsonSchema $schema): array
    {
        return [];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $root = $this->themeRoot();

        if (! is_dir($root)) {
            return Response::error('The active theme directory does not exist.');
        }

        $files = [];
        foreach (Finder::create()->files()->in($root)->name('*.blade.php')->sortByName() as $file) {
            $files[] = str_replace($root.DIRECTORY_SEPARATOR, '', $file->getRealPath());
        }

        return Response::structured([
            'theme' => config('app.template_name', 'default'),
            'count' => count($files),
            'files' => $files,
        ]);
    }
}
