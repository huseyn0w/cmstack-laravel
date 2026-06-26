<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders a container with the trigger and menu panel', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Open</button></x-slot:trigger>
            <x-dropdown.item>Item 1</x-dropdown.item>
        </x-dropdown>'
    );
    expect($html)
        ->toContain('aria-haspopup="menu"')
        ->toContain('Open');
});

it('renders the panel with role=menu', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
            <x-dropdown.item>Action</x-dropdown.item>
        </x-dropdown>'
    );
    expect($html)->toContain('role="menu"');
});

it('renders dropdown items with role=menuitem', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
            <x-dropdown.item>Edit</x-dropdown.item>
            <x-dropdown.item>Delete</x-dropdown.item>
        </x-dropdown>'
    );
    expect($html)
        ->toContain('role="menuitem"')
        ->toContain('Edit')
        ->toContain('Delete');
});

it('renders x-cloak on the panel', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)->toContain('x-cloak');
});

it('binds aria-expanded on the trigger wrapper', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)->toContain('aria-expanded');
});

it('includes Alpine x-data for open/close state', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)->toContain('x-data');
});

it('includes click-outside close binding', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)->toContain('click.outside');
});

it('includes keydown handler for arrow navigation and Escape', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
            <x-dropdown.item>Item</x-dropdown.item>
        </x-dropdown>'
    );
    expect($html)
        ->toContain('ArrowDown')
        ->toContain('ArrowUp')
        ->toContain('Escape');
});

it('applies surface and border classes to the panel', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)
        ->toContain('bg-surface')
        ->toContain('border-border')
        ->toContain('rounded-md')
        ->toContain('shadow-lift');
});

it('renders dropdown item as a link when href is provided', function () {
    $html = Blade::render('<x-dropdown.item href="/edit">Edit</x-dropdown.item>');
    expect($html)
        ->toContain('<a')
        ->toContain('href="/edit"')
        ->toContain('Edit');
});

it('renders dropdown item as a button when no href', function () {
    $html = Blade::render('<x-dropdown.item>Delete</x-dropdown.item>');
    expect($html)
        ->toContain('<button')
        ->toContain('Delete');
});

it('renders destructive item with text-error class', function () {
    $html = Blade::render('<x-dropdown.item :destructive="true">Delete</x-dropdown.item>');
    expect($html)->toContain('text-error');
});

it('renders item hover bg-surface-2 class', function () {
    $html = Blade::render('<x-dropdown.item>Item</x-dropdown.item>');
    expect($html)->toContain('hover:bg-surface-2');
});

it('aligns panel to the left when align=left', function () {
    $html = Blade::render(
        '<x-dropdown align="left">
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)->toContain('left-0');
});

it('aligns panel to the right by default', function () {
    $html = Blade::render(
        '<x-dropdown>
            <x-slot:trigger><button aria-haspopup="menu">Menu</button></x-slot:trigger>
        </x-dropdown>'
    );
    expect($html)->toContain('right-0');
});
