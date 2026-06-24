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

## 6. Status log

- 2026-06-24: Audit complete; baseline 170/170 green; this plan written. Next: Slice A.
