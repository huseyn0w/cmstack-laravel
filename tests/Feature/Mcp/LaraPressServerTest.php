<?php

namespace Tests\Feature\Mcp;

use App\Http\Models\User;
use App\Http\Models\UserRoles;
use App\Mcp\Servers\LaraPressServer;
use App\Mcp\Tools\Posts\ListPostsTool;
use App\Mcp\Tools\Theme\ListThemeFilesTool;
use App\Mcp\Tools\Theme\ReadThemeFileTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The MCP server is the remote-management surface for the whole CMS, so the
 * behaviour that actually matters is the access control: every tool must reject
 * unauthenticated callers and callers whose role lacks the required capability,
 * and the theme tools must never escape the theme directory. These tests pin
 * that down without depending on the (request-coupled) content write path.
 */
class LaraPressServerTest extends TestCase
{
    use RefreshDatabase;

    private function userWithPermissions(array $flags): User
    {
        $role = UserRoles::create([
            'name' => 'role_'.bin2hex(random_bytes(4)),
            'permissions' => json_encode($flags),
        ]);

        return User::factory()->create(['role_id' => $role->id]);
    }

    public function test_unauthenticated_calls_are_rejected(): void
    {
        LaraPressServer::tool(ListPostsTool::class, [])
            ->assertSee('Authentication required');
    }

    public function test_tool_is_denied_without_the_required_permission(): void
    {
        $user = $this->userWithPermissions(['manage_posts' => 0]);

        LaraPressServer::actingAs($user)
            ->tool(ListPostsTool::class, [])
            ->assertSee('Permission denied');
    }

    public function test_tool_is_allowed_with_the_required_permission(): void
    {
        $user = $this->userWithPermissions(['manage_posts' => 1]);

        LaraPressServer::actingAs($user)
            ->tool(ListPostsTool::class, ['per_page' => 5])
            ->assertOk();
    }

    public function test_theme_listing_returns_known_template_for_authorized_user(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        LaraPressServer::actingAs($user)
            ->tool(ListThemeFilesTool::class, [])
            ->assertOk()
            ->assertSee('index.blade.php');
    }

    public function test_theme_read_rejects_path_traversal(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        LaraPressServer::actingAs($user)
            ->tool(ReadThemeFileTool::class, ['path' => '../../../../config/app.blade.php'])
            ->assertSee('Rejected path');
    }

    public function test_theme_read_rejects_non_blade_extension(): void
    {
        $user = $this->userWithPermissions(['manage_general_settings' => 1]);

        LaraPressServer::actingAs($user)
            ->tool(ReadThemeFileTool::class, ['path' => 'index.php'])
            ->assertSee('Rejected path');
    }
}
