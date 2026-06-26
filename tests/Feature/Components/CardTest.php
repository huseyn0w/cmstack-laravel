<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders with base surface/border/rounded-lg classes', function () {
    $html = Blade::render('<x-card>Body</x-card>');
    expect($html)
        ->toContain('bg-surface')
        ->toContain('border-border')
        ->toContain('rounded-lg')
        ->toContain('p-6')
        ->toContain('Body');
});

it('renders header slot when provided', function () {
    // Use @component/@slot syntax (not x-slot) to avoid output-buffer leakage in PHPUnit.
    $html = Blade::render(
        '@component("components.card") @slot("header") My Header @endslot Content @endcomponent'
    );
    expect($html)->toContain('My Header')->toContain('Content');
});

it('renders footer slot when provided', function () {
    $html = Blade::render(
        '@component("components.card") @slot("footer") Footer text @endslot Main @endcomponent',
    );
    expect($html)->toContain('Footer text')->toContain('Main');
});

it('does not render header/footer markup when slots are absent', function () {
    $html = Blade::render('<x-card>Only body</x-card>');
    expect($html)->not->toContain('header')->not->toContain('footer');
});

it('adds hover and transition classes when interactive', function () {
    $html = Blade::render('<x-card :interactive="true">Clickable</x-card>');
    expect($html)
        ->toContain('hover:border-strong')
        ->toContain('hover:shadow-card')
        ->toContain('transition');
});

it('does not add hover classes when not interactive', function () {
    $html = Blade::render('<x-card>Static</x-card>');
    expect($html)->not->toContain('hover:border-strong');
});

it('spreads extra attributes onto root element', function () {
    $html = Blade::render('<x-card id="my-card" data-testid="card">Body</x-card>');
    expect($html)->toContain('id="my-card"')->toContain('data-testid="card"');
});
