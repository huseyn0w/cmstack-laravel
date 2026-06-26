@props(['tabs' => [], 'default' => null])

{{--
    Array API (canonical use — per-locale content editing):

        <x-tabs :tabs="['en' => 'English', 'ru' => 'Russian']" default="en">
            <x-slot:panel_en>
                English content here
            </x-slot:panel_en>
            <x-slot:panel_ru>
                Russian content here
            </x-slot:panel_ru>
        </x-tabs>

    Pass panel content as named slots using the convention "panel_{key}".
    Each key must match a key in the :tabs array.
--}}

@php
$tabKeys  = array_keys($tabs);
$firstTab = $default ?? ($tabKeys[0] ?? '');
@endphp

<div
    x-data="{ active: '{{ $firstTab }}' }"
    {{ $attributes }}
>
    {{-- Tab strip --}}
    <div
        role="tablist"
        aria-orientation="horizontal"
        class="flex border-b border-border"
        x-on:keydown="
            const allTabs = Array.from(\$el.querySelectorAll('[role=\'tab\']'));
            const currentIdx = allTabs.indexOf(document.activeElement);
            if (\$event.key === 'ArrowRight') {
                \$event.preventDefault();
                (allTabs[currentIdx + 1] || allTabs[0]).focus();
            } else if (\$event.key === 'ArrowLeft') {
                \$event.preventDefault();
                (allTabs[currentIdx - 1] || allTabs[allTabs.length - 1]).focus();
            } else if (\$event.key === 'Home') {
                \$event.preventDefault();
                allTabs[0].focus();
            } else if (\$event.key === 'End') {
                \$event.preventDefault();
                allTabs[allTabs.length - 1].focus();
            }
        "
    >
        @foreach($tabs as $key => $label)
            <button
                role="tab"
                type="button"
                id="tab-btn-{{ $key }}"
                aria-controls="tab-panel-{{ $key }}"
                :aria-selected="active === '{{ $key }}' ? 'true' : 'false'"
                :tabindex="active === '{{ $key }}' ? 0 : -1"
                x-on:click="active = '{{ $key }}'"
                :class="active === '{{ $key }}'
                    ? 'text-fg border-b-2 border-primary -mb-px font-medium'
                    : 'text-muted hover:text-fg border-b-2 border-transparent -mb-px'"
                class="px-4 py-2.5 text-sm font-sans transition-colors duration-[var(--dur-fast)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-inset whitespace-nowrap"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Panels (named slot per key: panel_{key}) --}}
    @foreach($tabs as $key => $label)
        @php $slotName = 'panel_' . $key; @endphp
        <div
            role="tabpanel"
            id="tab-panel-{{ $key }}"
            aria-labelledby="tab-btn-{{ $key }}"
            x-show="active === '{{ $key }}'"
            class="focus:outline-none"
            tabindex="0"
        >
            @isset($$slotName)
                {{ $$slotName }}
            @endisset
        </div>
    @endforeach

    {{-- Slot-only API: when no :tabs prop, render the default slot directly
         (used by x-tab children approach or simple wrappers). --}}
    @if(empty($tabs))
        {{ $slot }}
    @endif
</div>
