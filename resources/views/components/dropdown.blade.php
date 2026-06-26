@props(['align' => 'right'])

@php
$panelAlign = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div
    x-data="{
        open: false,
        triggerEl: null,
        init() {
            this.triggerEl = this.$el.querySelector('[aria-haspopup=\'menu\']');
        },
        toggle() { this.open = !this.open; },
        close() {
            this.open = false;
            if (this.triggerEl) this.triggerEl.focus();
        },
        handleKeydown(event) {
            if (!this.open) return;
            const items = Array.from(this.$el.querySelectorAll('[role=\'menuitem\']'));
            const idx = items.indexOf(document.activeElement);
            if (event.key === 'Escape') {
                event.preventDefault();
                this.close();
            } else if (event.key === 'ArrowDown') {
                event.preventDefault();
                const next = items[idx + 1] || items[0];
                if (next) next.focus();
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                const prev = items[idx - 1] || items[items.length - 1];
                if (prev) prev.focus();
            } else if (event.key === 'Home') {
                event.preventDefault();
                if (items[0]) items[0].focus();
            } else if (event.key === 'End') {
                event.preventDefault();
                if (items[items.length - 1]) items[items.length - 1].focus();
            }
        }
    }"
    x-on:keydown="handleKeydown($event)"
    x-on:click.outside="open && close()"
    class="relative inline-block"
    {{ $attributes }}
>
    {{-- Trigger slot: should render a <button> with aria-haspopup="menu" --}}
    <div
        x-on:click="toggle()"
        x-bind:aria-expanded="open.toString()"
    >
        {{ $trigger }}
    </div>

    {{-- Dropdown panel --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-[var(--ease-out)] duration-[var(--dur-base)]"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-[var(--dur-fast)]"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        role="menu"
        aria-orientation="vertical"
        class="absolute z-50 mt-1 min-w-[10rem] {{ $panelAlign }} bg-surface border border-border rounded-md shadow-lift py-1 focus:outline-none motion-reduce:transition-none"
        x-on:keydown.escape.prevent="close()"
    >
        {{ $slot }}
    </div>
</div>
