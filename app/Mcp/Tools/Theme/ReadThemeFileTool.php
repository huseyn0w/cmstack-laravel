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

#[Description('Read-only. Return the raw contents of a Blade template in the active theme. Pass a relative path from list-theme-files, e.g. "pages/default.blade.php". The file is never rendered or executed. Requires the manage_general_settings permission.')]
class ReadThemeFileTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesThemePath;

    public function schema(JsonSchema $schema): array
    {
        return [
            'path' => $schema->string()
                ->description('Relative path inside the active theme, ending in .blade.php.')
                ->required(),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'path' => ['required', 'string', 'max:1024'],
        ]);

        $absolute = $this->safeThemePath($validated['path'], mustExist: true);

        if (is_null($absolute)) {
            return Response::error('Rejected path. Provide an existing *.blade.php file inside the active theme (no absolute paths or "..").');
        }

        return Response::structured([
            'path' => $validated['path'],
            'bytes' => filesize($absolute),
            'contents' => file_get_contents($absolute),
        ]);
    }
}
