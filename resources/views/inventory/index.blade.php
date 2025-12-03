<x-app-layout>
    <x-slot name="header">
        @php
            $totalSku = (int) data_get($inventoryStats, 'total_sku', 0);
            $activeSku = (int) data_get($inventoryStats, 'active_sku', $totalSku);
        @endphp
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_auto] items-start">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl">
                        <i class="fas fa-warehouse"></i>
                    </span>
                    <div>
                        <h1 class="text-2xl font-extrabold text-emerald-900">Inventori Produk</h1>
                        <p class="text-sm text-slate-500">Pantau stok, harga, dan kategori produk dalam satu tampilan.</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3" x-data="{ openQuick: false }">
                @if(in_array(Auth::user()->role ?? '', ['manager', 'admin']))
                    <a href="{{ route('products.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white rounded-2xl shadow bg-gradient-to-r from-emerald-500 to-emerald-600 hover:scale-[1.02]">
                        <i class="fas fa-plus"></i>
                        Produk Baru
                    </a>
                @endif

                <div class="relative z-20" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button type="button" @click="open = !open"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100">
                        <i class="fa-solid fa-bolt"></i>
                        Aksi Cepat
                        <i class="fa-solid" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                    </button>
                    <div x-show="open" x-transition x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-2 w-56 rounded-2xl border border-slate-100 bg-white shadow-xl divide-y divide-slate-50 z-30">
                        <div class="py-2 text-sm">
                            <a href="{{ route('invent_notes') }}" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:bg-emerald-50">
                                <i class="fa-solid fa-book-open"></i>
                                Catatan Inventori
                            </a>
                            @if($products->count() > 0)
                                <a href="{{ route('stock.create') }}" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:bg-emerald-50">
                                    <i class="fa-solid fa-layer-group"></i>
                                    Tambah Stok
                                </a>
                            @endif
                            <a href="{{ route('invent', ['import' => 'csv']) }}" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:bg-emerald-50">
                                <i class="fa-solid fa-file-import"></i>
                                Import CSV
                            </a>
                            @if(Route::has('reports.stock-value'))
                                <a href="{{ route('reports.stock-value') }}" class="flex items-center gap-2 px-4 py-2 text-slate-600 hover:bg-emerald-50">
                                    <i class="fa-solid fa-print"></i>
                                    Cetak Laporan Stok
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                 class="flex items-center gap-3 p-4 text-sm font-semibold text-emerald-800 border border-emerald-200 rounded-2xl bg-emerald-50">
                <span class="inline-flex items-center justify-center w-8 h-8 text-white rounded-full bg-emerald-500">
                    <i class="fas fa-check"></i>
                </span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @php
            $stats = [
                [
                    'label' => 'Total Produk',
                    'value' => number_format((int) data_get($inventoryStats, 'total_sku', 0)),
                    'icon' => 'fa-boxes-stacked',
                    'accent' => 'emerald',
                ],
                [
                    'label' => 'Stok Menipis',
                    'value' => number_format((int) data_get($inventoryStats, 'low_stock', 0)),
                    'icon' => 'fa-triangle-exclamation',
                    'accent' => 'amber',
                ],
                [
                    'label' => 'Stok Habis',
                    'value' => number_format((int) data_get($inventoryStats, 'out_of_stock', 0)),
                    'icon' => 'fa-battery-empty',
                    'accent' => 'rose',
                ],
            ];

            $palette = [
                'emerald' => [
                    'border' => 'border-emerald-200/70',
                    'iconBg' => 'bg-emerald-50',
                    'iconFg' => 'text-emerald-600',
                    'glow' => 'from-emerald-50/70'
                ],
                'amber' => [
                    'border' => 'border-amber-200/70',
                    'iconBg' => 'bg-amber-50',
                    'iconFg' => 'text-amber-600',
                    'glow' => 'from-amber-50/70'
                ],
                'rose' => [
                    'border' => 'border-rose-200/70',
                    'iconBg' => 'bg-rose-50',
                    'iconFg' => 'text-rose-600',
                    'glow' => 'from-rose-50/70'
                ],
            ];

            $searchDataset = $products->map(function($product) {
                return strtolower(trim(($product->name ?? '') . ' ' . ($product->sku ?? '')));
            })->values();

            $filterQuery = [
                'search' => $search,
                'category' => $categoryFilter,
                'stock' => $stockFilter,
                'per_page' => $perPage,
            ];

            $exportQuery = [];
            if ($search) {
                $exportQuery['search'] = $search;
            }
            if ($categoryFilter !== 'all') {
                $exportQuery['category'] = $categoryFilter;
            }
            if ($stockFilter !== 'all') {
                $exportQuery['stock'] = $stockFilter;
            }
        @endphp

    <section class="grid gap-4 grid-cols-1 sm:grid-cols-3">
            @foreach($stats as $stat)
                @php $colors = $palette[$stat['accent']] ?? $palette['emerald']; @endphp
                <article class="relative rounded-3xl border {{ $colors['border'] }} bg-white shadow-[0_15px_35px_-20px_rgba(16,185,129,0.45)]">
                    <div class="flex items-center gap-4 p-6">
                        <span class="inline-flex items-center justify-center w-14 h-14 rounded-2xl {{ $colors['iconBg'] }} {{ $colors['iconFg'] }}">
                            <i class="fa-solid {{ $stat['icon'] }} text-xl"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold tracking-[0.08em] text-slate-500 uppercase">{{ $stat['label'] }}</p>
                            <p class="text-3xl font-black text-slate-900">{{ $stat['value'] }}</p>
                        </div>
                    </div>
                    <div class="h-2 rounded-b-3xl bg-gradient-to-r {{ $colors['glow'] }}"></div>
                </article>
            @endforeach
        </section>

    <section class="p-6 bg-white border border-emerald-100 rounded-2xl" x-data="{ manageModal: null }">
            <form method="GET" action="{{ route('invent') }}" class="grid gap-4 grid-cols-1 md:grid-cols-2 xl:grid-cols-12">
                <div class="md:col-span-2 xl:col-span-12" x-data="searchAssist({ initial: @js($search), dataset: @js($searchDataset) })">
                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Pencarian</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" x-model="term" value="{{ $search }}" placeholder="Cari nama, SKU, atau deskripsi"
                               class="w-full h-11 pl-10 pr-4 text-sm bg-emerald-50/40 border border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                        <div class="absolute inset-y-0 right-3 flex items-center">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-semibold rounded-full"
                                  :class="term ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'"
                                  x-text="term ? `${matchCount} SKU cocok` : `${dataset.length} SKU di daftar ini`"></span>
                        </div>
                    </div>
                    <p class="mt-2 text-[11px] text-slate-400">Tekan Enter untuk menerapkan pencarian. Badge menampilkan estimasi real-time dari daftar yang sedang ditampilkan.</p>
                </div>

                <div class="md:col-span-1 xl:col-span-4">
                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Kategori</label>
                    <select name="category" class="w-full h-11 px-4 text-sm rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 @class([ 'border-emerald-300 bg-emerald-50/60 text-emerald-700' => $categoryFilter !== 'all', 'border-emerald-100 bg-white text-slate-700' => $categoryFilter === 'all'])">
                        <option value="all" {{ $categoryFilter === 'all' ? 'selected' : '' }}>Semua kategori</option>
                        @foreach($category as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryFilter === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-1 xl:col-span-4">
                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Status Stok</label>
                    <select name="stock" class="w-full h-11 px-4 text-sm rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 @class([ 'border-emerald-300 bg-emerald-50/60 text-emerald-700' => $stockFilter !== 'all', 'border-emerald-100 bg-white text-slate-700' => $stockFilter === 'all'])">
                        <option value="all" {{ $stockFilter === 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="safe" {{ $stockFilter === 'safe' ? 'selected' : '' }}>Aman (&gt; 10)</option>
                        <option value="low" {{ $stockFilter === 'low' ? 'selected' : '' }}>Menipis (1-10)</option>
                        <option value="out" {{ $stockFilter === 'out' ? 'selected' : '' }}>Habis (&le; 0)</option>
                    </select>
                </div>

                <div class="md:col-span-1 xl:col-span-4">
                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Data per Halaman</label>
            <select name="per_page"
                class="w-full h-11 px-4 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 @class([ 'border-emerald-300 bg-emerald-50/60 text-emerald-700' => (int)$perPage !== 12, 'border-emerald-100 bg-white text-slate-700' => (int)$perPage === 12])">
                        @foreach($perPageOptions as $option)
                            <option value="{{ $option }}" {{ (int)$perPage === (int)$option ? 'selected' : '' }}>{{ $option }} data</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex flex-wrap items-end gap-2 md:col-span-2 xl:col-span-8">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl shadow bg-gradient-to-r from-emerald-500 to-emerald-600 hover:scale-[1.02]">
                        <i class="fas fa-filter"></i>
                        Terapkan Filter
                    </button>
                    <a href="{{ route('invent') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100">
                        <i class="fas fa-rotate-right"></i>
                        Reset
                    </a>
                    <span class="ml-auto"></span>
                    @php $exportDisabled = $products->count() === 0; @endphp
                    <a
                        @class([
                            'inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl border transition-colors shadow-sm',
                            'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed pointer-events-none' => $exportDisabled,
                            'bg-white text-emerald-700 border-slate-200 hover:border-emerald-300 hover:text-emerald-900' => ! $exportDisabled,
                        ])
                        href="{{ $exportDisabled ? '#' : route('invent.export', $exportQuery) }}"
                        @if(! $exportDisabled) target="_blank" rel="noreferrer" @else aria-disabled="true" @endif
                    >
                        <i class="fa-solid fa-file-arrow-down"></i>
                        Ekspor CSV
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-2 md:col-span-2 xl:col-span-4 text-xs font-semibold">
                    <button type="button" @click="manageModal = 'category'"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-emerald-100 text-emerald-700 bg-emerald-50 hover:bg-emerald-100">
                        <i class="fas fa-layer-group"></i>
                        Kelola Kategori
                    </button>
                    <button type="button" @click="manageModal = 'unit'"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-sky-100 text-sky-700 bg-sky-50 hover:bg-sky-100">
                        <i class="fas fa-ruler-horizontal"></i>
                        Kelola Satuan
                    </button>
                </div>
            </form>

            <template x-if="manageModal !== null">
                <div class="fixed inset-0 z-40 flex items-center justify-center">
                    <div class="absolute inset-0 bg-slate-900/50" @click="manageModal = null"></div>
                    <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-slate-100 p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900" x-text="manageModal === 'category' ? 'Kelola Kategori' : 'Kelola Satuan'"></p>
                                <p class="text-xs text-slate-500">Perubahan akan tersimpan tanpa meninggalkan halaman.</p>
                            </div>
                            <button type="button" @click="manageModal = null" class="text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <div x-show="manageModal === 'category'" x-cloak>
                            <form action="{{ route('store') }}" method="POST" class="space-y-4">
                                @csrf
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold text-slate-500 uppercase">Nama kategori</span>
                                    <input type="text" name="nama-kategori" required
                                           class="w-full h-11 rounded-xl border border-emerald-100 px-4 text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300">
                                </label>
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold text-slate-500 uppercase">Deskripsi (opsional)</span>
                                    <textarea name="deskripsi-kategori" rows="3"
                                              class="w-full rounded-xl border border-emerald-100 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-300"></textarea>
                                </label>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="manageModal = null" class="px-4 py-2 text-sm font-semibold text-slate-500">Batal</button>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-gradient-to-r from-emerald-500 to-emerald-600">
                                        <i class="fas fa-save"></i>
                                        Simpan Kategori
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div x-show="manageModal === 'unit'" x-cloak>
                            <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold text-slate-500 uppercase">Nama satuan</span>
                                    <input type="text" name="name" required
                                           class="w-full h-11 rounded-xl border border-sky-100 px-4 text-sm focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
                                </label>
                                <label class="flex flex-col gap-2">
                                    <span class="text-xs font-semibold text-slate-500 uppercase">Konversi ke satuan dasar</span>
                                    <input type="number" step="0.0001" min="0.0001" name="conversion_to_base" required
                                           class="w-full h-11 rounded-xl border border-sky-100 px-4 text-sm focus:ring-2 focus:ring-sky-300 focus:border-sky-300">
                                </label>
                                <div class="text-[11px] text-slate-400">Contoh: 12 untuk “Lusin” jika satuan dasar adalah pcs.</div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="manageModal = null" class="px-4 py-2 text-sm font-semibold text-slate-500">Batal</button>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-gradient-to-r from-sky-500 to-sky-600">
                                        <i class="fas fa-save"></i>
                                        Simpan Satuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            @php
                $activeFilters = [];
                if($search) $activeFilters[] = ['label' => 'Kata kunci', 'value' => $search];
                if($categoryFilter !== 'all') $activeFilters[] = ['label' => 'Kategori', 'value' => optional($category->firstWhere('id', $categoryFilter))->name ?? 'Custom'];
                if($stockFilter !== 'all') $activeFilters[] = ['label' => 'Stok', 'value' => ucfirst($stockFilter)];
                if((int)$perPage !== 12) $activeFilters[] = ['label' => 'Per halaman', 'value' => $perPage];
            @endphp

            @if(count($activeFilters))
                <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                    <span class="text-slate-400 uppercase tracking-wide">Filter aktif:</span>
                    @foreach($activeFilters as $chip)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700">
                            <strong>{{ $chip['label'] }}:</strong> {{ $chip['value'] }}
                        </span>
                    @endforeach
                </div>
            @endif
        </section>

        @if($products->count() > 0)
            <form action="{{ route('updateAll') }}" method="POST" class="space-y-4" x-data="{ dirtyRows: {} }">
                @csrf
                @method('PUT')

                <div x-show="Object.keys(dirtyRows).length" x-cloak class="sticky top-4 z-10 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm font-semibold text-amber-800 shadow-sm">
                    <span class="inline-flex w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span x-text="`Ada perubahan pada ${Object.keys(dirtyRows).length} produk. Jangan lupa simpan.`"></span>
                </div>

                <div class="space-y-4">
                    @foreach($products as $product)
                        @php
                            $priceAgent = optional($product->prices->firstWhere('customer_type', 'agent'))->price;
                            $priceReseller = optional($product->prices->firstWhere('customer_type', 'reseller'))->price;
                            $priceCustomer = optional($product->prices->firstWhere('customer_type', 'pelanggan'))->price;
                            $stockQty = $product->stock_quantity ?? 0;
                            $isLowStock = $stockQty > 0 && $stockQty <= 10;
                            $isOutStock = $stockQty <= 0;
                            $stockFormatted = number_format(round($stockQty), 0, ',', '.');
                            $statusMeta = [
                                'label' => 'Stok Aman',
                                'icon' => 'fa-circle-check',
                                'wrapper' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            ];
                            if ($isLowStock) {
                                $statusMeta = [
                                    'label' => 'Stok Menipis',
                                    'icon' => 'fa-triangle-exclamation',
                                    'wrapper' => 'bg-amber-50 text-amber-700 border-amber-100',
                                ];
                            } elseif ($isOutStock) {
                                $statusMeta = [
                                    'label' => 'Stok Habis',
                                    'icon' => 'fa-circle-xmark',
                                    'wrapper' => 'bg-rose-50 text-rose-700 border-rose-100',
                                ];
                            }
                        @endphp
                        <div x-data="{ open: false }" class="p-6 border border-emerald-100 rounded-3xl bg-white shadow-sm transition-all" :class="{ 'border-amber-300 ring-2 ring-amber-100/80': $root.dirtyRows[{{ $product->id }}] }">
                            <div class="grid gap-6 lg:grid-cols-3">
                                <div class="space-y-3">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold text-emerald-500">#{{ $product->id }}</p>
                                            <p class="text-lg font-bold text-slate-900">{{ $product->name }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-[11px] font-semibold {{ $statusMeta['wrapper'] }}">
                                                <i class="fa-solid {{ $statusMeta['icon'] }}"></i>
                                                {{ $statusMeta['label'] }}
                                            </span>
                                            <span x-show="$root.dirtyRows[{{ $product->id }}]" x-cloak
                                                  class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Draft
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-layer-group"></i>
                                            {{ $product->category?->name ?? 'Tanpa kategori' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                            SKU: {{ $product->sku ?? '-' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 border border-slate-200">
                                            <i class="fas fa-clock"></i>
                                            {{ optional($product->updated_at)->diffForHumans() ?? '-' }}
                                        </span>
                                    </div>
                                    <input type="hidden" name="produk[{{ $product->id }}][id]" value="{{ $product->id }}">
                                </div>

                                <div class="space-y-2">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Penetapan Harga</p>
                                    <div class="space-y-2">
                         <label class="flex items-center justify-between gap-3 rounded-2xl border border-blue-100 bg-blue-50/70 px-3 py-2 text-xs font-semibold text-blue-700"
                             x-data="currencyField({ initial: @js($priceAgent ?? 0), name: 'produk[{{ $product->id }}][prices][agent]' })">
                                            <span class="flex items-center gap-2"><i class="fa-solid fa-user-tie text-xs"></i> Agent</span>
                                            <div class="relative w-32">
                                                <span class="absolute inset-y-0 left-2 flex items-center text-[10px] font-bold text-slate-400">Rp</span>
                                                <input type="text" inputmode="numeric" x-model="display"
                                                       @focus="selectAll" @input="handleInput($event); $root.dirtyRows[{{ $product->id }}] = true"
                                  value="{{ number_format((int) ($priceAgent ?? 0), 0, ',', '.') }}"
                                  class="h-9 w-full rounded-xl border border-transparent bg-white pl-6 pr-2 text-right text-xs font-bold text-slate-900 focus:border-blue-300 focus:outline-none focus:ring-1 focus:ring-blue-300">
                              <input type="hidden" :name="name" :value="raw" value="{{ (int) ($priceAgent ?? 0) }}">
                                            </div>
                                        </label>
                         <label class="flex items-center justify-between gap-3 rounded-2xl border border-rose-100 bg-rose-50/70 px-3 py-2 text-xs font-semibold text-rose-700"
                             x-data="currencyField({ initial: @js($priceReseller ?? 0), name: 'produk[{{ $product->id }}][prices][reseller]' })">
                                            <span class="flex items-center gap-2"><i class="fa-solid fa-store text-xs"></i> Reseller</span>
                                            <div class="relative w-32">
                                                <span class="absolute inset-y-0 left-2 flex items-center text-[10px] font-bold text-slate-400">Rp</span>
                                                <input type="text" inputmode="numeric" x-model="display"
                                                       @focus="selectAll" @input="handleInput($event); $root.dirtyRows[{{ $product->id }}] = true"
                                  value="{{ number_format((int) ($priceReseller ?? 0), 0, ',', '.') }}"
                                  class="h-9 w-full rounded-xl border border-transparent bg-white pl-6 pr-2 text-right text-xs font-bold text-slate-900 focus:border-rose-300 focus:outline-none focus:ring-1 focus:ring-rose-300">
                              <input type="hidden" :name="name" :value="raw" value="{{ (int) ($priceReseller ?? 0) }}">
                                            </div>
                                        </label>
                         <label class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/70 px-3 py-2 text-xs font-semibold text-emerald-700"
                             x-data="currencyField({ initial: @js($priceCustomer ?? 0), name: 'produk[{{ $product->id }}][prices][pelanggan]' })">
                                            <span class="flex items-center gap-2"><i class="fa-solid fa-users text-xs"></i> Pelanggan</span>
                                            <div class="relative w-32">
                                                <span class="absolute inset-y-0 left-2 flex items-center text-[10px] font-bold text-slate-400">Rp</span>
                                                <input type="text" inputmode="numeric" x-model="display"
                                                       @focus="selectAll" @input="handleInput($event); $root.dirtyRows[{{ $product->id }}] = true"
                                  value="{{ number_format((int) ($priceCustomer ?? 0), 0, ',', '.') }}"
                                  class="h-9 w-full rounded-xl border border-transparent bg-white pl-6 pr-2 text-right text-xs font-bold text-slate-900 focus:border-emerald-300 focus:outline-none focus:ring-1 focus:ring-emerald-300">
                              <input type="hidden" :name="name" :value="raw" value="{{ (int) ($priceCustomer ?? 0) }}">
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3">
                                    <div class="rounded-3xl border border-slate-200 bg-gradient-to-br from-slate-50 to-white p-4 space-y-3">
                                        <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                                            <div class="flex items-center gap-2">
                                                <span>Stok tersedia</span>
                                                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" @focusin="open = true" @focusout="open = false">
                                                    <button type="button" class="text-slate-400 hover:text-emerald-500 focus:outline-none">
                                                        <i class="fa-solid fa-circle-info"></i>
                                                    </button>
                                                    <div x-show="open" x-cloak x-transition.opacity class="absolute z-10 mt-2 w-60 rounded-2xl border border-slate-200 bg-white p-3 text-[11px] font-normal text-slate-600 shadow">
                                                        <p class="font-semibold text-slate-800 mb-1">Batas stok:</p>
                                                        <ul class="space-y-1 list-disc pl-4">
                                                            <li>Aman &gt; 10 unit</li>
                                                            <li>Menipis 1-10 unit</li>
                                                            <li>Habis ≤ 0 unit</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                                                <i class="fa-solid fa-ruler-horizontal"></i>
                                                {{ $product->units?->name ?? '-' }}
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center justify-between gap-3">
                                            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-sm font-semibold border"
                                                  @class([
                                                      'border-emerald-200 text-emerald-700 bg-emerald-50' => !$isLowStock && !$isOutStock,
                                                      'border-amber-200 text-amber-700 bg-amber-50' => $isLowStock,
                                                      'border-rose-200 text-rose-700 bg-rose-50' => $isOutStock,
                                                  ])>
                                                <span class="inline-flex w-2 h-2 rounded-full"
                                                      @class([
                                                          'bg-emerald-500' => !$isLowStock && !$isOutStock,
                                                          'bg-amber-500' => $isLowStock,
                                                          'bg-rose-500' => $isOutStock,
                                                      ])></span>
                                                <i class="fa-solid text-xs"
                                                   @class([
                                                       'fa-circle-check text-emerald-500' => !$isLowStock && !$isOutStock,
                                                       'fa-triangle-exclamation text-amber-500' => $isLowStock,
                                                       'fa-circle-xmark text-rose-500' => $isOutStock,
                                                   ])></i>
                                                {{ $statusMeta['label'] }}
                                            </span>
                                            <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $stockFormatted }}</span>
                                        </div>
                                    </div>
                                    <button type="button"
                                            @click="open = !open"
                                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                        <span>{{ __('Lihat Detail') }}</span>
                                        <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                </div>
                            </div>

                            <div x-show="open" x-cloak class="mt-4">
                                <div class="rounded-3xl border border-emerald-100 bg-emerald-50/40 p-5 space-y-6">
                                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-100 bg-white px-4 py-3">
                                        <div class="text-xs text-slate-500">Detail Produk</div>
                                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700">
                                            <i class="fa-solid fa-circle-info"></i>
                                            Perubahan tersimpan setelah klik Simpan Perubahan
                                        </span>
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-500 uppercase">Nama Produk</span>
                                            <input type="text" name="produk[{{ $product->id }}][name]" value="{{ $product->name }}"
                                                   @input="$root.dirtyRows[{{ $product->id }}] = true"
                                                   class="w-full h-11 rounded-2xl border border-emerald-100 bg-white px-4 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                        </label>
                                        <label class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-500 uppercase">SKU</span>
                                            <input type="text" name="produk[{{ $product->id }}][sku]" value="{{ $product->sku }}"
                                                   @input="$root.dirtyRows[{{ $product->id }}] = true"
                                                   class="w-full h-11 rounded-2xl border border-emerald-100 bg-white px-4 text-sm text-slate-800 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                        </label>
                                    </div>

                                    <div class="rounded-2xl border border-dashed border-emerald-200 bg-white/80 p-4 text-[11px] text-slate-500">
                                        Penetapan harga dapat langsung diubah dari kartu utama tanpa membuka detail ini.
                                    </div>

                                    <div class="grid gap-4 md:grid-cols-2">
                                        <label class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-500 uppercase">Kategori</span>
                                            <select name="produk[{{ $product->id }}][category_id]"
                                                    @change="$root.dirtyRows[{{ $product->id }}] = true"
                                                    class="w-full h-11 rounded-2xl border border-emerald-100 bg-white px-4 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                                @foreach($category as $cat)
                                                    <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <div class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-500 uppercase">Satuan</span>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="inline-flex items-center gap-2 rounded-2xl border border-emerald-100 bg-white px-4 py-2 text-sm font-semibold text-emerald-700">
                                                    <i class="fa-solid fa-ruler-horizontal"></i>
                                                    {{ $product->units?->name ?? '-' }}
                                                </span>
                                                <input type="hidden" name="produk[{{ $product->id }}][satuan]" value="{{ $product->satuan }}">
                                                <span class="text-xs text-slate-400">Kelola melalui menu referensi.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-3 rounded-2xl border border-emerald-100 bg-white px-4 py-3 text-xs text-slate-500">
                                        <i class="fa-solid fa-clipboard-check text-emerald-500"></i>
                                        Pastikan data kategori dan satuan terhubung dengan benar.</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @php
                    $isFirstPage = $products->onFirstPage();
                    $isLastPage = $products->currentPage() === $products->lastPage();
                    $windowStart = max(1, $products->currentPage() - 2);
                    $windowEnd = min($products->lastPage(), $products->currentPage() + 2);
                @endphp

                <div class="rounded-3xl border border-slate-100 bg-white px-5 py-4 flex flex-col gap-4">
                    <div class="flex flex-wrap items-center gap-3 justify-between">
                        <button type="submit" onclick="return confirm('Simpan perubahan?')"
                                class="inline-flex items-center gap-2 px-5 py-3 text-sm font-semibold text-white rounded-2xl shadow bg-gradient-to-r from-emerald-500 to-emerald-600 hover:scale-[1.02]">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                        <div class="text-xs text-slate-500 flex flex-col">
                            <span>Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} produk</span>
                            <span>Halaman {{ $products->currentPage() }} dari {{ $products->lastPage() }} · {{ $products->perPage() }} data per halaman</span>
                        </div>
                    </div>
                    <nav class="flex flex-wrap items-center gap-1 text-sm font-semibold" aria-label="Navigasi halaman">
                        <a href="{{ $isFirstPage ? '#' : $products->url(1) }}"
                           @class(['inline-flex items-center gap-2 px-3 py-2 rounded-2xl border text-slate-500 bg-white', 'border-slate-200 hover:border-emerald-200' => !$isFirstPage, 'pointer-events-none opacity-40 border-slate-100' => $isFirstPage])>
                            <i class="fa-solid fa-angles-left"></i>
                            Awal
                        </a>
                        <a href="{{ $products->previousPageUrl() ?? '#' }}"
                           @class(['inline-flex items-center gap-2 px-3 py-2 rounded-2xl border text-slate-500 bg-white', 'border-slate-200 hover:border-emerald-200' => !$isFirstPage, 'pointer-events-none opacity-40 border-slate-100' => $isFirstPage])>
                            <i class="fa-solid fa-chevron-left"></i>
                            Prev
                        </a>
                        @for($page = $windowStart; $page <= $windowEnd; $page++)
                            <a href="{{ $products->url($page) }}"
                               @class([
                                   'inline-flex items-center justify-center rounded-2xl border px-3 py-2',
                                   'border-emerald-300 bg-emerald-50 text-emerald-700 shadow-inner' => $page === $products->currentPage(),
                                   'border-slate-200 bg-white text-slate-500 hover:border-emerald-200 hover:text-emerald-600' => $page !== $products->currentPage(),
                               ])>
                                {{ $page }}
                            </a>
                        @endfor
                        <a href="{{ $products->nextPageUrl() ?? '#' }}"
                           @class(['inline-flex items-center gap-2 px-3 py-2 rounded-2xl border text-slate-500 bg-white', 'border-slate-200 hover:border-emerald-200' => !$isLastPage, 'pointer-events-none opacity-40 border-slate-100' => $isLastPage])>
                            Next
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                        <a href="{{ $isLastPage ? '#' : $products->url($products->lastPage()) }}"
                           @class(['inline-flex items-center gap-2 px-3 py-2 rounded-2xl border text-slate-500 bg-white', 'border-slate-200 hover:border-emerald-200' => !$isLastPage, 'pointer-events-none opacity-40 border-slate-100' => $isLastPage])>
                            Akhir
                            <i class="fa-solid fa-angles-right"></i>
                        </a>
                    </nav>
                </div>
            </form>

            <div class="mt-6" x-data="{ confirmOpen: false, keyword: '', password: '', ack: false }">
                <div class="rounded-3xl border border-rose-100 bg-rose-50/70 p-5 space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 text-rose-600 bg-white rounded-xl border border-rose-100">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-rose-700">Tindakan sensitif</p>
                                <p class="text-xs text-rose-500">Ikuti setiap langkah sebelum membuka modal penghapusan.</p>
                            </div>
                        </div>
                        <button type="button" @click="confirmOpen = true"
                                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white rounded-xl shadow bg-gradient-to-r from-rose-500 to-rose-600">
                            <i class="fa-solid fa-lock"></i>
                            Buka Modal Penghapusan
                        </button>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3 text-[11px] font-semibold text-rose-600">
                        <div class="flex items-center gap-2 rounded-2xl border border-rose-100 bg-white px-3 py-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-50 text-rose-600">1</span>
                            Backup data laporan
                        </div>
                        <div class="flex items-center gap-2 rounded-2xl border border-rose-100 bg-white px-3 py-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-50 text-rose-600">2</span>
                            Pastikan tidak ada draft tersisa
                        </div>
                        <div class="flex items-center gap-2 rounded-2xl border border-rose-100 bg-white px-3 py-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-50 text-rose-600">3</span>
                            Siapkan password akun aktif
                        </div>
                    </div>
                </div>

                <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-40 flex items-center justify-center">
                    <div class="absolute inset-0 bg-slate-900/60" @click="confirmOpen = false"></div>
                    <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-2xl border border-rose-100 p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-base font-semibold text-rose-700">Konfirmasi Hapus Semua Data</p>
                                <p class="text-xs text-slate-500">Tindakan ini tidak bisa dibatalkan.</p>
                            </div>
                            <button type="button" @click="confirmOpen = false" class="text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <form action="{{ route('deleteallinvent') }}" method="POST" onsubmit="return confirm('Yakin hapus semua data?')" class="space-y-4">
                            @csrf
                            @method('DELETE')
                            <label class="flex flex-col gap-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase">Ketik HAPUS</span>
                                <input type="text" x-model="keyword" placeholder="HAPUS"
                                       class="h-11 rounded-2xl border border-rose-200 px-4 text-sm font-semibold uppercase tracking-wide focus:border-rose-400 focus:ring-1 focus:ring-rose-300">
                            </label>
                            <label class="flex flex-col gap-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase">Masukkan password akun</span>
                                <input type="password" name="confirmation_password" x-model="password" placeholder="••••••••"
                                       class="h-11 rounded-2xl border border-slate-200 px-4 text-sm focus:border-rose-300 focus:ring-1 focus:ring-rose-200">
                            </label>
                            <label class="flex items-start gap-2 text-[11px] text-slate-500">
                                <input type="checkbox" x-model="ack" class="mt-1 rounded border-rose-200 text-rose-500 focus:ring-rose-300">
                                <span>Saya sudah membuat cadangan data dan memahami bahwa tindakan ini tidak dapat dibatalkan.</span>
                            </label>
                            <p class="text-[11px] text-rose-400">Kami tidak menyimpan password ini; input hanya untuk memastikan Anda sadar risikonya.</p>
                            <div class="flex flex-wrap items-center gap-3 justify-end">
                                <button type="button" @click="confirmOpen = false" class="px-4 py-2 text-sm font-semibold text-slate-500">Batalkan</button>
                                <button type="submit"
                                        :disabled="keyword !== 'HAPUS' || password.length < 6 || !ack"
                                        :class="keyword === 'HAPUS' && password.length >= 6 && ack ? 'bg-gradient-to-r from-rose-500 to-rose-600 text-white shadow' : 'bg-rose-100 text-rose-400 cursor-not-allowed'"
                                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-xl">
                                    <i class="fa-solid fa-trash"></i>
                                    Hapus Semua Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center gap-3 p-10 text-center border border-dashed rounded-2xl bg-white/70 border-emerald-200">
                <span class="inline-flex items-center justify-center w-16 h-16 text-emerald-600 bg-emerald-50 rounded-full">
                    <i class="text-2xl fas fa-box-open"></i>
                </span>
                <p class="text-lg font-semibold text-slate-800">Belum ada data inventori</p>
                <p class="text-sm text-slate-500">Tambahkan produk baru atau import data untuk mulai mengelola stok.</p>
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-gradient-to-r from-emerald-500 to-emerald-600">
                    <i class="fas fa-plus"></i>
                    Tambah Produk Pertama
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
