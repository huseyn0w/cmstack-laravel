@props(['name', 'label'])

{{--
    Used as a child of <x-tabs> when not using the :tabs array API.
    Example:
        <x-tabs>
            <x-tab name="overview" label="Overview">Overview content</x-tab>
            <x-tab name="settings" label="Settings">Settings content</x-tab>
        </x-tabs>

    Note: when using this component, each x-tab renders its own tab button
    and panel using the parent x-tabs x-data context (active).
--}}

{{--
    Tab button (rendered into the tablist via CSS ordering).
    We use a self-contained approach: each x-tab renders a section
    wrapping its button+panel so the tablist + panels stay accessible.
--}}

<template x-if="true">
    <div class="contents">
        {{-- The tab button is placed first via CSS; the panel follows --}}
        <button
            role="tab"
            type="button"
            id="tab-btn-{{ $name }}"
            aria-controls="tab-panel-{{ $name }}"
            :aria-selected="$root.active === '{{ $name }}' ? 'true' : 'false'"
            :tabindex="$root.active === '{{ $name }}' ? 0 : -1"
            x-on:click="$root.active = '{{ $name }}'"
            :class="$root.active === '{{ $name }}'
                ? 'text-fg border-b-2 border-primary -mb-px font-medium'
                : 'text-muted hover:text-fg border-b-2 border-transparent -mb-px'"
            class="px-4 py-2.5 text-sm font-sans transition-colors duration-[var(--dur-fast)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset whitespace-nowrap"
        >
            {{ $label }}
        </button>

        <div
            role="tabpanel"
            id="tab-panel-{{ $name }}"
            aria-labelledby="tab-btn-{{ $name }}"
            x-show="$root.active === '{{ $name }}'"
            class="focus:outline-none col-span-full"
            tabindex="0"
        >
            {{ $slot }}
        </div>
    </div>
</template>
