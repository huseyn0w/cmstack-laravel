# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

LaraPress CMS — a WordPress/Joomla-style multilingual CMS built on **Laravel 11 / PHP 8.3** (min PHP 8.2). Requires the `ext-imagick` PHP extension and **MySQL 8** (SQLite is used only for the test suite). Front-end is **Tailwind CSS 3 + Alpine.js bundled with Vite** (no Bootstrap/jQuery/Vue — those were removed in the modernization). Local dev can run via Docker Compose; production runs on plain PHP-FPM (Hostinger/VPS) with **no** Docker dependency.

## Commands

```bash
# --- Local dev via Docker (recommended) ---
make setup                       # docker up + composer install + key:generate + migrate --seed + storage:link + npm build
make up / make down / make fresh # start / stop / rebuild-from-scratch the stack
make test                        # run the suite inside the container
# App: http://localhost:8080   Admin: http://localhost:8080/larapress-admin

# --- Manual (no Docker) ---
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed       # seeds are REQUIRED for a working install (roles/users/pages/posts/settings)
npm run build                    # or `npm run dev` for the Vite dev server (HMR)
php artisan serve

# --- Tests ---
php artisan test                              # full suite (isolated in-memory SQLite — never touches MySQL)
php artisan test --filter=SeoMetaTest         # a single test class
docker compose exec -T app php artisan test   # same, inside the container
```

Test isolation is pinned in `tests/CreatesApplication.php` (forces SQLite `:memory:`, array cache, `app.env=testing`, model-caching off) because the Docker container injects `DB_CONNECTION=mysql` via `$_SERVER` — without the pin the suite would run against (and wipe) the live MySQL dev DB. Don't weaken it.

Code style is enforced by StyleCI (`.styleci.yml`, Laravel preset) on push — there is no local lint command.

Admin panel lives at `/larapress-admin` (seeded credentials `admin` / `larapressadmin123` — change in production).

## Architecture

### Models live in a non-standard location
Eloquent models are in **`app/Http/Models/`** (not `app/Models/`). Admin-only models are under `app/Http/Models/CPanel/`. Keep this convention when adding models.

### Repository pattern
All DB access goes through repositories in `app/Repositories/`, extending the abstract `BaseRepository` (implements `BaseRepositoryInterface`). There are **two parallel families**: front-facing repos (e.g. `PostRepository`, `CategoryRepository`) and admin repos prefixed `CPanel*` (e.g. `CPanelPostRepository`). Controllers stay thin and delegate to repositories. `BaseRepository` has built-in logic for swapping to the `*Translation` model based on the current locale (`checkForTranslation`).

### Multilingual content (astrotomic/laravel-translatable)
Translatable models (`Post`, `Page`, `Category`, `Menu`) implement `TranslatableContract`, use the `Translatable` trait, and declare `$translatedAttributes`. Each has a companion `*Translation` model + `*_translations` table. The active locale is resolved per-request by the `Localization` middleware from `session('locale')`, falling back to `config('app.locale')`. Available languages are defined in `config/app.php` under `languages_list` (read via the `get_languages()` / `lang_exist()` helpers).

### Routing & access control are split front vs. admin
- **Front routes** (`routes/web.php`, default namespace): `PageController`, `PostController`, `CategoryController`, `UserController`, `PostCommentController`. Note the catch-all `/{locale?}/{slug?}` → `PageController@languageIndex` must stay last.
- **Admin routes** are all under the `larapress-admin` prefix + `CPanel` namespace, guarded by `auth` + `see_admin_panel`, with each section gated by a custom permission middleware.

The permission system is **custom** (not a package): `UserRoles` + `UserPermissions` models, and one middleware per capability (`ManageUsers`, `ManagePosts`, `ManagePages`, `ManageCategories`, `ManageComments`, `ManageRoles`, `ManageGeneralSettings`, `ManageMenu`). These are aliased in `app/Http/Kernel.php` as `manage_*`. The `see_admin_panel` alias maps to `AdminPanelMiddleware` (which checks the `see_admin_panel` permission and returns 403 when missing).

### Theming / template selection
Front views live under `resources/views/default/` (`pages/`, `posts/`, `categories/`, `users/`). A page's blade is chosen dynamically from a DB column: `PageController` does `view('default.pages.' . $this->data->template, ...)`. Adding a page template means adding a blade under `resources/views/default/pages/` and exposing its name to the page editor. `default` is effectively the active theme folder.

### Observers
Registered in `app/Providers/ObserverServiceProvider.php` (`PostObserver`, `PostTranslationObserver`, `PageObserver`) — they handle derived data like slug generation on model events. Add new observers there, not in `EventServiceProvider`.

### Model caching
`Post` and `Category` use the `genealabs/laravel-model-caching` `Cachable` trait. Writes auto-flush the model's cache, but be aware reads are cached when debugging stale data.

### Asset pipeline (Vite + Tailwind + Alpine)
Entry points `resources/css/app.css` (+ `resources/css/admin.css`) and `resources/js/app.js` (+ `resources/js/admin.js`) are bundled by Vite (`vite.config.js`, `tailwind.config.js`). Blade loads them with `@vite([...])`. Tailwind **preflight is disabled globally**; each theme scopes its own reset under a wrapper class (`.theme-default` for the public site, `.theme-admin` for the panel) so the two themes don't bleed into each other. The legacy `public/front/**` and `public/admin/**` assets are no longer loaded by the rewritten views.

### SEO / GEO
A single head partial `resources/views/default/partials/seo-meta.blade.php` emits title/description, canonical, Open Graph, Twitter cards, `hreflang` alternates, robots, and JSON-LD (via the `json_ld()` / `get_seo_settings()` helpers). `SeoController` serves dynamic `/sitemap.xml`, `/robots.txt`, `/llms.txt` (routes are registered before the front catch-all). Global SEO defaults live in the `seo_settings` singleton (admin **Settings → SEO**); per-entity `canonical_url` / `meta_noindex` live on the `*_translations` tables. Note: `/robots.txt` is dynamic, so nginx must NOT have a static `location = /robots.txt` block.

### AI / MCP server
A built-in MCP server (`laravel/mcp`) lets an authenticated AI client manage the live site. Route is `routes/ai.php` (`Mcp::oauthRoutes()` + `Mcp::web('/mcp/larapress', …)` behind `auth:api` + throttle). Auth is **OAuth 2.1 via Laravel Passport** — the `api` guard in `config/auth.php` uses the `passport` driver and `User` implements `OAuthenticatable` + `HasApiTokens`. Server is `app/Mcp/Servers/LaraPressServer.php`; tools live in `app/Mcp/Tools/<Domain>/` and **delegate to the existing `CPanel*Repository` classes** (don't re-implement business logic). Cross-cutting behavior is in `app/Mcp/Concerns/`: `AuthorizesAccess` (per-tool permission gate read from the bearer-token user, mirroring `UserPolicy` flags — every tool must call `deny()`), `ResolvesLocale` (translatable writes), `HydratesRequest` (merges validated input onto the global request because PostObserver/PageObserver/PostTranslationObserver read `content`/`preview`/`category` from `app('request')`), and `ResolvesThemePath` (theme-file path allow-listing — no code execution). `Response::structured()` returns a `ResponseFactory`, so tool `handle()` signatures are typed `Response|ResponseFactory`. There is **no plugin system** — "theme code" = Blade templates under the active theme only. Full guide: `docs/mcp.md`. Deployment needs `php artisan migrate` (Passport tables) + `php artisan passport:keys`.

### Other conventions
- **Validation**: dedicated Form Request classes in `app/Http/Requests/`. The base `LaraPressRequest` must **not** redeclare a `$locale` property (Symfony 7's `Request` already declares a typed `?string $locale`); it uses `$currentLocale` instead.
- **Custom helpers**: globally available, autoloaded via `bootstrap/larapress-helpers.php` (composer `files` autoload) — e.g. `get_languages()`, `lang_exist()`, `get_current_lang()`.
- **Config over env() in views**: never call `env()` at runtime in Blade (breaks under `php artisan config:cache`); use `config()` — e.g. the active theme is `config('app.template_name')`.
- **Authorization policies**: `app/Policies/UserPolicy.php` (registered in `AuthServiceProvider`).
- **Integrations**: HTML sanitization via `mews/purifier`, media via `unisharp/laravel-filemanager` (config built by `app/Handlers/LfmConfigHandler.php`), social login via `laravel/socialite` (Twitter/Facebook/LinkedIn/Google/GitHub), spam protection via a `captcha` service (`App\Services\Captcha`, Google reCAPTCHA v3 — disabled when no keys), image processing via `intervention/image`.
