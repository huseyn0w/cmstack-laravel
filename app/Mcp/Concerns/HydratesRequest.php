<?php

namespace App\Mcp\Concerns;

/**
 * Bridges MCP tool arguments onto the global HTTP request.
 *
 * Several model observers (PostObserver, PostTranslationObserver, PageObserver)
 * read their inputs from app('request') rather than from the saved model — e.g.
 * PostTranslationObserver overwrites `content`/`preview` from the request on
 * every save. The admin panel satisfies this because the data arrives as a form
 * POST. MCP calls carry a JSON-RPC body instead, so we must explicitly merge the
 * tool's validated input into the request before delegating to a repository,
 * otherwise the observers would clobber those fields with nulls.
 */
trait HydratesRequest
{
    /**
     * Merge validated tool input into the active request so request-coupled
     * observers see the same values that are written to the database.
     *
     * @param  array<string, mixed>  $data
     */
    protected function hydrateRequest(array $data): void
    {
        request()->merge($data);
    }
}
