<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders role=dialog on the panel', function () {
    $html = Blade::render('<x-modal name="test-modal">Body content</x-modal>');
    expect($html)->toContain('role="dialog"');
});

it('renders aria-modal=true', function () {
    $html = Blade::render('<x-modal name="confirm">Confirm?</x-modal>');
    expect($html)->toContain('aria-modal="true"');
});

it('renders title with h2 and aria-labelledby when title prop given', function () {
    $html = Blade::render('<x-modal name="edit" title="Edit Post">Body</x-modal>');
    expect($html)
        ->toContain('aria-labelledby="modal-title-edit"')
        ->toContain('id="modal-title-edit"')
        ->toContain('<h2')
        ->toContain('Edit Post');
});

it('uses font-serif class on the title', function () {
    $html = Blade::render('<x-modal name="my-modal" title="My Title">Body</x-modal>');
    expect($html)->toContain('font-serif');
});

it('renders x-cloak on the panel', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)->toContain('x-cloak');
});

it('includes Alpine x-data for open/close state', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)->toContain('x-data');
});

it('listens for open-modal and close-modal window events', function () {
    $html = Blade::render('<x-modal name="delete-confirm">Delete?</x-modal>');
    expect($html)
        ->toContain('open-modal')
        ->toContain('close-modal');
});

it('closes on Escape key', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)->toContain('keydown.escape');
});

it('renders the scrim with bg-black/45', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)->toContain('bg-black/45');
});

it('renders scrim click handler to close', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    // The scrim div should have an x-on:click binding that calls hide()
    expect($html)->toContain('hide()');
});

it('renders default md size as max-w-[480px]', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)->toContain('max-w-[480px]');
});

it('renders lg size as max-w-[640px]', function () {
    $html = Blade::render('<x-modal name="test" size="lg">Content</x-modal>');
    expect($html)->toContain('max-w-[640px]');
});

it('renders bg-surface and rounded-lg on the dialog panel', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)
        ->toContain('bg-surface')
        ->toContain('rounded-lg');
});

it('renders close button with aria-label when title is given', function () {
    $html = Blade::render('<x-modal name="test" title="Test">Content</x-modal>');
    expect($html)->toContain('aria-label="Close dialog"');
});

it('renders footer slot when provided', function () {
    $html = Blade::render(
        '@component("components.modal", ["name" => "test", "title" => "Confirm"])
            @slot("footer")
                <button>Cancel</button>
                <button>Confirm</button>
            @endslot
            Are you sure?
        @endcomponent'
    );
    expect($html)
        ->toContain('Cancel')
        ->toContain('Confirm')
        ->toContain('Are you sure?');
});

it('includes focus-trap logic (tab key handler)', function () {
    $html = Blade::render('<x-modal name="test">Content</x-modal>');
    expect($html)->toContain('trapFocus');
});

it('passes the name to window event listeners', function () {
    $html = Blade::render('<x-modal name="my-unique-modal">Content</x-modal>');
    expect($html)->toContain('my-unique-modal');
});
