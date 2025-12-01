@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 rounded-md bg-green-50 border border-green-200 px-2.5 py-1.5 text-xs font-medium text-green-700']) }}>
        <svg class="h-3.5 w-3.5 flex-shrink-0 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span>{{ $status }}</span>
    </div>
@endif
