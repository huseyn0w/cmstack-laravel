{{--
    Toast region — the live-region container that the adminToast() runtime
    (resources/js/admin.js) targets via the `.toast-stack` selector.

    The admin.js ensureToastStack() function looks for `.toast-stack` in the DOM
    and appends to it if found, or creates one if absent. By placing this component
    in the layout, we pre-register the container so toasts always appear here,
    bottom-right, stacked, with correct ARIA attributes.

    For the front-end a compatible window.toast() can be wired similarly.

    Usage:
        Place once per layout (admin or front), typically just before </body>:
        <x-toast-region />

    Styling of individual toast items (.toast-item) is handled by admin.css /
    app.css. The component itself only provides the stack container.
--}}

<div
    class="toast-stack fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 w-[clamp(280px,90vw,400px)] pointer-events-none"
    role="status"
    aria-live="polite"
    aria-atomic="false"
    aria-label="Notifications"
>
    {{-- Toast items are injected here by adminToast() / window.toast() at runtime --}}
    {{-- Static toast items can also be pre-rendered in this slot (e.g. flash messages) --}}
    {{ $slot ?? '' }}
</div>

<style>
    /*
     * Toast item styles — co-located for portability.
     * Mirrors the DESIGN_SYSTEM §5 Toasts spec:
     *   bg-surface | 1px border-border | rounded-md | shadow-card
     *   leading icon | text-sm | auto-dismiss (errors persist)
     *   slide+fade in; respects prefers-reduced-motion
     */
    .toast-stack .toast-item {
        pointer-events: auto;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-md);
        box-shadow: 0 2px 8px 0 rgb(0 0 0 / 0.08), 0 0 0 1px rgb(0 0 0 / 0.03);
        font-family: var(--font-sans, inherit);
        font-size: 0.875rem;
        color: var(--text);
        cursor: pointer;
        animation: toast-slide-in var(--dur-base, 200ms) var(--ease-out, ease-out) both;
    }

    .toast-stack .toast-item.is-leaving {
        animation: toast-fade-out var(--dur-fast, 120ms) ease-in both;
    }

    .toast-stack .toast-item.toast-success { border-color: var(--success); }
    .toast-stack .toast-item.toast-error   { border-color: var(--error); }
    .toast-stack .toast-item.toast-info    { border-color: var(--border); }

    @keyframes toast-slide-in {
        from { opacity: 0; transform: translateX(1rem); }
        to   { opacity: 1; transform: translateX(0); }
    }

    @keyframes toast-fade-out {
        from { opacity: 1; transform: scale(1); }
        to   { opacity: 0; transform: scale(0.96); }
    }

    @media (prefers-reduced-motion: reduce) {
        .toast-stack .toast-item,
        .toast-stack .toast-item.is-leaving {
            animation: none;
            transition: none;
        }
    }
</style>
