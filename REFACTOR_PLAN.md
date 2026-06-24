# cmstack-laravel — Refactor & Parity Plan

> Master plan coordinating the architecture refactor, feature-parity work, unified UI,
> tooling, and tests required to bring `cmstack-laravel` to the cmstack canon
> (`../FEATURE_MATRIX.md`, `../DESIGN_SYSTEM.md`). Detailed, executable per-slice plans
> live under `docs/superpowers/plans/`.

**Baseline (verified 2026-06-24):** PHP 8.3.10, Laravel 11, `vendor/` + `node_modules/`
present. Full suite **170 tests / 449 assertions PASS** in ~16s (in-memory SQLite). This
plan must keep that green and only add to it.

---

## 0. Audit findings (ground truth, not the prompt's premise)

The prompt's premise is "business logic and **direct Eloquent queries** live in
`app/Http/Controllers`." A code-level audit shows this is **largely already solved** in
this implementation, and the plan is recalibrated accordingly. Honest findings:

| Claim in prompt | Reality in code | Action |
|---|---|---|
| Eloquent calls inside controllers | grep finds **only** `LoginController` (social-login lookup/link) and `RegisterController::create()` using `User::` directly. CPanel controllers call `parent::create()` → `BaseController` → **repository**. `PageController` line is `back()->with(...)`, not Eloquent. | Extract the genuine 2 spots (auth) into a service. Everything else already conforms. |
| Business logic in controllers | CPanel controllers are already thin (validate via Form Request → repository). Auth controllers carry the only real branching logic (social linking, user creation). | Service-layer extraction limited to Auth. |
| No service layer | `app/Services/` exists but holds only `Captcha`. No `Events`/`Listeners`/`Actions`. | Add `App\Services\Auth\SocialAuthService`; introduce Events for side effects (comment notification — a parity gap). |
| Repositories | Mature two-family repo layer (front + `CPanel*`) over `BaseRepository`. | Keep, extend; do **not** reinvent. |
| Policies / Observers / Form Requests / Middleware | All present and used (`UserPolicy`, `PostObserver`/`PageObserver`, `app/Http/Requests/*`, per-capability middleware). | Keep, extend. |

**Conclusion:** Task 2 (architecture refactor) is **small and surgical**, not a rescue.
The bulk of remaining effort is **feature parity**, **tooling/CI**, **test coverage**, and
the **UI redesign** to `DESIGN_SYSTEM.md`.

---

## 1. Architecture refactor — pattern mapping

Each row: target method → chosen pattern → justification → rejected alternative.

| Fat method / concern | Pattern applied | Justification | Rejected alternative |
|---|---|---|---|
| `LoginController::handleProviderCallback` + `findOrLinkUser` + `createUser` + `validateSocialUser` | **Service** (`App\Services\Auth\SocialAuthService`) | Multi-step branching (lookup by provider id → link by email → create) + a DB transaction is business logic, not HTTP concern. Controller becomes: resolve socialite user → call service → `Auth::login` → redirect. | *Action classes* — overkill for one cohesive flow; a single service with named methods is clearer and testable in isolation. |
| `RegisterController::create()` building a `User` | **Service** (reuse `SocialAuthService`-adjacent `UserRegistrar` OR existing `UserRepository`) | Centralizes the "create a user with hashed password + default role" rule so social + form registration share one code path. | *Leave in trait* — keeps a second source of truth for user creation; rejected. |
| New-comment side effect (notify author/moderators) — **parity gap** §18 | **Event + Listener** (`CommentSubmitted` → `SendCommentNotification`) | Side effects belong off the request path (Observer pattern family); email must not block the submit response and must be testable via `Event::fake()`. | *Inline mail in controller/repo* — couples submit to mail, untestable, blocks request; rejected. |
| Scheduled publishing — **net-new** §1 | **Console command + scheduler** (`posts:publish-due`) | A cron-driven sweep is the idiomatic Laravel approach; pure data transition. | *Observer/queue per item* — no trigger exists until the due time; rejected. |

No Strategy/Factory/Adapter/Bridge is introduced: there is no real branching or
external-integration concern here that justifies them. Adding them would be over-engineering
(scope guardrail) — recorded as deliberately rejected.

---

## 2. Feature-parity gaps for cmstack-laravel (from FEATURE_MATRIX §"cmstack-laravel needs")

Ordered by value/independence. Each becomes its own TDD slice with characterization +
feature tests. **None may silently drop existing behavior.**

| # | Gap | Matrix ref | Notes |
|---|---|---|---|
| P1 | **Tags** taxonomy (M2M with posts, slug, browseable archive) | §2 | Mirror existing `Category` model/repo/observer; add `tags` + `taggables` (or `post_tag`) + translations. |
| P2 | **Revisions** for posts+pages (immutable snapshot before update) **+ restore UI** | §1 | Net-new restore UI is shared across all three stacks. Snapshot in observer/service on update. |
| P3 | **Soft-delete for pages** (trash/restore/permanent-delete) | §1 | Posts already soft-delete; extend to `Page` + admin bulk actions. |
| P4 | **Category tree admin UI** (parent picker at minimum) | §2 | Column exists; add hierarchy editing. |
| P5 | **Comment-notification email** (author/moderators) | §18 | Implemented via Event/Listener from §1 mapping. |
| P6 | **Scheduled publishing** (`scheduled_at` + command) | §1 | Console command + `routes/console.php` schedule. |
| P7 | **RSS/Atom feeds** (`/rss.xml`, per-category) | §16 | Net-new for all three; self-contained, fully headless-testable. |
| P8 | **Membership/registration toggle** wired + **email-verification enforcement** | §5 | Settings already dangle; wire to signup + `verified` middleware. |
| P9 | **Plugin/extensibility hook registry** (actions/filters + render-region) | §12 | Adopt django's canonical model. Largest; lands last. |

**Tooling parity (FEATURE_MATRIX §22):** Pint (local lint), Larastan/PHPStan (static
analysis), coverage reporting, CI pipeline (lint → static → test → build → e2e).

**Flagged matrix note (do NOT edit shared file):** none found so far that contradict the
matrix; this section is the place to record any discovered discrepancy for the human.

---

## 3. UI redesign to DESIGN_SYSTEM.md (Blade + Tailwind, native)

Scope: every public surface (`resources/views/default/`) and admin
(`resources/views/.../CPanel` / admin theme). Approach:

1. **Tokens first** — define §2 color tokens + §4 radius/spacing as CSS custom properties
   on `:root`/`.dark`, scoped under `.theme-default` (public) and `.theme-admin` (admin)
   per the existing preflight-disabled convention. Bridge into `tailwind.config.js`
   `theme.extend` (`bg-surface`, `text-muted`, `border-border`, …).
2. **Fonts** — self-host Newsreader / Inter / Geist Mono variable woff2, subset, `preload`
   the two critical weights, `font-display: swap`. No CDN.
3. **Components** — build the §5 component set as Blade components/partials (buttons,
   inputs, cards, nav header, admin sidebar/topbar, tables w/ bulk bar, tabs, breadcrumbs,
   dropdown, avatar, dropzone, sortable list, rich-text chrome, badges, modals, toasts,
   alerts, pagination, empty states, prose).
4. **Performance budget (§7)** — near-zero public JS (Alpine islands only), purged
   Tailwind, lazy editor, responsive images. **Lighthouse ≥95 mobile (Perf/SEO/A11y/BP)
   must be measured with a real run** — requires a served app + Lighthouse; flagged as
   needing an environment that can run headless Chrome against MySQL-backed app. Do not
   claim the score without the run.
5. **A11y (§8)** — WCAG 2.1 AA: landmarks, focus-visible, contrast (token set already
   compliant), keyboard nav, reduced-motion. Verify per screen.

> **Honesty note:** the full UI redesign + measured Lighthouse is the single largest piece
> and spans multiple work sessions. It is sequenced after refactor + tooling + parity
> primitives so the design system lands on a stable base.

---

## 4. Tests (FEATURE_MATRIX §22 / prompt Task 4)

- Keep the 170-test baseline green at every step.
- **Characterization tests before each refactor** (pin current behavior).
- Targets: **≥80% line coverage on services/repositories**, **100% of critical request
  paths** (auth, content CRUD, publishing, media). Add coverage reporting (Xdebug/PCOV).
- Feature/integration test per refactored controller; regression test for every
  adversarial finding.
- Run full suite **with coverage** and show real output — never assert passing without it.

---

## 5. Execution sequence (this is the order of slices)

1. ✅ `REFACTOR_PLAN.md` (this doc).
2. **Slice A — Auth service extraction** (Task 2 core). TDD: characterization tests for
   social-login linking + register, then extract to `SocialAuthService`, controllers thin.
   Plan: `docs/superpowers/plans/2026-06-24-auth-service-extraction.md`.
3. **Slice B — Quality tooling**: Larastan + Pint + coverage config; make suite + static
   analysis green.
4. **Slice C — Parity primitives** P5 (comment-notification via Event/Listener) and P7
   (RSS feeds) — both self-contained and fully headless-testable.
5. **Slice D+** — remaining parity (P1–P4, P6, P8, P9) per priority.
6. **Slice UI** — DESIGN_SYSTEM rollout (tokens → fonts → components → budget/a11y),
   measured Lighthouse.
7. **README** rewrite + **completeness-critic** pass.

Each slice: TDD → run suite (show output) → 2–3 independent adversarial skeptics
(behavior-preservation / correctness / security / performance) → fix → re-verify →
refresh `HANDOFF.md`.

---

## 1b. HARD RULE (prompt update) — ZERO business logic / data access in controllers

The prompt now mandates (top priority): every controller in `app/Http/Controllers`
is a **pure HTTP boundary** — read/validate (Form Request) → call a **service** → map
result to response. **No** repository calls, Eloquent/query-builder/raw SQL, domain
conditionals, multi-step orchestration, or result→meaning mapping in controllers. Chain is
strictly **Controller → Service → Repository**. Adversarial verification must reject any
controller still holding logic/data access.

### Full controller audit (drives the refactor)

The two base controllers do the data access for nearly the whole app:
- `CPanelBaseController` — `create/update/edit/delete/restore/destroy/deleteAjax/destroyAjax`
  all call `$this->repository->…` and map results to `green/red`/echo-trans. → retarget at a
  **`BaseCrudService`** (returns domain results, not redirects). Cleans inherited violations
  in every admin controller's `parent::*` calls at once.
- `BaseController` (front) — `index/modifyTranslatedSlug/setLang` do repo `getBy` + slug/locale
  orchestration. → **`ContentResolverService`** + **`LocaleService`**. Cleans front
  `PostController/PageController/CategoryController` `index()` at once.

| Controller | Violating methods | Owning service |
|---|---|---|
| CPanelBaseController | 8 (all CRUD + ajax + setLang) | **BaseCrudService** + LocaleService |
| BaseController (front) | 3 (index, modifyTranslatedSlug, setLang) | **ContentResolverService** + LocaleService |
| CPanelPostController | 8 | CPanelPostService |
| CPanelCommentController | 5 | CPanelCommentService |
| CPanelCategoryController | 5 | CPanelCategoryService |
| CPanelMenuController | 5 (incl. slug-mutation rule) | CPanelMenuService |
| CPanelPageController | 4 | CPanelPageService |
| CPanelUserController | 4 | CPanelUserService |
| CPanelRoleController | 4 (3 inherited + index) | CPanelRoleService |
| CPanelHomeController | 4 (raw Eloquent: Post/User/Comments L34/40/46) | CPanelDashboardService |
| CPanelSeoSettingsController | 2 (raw firstOrNew→fill→save) | SeoSettingsService |
| CPanelGeoSettingsController | 2 (raw firstOrNew→fill→save) | GeoSettingsService |
| CPanelGeneralSettingController | 2 | GeneralSettingsService |
| CPanelSiteOptionsController | 2 | SiteOptionsService |
| CPanelLanguageController | 1 | LocaleService |
| CPanelMediaController | 0 (drop unused import) | — |
| SeoController | 5 (raw Eloquent: Page/Post/Category joins L48-62, L234) | SeoFeedService |
| PostController | 2 (index inherited, handleLike) | PostViewService + PostLikeService |
| PageController | 4 (index, sendMail, searchResult, paginatedResult) | PageViewService + ContactService + SearchService |
| PostCommentController | 3 (store/delete/update) | CommentService |
| UserController | 4 (yourProfile/update/changePassword/show) | ProfileService |
| CategoryController | 1 (index) | CategoryViewService |
| Auth/* | 0 — COMPLIANT (SocialAuthService, UserRegistrationService) | done |

### Service-layer design
`App\Services\BaseCrudService` wraps a `BaseRepositoryInterface` and exposes
`list($perPage) / getById($id) / create($request) / update($id,$data) / delete($ids) /
destroy($ids) / restore($ids)` returning **domain results** (entity/bool/paginator), never
redirects/views. Each domain service `extends BaseCrudService` and passes its repository to
`parent::__construct()`, adding domain methods (bulk actions, approve/unapprove, settings
singleton save, slug rules). Controllers inject the **domain service** (not the repository),
`CPanelBaseController` holds `protected $service` and delegates; response mapping
(redirect/`green|red`/echo-trans/json/view) stays in the controller.

### Execution order (Slice C)
1. **C1 (foundation):** `BaseCrudService`; retarget `CPanelBaseController` → `$this->service`.
2. **C2 (front foundation):** `ContentResolverService` + `LocaleService`; retarget `BaseController`.
3. **C3:** posts + comments controllers (admin + front).
4. **C4:** pages, categories, menu, users, roles.
5. **C5:** settings (Seo/Geo/General/SiteOptions/Language), CPanelHomeController, SeoController.
Each slice: characterization test → refactor → suite green (shown) → adversarial skeptic →
fix → commit.

---

## 6. Status log

- 2026-06-24: Audit complete; baseline 170 green; plan written.
- 2026-06-24: Slice A (auth service extraction) done + adversarially verified; fixed a
  latent double-hash bug (register + password reset). Suite 182 green.
- 2026-06-24: Slice B (Pint + Larastan level5/baseline + composer scripts) done; analyse green.
- 2026-06-24: Prompt updated with the ZERO-logic-in-controllers hard rule; full controller
  audit added above. Next: Slice C1 (BaseCrudService + CPanelBaseController).
