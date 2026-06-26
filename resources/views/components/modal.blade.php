@props([
    'name',
    'title' => null,
    'size'  => 'md',
])

@php
$maxWidth = match($size) {
    'lg'    => 'max-w-[640px]',
    'sm'    => 'max-w-sm',
    default => 'max-w-[480px]',  // md
};
$titleId = 'modal-title-' . $name;
@endphp

<div
    x-data="{
        open: false,
        triggerEl: null,
        name: '{{ $name }}',
        init() {
            window.addEventListener('open-modal', (e) => {
                if (e.detail === this.name) this.show();
            });
            window.addEventListener('close-modal', (e) => {
                if (e.detail === this.name) this.hide();
            });
        },
        show(triggerEl = null) {
            if (triggerEl) this.triggerEl = triggerEl;
            this.open = true;
            this.$nextTick(() => {
                const firstFocusable = this.$refs.panel?.querySelector(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex=\'-1\'])'
                );
                if (firstFocusable) firstFocusable.focus();
            });
        },
        hide() {
            this.open = false;
            if (this.triggerEl) {
                this.triggerEl.focus();
                this.triggerEl = null;
            }
        },
        trapFocus(event) {
            if (!this.open) return;
            const panel = this.$refs.panel;
            if (!panel) return;
            const focusable = Array.from(panel.querySelectorAll(
                'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex=\'-1\'])'
            )).filter(el => !el.closest('[x-cloak]'));
            if (!focusable.length) return;
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey) {
                if (document.activeElement === first) {
                    event.preventDefault();
                    last.focus();
                }
            } else {
                if (document.activeElement === last) {
                    event.preventDefault();
                    first.focus();
                }
            }
        }
    }"
    x-on:keydown.escape.window="open && hide()"
    x-on:keydown.tab.window="trapFocus($event)"
>
    {{-- Scrim --}}
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-[var(--ease-out)] duration-[var(--dur-slow)]"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-[var(--dur-base)]"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 bg-black/45 backdrop-blur-[2px] motion-reduce:transition-none"
        aria-hidden="true"
        x-on:click="hide()"
    ></div>

    {{-- Dialog panel --}}
    <div
        x-show="open"
        x-cloak
        x-ref="panel"
        x-transition:enter="transition ease-[var(--ease-out)] duration-[var(--dur-slow)]"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-[var(--dur-base)]"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        role="dialog"
        aria-modal="true"
        @if($title) aria-labelledby="{{ $titleId }}" @endif
        class="fixed inset-0 z-50 flex items-center justify-center p-4 motion-reduce:transition-none"
    >
        <div class="relative w-full {{ $maxWidth }} bg-surface rounded-lg shadow-lift flex flex-col max-h-[90vh]">
            {{-- Header --}}
            @if($title)
                <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-border shrink-0">
                    <h2 id="{{ $titleId }}" class="font-serif text-xl text-fg leading-tight">{{ $title }}</h2>
                    <button
                        type="button"
                        x-on:click="hide()"
                        aria-label="Close dialog"
                        class="shrink-0 rounded text-muted hover:text-fg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 transition-colors duration-[var(--dur-fast)]"
                    >
                        <x-icon name="x" width="20" height="20" aria-hidden="true" />
                    </button>
                </div>
            @endif

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto px-6 py-5">
                {{ $slot }}
            </div>

            {{-- Footer --}}
            @isset($footer)
                <div class="shrink-0 px-6 py-4 border-t border-border flex items-center justify-end gap-3">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
