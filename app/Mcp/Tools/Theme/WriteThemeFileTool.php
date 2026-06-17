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

#[Description('Write (create or overwrite) a Blade template in the active theme. The path must stay inside the theme and end in .blade.php; content is written verbatim and never executed. This edits live theme code — read the file first and preview your change. Requires the manage_general_settings permission.')]
class WriteThemeFileTool extends Tool
{
    use AuthorizesAccess;
    use ResolvesThemePath;

    public function schema(JsonSchema $schema): array
    {
        return [
            'path' => $schema->string()
                ->description('Relative path inside the active theme, ending in .blade.php, e.g. "partials/promo.blade.php".')
                ->required(),
            'contents' => $schema->string()
                ->description('Full new file contents (Blade markup). Replaces the file entirely.')
                ->required(),
            'create' => $schema->boolean()
                ->description('Allow creating a new file when the path does not exist yet. Defaults to false (update only).'),
        ];
    }

    public function handle(Request $request): Response|ResponseFactory
    {
        if ($denied = $this->deny($request, 'manage_general_settings')) {
            return $denied;
        }

        $validated = $request->validate([
            'path' => ['required', 'string', 'max:1024'],
            'contents' => ['required', 'string'],
            'create' => ['nullable', 'boolean'],
        ]);

        $allowCreate = (bool) ($validated['create'] ?? false);
        $existing = $this->safeThemePath($validated['path'], mustExist: true);

        if (is_null($existing) && ! $allowCreate) {
            return Response::error('That template does not exist. Pass "create": true to create a new file, or fix the path.');
        }

        $target = $existing ?? $this->safeThemePath($validated['path'], mustExist: false);

        if (is_null($target)) {
            return Response::error('Rejected path. The target must be a *.blade.php file inside the active theme (no absolute paths or "..").');
        }

        $isNew = ! file_exists($target);
        $bytes = file_put_contents($target, $validated['contents']);

        if ($bytes === false) {
            return Response::error('Failed to write the template (check filesystem permissions).');
        }

        return Response::structured([
            'written' => true,
            'created' => $isNew,
            'path' => $validated['path'],
            'bytes' => $bytes,
        ]);
    }
}
