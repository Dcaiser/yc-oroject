<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                <i class="fas fa-warehouse"></i>
            </span>
            <h2 class="text-xl font-semibold leading-tight text-slate-700">Inventori Produk</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['title' => 'Inventori']]" />

        <!-- Header Section with Quick Actions -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Inventori Produk</h1>
                    <p class="text-sm text-slate-500">
                        Pantau stok, harga, dan kategori produk dalam satu tampilan.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @if(in_array(Auth::user()->role ?? '', ['manager', 'admin']))
                        <a href="{{ route('products.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-emerald-500 hover:bg-emerald-600 transition">
                            <i class="fas fa-plus"></i>
                            Produk Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>

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
            $statCards = [
                [
                    'label' => 'Total Produk',
                    'value' => number_format((int) data_get($inventoryStats, 'total_sku', 0)),
                    'sub' => 'SKU terdaftar',
                    'icon' => 'fa-boxes-stacked',
                    'accent' => 'bg-emerald-500/10 text-emerald-600',
                ],
                [
                    'label' => 'Stok Menipis',
                    'value' => number_format((int) data_get($inventoryStats, 'low_stock', 0)),
                    'sub' => 'Perlu restock',
                    'icon' => 'fa-triangle-exclamation',
                    'accent' => 'bg-amber-500/10 text-amber-600',
                ],
                [
                    'label' => 'Stok Habis',
                    'value' => number_format((int) data_get($inventoryStats, 'out_of_stock', 0)),
                    'sub' => 'Tidak tersedia',
                    'icon' => 'fa-circle-xmark',
                    'accent' => 'bg-rose-500/10 text-rose-600',
                ],
                [
                    'label' => 'Kategori Aktif',
                    'value' => number_format($category->count()),
                    'sub' => 'Memiliki produk',
                    'icon' => 'fa-tags',
                    'accent' => 'bg-indigo-500/10 text-indigo-600',
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

        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($statCards as $card)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl {{ $card['accent'] }}">
                            <i class="fas {{ $card['icon'] }} text-lg"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>


        <!-- Main Content Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50" x-data="{ manageModal: null }">
            <!-- Toolbar -->
            <div class="flex flex-col gap-4 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- Search & Filters -->
                <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center sm:flex-wrap">
                    <!-- Search -->
                    <form action="{{ route('invent') }}" method="GET" class="relative flex-1 sm:max-w-xs">
                        @if($categoryFilter !== 'all')
                            <input type="hidden" name="category" value="{{ $categoryFilter }}">
                        @endif
                        @if($stockFilter !== 'all')
                            <input type="hidden" name="stock" value="{{ $stockFilter }}">
                        @endif
                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" name="search" value="{{ $search }}" placeholder="Cari produk atau SKU..."
                            class="w-full rounded-xl border-2 border-emerald-100 bg-emerald-50/60 py-2.5 pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </form>

                    <!-- Category Filter Dropdown -->
                    @php
                        $categoryOptions = ['all' => ['label' => 'Semua Kategori', 'icon' => 'fas fa-layer-group', 'iconClasses' => 'bg-emerald-100 text-emerald-600']];
                        foreach($category as $cat) { $categoryOptions[$cat->id] = ['label' => $cat->name, 'icon' => 'fas fa-tag', 'iconClasses' => 'bg-indigo-100 text-indigo-600']; }
                        $selectedCatLabel = $categoryOptions[$categoryFilter]['label'] ?? 'Semua Kategori';
                        $selectedCatIconClasses = $categoryOptions[$categoryFilter]['iconClasses'] ?? 'bg-emerald-100 text-emerald-600';
                    @endphp
                    <div class="relative sm:w-48" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 py-2 pl-2.5 pr-2.5 text-sm font-medium text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $selectedCatIconClasses }}"><i class="{{ $categoryOptions[$categoryFilter]['icon'] ?? 'fas fa-layer-group' }}"></i></span>
                                <span class="text-sm font-medium text-slate-700 truncate">{{ $selectedCatLabel }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-emerald-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute z-30 mt-1.5 w-48 bg-white border border-emerald-100 rounded-xl shadow-lg">
                            <div class="py-1 max-h-52 overflow-y-auto">
                                @foreach($categoryOptions as $value => $option)
                                    <a href="{{ route('invent', array_merge(request()->except('category'), $value === 'all' ? [] : ['category' => $value])) }}"
                                       class="w-full px-3 py-2 flex items-center justify-between gap-2 text-sm {{ (string)$categoryFilter === (string)$value ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-slate-600 hover:bg-emerald-50' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $option['iconClasses'] }}"><i class="{{ $option['icon'] }}"></i></span>
                                            <span>{{ $option['label'] }}</span>
                                        </div>
                                        @if((string)$categoryFilter === (string)$value)<i class="fas fa-check text-xs text-emerald-500"></i>@endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Stock Status Filter -->
                    @php
                        $stockOptions = [
                            'all' => ['label' => 'Semua Stok', 'icon' => 'fas fa-boxes-stacked', 'iconClasses' => 'bg-emerald-100 text-emerald-600'],
                            'safe' => ['label' => 'Stok Aman', 'icon' => 'fas fa-check-circle', 'iconClasses' => 'bg-emerald-100 text-emerald-600'],
                            'low' => ['label' => 'Stok Menipis', 'icon' => 'fas fa-triangle-exclamation', 'iconClasses' => 'bg-amber-100 text-amber-600'],
                            'out' => ['label' => 'Stok Habis', 'icon' => 'fas fa-circle-xmark', 'iconClasses' => 'bg-rose-100 text-rose-600'],
                        ];
                    @endphp
                    <div class="relative sm:w-44" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between gap-2 py-2 pl-2.5 pr-2.5 text-sm font-medium text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $stockOptions[$stockFilter]['iconClasses'] ?? 'bg-emerald-100 text-emerald-600' }}"><i class="{{ $stockOptions[$stockFilter]['icon'] ?? 'fas fa-boxes-stacked' }}"></i></span>
                                <span class="text-sm font-medium text-slate-700">{{ $stockOptions[$stockFilter]['label'] ?? 'Semua Stok' }}</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-emerald-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute z-30 mt-1.5 w-44 bg-white border border-emerald-100 rounded-xl shadow-lg">
                            <div class="py-1">
                                @foreach($stockOptions as $value => $option)
                                    <a href="{{ route('invent', array_merge(request()->except('stock'), $value === 'all' ? [] : ['stock' => $value])) }}"
                                       class="w-full px-3 py-2 flex items-center justify-between gap-2 text-sm {{ $stockFilter === $value ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-slate-600 hover:bg-emerald-50' }}">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $option['iconClasses'] }}"><i class="{{ $option['icon'] }}"></i></span>
                                            <span>{{ $option['label'] }}</span>
                                        </div>
                                        @if($stockFilter === $value)<i class="fas fa-check text-xs text-emerald-500"></i>@endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side -->
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm text-slate-500"><span class="font-medium text-slate-700">{{ $products->total() }}</span> produk</span>
                    
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button type="button" @click="open = !open" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50">
                            <i class="fas fa-cog"></i><span class="hidden sm:inline">Kelola</span><i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-lg z-30">
                            <button type="button" @click="manageModal = 'category'; open = false" class="w-full px-4 py-2 text-left text-sm text-slate-600 hover:bg-emerald-50 flex items-center gap-2"><i class="fas fa-layer-group"></i> Kelola Kategori</button>
                            <button type="button" @click="manageModal = 'unit'; open = false" class="w-full px-4 py-2 text-left text-sm text-slate-600 hover:bg-sky-50 flex items-center gap-2"><i class="fas fa-ruler-horizontal"></i> Kelola Satuan</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Filters -->
            @php
                $activeFilters = [];
                if($search) $activeFilters[] = ['label' => 'Pencarian', 'value' => $search];
                if($categoryFilter !== 'all') $activeFilters[] = ['label' => 'Kategori', 'value' => optional($category->firstWhere('id', $categoryFilter))->name ?? '-'];
                if($stockFilter !== 'all') $activeFilters[] = ['label' => 'Stok', 'value' => $stockOptions[$stockFilter]['label'] ?? '-'];
            @endphp
            @if(count($activeFilters))
                <div class="flex flex-wrap items-center gap-2 px-4 py-3 border-b border-slate-100 bg-slate-50/50">
                    <span class="text-xs font-medium text-slate-500">Filter aktif:</span>
                    @foreach($activeFilters as $chip)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ $chip['label'] }}: {{ $chip['value'] }}</span>
                    @endforeach
                    <a href="{{ route('invent') }}" class="text-xs font-medium text-slate-500 hover:text-emerald-600 ml-2"><i class="fas fa-times mr-1"></i>Reset</a>
                </div>
            @endif

            <!-- Manage Modals -->
            <template x-if="manageModal !== null">
                <div class="fixed inset-0 z-40 flex items-center justify-center">
                    <div class="absolute inset-0 bg-slate-900/50" @click="manageModal = null"></div>
                    <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl p-6 space-y-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900" x-text="manageModal === 'category' ? 'Kelola Kategori' : 'Kelola Satuan'"></p>
                                <p class="text-xs text-slate-500">Perubahan akan tersimpan tanpa meninggalkan halaman.</p>
                            </div>
                            <button type="button" @click="manageModal = null" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                        </div>
                        <div x-show="manageModal === 'category'" x-cloak>
                            <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <label class="flex flex-col gap-2"><span class="text-xs font-semibold text-slate-500 uppercase">Nama kategori</span><input type="text" name="nama-kategori" required class="w-full h-11 rounded-xl border border-emerald-100 px-4 text-sm focus:ring-2 focus:ring-emerald-300"></label>
                                <label class="flex flex-col gap-2"><span class="text-xs font-semibold text-slate-500 uppercase">Deskripsi (opsional)</span><textarea name="deskripsi-kategori" rows="3" class="w-full rounded-xl border border-emerald-100 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-300"></textarea></label>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="manageModal = null" class="px-4 py-2 text-sm font-semibold text-slate-500">Batal</button>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-emerald-500 hover:bg-emerald-600"><i class="fas fa-save"></i> Simpan</button>
                                </div>
                            </form>
                        </div>
                        <div x-show="manageModal === 'unit'" x-cloak>
                            <form action="{{ route('units.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <label class="flex flex-col gap-2"><span class="text-xs font-semibold text-slate-500 uppercase">Nama satuan</span><input type="text" name="name" required class="w-full h-11 rounded-xl border border-sky-100 px-4 text-sm focus:ring-2 focus:ring-sky-300"></label>
                                <label class="flex flex-col gap-2"><span class="text-xs font-semibold text-slate-500 uppercase">Konversi ke satuan dasar</span><input type="number" step="0.0001" min="0.0001" name="conversion_to_base" required class="w-full h-11 rounded-xl border border-sky-100 px-4 text-sm focus:ring-2 focus:ring-sky-300"></label>
                                <div class="text-[11px] text-slate-400">Contoh: 12 untuk "Lusin" jika satuan dasar adalah pcs.</div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="manageModal = null" class="px-4 py-2 text-sm font-semibold text-slate-500">Batal</button>
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-sky-500 hover:bg-sky-600"><i class="fas fa-save"></i> Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Content Area -->
            <div class="p-4">
        @if($products->count() > 0)
            <form action="{{ route('updateAll') }}" method="POST" class="space-y-4" x-data="{ 
                viewMode: 'card',
                dirtyRows: {},
                selectedItems: [],
                selectAll: false,
                openCards: {},
                toggleSelectAll() {
                    if (this.selectAll) {
                        this.selectedItems = [{{ $products->pluck('id')->implode(',') }}];
                    } else {
                        this.selectedItems = [];
                    }
                },
                toggleItem(id) {
                    if (this.selectedItems.includes(id)) {
                        this.selectedItems = this.selectedItems.filter(i => i !== id);
                    } else {
                        this.selectedItems.push(id);
                    }
                    this.selectAll = this.selectedItems.length === {{ $products->count() }};
                },
                toggleCard(id) {
                    this.openCards[id] = !this.openCards[id];
                },
                isCardOpen(id) {
                    return this.openCards[id] || false;
                }
            }">
                @csrf
                @method('PUT')

                <!-- Bulk Action Toolbar -->
                <div x-show="selectedItems.length > 0" x-cloak x-transition
                     class="sticky top-4 z-20 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50/95 px-4 py-3 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500 text-white text-sm font-bold" x-text="selectedItems.length"></span>
                        <span class="text-sm font-semibold text-emerald-800">item dipilih</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="selectedItems = []; selectAll = false"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-600 rounded-xl hover:bg-slate-100 transition">
                            <i class="fas fa-times"></i>
                            <span class="hidden sm:inline">Batal Pilih</span>
                        </button>
                        <button type="button" 
                                @click="if(confirm('Hapus ' + selectedItems.length + ' produk yang dipilih?')) { 
                                    const form = document.createElement('form');
                                    form.method = 'POST';
                                    form.action = '{{ route('products.bulkDelete') }}';
                                    const csrf = document.createElement('input');
                                    csrf.type = 'hidden';
                                    csrf.name = '_token';
                                    csrf.value = '{{ csrf_token() }}';
                                    form.appendChild(csrf);
                                    selectedItems.forEach(id => {
                                        const input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = 'ids[]';
                                        input.value = id;
                                        form.appendChild(input);
                                    });
                                    document.body.appendChild(form);
                                    form.submit();
                                }"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl bg-rose-500 hover:bg-rose-600 transition shadow-sm">
                            <i class="fas fa-trash-alt"></i>
                            <span class="hidden sm:inline">Hapus Terpilih</span>
                        </button>
                    </div>
                </div>

                <div x-show="Object.keys(dirtyRows).length" x-cloak class="sticky top-4 z-10 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50/90 px-4 py-3 text-sm font-semibold text-amber-800 shadow-sm">
                    <span class="inline-flex w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span x-text="`Ada perubahan pada ${Object.keys(dirtyRows).length} produk. Jangan lupa simpan.`"></span>
                </div>

                <div class="space-y-4">
                    <!-- Select All Header with View Toggle -->
                    <div class="flex items-center justify-between gap-4 px-4 py-2 rounded-xl bg-slate-50/80 border border-slate-100">
                        <div class="flex items-center gap-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" class="sr-only peer">
                                <div class="w-5 h-5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-emerald-500 peer-checked:border-emerald-500 flex items-center justify-center transition-all">
                                    <i x-show="selectAll" x-cloak class="fas fa-check text-white text-xs"></i>
                                </div>
                            </label>
                            <span class="text-sm font-medium text-slate-600">
                                <span x-show="!selectAll">Pilih Semua</span>
                                <span x-show="selectAll" x-cloak>{{ $products->count() }} produk dipilih</span>
                            </span>
                        </div>
                        <!-- View Toggle -->
                        <div class="flex items-center gap-1 p-1 bg-white border border-slate-200 rounded-xl">
                            <button type="button" @click="viewMode = 'card'" 
                                    :class="viewMode === 'card' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-400 hover:text-slate-600'"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all">
                                <i class="fas fa-grip"></i>
                            </button>
                            <button type="button" @click="viewMode = 'table'"
                                    :class="viewMode === 'table' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-400 hover:text-slate-600'"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-all">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- TABLE VIEW -->
                    <div x-show="viewMode === 'table'" x-cloak>
                        <div class="overflow-x-auto rounded-xl border border-slate-100">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50">
                                    <tr>
                                        <th class="w-12 px-4 py-3"></th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Produk</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Kategori</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Stok</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Harga Agent</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Harga Reseller</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Harga Pelanggan</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($products as $product)
                                        @php
                                            $priceAgent = optional($product->prices->firstWhere('customer_type', 'agent'))->price;
                                            $priceReseller = optional($product->prices->firstWhere('customer_type', 'reseller'))->price;
                                            $priceCustomer = optional($product->prices->firstWhere('customer_type', 'pelanggan'))->price;
                                            $stockQty = $product->stock_quantity ?? 0;
                                            $isLowStock = $stockQty > 0 && $stockQty <= 20;
                                            $isOutStock = $stockQty <= 0;
                                            $stockFormatted = number_format(round($stockQty), 0, ',', '.');
                                            $statusMeta = [
                                                'label' => 'Aman',
                                                'icon' => 'fa-circle-check',
                                                'wrapper' => 'bg-emerald-50 text-emerald-700',
                                            ];
                                            if ($isLowStock) {
                                                $statusMeta = [
                                                    'label' => 'Menipis',
                                                    'icon' => 'fa-triangle-exclamation',
                                                    'wrapper' => 'bg-amber-50 text-amber-700',
                                                ];
                                            } elseif ($isOutStock) {
                                                $statusMeta = [
                                                    'label' => 'Habis',
                                                    'icon' => 'fa-circle-xmark',
                                                    'wrapper' => 'bg-rose-50 text-rose-700',
                                                ];
                                            }
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition" :class="{ 'bg-emerald-50/30': selectedItems.includes({{ $product->id }}) }">
                                            <td class="px-4 py-3">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           :checked="selectedItems.includes({{ $product->id }})"
                                                           @change="toggleItem({{ $product->id }})"
                                                           class="sr-only peer">
                                                    <div class="w-5 h-5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-emerald-500 peer-checked:border-emerald-500 flex items-center justify-center transition-all hover:border-emerald-400">
                                                        <i x-show="selectedItems.includes({{ $product->id }})" class="fas fa-check text-white text-xs"></i>
                                                    </div>
                                                </label>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-3">
                                                    @if($product->image_path)
                                                        <img src="{{ asset('storage/' . $product->image_path) }}" class="w-10 h-10 rounded-lg object-cover border border-slate-100" alt="">
                                                    @else
                                                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-400">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-800">{{ $product->name }}</p>
                                                        <p class="text-xs text-slate-400">{{ $product->sku }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                                            <td class="px-4 py-3">
                                                <span class="text-sm font-semibold text-slate-800">{{ $stockFormatted }}</span>
                                                <span class="text-xs text-slate-400">{{ $product->units?->name ?? 'pcs' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                                @if($priceAgent) Rp {{ number_format($priceAgent, 0, ',', '.') }} @else <span class="text-slate-400">-</span> @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                                @if($priceReseller) Rp {{ number_format($priceReseller, 0, ',', '.') }} @else <span class="text-slate-400">-</span> @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-slate-700">
                                                @if($priceCustomer) Rp {{ number_format($priceCustomer, 0, ',', '.') }} @else <span class="text-slate-400">-</span> @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusMeta['wrapper'] }}">
                                                    <i class="fa-solid {{ $statusMeta['icon'] }}"></i>
                                                    {{ $statusMeta['label'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- CARD VIEW -->
                    <div x-show="viewMode === 'card'" class="space-y-4">
                    @foreach($products as $product)
                        @php
                            $priceAgent = optional($product->prices->firstWhere('customer_type', 'agent'))->price;
                            $priceReseller = optional($product->prices->firstWhere('customer_type', 'reseller'))->price;
                            $priceCustomer = optional($product->prices->firstWhere('customer_type', 'pelanggan'))->price;
                            $stockQty = $product->stock_quantity ?? 0;
                            $isLowStock = $stockQty > 0 && $stockQty <= 20;
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
                        <div class="p-6 border border-emerald-100 rounded-3xl bg-white shadow-sm transition-all" 
                             :class="{ 
                                 'border-amber-300 ring-2 ring-amber-100/80': dirtyRows[{{ $product->id }}],
                                 'border-emerald-400 ring-2 ring-emerald-100 bg-emerald-50/30': selectedItems.includes({{ $product->id }})
                             }">
                            <div class="flex gap-4">
                                <!-- Checkbox -->
                                <div class="shrink-0 pt-1">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" 
                                               :checked="selectedItems.includes({{ $product->id }})"
                                               @change="toggleItem({{ $product->id }})"
                                               class="sr-only peer">
                                        <div class="w-5 h-5 rounded-md border-2 border-slate-300 bg-white peer-checked:bg-emerald-500 peer-checked:border-emerald-500 flex items-center justify-center transition-all hover:border-emerald-400">
                                            <i x-show="selectedItems.includes({{ $product->id }})" class="fas fa-check text-white text-xs"></i>
                                        </div>
                                    </label>
                                </div>
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                            <div class="grid gap-6 lg:grid-cols-3">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-emerald-500">#{{ $product->id }}</p>
                                            <p class="text-lg font-bold text-slate-900 truncate" title="{{ $product->name }}">{{ $product->name }}</p>
                                        </div>
                                        <div class="shrink-0 flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-[11px] font-semibold whitespace-nowrap {{ $statusMeta['wrapper'] }}">
                                                <i class="fa-solid {{ $statusMeta['icon'] }}"></i>
                                                {{ $statusMeta['label'] }}
                                            </span>
                                            <span x-show="dirtyRows[{{ $product->id }}]" x-cloak
                                                  class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700 whitespace-nowrap">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Draft
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            <i class="fas fa-tag text-[10px]"></i>
                                            {{ $product->category?->name ?? 'Tanpa kategori' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 text-slate-600 border border-slate-200">
                                            <i class="fas fa-barcode text-[10px]"></i>
                                            {{ $product->sku ?? '-' }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-50 text-slate-500 border border-slate-200">
                                            <i class="fas fa-clock text-[10px]"></i>
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
                                                       @focus="selectAll" @input="handleInput($event); dirtyRows[{{ $product->id }}] = true"
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
                                                       @focus="selectAll" @input="handleInput($event); dirtyRows[{{ $product->id }}] = true"
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
                                                       @focus="selectAll" @input="handleInput($event); dirtyRows[{{ $product->id }}] = true"
                                  value="{{ number_format((int) ($priceCustomer ?? 0), 0, ',', '.') }}"
                                  class="h-9 w-full rounded-xl border border-transparent bg-white pl-6 pr-2 text-right text-xs font-bold text-slate-900 focus:border-emerald-300 focus:outline-none focus:ring-1 focus:ring-emerald-300">
                              <input type="hidden" :name="name" :value="raw" value="{{ (int) ($priceCustomer ?? 0) }}">
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3">
                                    <div class="rounded-3xl border border-slate-200 bg-linear-to-br from-slate-50 to-white p-4 space-y-3">
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
                                            <span class="inline-flex items-center gap-2 rounded-xl px-3 py-2 text-sm font-semibold border {{ $statusMeta['wrapper'] }}">
                                                <i class="fa-solid {{ $statusMeta['icon'] }}"></i>
                                                {{ $statusMeta['label'] }}
                                            </span>
                                            <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $stockFormatted }}</span>
                                        </div>
                                    </div>
                                    <button type="button"
                                            @click="toggleCard({{ $product->id }})"
                                            class="inline-flex items-center justify-center gap-2 rounded-2xl border border-emerald-200 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                        <span>{{ __('Lihat Detail') }}</span>
                                        <i class="fas" :class="isCardOpen({{ $product->id }}) ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                                    </button>
                                </div>
                            </div>

                            <div x-show="isCardOpen({{ $product->id }})" x-cloak class="mt-4">
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
                                                   @input="dirtyRows[{{ $product->id }}] = true"
                                                   class="w-full h-11 rounded-2xl border border-emerald-100 bg-white px-4 text-sm font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400" />
                                        </label>
                                        <label class="flex flex-col gap-2">
                                            <span class="text-xs font-semibold text-slate-500 uppercase">SKU</span>
                                            <input type="text" name="produk[{{ $product->id }}][sku]" value="{{ $product->sku }}"
                                                   @input="dirtyRows[{{ $product->id }}] = true"
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
                                                    @change="dirtyRows[{{ $product->id }}] = true"
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
                                <!-- End Content Wrapper -->
                            </div>
                            <!-- End Flex Wrapper -->
                        </div>
                    @endforeach
                    </div>
                    <!-- End Card View -->
                </div>

                @php
                    $currentPage = $products->currentPage();
                    $lastPage = $products->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);
                @endphp

                <!-- Footer with Save & Pagination -->
                <div class="flex flex-col items-center justify-between gap-4 pt-6 mt-6 border-t border-slate-100 lg:flex-row">
                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <button type="submit" onclick="return confirm('Simpan perubahan?')"
                                class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow bg-emerald-500 hover:bg-emerald-600 transition">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                        <div class="text-sm text-slate-600">
                            Menampilkan <span class="font-medium text-slate-800">{{ $products->firstItem() ?? 0 }}</span> - <span class="font-medium text-slate-800">{{ $products->lastItem() ?? 0 }}</span> dari <span class="font-medium text-slate-800">{{ $products->total() }}</span> produk
                        </div>
                    </div>
                    
                    @if($products->hasPages())
                    <div class="flex flex-wrap items-center gap-1.5">
                        {{-- Previous --}}
                        @if ($products->onFirstPage())
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">
                                <i class="fas fa-chevron-left text-xs"></i>
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                <i class="fas fa-chevron-left text-xs"></i>
                                Sebelumnya
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @if($start > 1)
                            <a href="{{ $products->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-slate-600 transition bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800">1</a>
                            @if($start > 2)
                                <span class="px-1 text-slate-400">...</span>
                            @endif
                        @endif

                        @for($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-emerald-500 rounded-lg shadow-sm">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $products->url($page) }}"
                                   class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-slate-600 transition bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        @if($end < $lastPage)
                            @if($end < $lastPage - 1)
                                <span class="px-1 text-slate-400">...</span>
                            @endif
                            <a href="{{ $products->url($lastPage) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-slate-600 transition bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                Selanjutnya
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">
                                Selanjutnya
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </div>
                    @endif
                </div>
            </form>

        @else
            <div class="flex flex-col items-center justify-center gap-3 p-10 text-center border border-dashed rounded-2xl bg-white/70 border-emerald-200">
                <span class="inline-flex items-center justify-center w-16 h-16 text-emerald-600 bg-emerald-50 rounded-full">
                    <i class="text-2xl fas fa-box-open"></i>
                </span>
                <p class="text-lg font-semibold text-slate-800">Belum ada data inventori</p>
                <p class="text-sm text-slate-500">Tambahkan produk baru atau import data untuk mulai mengelola stok.</p>
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-linear-to-r from-emerald-500 to-emerald-600">
                    <i class="fas fa-plus"></i>
                    Tambah Produk Pertama
                </a>
            </div>
        @endif
            </div>
            <!-- End Content Area -->
        </div>
        <!-- End Main Content Card -->
    </div>
</x-app-layout>
