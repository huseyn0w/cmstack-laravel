<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders a container with role=status', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('role="status"');
});

it('renders aria-live=polite for polite announcements', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('aria-live="polite"');
});

it('renders aria-atomic=false to allow individual toast announcements', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('aria-atomic="false"');
});

it('renders the toast-stack class so adminToast() can target it', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('toast-stack');
});

it('is positioned fixed at bottom-right', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)
        ->toContain('fixed')
        ->toContain('bottom-4')
        ->toContain('right-4');
});

it('has a high z-index for layering above other content', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('z-[9999]');
});

it('renders pointer-events-none on the stack container', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('pointer-events-none');
});

it('includes CSS for toast-item with surface background and border', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)
        ->toContain('var(--surface)')
        ->toContain('var(--border)');
});

it('includes keyframe animation for slide-in', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('toast-slide-in');
});

it('includes CSS for is-leaving fade-out animation', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('is-leaving');
});

it('includes reduced-motion media query to disable animations', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('prefers-reduced-motion');
});

it('includes border-radius token reference for rounded-md', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('var(--radius-md)');
});

it('includes toast-success error and info style variants', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)
        ->toContain('toast-success')
        ->toContain('toast-error')
        ->toContain('toast-info');
});

it('renders aria-label for screen readers', function () {
    $html = Blade::render('<x-toast-region />');
    expect($html)->toContain('aria-label="Notifications"');
});
