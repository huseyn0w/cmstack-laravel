<?php

/*
|--------------------------------------------------------------------------
| Architecture layering presets
|--------------------------------------------------------------------------
| Encodes the HARD rules: controller -> service -> repository -> model.
| Controllers are a pure HTTP boundary; services never touch the ORM/DB
| layer directly; repositories are the single home for Eloquent query
| building and should only be imported by known legitimate consumers.
*/

// -----------------------------------------------------------------------
// Rule 1 (NON-NEGOTIABLE): Controllers must not touch the ORM or DB facade
// -----------------------------------------------------------------------
arch('controllers do not touch the ORM or the DB facade')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
        'Illuminate\Database\Eloquent\Builder',
        'Illuminate\Database\Query\Builder',
    ]);

// -----------------------------------------------------------------------
// Rule 2 (NON-NEGOTIABLE): Services must never bypass repositories
// -----------------------------------------------------------------------
arch('services never touch the DB facade or query builder')
    ->expect('App\Services')
    ->not->toUse([
        'Illuminate\Support\Facades\DB',
        'Illuminate\Database\Query\Builder',
    ]);

// -----------------------------------------------------------------------
// Rule 3: Repositories are the only home for Eloquent query building.
// The allow-list below enumerates every namespace that legitimately
// consumes repository classes — each entry is justified below.
// -----------------------------------------------------------------------
arch('repositories are the only home for Eloquent query building')
    ->expect('App\Repositories')
    ->toOnlyBeUsedIn([
        // Primary consumers — services delegate all data access here.
        'App\Services',

        // Inter-repository references (e.g. BaseRepository is extended by all repos).
        'App\Repositories',

        // MCP tools delegate directly to CPanel*Repository classes per design
        // (see CLAUDE.md: "tools live in app/Mcp/Tools/<Domain>/ and delegate
        // to the existing CPanel*Repository classes — don't re-implement logic").
        'App\Mcp',

        // Eloquent model observers (PostObserver, PostTranslationObserver,
        // PageTranslationObserver) pull repositories via app() to sync tags
        // and snapshot revisions on model events — registered in ObserverServiceProvider.
        'App\Observers',

        // CategoryRequest uses CPanelCategoryRepository::descendantIds() inside
        // a validation closure to prevent assigning a category as its own child.
        // A form request is the natural place for cross-field DB validation.
        'App\Http\Requests',

        // PluginManager (App\Support) uses CPanelPluginRepository to load plugin
        // metadata from the DB — it is a framework-level support class, not a
        // controller or service, so this is a pragmatic legitimate exception.
        'App\Support',

        // Test files resolve repositories directly for integration / unit testing.
        'Tests',
    ]);

// -----------------------------------------------------------------------
// Rule 4: No debugging leftovers in production code
// -----------------------------------------------------------------------
arch('no debugging leftovers')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die'])
    ->not->toBeUsed();
