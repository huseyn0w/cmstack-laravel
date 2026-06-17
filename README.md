<p align="center">
<img alt="LaraPress Logo" src="https://raw.githubusercontent.com/huseyn0w/LaraPress-CMS/master/public/front/default/img/readme.png">
</p>

# LaraPress CMS

**LaraPress — the Laravel version of WordPress.** A modern, multilingual, SEO/GEO-ready
content management system built on **Laravel 11 / PHP 8.3**, with a Tailwind + Vite
front-end and a first-class admin panel. It plays the same role as WordPress, DLE or
Joomla, but on a clean, testable Laravel codebase that developers actually enjoy extending.

Built and maintained by **[Elman Group](https://elman.group)**.

---

## Table of contents

- [Features](#features)
- [Tech stack](#tech-stack)
- [Requirements](#requirements)
- [Quick start (Docker + Makefile)](#quick-start-docker--makefile)
- [Manual setup (no Docker)](#manual-setup-no-docker)
- [Testing](#testing)
- [SEO / GEO](#seo--geo)
- [AI / MCP connector (manage your site from Claude)](#ai--mcp-connector-manage-your-site-from-claude)
- [Multilingual configuration](#multilingual-configuration)
- [Admin credentials](#admin-credentials)
- [Deployment](#deployment)
  - [Hostinger (shared hosting, no Docker)](#hostinger-shared-hosting-no-docker)
  - [VPS (nginx + php-fpm + mysql)](#vps-nginx--php-fpm--mysql)
- [Is it ready to deploy?](#is-it-ready-to-deploy)
- [Author & license](#author--license)

---

## Features

* **Full CMS**: pages, posts, categories, menus, comments, media / file manager
* **Modern responsive UI** (Tailwind CSS 3 + Alpine.js) for both the public site and the admin panel
* **Built-in SEO/GEO** — Open Graph, Twitter cards, canonical + `hreflang`, JSON-LD structured data, dynamic `sitemap.xml`, `robots.txt`, and `llms.txt`
* **Multilingual content** via `astrotomic/laravel-translatable` (en/ru out of the box, easily extended)
* **Social-media authentication** (Facebook, GitHub, LinkedIn, and other Socialite providers)
* **Users, roles & granular permissions** (custom role/permission middleware — one capability per middleware)
* **Custom fields** and a **flexible template-switching system**
* **Website search**
* **Spam protection** via Google reCAPTCHA v3 (gracefully disabled when no keys are set)
* **Database / model caching**
* **AI / MCP connector** — manage the live site from Claude (posts, pages, users, settings, theme) over an authenticated MCP server (see [AI / MCP connector](#ai--mcp-connector-manage-your-site-from-claude))
* **164 automated tests** (PHPUnit) running on isolated in-memory SQLite

### Why it's easy to extend

Written with Laravel best practices: thin controllers, dedicated Form Request validators,
the repository pattern for all DB access, observers, policies, the TinyMCE editor, and the
Laravel File Manager.

### Planned

* E-Commerce extension
* REST API for posts and pages

---

## Tech stack

* **Laravel 11** (PHP 8.3)
* **Tailwind CSS 3** + **Alpine.js**, bundled with **Vite** — lightweight front-end, no jQuery/plugin bloat
* **MySQL 8** with `astrotomic/laravel-translatable` for multilingual content
* Repository pattern, custom role/permission middleware, model caching
* Google reCAPTCHA (v3) for spam protection
* Docker stack for local development (nginx + php-fpm + MySQL 8)

---

## Requirements

* **PHP 8.2+** (developed and tested on PHP 8.3) with the `imagick` extension
* **Composer 2**
* **MySQL 8** — SQLite is used only for the test suite (in-memory)
* **Node.js 20+** and npm (for the Vite/Tailwind asset build)
* *(optional)* **Docker + Docker Compose** for the one-command local stack

---

## Quick start (Docker + Makefile)

The fastest path. The Docker stack (nginx + PHP 8.3-FPM with imagick + MySQL 8) is a
**local development convenience** — it is **not** required at runtime in production.

```bash
git clone <your-repo-url> larapress && cd larapress
make setup
```

`make setup` does everything end-to-end:

1. copies `.env.example` → `.env` (if missing),
2. builds and starts the Docker stack (`docker compose up -d --build`),
3. `composer install`,
4. `php artisan key:generate`,
5. `php artisan migrate --seed`,
6. `php artisan storage:link`,
7. `npm install && npm run build` (assets, on the host).

When it finishes:

* **App:** http://localhost:8080
* **Admin:** http://localhost:8080/larapress-admin (see [credentials](#admin-credentials))

### Available `make` targets

| Target        | Description                                                        |
|---------------|--------------------------------------------------------------------|
| `make setup`  | First-time bootstrap (everything above)                            |
| `make up`     | Start the Docker stack                                             |
| `make down`   | Stop the stack (keeps the DB volume)                               |
| `make fresh`  | `migrate:fresh --seed` (rebuild the database)                      |
| `make test`   | Run the PHPUnit suite inside the container                         |
| `make build`  | Build front-end assets (Vite production build)                     |
| `make shell`  | Open a shell in the app container                                  |
| `make logs`   | Tail container logs                                                |
| `make clean`  | Stop the stack **and remove the DB volume** (destroys data)        |

Run `make help` to see all targets.

### Optional Docker services

* **Mailpit** (catches local outgoing mail, UI on http://localhost:8025):
  ```bash
  docker compose --profile mail up -d mailpit
  ```
  Then set in `.env`: `MAIL_MAILER=smtp`, `MAIL_HOST=mailpit`, `MAIL_PORT=1025`.
* **Vite HMR** (live reload instead of a one-off build):
  ```bash
  docker compose --profile dev up node     # Vite on http://localhost:5173
  ```

### Live code reload (no container restart needed)

The project directory is bind-mounted into the containers (`./:/var/www/html`), so:

* **PHP, Blade, routes, config, helpers, classes** — changes apply **immediately** on
  the next request. No `docker compose restart` needed.
* **Tailwind CSS / JS** — bundled by Vite, so run **`npm run dev`** (or the `node`
  service above) for hot reload, or `npm run build` for a one-off rebuild.
* **`.env` infrastructure vars** (DB host, etc.) are injected at container start —
  re-run `docker compose up -d` after changing those.
* Avoid `php artisan config:cache` / `route:cache` in dev (they freeze edits until
  `php artisan config:clear`); they're a production-only optimization.

> **Docker-only files:** `Dockerfile`, `docker-compose.yml`, `docker/nginx/default.conf`,
> `docker/php/php.ini`, `.dockerignore`, `Makefile`. **None of these are part of the
> production runtime.**

---

## Manual setup (no Docker)

Requires a local PHP 8.3 + Composer + Node + a running MySQL 8 server.

1. Copy the env file and fill in your settings (database, mail, API keys, default language):
   ```bash
   cp .env.example .env
   ```
   For a **local MySQL** server, set `DB_HOST=127.0.0.1` (the `.env.example` default of
   `DB_HOST=mysql` is the Docker service name). Create a database named `larapress`
   (or update `DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` accordingly).

2. Install PHP and front-end dependencies:
   ```bash
   composer install
   npm install
   ```

3. Generate the app key, run migrations + seeders, link storage, and build assets:
   ```bash
   php artisan key:generate
   composer setup            # = migrate --seed --force + storage:link (key:generate is idempotent)
   npm run build             # or `npm run dev` for the Vite dev server with HMR
   ```
   The `composer setup` script bundles `key:generate`, `migrate --seed`, and `storage:link`
   for the non-Docker path. You can also run those artisan commands individually.

4. Serve the app:
   ```bash
   php artisan serve         # http://127.0.0.1:8000
   ```

> **Media uploads:** the file manager writes to `public/uploads`, and the Laravel `public`
> disk maps to `public/storage` → `storage/app/public`. Run `php artisan storage:link`
> (included in `composer setup` / `make setup`) so uploaded media on the public disk is
> served correctly.

### Spam protection (reCAPTCHA)

The contact, search, and password-change forms support Google reCAPTCHA v3. It is
**disabled by default** (forms work without it). To enable it, set in `.env`:

```bash
CAPTCHA_ENABLED=true
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

---

## Testing

```bash
php artisan test                                # full suite (host)
docker compose exec app php artisan test        # inside Docker
make test                                       # inside Docker (shortcut)
php artisan test --filter=SeoMetaTest           # a single test
```

The suite is **158 tests** and runs on an **isolated in-memory SQLite** database (pinned in
`tests/CreatesApplication.php` and `phpunit.xml`) so it **never** touches your local MySQL /
Docker data — no DB setup required to run tests.

---

## SEO / GEO

LaraPress ships SEO/GEO support out of the box, built to stay fast (no WordPress-style
script bloat — public pages load only the Vite bundle and, optionally, a single async
analytics tag):

* Per-page `<title>` / `<meta description>`, `<link rel="canonical">`, per-entity `noindex` / canonical overrides.
* **Open Graph** + **Twitter Card** tags (with a configurable default social image).
* **`hreflang`** alternates (en/ru + `x-default`) for multilingual content.
* **JSON-LD structured data** (schema.org) — `WebSite` + `SearchAction` + `Organization` on the homepage,
  `BlogPosting` + `BreadcrumbList` on posts, `CollectionPage` on categories, `ProfilePage` / `Person`
  on profiles — which also helps generative engines (GEO).
* Dynamic **`/sitemap.xml`** (pages/posts/categories with `hreflang`), **`/robots.txt`**, and **`/llms.txt`**.
* Lazy-loaded images with width/height (CLS-safe), `preconnect` fonts with `display=swap`, deferred/module scripts.

Configure global SEO defaults in the admin panel under **Settings → SEO** (title separator,
default description, default OG image, social handles, Google/Bing verification tags, optional
async GA4/GTM id, a global "discourage search engines" toggle, sitemap toggle, and extra
`robots.txt` lines).

---

## AI / MCP connector (manage your site from Claude)

LaraPress includes a built-in **Model Context Protocol (MCP) server**, so you can manage
your **live** site from an AI client such as **Claude** (Claude Code CLI, the VS Code
extension, or claude.ai) using natural language — *"create a draft post about X"*,
*"update the SEO meta description"*, *"add a partial to the theme"*.

It is built on the official [`laravel/mcp`](https://laravel.com/docs/12.x/mcp) package and
runs **inside the Laravel app** (no separate service). Security is first-class:

* **OAuth 2.1** authentication via Laravel Passport — endpoint `POST /mcp/larapress`.
* Every tool runs as the authenticated admin and is **gated by the same `manage_*`
  permissions** as the admin panel.
* **No raw code execution.** The only code surface is editing theme Blade templates,
  restricted to `*.blade.php` files inside the active theme with path allow-listing.

Tool coverage: **posts, pages, categories** (full CRUD, multilingual), **users & roles**,
**general + SEO settings**, and **theme templates** (list/read/write).

Enable it on a deployment (Passport is already pulled in via `composer install`):

```bash
php artisan migrate          # adds Passport oauth_* tables
php artisan passport:keys    # generate encryption keys (once per environment)
# ensure APP_URL is your real https URL, then: php artisan config:clear
```

Connect from Claude Code:

```bash
claude mcp add --transport http larapress https://your-site.com/mcp/larapress
# then run /mcp in Claude and authenticate in the browser
```

**Full guide:** [`docs/mcp.md`](docs/mcp.md) — complete tool list, VS Code / claude.ai
setup, the OAuth consent flow, and how to extend the toolset.

---

## Multilingual configuration

1. Edit the language list in `config/app.php`:
   ```php
   'languages_list' => [
       'en' => ['title' => 'English', 'icon' => env('APP_URL').'/admin/img/flags/en.png'],
       'ru' => ['title' => 'Русский', 'icon' => env('APP_URL').'/admin/img/flags/ru.png'],
   ],
   ```
2. Manage the localization strings under `resources/lang/`.

The active locale is resolved per request from `session('locale')`, falling back to
`config('app.locale')` (set via the `LOCALE` env key).

---

## Admin credentials

After seeding, the admin panel lives at:

```
<APP_URL>/larapress-admin
```

Seeded login:

```
Username: admin
Password: larapressadmin123
```

> Change this password immediately in any non-local environment.

---

## Deployment

LaraPress runs on traditional PHP hosting with **no runtime dependency on Docker**. Two
supported targets:

### Hostinger (shared hosting, no Docker)

1. Upload / pull the code to your Hostinger account.
2. Install PHP dependencies (production, optimized):
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
3. Build front-end assets **locally or in CI**, then upload the generated `public/build/`
   directory (shared hosting usually has no Node toolchain):
   ```bash
   npm install && npm run build
   ```
4. Set the domain's **document root to `/public`** in hPanel.
5. Configure `.env` with your Hostinger MySQL credentials:
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=localhost          # Hostinger MySQL is local to the account
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```
6. Via SSH (or a one-off cron job), generate the key, migrate, cache config/routes, and link storage:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan storage:link
   ```
7. Enable the **`imagick` PHP extension** in the hPanel PHP-extensions panel (required by the
   file manager / image features).

**Shared-hosting friendliness:** the app needs no queue workers, no websockets, and no
always-on processes (`QUEUE_CONNECTION=sync`). `robots.txt`/`sitemap.xml` are served
dynamically by Laravel. The scheduler is **optional**. `php artisan config:cache` and
`route:cache` are **safe** — the app reads no `env()` in views, so cached config does not
break rendering (verified: home returns `200`, admin returns `302` with caches enabled).

### VPS (nginx + php-fpm + mysql)

You have two options on a VPS:

**A) Reuse the included Docker stack** (simplest):
```bash
git clone <repo> && cd larapress
make setup            # or: docker compose up -d ...
```
Then put your real reverse proxy / TLS in front (or expose port 8080 behind one).

**B) Native nginx + php-fpm + MySQL** (no Docker). Install PHP 8.3-FPM (with `imagick`,
`gd`, `intl`, `zip`, `bcmath`, `mbstring`, `pdo_mysql`), MySQL 8, and Node (for the build).
Deploy the code, then:

```bash
composer install --no-dev --optimize-autoloader
npm install && npm run build
cp .env.example .env        # set APP_ENV=production, APP_DEBUG=false, DB_HOST=127.0.0.1, etc.
php artisan key:generate
php artisan migrate --force
php artisan config:cache && php artisan route:cache
php artisan storage:link
```

Example nginx server block (adapted from `docker/nginx/default.conf`, which is a working
reference config):

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/larapress/public;
    index index.php index.html;
    charset utf-8;
    client_max_body_size 64M;            # sized for the media/file manager

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    # robots.txt is served dynamically by Laravel — do NOT add a static location for it.

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;   # or 127.0.0.1:9000
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* { deny all; }       # block .env, .git, etc.
}
```

Make sure `storage/` and `bootstrap/cache/` are writable by the php-fpm user. **Queue
workers, the scheduler, and Supervisor are all optional** — only add them if you start
using queued jobs or scheduled tasks.

### Is it ready to deploy?

**Yes.** LaraPress runs cleanly **locally** (Docker via `make setup`, or fully manual), and
deploys to both **Hostinger shared hosting** and a **VPS** with no required background
services. A clean Docker bring-up was verified end to end: migrations + seeders succeed
against a fresh `larapress` MySQL database, the home page returns `200`, `/larapress-admin`
returns `302`, and `/sitemap.xml` returns `200` — and these stay correct with
`config:cache` + `route:cache` enabled (the production code path).

**Caveats / things to do per environment:**

* Provide real production `.env` values (`APP_KEY`, DB credentials, mail, and reCAPTCHA keys
  if you enable captcha) and set `APP_ENV=production`, `APP_DEBUG=false`.
* On shared hosting, **build assets off-host** (`npm run build`) and upload `public/build/`.
* Ensure the **`imagick`** PHP extension is enabled on the target.
* Run `php artisan storage:link` so public-disk media is served.
* Change the seeded admin password.

---

## Author & license

**Author**

* **Elman Hüseynov** — [huseyn0w](https://linkedin.com/in/huseyn0w) · [Elman Group](https://elman.group)

**License**

This project is licensed under the GNU General Public License v3 — see the
[LICENSE](LICENSE) file for details.
