@props([
    'name',
    'show' => false,
    'maxWidth' => '2xl',
    'initialFocus' => null,
])

@php
$maxWidthClass = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth] ?? 'sm:max-w-2xl';
@endphp

<div
    x-data="{
        show: @js($show),
        initialFocus: @js($initialFocus),
        focusables() {
            const selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
            return Array.from($el.querySelectorAll(selector)).filter(el => !el.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0] ?? null; },
        lastFocusable() { return this.focusables().slice(-1)[0] ?? null; },
        nextFocusable() {
            const index = this.focusables().indexOf(document.activeElement);
            return this.focusables()[index + 1] ?? null;
        },
        prevFocusable() {
            const focusableElements = this.focusables();
            const index = focusableElements.indexOf(document.activeElement);
            return focusableElements[index - 1] ?? null;
        },
        handleTab(event) {
            const hasItems = this.focusables().length > 0;
            if (!hasItems) {
                return;
            }

            event.preventDefault();

            if (event.shiftKey) {
                const previous = this.prevFocusable() ?? this.lastFocusable();
                if (previous) {
                    previous.focus();
                }
            } else {
                const next = this.nextFocusable() ?? this.firstFocusable();
                if (next) {
                    next.focus();
                }
            }
        }
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => {
                const initialSelector = this.initialFocus;
                if (initialSelector) {
                    const element = document.querySelector(initialSelector);
                    if (element) {
                        element.focus();
                        return;
                    }
                }
                const first = this.firstFocusable();
                if (first) {
                    first.focus();
                }
            }, 80);' : '' }}
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab="handleTab($event)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[120] flex items-center justify-center px-4 py-6 sm:px-6"
    style="display: {{ $show ? 'flex' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    ></div>

    <div
        x-show="show"
        class="relative w-full max-w-[calc(100vw-2rem)] {{ $maxWidthClass }} sm:w-full transform transition-all"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-6 sm:translate-y-0 sm:scale-95"
    >
        <div class="absolute inset-0 rounded-3xl bg-white/85 backdrop-blur-xl border border-emerald-100 shadow-2xl"></div>
        <div class="absolute inset-x-6 top-0 h-24 bg-linear-to-br from-emerald-100/80 via-transparent to-transparent rounded-b-full pointer-events-none"></div>
        <div class="relative overflow-hidden rounded-3xl">
            {{ $slot }}
        </div>
    </div>
</div>
