<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                <i class="fas fa-boxes-stacked"></i>
            </span>
            <h2 class="text-xl font-semibold leading-tight text-slate-700">Manajemen Produk</h2>
        </div>
    </x-slot>

    @php
        $statCards = [
            [
                'label' => 'Total Produk',
                'value' => number_format($stats['total']),
                'sub' => 'Produk terdaftar',
                'icon' => 'fa-boxes-stacked',
                'accent' => 'bg-emerald-500/10 text-emerald-600',
            ],
            [
                'label' => 'Stok Rendah',
                'value' => number_format($stats['low_stock']),
                'sub' => 'Stok ≤ 20 unit',
                'icon' => 'fa-triangle-exclamation',
                'accent' => 'bg-amber-500/10 text-amber-600',
            ],
            [
                'label' => 'Stok Habis',
                'value' => number_format($stats['out_of_stock']),
                'sub' => 'Perlu restock',
                'icon' => 'fa-circle-xmark',
                'accent' => 'bg-rose-500/10 text-rose-600',
            ],
            [
                'label' => 'Kategori Aktif',
                'value' => number_format($stats['categories_used']),
                'sub' => 'Memiliki produk',
                'icon' => 'fa-tags',
                'accent' => 'bg-indigo-500/10 text-indigo-600',
            ],
        ];
    @endphp

    <div class="space-y-6"
        x-data="productManager()"
        @keydown.escape.window="closeAllModals()">

        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['title' => 'Manajemen Produk']]" />

        <!-- Header Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Manajemen Produk</h1>
                    <p class="text-sm text-slate-500">
                        Kelola seluruh data produk, pantau stok, dan atur harga untuk berbagai kategori pelanggan.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <a href="{{ route('products.create') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Produk
                    </a>
                </div>
            </div>
        </div>

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

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold">Berhasil</p>
                        <p class="text-sm text-emerald-700/80">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold">Error</p>
                        <p class="text-sm text-rose-700/80">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <!-- Toolbar -->
            <div class="flex flex-col gap-4 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- Search & Filter -->
                <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                    <!-- Search -->
                    <form action="{{ route('products.index') }}" method="GET" class="relative flex-1 sm:max-w-xs">
                        @if($categoryFilter ?? false)
                            <input type="hidden" name="category" value="{{ $categoryFilter }}">
                        @endif
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari produk atau SKU..."
                            class="w-full rounded-xl border-2 border-emerald-100 bg-emerald-50/60 py-2.5 pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </form>

                    <!-- Category Filter -->
                    @php
                        $categoryOptions = [
                            '' => [
                                'label' => 'Semua Kategori',
                                'icon' => 'fas fa-layer-group',
                                'iconClasses' => 'bg-emerald-100 text-emerald-600'
                            ],
                        ];
                        foreach($category as $cat) {
                            $categoryOptions[$cat->id] = [
                                'label' => $cat->name,
                                'icon' => 'fas fa-tag',
                                'iconClasses' => 'bg-indigo-100 text-indigo-600'
                            ];
                        }
                        $selectedCategoryLabel = $categoryOptions[$categoryFilter ?? '']['label'] ?? 'Semua Kategori';
                        $selectedCategoryIcon = $categoryOptions[$categoryFilter ?? '']['icon'] ?? 'fas fa-layer-group';
                        $selectedCategoryIconClasses = $categoryOptions[$categoryFilter ?? '']['iconClasses'] ?? 'bg-emerald-100 text-emerald-600';
                    @endphp
                    <div class="sm:w-48" x-data="{ open: false }" @click.away="open = false">
                        <div class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    class="w-full flex items-center justify-between gap-2 py-2 pl-2.5 pr-2.5 text-sm font-medium text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $selectedCategoryIconClasses }}">
                                        <i class="{{ $selectedCategoryIcon }}"></i>
                                    </span>
                                    <span class="text-sm font-medium text-slate-700">{{ $selectedCategoryLabel }}</span>
                                </div>
                                <span class="text-emerald-400" :class="{ 'rotate-180': open }">
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                                </span>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 right-0 z-30 mt-1.5 origin-top overflow-hidden bg-white border border-emerald-100 rounded-xl shadow-lg">
                                <div class="py-1 max-h-52 overflow-y-auto">
                                    @foreach($categoryOptions as $value => $option)
                                        @php
                                            $isActive = ($categoryFilter ?? '') == $value;
                                        @endphp
                                        <a href="{{ route('products.index', array_merge(request()->except('category'), $value ? ['category' => $value] : [])) }}"
                                           class="w-full px-3 py-2 flex items-center justify-between gap-2 text-sm text-left transition {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $option['iconClasses'] }}">
                                                    <i class="{{ $option['icon'] }}"></i>
                                                </span>
                                                <span>{{ $option['label'] }}</span>
                                            </div>
                                            @if($isActive)
                                                <i class="fas fa-check text-xs text-emerald-500"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions & Info -->
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-500">
                        <span class="font-medium text-slate-700">{{ $products->total() }}</span> produk ditemukan
                    </span>
                </div>
            </div>

            <!-- Products Table/Grid -->
            <div class="p-4">
                @if($products->isEmpty())
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                        <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="fas fa-boxes-stacked text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">Belum ada produk</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            @if(($search ?? false) || ($categoryFilter ?? false))
                                Tidak ditemukan produk dengan filter yang dipilih
                            @else
                                Mulai dengan menambahkan produk pertama
                            @endif
                        </p>
                        @if(($search ?? false) || ($categoryFilter ?? false))
                            <a href="{{ route('products.index') }}" class="mt-4 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                                <i class="fas fa-rotate-left mr-1"></i>Reset filter
                            </a>
                        @else
                            <a href="{{ route('products.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                                <i class="fas fa-plus"></i>Tambah Produk
                            </a>
                        @endif
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Produk</th>
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">SKU</th>
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Kategori</th>
                                        <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500">Stok</th>
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Harga</th>
                                        <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($products as $product)
                                        <tr class="group transition hover:bg-slate-50/50">
                                            <!-- Product -->
                                            <td class="px-3 py-3">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                                        @if($product->image_path)
                                                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                                        @elseif($product->gambar)
                                                            <img src="{{ $product->gambar }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                                        @else
                                                            <div class="flex h-full w-full items-center justify-center text-slate-400">
                                                                <i class="fas fa-image text-xs"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-slate-900 truncate max-w-[160px]">{{ $product->name }}</p>
                                                        <p class="text-xs text-slate-500 truncate max-w-[160px]">{{ Str::limit($product->description, 30) ?: '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <!-- SKU -->
                                            <td class="px-3 py-3">
                                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">
                                                    {{ $product->sku ?: '-' }}
                                                </span>
                                            </td>
                                            <!-- Category -->
                                            <td class="px-3 py-3">
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                                    <i class="fas fa-tag text-[10px]"></i>
                                                    {{ $product->category?->name ?? '-' }}
                                                </span>
                                            </td>
                                            <!-- Stock -->
                                            <td class="px-3 py-3 text-center">
                                                @php
                                                    $stock = $product->stock_quantity ?? 0;
                                                    $stockFormatted = fmod($stock, 1) === 0.0 
                                                        ? number_format($stock, 0, ',', '.') 
                                                        : rtrim(rtrim(number_format($stock, 2, ',', '.'), '0'), ',');
                                                    $unitName = $product->units?->name ?? 'unit';
                                                    
                                                    if ($stock <= 0) {
                                                        $stockClass = 'bg-rose-50 text-rose-600 border-rose-100';
                                                        $stockIcon = 'fa-circle-xmark';
                                                    } elseif ($stock <= 20) {
                                                        $stockClass = 'bg-amber-50 text-amber-600 border-amber-100';
                                                        $stockIcon = 'fa-triangle-exclamation';
                                                    } else {
                                                        $stockClass = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                                        $stockIcon = 'fa-check-circle';
                                                    }
                                                @endphp
                                                <span class="inline-flex flex-col items-center gap-0.5 rounded-xl border {{ $stockClass }} px-3 py-1.5">
                                                    <i class="fas {{ $stockIcon }} text-[10px]"></i>
                                                    <span class="text-sm font-semibold">{{ $stockFormatted }}</span>
                                                    <span class="text-[10px] font-medium opacity-75">{{ $unitName }}</span>
                                                </span>
                                            </td>
                                            <!-- Price -->
                                            <td class="px-3 py-3">
                                                @php
                                                    $price = $product->prices->firstWhere('customer_type', 'pelanggan');
                                                    $priceValue = $price ? number_format($price->price, 0, ',', '.') : '-';
                                                @endphp
                                                <span class="text-sm font-semibold text-slate-800">Rp {{ $priceValue }}</span>
                                            </td>
                                            <!-- Actions -->
                                            <td class="px-3 py-3">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" @click="openDetailModal({{ json_encode($product) }})"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-slate-50 hover:text-slate-600 hover:border-slate-300"
                                                        title="Detail">
                                                        <i class="fas fa-eye text-xs"></i>
                                                    </button>
                                                    <button type="button" @click="openEditModal({{ json_encode($product) }})"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200"
                                                        title="Edit">
                                                        <i class="fas fa-pen-to-square text-xs"></i>
                                                    </button>
                                                    <button type="button" @click="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200"
                                                        title="Hapus">
                                                        <i class="fas fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="grid gap-4 sm:grid-cols-2 lg:hidden">
                        @foreach($products as $product)
                            <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
                                <div class="flex gap-3">
                                    <!-- Image -->
                                    <div class="h-16 w-16 shrink-0 overflow-hidden rounded-xl bg-slate-100">
                                        @if($product->image_path)
                                            <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @elseif($product->gambar)
                                            <img src="{{ $product->gambar }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-slate-400">
                                                <i class="fas fa-image text-xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Info -->
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-slate-900 truncate">{{ $product->name }}</h4>
                                        <p class="text-xs text-slate-500">{{ $product->sku ?: 'No SKU' }}</p>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            <span class="inline-flex items-center gap-1 rounded-full bg-indigo-500/10 px-2 py-0.5 text-[10px] font-medium text-indigo-700">
                                                {{ $product->category?->name ?? '-' }}
                                            </span>
                                            @php
                                                $stock = $product->stock_quantity ?? 0;
                                                $stockFormatted = fmod($stock, 1) === 0.0 
                                                    ? number_format($stock, 0, ',', '.') 
                                                    : rtrim(rtrim(number_format($stock, 2, ',', '.'), '0'), ',');
                                                $unitName = $product->units?->name ?? 'pcs';
                                                
                                                if ($stock <= 0) {
                                                    $stockClass = 'bg-rose-500/10 text-rose-700';
                                                } elseif ($stock <= 20) {
                                                    $stockClass = 'bg-amber-500/10 text-amber-700';
                                                } else {
                                                    $stockClass = 'bg-emerald-500/10 text-emerald-700';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center gap-1 rounded-full {{ $stockClass }} px-2 py-0.5 text-[10px] font-medium">
                                                {{ $stockFormatted }} {{ $unitName }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Price & Actions -->
                                <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                                    @php
                                        $price = $product->prices->firstWhere('customer_type', 'pelanggan');
                                        $priceFormatted = $price ? 'Rp ' . number_format($price->price, 0, ',', '.') : '-';
                                    @endphp
                                    <span class="font-semibold text-slate-900">{{ $priceFormatted }}</span>
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="openDetailModal({{ json_encode($product) }})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button type="button" @click="openEditModal({{ json_encode($product) }})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-emerald-100 hover:text-emerald-600">
                                            <i class="fas fa-pen-to-square text-sm"></i>
                                        </button>
                                        <button type="button" @click="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-100 hover:text-rose-600">
                                            <i class="fas fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="flex flex-col items-center justify-between gap-3 pt-6 mt-6 border-t border-slate-100 lg:flex-row">
                            <div class="text-sm text-slate-600">
                                Menampilkan <span class="font-medium text-slate-800">{{ $products->firstItem() ?? 0 }}</span> - <span class="font-medium text-slate-800">{{ $products->lastItem() ?? 0 }}</span> dari <span class="font-medium text-slate-800">{{ $products->total() }}</span> produk
                            </div>
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
                                @php
                                    $currentPage = $products->currentPage();
                                    $lastPage = $products->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                @endphp

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
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Detail Modal -->
        <div x-show="showDetailModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showDetailModal = false">
                
                <!-- Header -->
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4 rounded-t-3xl">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600">
                            <i class="fas fa-box"></i>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900">Detail Produk</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <template x-if="selectedProduct">
                        <div class="space-y-6">
                            <!-- Product Image & Basic Info -->
                            <div class="flex flex-col sm:flex-row gap-6">
                                <div class="h-40 w-40 shrink-0 overflow-hidden rounded-2xl bg-slate-100 mx-auto sm:mx-0">
                                    <template x-if="selectedProduct.image_path">
                                        <img :src="'/storage/' + selectedProduct.image_path" :alt="selectedProduct.name" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!selectedProduct.image_path && selectedProduct.gambar">
                                        <img :src="selectedProduct.gambar" :alt="selectedProduct.name" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!selectedProduct.image_path && !selectedProduct.gambar">
                                        <div class="flex h-full w-full items-center justify-center text-slate-400">
                                            <i class="fas fa-image text-4xl"></i>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex-1 text-center sm:text-left">
                                    <h4 class="text-xl font-bold text-slate-900" x-text="selectedProduct.name"></h4>
                                    <p class="mt-1 text-sm text-slate-500" x-text="selectedProduct.description || 'Tidak ada deskripsi'"></p>
                                    <div class="mt-3 flex flex-wrap justify-center sm:justify-start gap-2">
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-700">
                                            <i class="fas fa-barcode"></i>
                                            <span x-text="selectedProduct.sku || 'No SKU'"></span>
                                        </span>
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-medium text-indigo-700">
                                            <i class="fas fa-tag"></i>
                                            <span x-text="selectedProduct.category?.name || '-'"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Stock Info -->
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <h5 class="text-sm font-semibold text-slate-700 mb-3">Informasi Stok</h5>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-slate-500">Jumlah Stok</p>
                                        <p class="text-lg font-bold text-slate-900" x-text="formatStock(selectedProduct.stock_quantity) + ' ' + (selectedProduct.units?.name || 'pcs')"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Satuan</p>
                                        <p class="text-lg font-bold text-slate-900" x-text="selectedProduct.units?.name || '-'"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Prices -->
                            <div class="rounded-2xl border border-slate-200 p-4">
                                <h5 class="text-sm font-semibold text-slate-700 mb-3">Harga per Kategori Customer</h5>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <template x-for="priceItem in selectedProduct.prices" :key="priceItem.customer_type">
                                        <div class="rounded-xl bg-slate-50 p-3 text-center">
                                            <p class="text-xs text-slate-500 capitalize" x-text="priceItem.customer_type"></p>
                                            <p class="text-lg font-bold text-emerald-600" x-text="'Rp ' + formatPrice(priceItem.price)"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 z-10 flex items-center justify-end gap-3 border-t border-slate-100 bg-white px-6 py-4 rounded-b-3xl">
                    <button type="button" @click="showDetailModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                        Tutup
                    </button>
                    <button type="button" @click="showDetailModal = false; openEditModal(selectedProduct)"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                        <i class="fas fa-pen-to-square"></i>
                        Edit Produk
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showEditModal = false">
                
                <!-- Header -->
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4 rounded-t-3xl">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600">
                            <i class="fas fa-pen-to-square"></i>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900">Edit Produk</h3>
                    </div>
                    <button type="button" @click="showEditModal = false" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Form -->
                <form :action="editFormAction" method="POST" enctype="multipart/form-data" class="p-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-5">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Produk <span class="text-rose-500">*</span></label>
                            <input type="text" name="title1" x-model="editForm.name" required
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                            <textarea name="description1" x-model="editForm.description" rows="3"
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500 resize-none"></textarea>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gambar Produk</label>
                            <input type="file" name="gambar-edit" accept="image/*"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100">
                            <p class="mt-1 text-xs text-slate-500">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                        </div>

                        <input type="hidden" name="supplier_id1" :value="editForm.supplier_id">

                        <!-- Prices -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">Harga per Kategori Customer</label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                @foreach ($customertypes as $type)
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ ucfirst($type) }}</label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                                            <input type="text" 
                                                x-model="editForm.prices['{{ $type }}']"
                                                @input="editForm.prices['{{ $type }}'] = formatInputPrice($event.target.value)"
                                                class="w-full rounded-xl border-slate-200 pl-10 pr-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                            <input type="hidden" name="prices[{{ $type }}]" :value="parsePrice(editForm.prices['{{ $type }}'])">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Category & SKU -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Kategori</label>
                                <select name="kategori_id1" x-model="editForm.category_id"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">SKU</label>
                                <input type="text" name="sku1" x-model="editForm.sku"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>

                        <!-- Stock & Unit -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Jumlah Stok</label>
                                <input type="number" name="stock1" x-model="editForm.stock_quantity" step="0.01" min="0"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Satuan</label>
                                <select name="satuan1" x-model="editForm.satuan"
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                        <button type="button" @click="showEditModal = false"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                            <i class="fas fa-check"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="relative w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl text-center"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="showDeleteModal = false">
                
                <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <i class="fas fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="text-lg font-bold text-slate-900">Hapus Produk?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Apakah Anda yakin ingin menghapus produk <span class="font-semibold text-slate-700" x-text="deleteProductName"></span>? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                        Batal
                    </button>
                    <form :action="deleteFormAction" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-rose-400/30 transition hover:bg-rose-600">
                            <i class="fas fa-trash-can"></i>Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function productManager() {
            return {
                // Modals
                showDetailModal: false,
                showEditModal: false,
                showDeleteModal: false,

                // Selected data
                selectedProduct: null,
                deleteProductId: null,
                deleteProductName: '',

                // Edit form
                editForm: {
                    id: null,
                    name: '',
                    description: '',
                    sku: '',
                    category_id: '',
                    stock_quantity: 0,
                    satuan: '',
                    supplier_id: '',
                    prices: {}
                },

                // Computed
                get editFormAction() {
                    return `/products/${this.editForm.id}`;
                },
                get deleteFormAction() {
                    return `/products/${this.deleteProductId}`;
                },

                // Methods
                closeAllModals() {
                    this.showDetailModal = false;
                    this.showEditModal = false;
                    this.showDeleteModal = false;
                },

                openDetailModal(product) {
                    this.selectedProduct = product;
                    this.showDetailModal = true;
                },

                openEditModal(product) {
                    this.editForm.id = product.id;
                    this.editForm.name = product.name;
                    this.editForm.description = product.description || '';
                    this.editForm.sku = product.sku || '';
                    this.editForm.category_id = product.category_id;
                    this.editForm.stock_quantity = product.stock_quantity;
                    this.editForm.satuan = product.satuan;
                    this.editForm.supplier_id = product.supplier_id;
                    
                    // Format prices
                    this.editForm.prices = {};
                    if (product.prices) {
                        product.prices.forEach(p => {
                            this.editForm.prices[p.customer_type] = this.formatPrice(p.price);
                        });
                    }
                    
                    this.showEditModal = true;
                },

                confirmDelete(id, name) {
                    this.deleteProductId = id;
                    this.deleteProductName = name;
                    this.showDeleteModal = true;
                },

                formatStock(value) {
                    if (!value) return '0';
                    const num = parseFloat(value);
                    if (num % 1 === 0) {
                        return new Intl.NumberFormat('id-ID').format(num);
                    }
                    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 2 }).format(num);
                },

                formatPrice(value) {
                    if (!value) return '0';
                    return new Intl.NumberFormat('id-ID').format(parseInt(value));
                },

                formatInputPrice(value) {
                    const clean = value.replace(/[^0-9]/g, '');
                    return clean ? new Intl.NumberFormat('id-ID').format(parseInt(clean)) : '';
                },

                parsePrice(value) {
                    if (!value) return '';
                    return value.toString().replace(/[^0-9]/g, '');
                }
            };
        }
    </script>
    @endpush
</x-app-layout>
