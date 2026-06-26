<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders a tablist with role=tablist', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('role="tablist"');
});

it('renders tab buttons with role=tab', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('role="tab"');
});

it('renders tab panels with role=tabpanel', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('role="tabpanel"');
});

it('renders aria-selected on tab buttons', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('aria-selected');
});

it('renders aria-controls linking tabs to panels', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)
        ->toContain('aria-controls')
        ->toContain('tab-panel-en')
        ->toContain('tab-panel-ru');
});

it('renders aria-labelledby on panels', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('aria-labelledby');
});

it('renders tab labels from the tabs prop', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)
        ->toContain('English')
        ->toContain('Russian');
});

it('includes Alpine x-data for active tab state', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\']"></x-tabs>'
    );
    expect($html)->toContain('x-data');
});

it('uses the first tab key as default active tab', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    // The active state should initialize to the first key 'en'
    expect($html)->toContain("active: 'en'");
});

it('respects the default prop to set initial active tab', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']" default="ru"></x-tabs>'
    );
    expect($html)->toContain("active: 'ru'");
});

it('includes arrow key navigation bindings', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)
        ->toContain('ArrowRight')
        ->toContain('ArrowLeft');
});

it('includes focus-visible ring class for keyboard navigation', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\']"></x-tabs>'
    );
    expect($html)->toContain('focus-visible:ring-2');
});

it('applies border-primary class for active tab underline', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\']"></x-tabs>'
    );
    expect($html)->toContain('border-primary');
});

it('renders tablist with border-b border-border', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\']"></x-tabs>'
    );
    expect($html)
        ->toContain('border-b')
        ->toContain('border-border');
});

it('uses text-muted for inactive tabs', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('text-muted');
});

it('renders x-show binding on panels to toggle visibility', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\', \'ru\' => \'Russian\']"></x-tabs>'
    );
    expect($html)->toContain('x-show');
});

it('renders panel id and tab id for aria linkage', function () {
    $html = Blade::render(
        '<x-tabs :tabs="[\'en\' => \'English\']"></x-tabs>'
    );
    expect($html)
        ->toContain('id="tab-btn-en"')
        ->toContain('id="tab-panel-en"');
});
