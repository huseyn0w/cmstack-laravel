<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\Categories\CreateCategoryTool;
use App\Mcp\Tools\Categories\DeleteCategoryTool;
use App\Mcp\Tools\Categories\GetCategoryTool;
use App\Mcp\Tools\Categories\ListCategoriesTool;
use App\Mcp\Tools\Categories\UpdateCategoryTool;
use App\Mcp\Tools\Pages\CreatePageTool;
use App\Mcp\Tools\Pages\DeletePageTool;
use App\Mcp\Tools\Pages\GetPageTool;
use App\Mcp\Tools\Pages\ListPagesTool;
use App\Mcp\Tools\Pages\UpdatePageTool;
use App\Mcp\Tools\Posts\CreatePostTool;
use App\Mcp\Tools\Posts\DeletePostTool;
use App\Mcp\Tools\Posts\GetPostTool;
use App\Mcp\Tools\Posts\ListPostsTool;
use App\Mcp\Tools\Posts\UpdatePostTool;
use App\Mcp\Tools\Settings\GetGeneralSettingsTool;
use App\Mcp\Tools\Settings\GetSeoSettingsTool;
use App\Mcp\Tools\Settings\UpdateGeneralSettingsTool;
use App\Mcp\Tools\Settings\UpdateSeoSettingsTool;
use App\Mcp\Tools\Theme\ListThemeFilesTool;
use App\Mcp\Tools\Theme\ReadThemeFileTool;
use App\Mcp\Tools\Theme\WriteThemeFileTool;
use App\Mcp\Tools\Users\CreateUserTool;
use App\Mcp\Tools\Users\DeleteUserTool;
use App\Mcp\Tools\Users\GetUserTool;
use App\Mcp\Tools\Users\ListRolesTool;
use App\Mcp\Tools\Users\ListUsersTool;
use App\Mcp\Tools\Users\UpdateUserTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('LaraPress')]
#[Version('1.0.0')]
#[Instructions(<<<'TXT'
This server lets an AI assistant manage a LaraPress CMS installation: posts,
pages, categories, users, site settings, and theme (Blade) templates.

Authorization: every tool runs as the OAuth-authenticated user and is gated by
that user's admin permissions (manage_posts, manage_pages, manage_post_categories,
manage_users, manage_general_settings). A "Permission denied" result means the
connected account lacks that capability — it is not retryable without a role change.

Multilingual content: posts, pages and categories are translatable. Pass an
explicit `locale` (e.g. "en") when creating or updating them; omitting it targets
the site's default language. Use list/get tools to discover existing slugs and ids
before updating or deleting.

Theme tools edit Blade template files under the active theme only and never
execute code. There is no plugin system in LaraPress.
TXT)]
class LaraPressServer extends Server
{
    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [
        // Posts
        ListPostsTool::class,
        GetPostTool::class,
        CreatePostTool::class,
        UpdatePostTool::class,
        DeletePostTool::class,
        // Pages
        ListPagesTool::class,
        GetPageTool::class,
        CreatePageTool::class,
        UpdatePageTool::class,
        DeletePageTool::class,
        // Categories
        ListCategoriesTool::class,
        GetCategoryTool::class,
        CreateCategoryTool::class,
        UpdateCategoryTool::class,
        DeleteCategoryTool::class,
        // Users
        ListUsersTool::class,
        GetUserTool::class,
        CreateUserTool::class,
        UpdateUserTool::class,
        DeleteUserTool::class,
        ListRolesTool::class,
        // Settings
        GetGeneralSettingsTool::class,
        UpdateGeneralSettingsTool::class,
        GetSeoSettingsTool::class,
        UpdateSeoSettingsTool::class,
        // Theme templates
        ListThemeFilesTool::class,
        ReadThemeFileTool::class,
        WriteThemeFileTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
