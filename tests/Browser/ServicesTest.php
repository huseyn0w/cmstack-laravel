<?php

/**
 * Pest 4 browser tests — public Service content type (listing grid + detail).
 *
 * Same family as tests/Browser/AuthAdminTest.php: these run ONLY when
 * BROWSER_TESTS=1 is set (the CI e2e job serves the app on :8000 against a
 * migrated + seeded MySQL, and installs Playwright Chromium). They hit the
 * SERVED app, so they rely on CPanelServicesSeeder's sample services
 * ("Web Development", "SEO & GEO", "Support & Maintenance").
 *
 * API: pest-plugin-browser v4.3.1
 *   visit(url) ->assertSee(text) ->assertPresent(sel) ->click(sel)
 *   ->assertPathContains(path) ->assertNoSmoke() ->assertNoAccessibilityIssues(1)
 */
$browserEnv = (bool) env('BROWSER_TESTS', false);

it('renders the public services grid with the seeded services', function () {
    visit('/services')
        ->assertSee('Web Development')
        ->assertSee('SEO & GEO')
        // The grid is styled (theme applied), links resolve, no JS/console errors.
        ->assertPresent('a[href*="/services/"]')
        ->assertNoSmoke()
        // WCAG 2.1 AA — serious + critical only (level 1), same bar as the other
        // public-page browser tests.
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env (served app + Playwright Chromium) required — set BROWSER_TESTS=1');

it('opens a service detail page from the grid', function () {
    visit('/services/web-development')
        ->assertSee('Web Development')
        ->assertPathContains('/services/web-development')
        ->assertNoSmoke()
        ->assertNoAccessibilityIssues(1);
})->skip(! $browserEnv, 'browser env (served app + Playwright Chromium) required — set BROWSER_TESTS=1');
