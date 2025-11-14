@props(['items' => []])

@php
    $formattedItems = collect($items)
        ->map(function ($item, $index) {
            $item = is_array($item) ? $item : ['title' => $item];

            return [
                'title' => $item['title'] ?? $item['label'] ?? __('Menu :number', ['number' => $index + 1]),
                'url' => $item['url'] ?? null,
            ];
        })
        ->filter(fn ($item) => filled($item['title']))
        ->values();

    if ($formattedItems->isEmpty()) {
        $segments = Request::segments();
        $formattedItems = collect($segments)->map(function ($segment, $index) use ($segments) {
            $title = ucwords(str_replace('-', ' ', $segment));
            $isLast = $index === array_key_last($segments);

            return [
                'title' => $title,
                'url' => $isLast ? null : url(implode('/', array_slice($segments, 0, $index + 1))),
            ];
        });
    }

    $homeUrl = Route::has('dashboard') ? route('dashboard') : url('/');
    $chipBase = 'inline-flex items-center gap-2 px-4 py-2 text-[13px] font-semibold rounded-xl transition';
    $chipLink = $chipBase . ' text-slate-500 border border-transparent hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-200';
    $chipCurrent = $chipBase . ' text-slate-800 bg-slate-50 border border-slate-200';
@endphp

<nav aria-label="Breadcrumb" class="mb-6">
    <ol class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
        <li>
            <a href="{{ $homeUrl }}" class="{{ $chipBase }} text-emerald-700 bg-emerald-50 border border-emerald-100 hover:bg-emerald-100">
                <i class="fas fa-house text-[11px]"></i>
                <span>Dashboard</span>
            </a>
        </li>

        @foreach ($formattedItems as $item)
            <li class="text-slate-300">
                <i class="fas fa-chevron-right text-[10px]"></i>
            </li>
            <li>
                @php
                    $isLast = $loop->last || empty($item['url']);
                @endphp

                @if ($isLast)
                    <span class="{{ $chipCurrent }}">
                        {{ $item['title'] }}
                    </span>
                @else
                    <a href="{{ $item['url'] }}" class="{{ $chipLink }}">
                        {{ $item['title'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
