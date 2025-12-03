<x-app-layout>
    @php
        $todayLabel = \Illuminate\Support\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
        $userName = Auth::user()->name ?? 'Kasir';
    @endphp
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl flex-shrink-0">
                    <i class="fas fa-cash-register text-lg"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900">POS Kasir</h1>
                    <p class="text-sm text-slate-600 mt-0.5">Kasir: <span class="font-semibold text-emerald-700">{{ $userName }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <!-- Cart Counter Badge -->
                <div x-data x-show="$store.posCart && $store.posCart.count > 0" x-cloak
                     class="relative inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl lg:hidden">
                    <i class="fas fa-shopping-cart"></i>
                    <span x-text="$store.posCart ? $store.posCart.count + ' item' : '0 item'"></span>
                </div>
                <div class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <i class="fas fa-calendar-day"></i>
                    <span>{{ $todayLabel }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <form action="{{ route('pos.checkout') }}" method="POST">
        @csrf
        <div
            x-data="posApp({{ $product->toJson() }}, {{ json_encode($customertypes) }}, {{ json_encode($regularCustomers ?? []) }}, {{ json_encode($categories ?? []) }})"
            x-init="initStore()"
            class="space-y-6 pb-12 max-w-full overflow-x-hidden">

            <!-- Toast Notification (Poin 3) -->
            <div x-show="toastVisible" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50">
                <div class="flex items-center gap-3 px-5 py-3 bg-slate-900 text-white rounded-xl shadow-2xl">
                    <i class="fas fa-check-circle text-emerald-400"></i>
                    <span class="font-medium" x-text="toastMessage"></span>
                </div>
            </div>
            
            <!-- Undo Toast with action button (Poin 3) -->
            <div x-show="undoToastVisible" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50">
                <div class="flex items-center gap-4 px-5 py-3 bg-slate-900 text-white rounded-xl shadow-2xl">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-trash-alt text-red-400"></i>
                        <span class="font-medium" x-text="undoToastMessage"></span>
                    </div>
                    <button type="button" 
                            @click="undoRemove()"
                            class="px-3 py-1.5 text-xs font-bold text-emerald-400 bg-emerald-900/50 rounded-lg hover:bg-emerald-800 transition">
                        UNDO
                    </button>
                </div>
            </div>
            
            <!-- Floating Grand Total (Poin 15) - visible when scrolling cart -->
            <div x-show="cart.length > 0" x-cloak
                 class="fixed bottom-4 right-4 z-40 lg:hidden">
                <div class="flex items-center gap-3 px-4 py-3 bg-emerald-600 text-white rounded-2xl shadow-xl">
                    <i class="fas fa-shopping-cart"></i>
                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-wide opacity-80">Total</p>
                        <p class="text-lg font-bold" x-text="'Rp ' + formatCurrency(grandTotal())"></p>
                    </div>
                </div>
            </div>

            <x-breadcrumb :items="[['title' => 'POS Kasir']]" />

            @if ($errors->any())
                <div class="flex items-start gap-3 p-4 text-red-800 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-500 rounded-full">
                        <i class="fas fa-circle-exclamation"></i>
                    </span>
                    <ul class="space-y-1 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Informasi Pembeli -->
            <section class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex flex-col gap-1 px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl">
                            <i class="fas fa-user-tag"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Informasi Pembeli</h2>
                            <p class="text-sm text-slate-600">Pilih jenis pembeli dan isi data singkat penjualan.</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label class="block mb-3 text-sm font-semibold text-slate-700 uppercase tracking-wide">Jenis Pembeli</label>
                        <div class="flex flex-wrap gap-3">
                            <template x-for="type in customertypes" :key="type">
                                <button
                                    type="button"
                                    class="group inline-flex items-center gap-3 px-4 py-3 text-sm font-semibold transition border rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2"
                                    :class="customerType === type
                                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg'
                                        : 'bg-white border-emerald-100 text-slate-600 hover:border-emerald-300 hover:text-emerald-700'"
                                    @click="cart.length === 0 ? customerType = type : null"
                                    :disabled="cart.length > 0"
                                    :aria-pressed="customerType === type"
                                    :title="cart.length > 0 ? 'Jenis pembeli tidak bisa diganti setelah menambahkan barang' : ''"
                                    :style="cart.length > 0 ? 'opacity:0.5;cursor:not-allowed;' : ''">
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold bg-emerald-50 text-emerald-600 rounded-lg group-[aria-pressed=true]:bg-white/20 group-[aria-pressed=true]:text-white capitalize">
                                        <i class="fas" :class="getCustomerTypeIcon(type)"></i>
                                    </span>
                                    <span class="capitalize" x-text="type"></span>
                                </button>
                            </template>
                        </div>
                        <p class="mt-2 text-xs text-slate-500" x-show="cart.length > 0" x-cloak>
                            <i class="fas fa-info-circle text-amber-500"></i> Jenis pembeli tidak bisa diganti setelah menambahkan barang.
                        </p>
                        <input type="hidden" name="customer_type" :value="customerType">
                    </div>

                    <div class="space-y-4" x-show="customerType === 'pelanggan'" x-cloak>
                        <div class="relative">
                            <label class="block mb-2 text-sm font-semibold text-slate-700 uppercase tracking-wide">Pelanggan Terdaftar</label>
                            <!-- Search Input for Customer Autocomplete -->
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input
                                    type="text"
                                    x-model="customerSearchQuery"
                                    @input="filterCustomers()"
                                    @focus="showCustomerDropdown = true"
                                    @click.away="showCustomerDropdown = false"
                                    placeholder="Cari nama pelanggan..."
                                    class="w-full py-3 pl-12 pr-4 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                
                                <!-- Dropdown Results -->
                                <div x-show="showCustomerDropdown && filteredCustomerResults.length > 0" 
                                     x-cloak
                                     class="absolute z-50 w-full mt-1 bg-white border border-emerald-200 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    <button type="button"
                                            @click="selectCustomer(null); showCustomerDropdown = false"
                                            class="w-full px-4 py-3 text-left text-sm hover:bg-emerald-50 flex items-center gap-3 border-b border-emerald-100">
                                        <i class="fas fa-plus-circle text-emerald-500"></i>
                                        <span class="font-medium text-slate-700">Pelanggan baru / input manual</span>
                                    </button>
                                    <template x-for="customer in filteredCustomerResults" :key="customer.id">
                                        <button type="button"
                                                @click="selectCustomer(customer); showCustomerDropdown = false"
                                                class="w-full px-4 py-3 text-left text-sm hover:bg-emerald-50 flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-user text-emerald-400"></i>
                                                <span class="font-medium text-slate-700" x-text="customer.customer_name"></span>
                                            </div>
                                            <span class="text-xs text-slate-400" x-text="customer.address ? customer.address.substring(0, 30) + '...' : ''"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <!-- Selected Customer Badge -->
                            <div x-show="selectedRegularCustomer" x-cloak class="mt-2 inline-flex items-center gap-2 px-3 py-2 bg-emerald-100 text-emerald-700 rounded-lg text-sm">
                                <i class="fas fa-check-circle"></i>
                                <span x-text="selectedRegularCustomer?.customer_name"></span>
                                <button type="button" @click="selectCustomer(null)" class="ml-2 text-emerald-600 hover:text-emerald-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <input type="hidden" name="customer_id" :value="selectedRegularCustomer ? selectedRegularCustomer.id : ''">
                            <p class="mt-2 text-xs font-medium text-emerald-600" x-show="!selectedRegularCustomer && customerType === 'pelanggan'" x-cloak>
                                <i class="fas fa-lightbulb"></i> Ketik nama pelanggan untuk mencari, atau input manual.
                            </p>
                        </div>

                        <div class="p-4 border border-emerald-100 rounded-2xl bg-emerald-50/60" x-show="customerType === 'pelanggan' && selectedRegularCustomer" x-cloak>
                            <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Alamat Pelanggan</p>
                            <p class="mt-1 text-sm leading-relaxed text-emerald-700" x-text="selectedRegularCustomer.address || 'Alamat belum tersedia' "></p>
                        </div>
                    </div>

                    <div x-show="customerType !== 'pelanggan' || !selectedRegularCustomer" x-cloak>
                        <label class="block mb-2 text-sm font-semibold text-slate-700 uppercase tracking-wide">Nama Pembeli</label>
                        <input
                            type="text"
                            name="customer_name"
                            x-model="buyerName"
                            placeholder="Masukkan nama pembeli"
                            :required="!selectedRegularCustomer"
                            class="w-full px-4 py-3 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-[1fr_400px] xl:grid-cols-[1fr_420px] lg:items-start max-w-full overflow-hidden">
                <div class="space-y-6 min-w-0 overflow-hidden">
                    <!-- Produk -->
                    <section class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                        <div class="flex flex-col gap-3 px-6 py-5 border-b border-emerald-100 bg-emerald-50/40 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-900">Daftar Produk</h2>
                                <p class="text-sm text-slate-600">Cari dan pilih produk yang akan dijual.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-slate-500">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fas fa-box text-emerald-500"></i>
                                    <span x-text="filteredProducts().length + ' produk'"></span>
                                </span>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input
                                        type="search"
                                        x-ref="productSearch"
                                        x-model.debounce.300ms="searchQuery"
                                        @input="currentPage = 1"
                                        placeholder="Cari nama produk, kode, atau deskripsi…"
                                        class="w-full py-3 pl-12 pr-4 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                </div>
                                <button
                                    type="button"
                                    @click="resetFilters()"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-semibold text-emerald-600 transition border border-slate-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50">
                                    <i class="fas fa-rotate-left"></i> <span>Bersihkan filter</span>
                                </button>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Urutkan</label>
                                    <div class="relative">
                                        <select
                                            x-model="sortBy"
                                            @change="currentPage = 1"
                                            class="w-full px-4 py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 appearance-none">
                                            <option value="name_asc">Nama A-Z</option>
                                            <option value="price_asc">Harga terendah</option>
                                            <option value="price_desc">Harga tertinggi</option>
                                        </select>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none"><i class="fas fa-chevron-down text-xs"></i></span>
                                    </div>
                                </div>

                                <div>
                                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Jumlah per halaman</label>
                                    <div class="relative">
                                        <select
                                            x-model.number="perPage"
                                            @change="currentPage = 1"
                                            class="w-full px-4 py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 appearance-none">
                                            <template x-for="size in perPageOptions" :key="'per-page-' + size">
                                                <option :value="size" x-text="size + ' produk'"></option>
                                            </template>
                                        </select>
                                        <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 pointer-events-none"><i class="fas fa-chevron-down text-xs"></i></span>
                                    </div>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-1">
                                    <label class="block mb-2 text-xs font-semibold tracking-wide text-slate-500 uppercase">Kategori</label>
                                    <div class="relative overflow-hidden">
                                        <!-- Clickable scroll arrow left (Poin 12) -->
                                        <button type="button"
                                                @click="$refs.categoryScroller.scrollBy({ left: -150, behavior: 'smooth' })"
                                                x-show="categoryScrollLeft > 0"
                                                class="absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white via-white/90 to-transparent z-10 flex items-center justify-start pl-1 opacity-0 hover:opacity-100 transition-opacity cursor-pointer" 
                                                :class="{ 'opacity-80': categoryScrollLeft > 0 }">
                                            <i class="fas fa-chevron-left text-emerald-600 text-sm"></i>
                                        </button>
                                        
                                        <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide scroll-smooth px-2"
                                             x-ref="categoryScroller"
                                             @scroll="categoryScrollLeft = $refs.categoryScroller.scrollLeft; categoryScrollRight = $refs.categoryScroller.scrollWidth - $refs.categoryScroller.clientWidth - $refs.categoryScroller.scrollLeft">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl border whitespace-nowrap transition"
                                                :class="selectedCategory === 'all' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'text-slate-600 bg-white border-slate-200 hover:border-emerald-300 hover:bg-emerald-50'"
                                                @click="selectedCategory = 'all'; currentPage = 1">
                                                <i class="fas fa-layer-group mr-2"></i> Semua
                                            </button>
                                            <template x-for="category in categories" :key="'chip-'+category.id">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-4 py-2.5 text-xs font-semibold rounded-xl border whitespace-nowrap transition"
                                                    :class="String(selectedCategory) === String(category.id)
                                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                                        : 'text-slate-600 bg-white border-slate-200 hover:border-emerald-300 hover:bg-emerald-50'"
                                                    @click="selectedCategory = category.id; currentPage = 1"
                                                    x-text="category.name"></button>
                                            </template>
                                        </div>
                                        
                                        <!-- Clickable scroll arrow right (Poin 12) -->
                                        <button type="button"
                                                @click="$refs.categoryScroller.scrollBy({ left: 150, behavior: 'smooth' })"
                                                x-show="categoryScrollRight > 5"
                                                class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white via-white/90 to-transparent z-10 flex items-center justify-end pr-1 opacity-0 hover:opacity-100 transition-opacity cursor-pointer"
                                                :class="{ 'opacity-80': categoryScrollRight > 5 }">
                                            <i class="fas fa-chevron-right text-emerald-600 text-sm"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-end">
                                    <label class="inline-flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-xl w-full cursor-pointer hover:border-emerald-300 hover:bg-emerald-50 transition">
                                        <input type="checkbox" class="w-4 h-4 text-emerald-600 border-emerald-300 rounded focus:ring-emerald-500" x-model="showInStockOnly" @change="currentPage = 1">
                                        <span class="text-xs">Stok tersedia saja</span>
                                    </label>
                                </div>
                            </div>

                            <template x-if="filteredProducts().length === 0">
                                <div class="flex flex-col items-center gap-3 py-12 text-center text-emerald-700 bg-emerald-50/80 border border-emerald-100 rounded-2xl">
                                    <span class="text-4xl"><i class="fas fa-search-minus"></i></span>
                                    <p class="text-sm font-semibold">Produk tidak ditemukan</p>
                                    <p class="text-xs text-emerald-600">Coba ubah kata kunci atau pilih kategori lain.</p>
                                </div>
                            </template>

                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 max-w-full" x-show="filteredProducts().length > 0">
                                <template x-for="product in paginatedProducts()" :key="product.id">
                                    <div class="relative flex flex-col h-full gap-4 p-5 text-left transition border rounded-2xl shadow-sm bg-white border-emerald-100 hover:border-emerald-300 hover:shadow-lg focus-within:ring-2 focus-within:ring-emerald-400"
                                         :class="[isOutOfStock(product) ? 'opacity-70' : '', getCartQty(product.id) > 0 ? 'ring-2 ring-emerald-400 border-emerald-400' : '']">
                                        
                                        <!-- Qty Badge di pojok kanan atas (Poin 1) -->
                                        <span x-show="getCartQty(product.id) > 0" 
                                              class="absolute -top-2 -right-2 inline-flex items-center justify-center w-8 h-8 text-sm font-bold text-white bg-emerald-600 rounded-full shadow-lg z-10 animate-bounce-once"
                                              x-text="'x' + getCartQty(product.id)"></span>
                                        
                                        <!-- Badge Stok Habis -->
                                        <span x-show="isOutOfStock(product)" 
                                              class="absolute top-3 right-3 inline-flex items-center gap-1 px-2.5 py-1 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-lg">
                                            <i class="fas fa-ban"></i> HABIS
                                        </span>
                                        
                                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold tracking-wide text-slate-500 uppercase">
                                            <span class="inline-flex items-center px-2 py-0.5 bg-emerald-50 text-emerald-600 rounded-full border border-emerald-100" x-text="categoryLabel(product)"></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[10px]"
                                                  :class="stockBadgeClass(product)">
                                                <i class="mr-1 fas" :class="stockIconClass(product)"></i>
                                                <span x-text="stockLabel(product)"></span>
                                            </span>
                                        </div>
                                        <div class="flex-1 cursor-pointer" @click="!isOutOfStock(product) && addToCart(product)">
                                            <h3 class="text-base font-semibold text-slate-900 line-clamp-2" x-text="product.name"></h3>
                                            <p class="text-xs text-slate-500">Kode: <span x-text="product.sku || '-'" class="font-semibold"></span></p>
                                        </div>
                                        <div class="mt-auto">
                                            <p class="text-xl font-extrabold text-slate-900" x-text="'Rp ' + formatCurrency(getPrice(product))"></p>
                                            
                                            <!-- Quick Edit Qty langsung dari kartu produk (Poin 5) -->
                                            <div class="mt-3" x-show="getCartQty(product.id) > 0" x-cloak>
                                                <div class="flex items-center justify-between gap-2 p-2 bg-emerald-50 rounded-xl">
                                                    <button type="button" 
                                                            @click.stop="decrementFromCard(product.id)"
                                                            class="w-9 h-9 flex items-center justify-center bg-white border border-emerald-200 rounded-lg text-emerald-600 hover:bg-emerald-100 transition">
                                                        <i class="fas fa-minus text-xs"></i>
                                                    </button>
                                                    <span class="text-lg font-bold text-emerald-700" x-text="getCartQty(product.id)"></span>
                                                    <button type="button" 
                                                            @click.stop="addToCart(product)"
                                                            :disabled="getCartQty(product.id) >= (product.stock_quantity || 999)"
                                                            class="w-9 h-9 flex items-center justify-center bg-white border border-emerald-200 rounded-lg text-emerald-600 hover:bg-emerald-100 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                                        <i class="fas fa-plus text-xs"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <!-- Tombol Add to Cart -->
                                            <button type="button"
                                                    x-show="getCartQty(product.id) === 0"
                                                    @click="addToCart(product)"
                                                    :disabled="isOutOfStock(product)"
                                                    class="inline-flex items-center justify-center w-full h-11 mt-3 text-sm font-semibold text-white rounded-xl shadow transition"
                                                    :class="isOutOfStock(product) ? 'bg-slate-300 cursor-not-allowed' : 'bg-emerald-500 hover:bg-emerald-600'">
                                                <i class="fas mr-2" :class="isOutOfStock(product) ? 'fa-lock' : 'fa-cart-plus'"></i>
                                                <span x-text="isOutOfStock(product) ? 'Stok Habis' : 'Tambah'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="flex flex-col gap-3 pt-4 mt-4 border-t border-emerald-100" x-show="totalPages() > 1">
                                <div class="text-xs font-semibold text-slate-600">
                                    Menampilkan <span class="text-slate-900" x-text="paginationRangeLabel()"></span>
                                    dari <span class="text-slate-900" x-text="filteredProducts().length"></span> produk
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <button type="button" @click="goToPreviousPage()" :disabled="currentPage === 1"
                                            class="px-3 py-2 text-xs font-semibold border rounded-lg"
                                            :class="currentPage === 1 ? 'text-slate-300 border-slate-100 cursor-not-allowed' : 'text-emerald-700 border-emerald-200 hover:bg-emerald-50'">
                                        <i class="mr-1 fas fa-chevron-left"></i> Sebelumnya
                                    </button>
                                    <template x-for="page in pageNumbers()" :key="'page-'+page">
                                        <button type="button" @click="goToPage(page)"
                                                class="px-3 py-2 text-xs font-semibold border rounded-lg"
                                                :class="page === currentPage ? 'bg-emerald-600 text-white border-emerald-600 shadow' : 'text-emerald-700 border-emerald-200 hover:bg-emerald-50'"
                                                x-text="page"></button>
                                    </template>
                                    <button type="button" @click="goToNextPage()" :disabled="currentPage === totalPages()"
                                            class="px-3 py-2 text-xs font-semibold border rounded-lg"
                                            :class="currentPage === totalPages() ? 'text-slate-300 border-slate-100 cursor-not-allowed' : 'text-emerald-700 border-emerald-200 hover:bg-emerald-50'">
                                        Selanjutnya <i class="ml-1 fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
                <div class="min-w-0 lg:sticky lg:top-24">
                    <!-- Keranjang & Ringkasan Terintegrasi -->
                    <section class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                        <div class="flex items-center justify-between gap-3 px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Keranjang</h2>
                                    <p class="text-sm text-slate-600" x-show="cart.length === 0">Kelola item yang akan dibayar.</p>
                                    <!-- Total items dan total units (Poin 6) -->
                                    <p class="text-sm text-emerald-600 font-medium" x-show="cart.length > 0" x-cloak>
                                        <span x-text="cart.length"></span> produk 
                                        (<span x-text="getTotalUnits()"></span> unit)
                                    </p>
                                </div>
                            </div>
                            <!-- Consolidated action buttons (Poin 5) -->
                            <div class="flex items-center gap-2">
                                <!-- Held transactions badge - always visible if exists -->
                                <button
                                    type="button"
                                    @click="showHeldTransactions = true"
                                    class="relative inline-flex items-center justify-center w-10 h-10 text-blue-600 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition"
                                    x-show="heldTransactions.length > 0"
                                    title="Transaksi Tertunda">
                                    <i class="fas fa-history"></i>
                                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-blue-600 rounded-full" x-text="heldTransactions.length"></span>
                                </button>
                                
                                <!-- Dropdown menu for cart actions -->
                                <div class="relative" x-data="{ open: false }" x-show="cart.length > 0">
                                    <button type="button" 
                                            @click="open = !open"
                                            class="inline-flex items-center justify-center w-10 h-10 text-slate-600 bg-slate-100 border border-slate-200 rounded-xl hover:bg-slate-200 transition">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
                                        <button type="button" 
                                                @click="holdTransaction(); open = false"
                                                class="w-full px-4 py-2.5 text-left text-sm font-medium text-amber-700 hover:bg-amber-50 flex items-center gap-3">
                                            <i class="fas fa-pause-circle w-4"></i>
                                            <span>Tunda Transaksi</span>
                                        </button>
                                        <button type="button" 
                                                @click="clearCart(); open = false"
                                                class="w-full px-4 py-2.5 text-left text-sm font-medium text-red-600 hover:bg-red-50 flex items-center gap-3">
                                            <i class="fas fa-trash-can w-4"></i>
                                            <span>Bersihkan Keranjang</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty Cart State -->
                        <div x-show="cart.length === 0" class="p-6">
                            <div class="flex flex-col items-center justify-center w-full gap-4 py-10 text-emerald-500 bg-emerald-50/70 border-2 border-dashed border-emerald-200 rounded-2xl">
                                <span class="text-5xl"><i class="fas fa-cart-arrow-down"></i></span>
                                <div class="text-center">
                                    <p class="text-base font-bold text-emerald-700">Keranjang Kosong</p>
                                    <p class="text-sm text-emerald-600 mt-1">Klik produk untuk menambahkan ke keranjang</p>
                                </div>
                                <button type="button" 
                                        @click="document.querySelector('[x-ref=productSearch]')?.focus()"
                                        class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold text-white bg-emerald-500 rounded-xl hover:bg-emerald-600 transition shadow-lg">
                                    <i class="fas fa-search"></i>
                                    <span>Cari Produk</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Quick Total Bar - Always visible when cart has items -->
                        <div x-show="cart.length > 0" 
                             class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 flex items-center justify-between">
                            <div class="text-white">
                                <p class="text-xs opacity-80">Total Belanja</p>
                                <p class="text-xl font-extrabold" x-text="'Rp ' + formatCurrency(total)"></p>
                            </div>
                            <div class="text-right text-white">
                                <p class="text-xs opacity-80" x-text="cart.length + ' produk'"></p>
                                <p class="text-lg font-bold" x-text="getTotalUnits() + ' unit'"></p>
                            </div>
                        </div>

                        <div class="p-4 space-y-3 max-h-[50vh] overflow-y-auto" x-show="cart.length > 0" x-ref="cartContainer">
                            <!-- Unified Card Layout for Desktop & Mobile - No horizontal scroll -->
                            <template x-for="(item, index) in cart" :key="'cart-'+item.id">
                                <div class="relative p-4 bg-gradient-to-br from-slate-50 to-white rounded-2xl border-2 border-slate-100 hover:border-emerald-200 transition-all group">
                                    <!-- Delete Button - Top Right -->
                                    <button type="button"
                                            @click="removeProduct(index)"
                                            class="absolute -top-2 -right-2 w-8 h-8 flex items-center justify-center bg-red-500 text-white rounded-full shadow-lg hover:bg-red-600 hover:scale-110 transition-all opacity-0 group-hover:opacity-100 z-10">
                                        <i class="fas fa-times text-sm"></i>
                                    </button>
                                    
                                    <!-- Low Stock Warning Badge -->
                                    <div x-show="item.stock_quantity && item.stock_quantity <= 5" 
                                         class="absolute top-2 right-2 px-2 py-1 text-[10px] font-bold rounded-full"
                                         :class="item.stock_quantity <= 2 ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600'">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        <span x-text="'Sisa ' + item.stock_quantity"></span>
                                    </div>
                                    
                                    <!-- Product Info Row -->
                                    <div class="mb-3">
                                        <h4 class="text-lg font-bold text-slate-900 leading-tight pr-8" x-text="item.name"></h4>
                                        <p class="text-sm text-slate-500 mt-1">
                                            <span class="font-semibold text-emerald-600" x-text="'Rp ' + formatCurrency(item.price)"></span>
                                            <span class="mx-1">×</span>
                                            <span class="font-bold" x-text="item.qty"></span>
                                            <span class="text-xs" x-text="item.satuan || 'pcs'"></span>
                                        </p>
                                    </div>
                                    
                                    <!-- Qty Control & Subtotal Row -->
                                    <div class="flex items-center justify-between gap-4">
                                        <!-- Large Stepper Buttons -->
                                        <div class="flex items-center gap-2">
                                            <button type="button" 
                                                    @click="decrementQty(index)"
                                                    class="w-12 h-12 flex items-center justify-center bg-slate-200 text-slate-700 rounded-xl hover:bg-red-100 hover:text-red-600 active:scale-95 transition-all text-lg font-bold">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number"
                                                   inputmode="numeric"
                                                   min="1"
                                                   :max="item.stock_quantity"
                                                   x-model.number="item.qty"
                                                   @input="updateSubtotal(index)"
                                                   class="w-16 h-12 text-xl font-bold text-center bg-white border-2 border-emerald-300 rounded-xl focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">
                                            <button type="button" 
                                                    @click="incrementQty(index)"
                                                    :disabled="item.qty >= item.stock_quantity"
                                                    class="w-12 h-12 flex items-center justify-center bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 active:scale-95 transition-all text-lg font-bold disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-slate-300">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Subtotal - Large & Prominent -->
                                        <div class="text-right">
                                            <p class="text-xs text-slate-400 uppercase tracking-wide">Subtotal</p>
                                            <p class="text-xl font-extrabold text-emerald-600" x-text="'Rp ' + formatCurrency(item.subtotal)"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Hidden inputs for form submission -->
                        <div class="hidden">
                            <template x-for="(item, index) in cart" :key="'hidden-'+index">
                                <div>
                                    <input type="hidden" name="cart[id][]" :value="item.id">
                                    <input type="hidden" name="cart[name][]" :value="item.name">
                                    <input type="hidden" name="cart[qty][]" :value="item.qty">
                                    <input type="hidden" name="cart[price][]" :value="item.price">
                                    <input type="hidden" name="cart[satuan][]" :value="item.satuan">
                                    <input type="hidden" name="cart[subtotal][]" :value="item.subtotal">
                                </div>
                            </template>
                        </div>

                        <!-- ========== INTEGRATED SUMMARY SECTION ========== -->
                        
                        <!-- Compact Summary - Always Visible when cart has items -->
                        <div x-show="cart.length > 0" class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 space-y-2 text-sm">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span class="font-semibold" x-text="'Rp ' + formatCurrency(total)"></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600" x-show="shippingCost > 0">
                                <span>Ongkir</span>
                                <span class="font-semibold" x-text="'Rp ' + formatCurrency(shippingCost)"></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600" x-show="tip > 0">
                                <span>Tip</span>
                                <span class="font-semibold" x-text="'Rp ' + formatCurrency(tip)"></span>
                            </div>
                        </div>
                        
                        <!-- Grand Total Bar - Prominent -->
                        <div x-show="cart.length > 0" 
                             class="px-4 py-4 bg-gradient-to-r from-emerald-600 to-emerald-500">
                            <div class="flex items-center justify-between text-white">
                                <span class="text-base font-bold">Grand Total</span>
                                <span class="text-2xl font-extrabold" x-text="'Rp ' + formatCurrency(grandTotal())"></span>
                            </div>
                        </div>
                        
                        <!-- Ongkir, Tip, Catatan - Collapsible -->
                        <div x-show="cart.length > 0" class="border-t border-slate-100">
                            <button type="button" 
                                    @click="summaryExpanded = !summaryExpanded"
                                    class="w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-emerald-500"></i>
                                    <span>Ongkir, Tip & Catatan</span>
                                </span>
                                <i class="fas transition-transform duration-200" :class="summaryExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>
                            
                            <div x-show="summaryExpanded" x-collapse class="px-4 pb-4 space-y-3">
                                <div class="grid gap-3">
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Ongkir</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400 font-semibold text-sm">Rp</span>
                                            <input
                                                type="text"
                                                x-model="shippingCostFormatted"
                                                @input="formatShippingCost"
                                                placeholder="0"
                                                class="w-full py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                            <input type="hidden" name="shipping_cost" :value="shippingCost">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Tip (opsional)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400 font-semibold text-sm">Rp</span>
                                            <input
                                                type="text"
                                                x-model="tipFormatted"
                                                @input="formatTip"
                                                placeholder="0"
                                                class="w-full py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                            <input type="hidden" name="tip" :value="tip">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Catatan</label>
                                        <textarea
                                            name="note"
                                            rows="2"
                                            placeholder="Catatan opsional…"
                                            class="w-full px-3 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Section - Always Visible -->
                        <div x-show="cart.length > 0" class="px-4 py-4 border-t border-slate-100 bg-slate-50/80 space-y-3">
                            <input type="hidden" name="grand_total" :value="grandTotal()">

                            <div>
                                <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase tracking-wide">Uang Diterima</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-500 font-bold">Rp</span>
                                    <input
                                        type="text"
                                        inputmode="numeric"
                                        x-model="paymentReceivedFormatted"
                                        @input="formatPaymentReceived"
                                        placeholder="0"
                                        class="w-full py-3 pl-10 pr-10 text-lg font-bold text-slate-900 bg-white border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                    <input type="hidden" name="payment_received" :value="paymentReceived">
                                    <button type="button" 
                                            x-show="paymentReceived > 0"
                                            @click="paymentReceived = 0; paymentReceivedFormatted = ''"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-red-500 transition">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                                
                                <!-- Quick Amount Buttons - Compact Grid -->
                                <div class="grid grid-cols-4 gap-1.5 mt-2">
                                    <button type="button" @click="setPaymentExact()"
                                            class="px-2 py-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-lg hover:bg-emerald-200 transition col-span-2">
                                        <i class="fas fa-check mr-1"></i> Uang Pas
                                    </button>
                                    <button type="button" @click="addPaymentAmount(5000)"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                        +5rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(10000)"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                        +10rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(20000)"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                        +20rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(50000)"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                        +50rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(100000)"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                        +100rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(200000)"
                                            class="px-2 py-1.5 text-[10px] font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-100 transition">
                                        +200rb
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Kembalian Display - Compact -->
                            <div x-show="paymentReceived > 0" class="p-3 rounded-xl"
                                 :class="paymentReceived >= grandTotal() ? 'bg-blue-50 border border-blue-200' : 'bg-amber-50 border border-amber-200'">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium" 
                                          :class="paymentReceived >= grandTotal() ? 'text-blue-700' : 'text-amber-700'"
                                          x-text="paymentReceived >= grandTotal() ? 'Kembalian' : 'Kurang'"></span>
                                    <span class="text-lg font-extrabold" 
                                          :class="paymentReceived >= grandTotal() ? 'text-blue-700' : 'text-red-600'"
                                          x-text="'Rp ' + formatCurrency(Math.abs(paymentReceived - grandTotal()))"></span>
                                </div>
                            </div>

                            <!-- Action Buttons - Stacked -->
                            <div class="space-y-2 pt-2">
                                <button
                                    type="button"
                                    @click="showConfirmModal = true"
                                    :disabled="cart.length === 0 || paymentReceived < grandTotal() || isSubmitting"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3.5 text-base font-bold text-white transition rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 active:scale-[0.98]">
                                    <template x-if="!isSubmitting">
                                        <span class="inline-flex items-center gap-2">
                                            <i class="fas fa-check-circle"></i> 
                                            <span>Proses Pembayaran</span>
                                        </span>
                                    </template>
                                    <template x-if="isSubmitting">
                                        <span class="inline-flex items-center gap-2">
                                            <i class="fas fa-spinner fa-spin"></i> 
                                            <span>Memproses...</span>
                                        </span>
                                    </template>
                                </button>
                                
                                <button
                                    type="button"
                                    @click="showReceiptPreview = true"
                                    :disabled="cart.length === 0"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-slate-600 transition border border-slate-200 rounded-xl hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-receipt"></i> 
                                    <span>Preview Struk</span>
                                </button>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Confirmation Modal -->
        <div x-show="showConfirmModal" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden"
                 @click.away="showConfirmModal = false"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-amber-100 text-amber-600 rounded-full">
                        <i class="fas fa-question-circle text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Konfirmasi Penjualan</h3>
                    <p class="text-slate-600 mb-6">Penjualan akan disimpan dan tidak bisa diubah. Yakin ingin melanjutkan?</p>
                    
                    <div class="bg-slate-50 rounded-xl p-4 mb-6 text-left">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-500">Total Item:</span>
                            <span class="font-semibold" x-text="cart.length + ' produk'"></span>
                        </div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-slate-500">Grand Total:</span>
                            <span class="font-bold text-emerald-600" x-text="'Rp ' + formatCurrency(grandTotal())"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Pembayaran:</span>
                            <span class="font-semibold" x-text="'Rp ' + formatCurrency(paymentReceived)"></span>
                        </div>
                    </div>
                    
                    <div class="flex gap-3">
                        <button type="button"
                                @click="showConfirmModal = false"
                                class="flex-1 px-4 py-3 text-sm font-semibold text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="button"
                                @click="isSubmitting = true; showConfirmModal = false; $el.closest('form').submit()"
                                class="flex-1 px-4 py-3 text-sm font-bold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl hover:scale-[1.02] transition">
                            Ya, Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Preview Modal -->
        <div x-show="showReceiptPreview" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full max-h-[90vh] overflow-hidden"
                 @click.away="showReceiptPreview = false">
                <div class="p-6 overflow-y-auto max-h-[80vh]">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 mx-auto mb-2 flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-full">
                            <i class="fas fa-receipt text-xl"></i>
                        </div>
                        <h3 class="font-bold text-lg text-slate-900">Preview Struk</h3>
                        <p class="text-xs text-slate-500" x-text="new Date().toLocaleString('id-ID')"></p>
                    </div>
                    
                    <div class="border-t border-dashed border-slate-300 pt-4 mb-4">
                        <div class="text-sm mb-2">
                            <span class="text-slate-500">Pembeli:</span>
                            <span class="font-medium ml-2" x-text="buyerName || '-'"></span>
                        </div>
                        <div class="text-sm">
                            <span class="text-slate-500">Tipe:</span>
                            <span class="font-medium ml-2 capitalize" x-text="customerType"></span>
                        </div>
                    </div>
                    
                    <div class="border-t border-dashed border-slate-300 pt-4 mb-4 space-y-2">
                        <template x-for="item in cart" :key="'receipt-'+item.id">
                            <div class="flex justify-between text-sm">
                                <div class="flex-1">
                                    <p class="font-medium text-slate-900" x-text="item.name"></p>
                                    <p class="text-xs text-slate-500" x-text="item.qty + ' x Rp ' + formatCurrency(item.price)"></p>
                                </div>
                                <span class="font-semibold text-slate-700" x-text="'Rp ' + formatCurrency(item.subtotal)"></span>
                            </div>
                        </template>
                    </div>
                    
                    <div class="border-t border-dashed border-slate-300 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-medium" x-text="'Rp ' + formatCurrency(total)"></span>
                        </div>
                        <div class="flex justify-between" x-show="shippingCost > 0">
                            <span class="text-slate-500">Ongkir</span>
                            <span class="font-medium" x-text="'Rp ' + formatCurrency(shippingCost)"></span>
                        </div>
                        <div class="flex justify-between" x-show="tip > 0">
                            <span class="text-slate-500">Tip</span>
                            <span class="font-medium" x-text="'Rp ' + formatCurrency(tip)"></span>
                        </div>
                        <div class="flex justify-between pt-2 border-t border-slate-200 text-base">
                            <span class="font-bold text-slate-900">TOTAL</span>
                            <span class="font-bold text-emerald-600" x-text="'Rp ' + formatCurrency(grandTotal())"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bayar</span>
                            <span class="font-medium" x-text="'Rp ' + formatCurrency(paymentReceived)"></span>
                        </div>
                        <div class="flex justify-between" x-show="paymentReceived > grandTotal()">
                            <span class="text-slate-500">Kembalian</span>
                            <span class="font-medium text-emerald-600" x-text="'Rp ' + formatCurrency(paymentReceived - grandTotal())"></span>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <p class="text-xs text-slate-400">--- Terima Kasih ---</p>
                    </div>
                </div>
                
                <div class="px-6 pb-6 space-y-2">
                    <!-- Print button (Poin 16) -->
                    <button type="button"
                            @click="printReceipt()"
                            class="w-full px-4 py-3 text-sm font-semibold text-white bg-emerald-500 rounded-xl hover:bg-emerald-600 transition flex items-center justify-center gap-2">
                        <i class="fas fa-print"></i>
                        <span>Cetak Struk</span>
                    </button>
                    <button type="button"
                            @click="showReceiptPreview = false"
                            class="w-full px-4 py-3 text-sm font-semibold text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                        Tutup Preview
                    </button>
                </div>
            </div>
        </div>

        <!-- Held Transactions Modal -->
        <div x-show="showHeldTransactions" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[80vh] overflow-hidden"
                 @click.away="showHeldTransactions = false">
                <div class="p-6 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 flex items-center justify-center bg-blue-100 text-blue-600 rounded-xl">
                                <i class="fas fa-pause-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-slate-900">Transaksi Tertunda</h3>
                                <p class="text-sm text-slate-500" x-text="heldTransactions.length + ' transaksi'"></p>
                            </div>
                        </div>
                        <button type="button" @click="showHeldTransactions = false" class="text-slate-400 hover:text-slate-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[50vh] space-y-3">
                    <template x-if="heldTransactions.length === 0">
                        <div class="text-center py-8 text-slate-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p class="text-sm">Tidak ada transaksi tertunda</p>
                        </div>
                    </template>
                    
                    <template x-for="(held, index) in heldTransactions" :key="'held-'+index">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-semibold text-slate-900" x-text="held.buyerName || 'Tanpa Nama'"></p>
                                    <p class="text-xs text-slate-500 capitalize" x-text="held.customerType"></p>
                                </div>
                                <span class="text-xs text-slate-400" x-text="held.timestamp"></span>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm text-slate-600" x-text="held.cart.length + ' item'"></span>
                                <span class="font-bold text-emerald-600" x-text="'Rp ' + formatCurrency(held.grandTotal)"></span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button"
                                        @click="resumeTransaction(index)"
                                        class="flex-1 px-3 py-2 text-xs font-semibold text-white bg-emerald-500 rounded-lg hover:bg-emerald-600 transition">
                                    <i class="fas fa-play mr-1"></i> Lanjutkan
                                </button>
                                <button type="button"
                                        @click="removeHeldTransaction(index)"
                                        class="px-3 py-2 text-xs font-semibold text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        </div>
    </form>

    <style>
        /* Animation untuk badge qty di kartu produk */
        @keyframes bounce-once {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        .animate-bounce-once {
            animation: bounce-once 0.3s ease-out;
        }
        
        /* Smooth drag effect */
        [draggable="true"] {
            cursor: grab;
        }
        [draggable="true"]:active {
            cursor: grabbing;
            opacity: 0.8;
        }
        
        /* Collapse animation */
        [x-cloak] { display: none !important; }
    </style>

    <script>
        // Alpine.js Store for cart counter in header
        document.addEventListener('alpine:init', () => {
            Alpine.store('posCart', {
                count: 0
            });
        });

        function posApp(productsData, customerTypesData, regularCustomersData, categoriesData) {
            return {
                customertypes: Array.isArray(customerTypesData) ? customerTypesData : [],
                customerType: Array.isArray(customerTypesData) && customerTypesData.length ? customerTypesData[0] : null,
                products: Array.isArray(productsData) ? productsData : [],
                regularCustomers: Array.isArray(regularCustomersData) ? regularCustomersData : [],
                categories: Array.isArray(categoriesData) ? categoriesData : [],
                cart: [],
                total: 0,
                shippingCost: 0,
                shippingCostFormatted: '',
                tip: 0,
                tipFormatted: '',
                paymentReceived: 0,
                paymentReceivedFormatted: '',
                selectedRegularCustomerId: '__manual',
                selectedRegularCustomer: null,
                buyerName: '',
                searchQuery: '',
                selectedCategory: 'all',
                showInStockOnly: false,
                sortBy: 'name_asc',
                perPageOptions: [12, 20, 24],
                perPage: 24,
                currentPage: 1,
                
                // New features
                isSubmitting: false,
                showConfirmModal: false,
                showReceiptPreview: false,
                showHeldTransactions: false,
                heldTransactions: [],
                customerSearchQuery: '',
                showCustomerDropdown: false,
                filteredCustomerResults: [],
                categoryScrollLeft: 0,
                categoryScrollRight: 0,
                
                // Second round features
                summaryExpanded: true,
                toastMessage: '',
                toastVisible: false,
                draggedItem: null,
                
                // Third round features (Poin 3, 8, 10)
                lastRemovedItem: null,
                undoToastVisible: false,
                undoToastMessage: '',
                undoTimeout: null,
                audioEnabled: false,

                init() {
                    if (!this.customerType && this.customertypes.length) {
                        this.customerType = this.customertypes[0];
                    }
                    this.filteredCustomerResults = this.regularCustomers;
                    if (!this.perPageOptions.includes(this.perPage)) {
                        const fallback = this.perPageOptions[this.perPageOptions.length - 1] || 24;
                        this.perPage = Math.min(24, fallback);
                    }
                    
                    // Load held transactions from localStorage
                    this.loadHeldTransactions();
                    
                    // Load cart from localStorage
                    this.loadCartFromStorage();
                    
                    // Load summary expanded state (Poin 8)
                    const savedSummaryState = localStorage.getItem('pos_summary_expanded');
                    if (savedSummaryState !== null) {
                        this.summaryExpanded = savedSummaryState === 'true';
                    }
                    
                    // Watch summary expanded state
                    this.$watch('summaryExpanded', (value) => {
                        localStorage.setItem('pos_summary_expanded', value.toString());
                    });
                },

                initStore() {
                    // Update Alpine store when cart changes
                    this.$watch('cart', (value) => {
                        Alpine.store('posCart').count = value.length;
                        this.saveCartToStorage();
                    });
                },

                // Customer type icons
                getCustomerTypeIcon(type) {
                    const icons = {
                        'agent': 'fa-user-tie',
                        'reseller': 'fa-store',
                        'pelanggan': 'fa-user'
                    };
                    return icons[type] || 'fa-user';
                },

                // Customer search autocomplete
                filterCustomers() {
                    const query = this.customerSearchQuery.toLowerCase().trim();
                    if (!query) {
                        this.filteredCustomerResults = this.regularCustomers;
                    } else {
                        this.filteredCustomerResults = this.regularCustomers.filter(c => 
                            c.customer_name.toLowerCase().includes(query) ||
                            (c.address && c.address.toLowerCase().includes(query))
                        );
                    }
                },

                selectCustomer(customer) {
                    if (customer) {
                        this.selectedRegularCustomer = customer;
                        this.selectedRegularCustomerId = customer.id;
                        this.buyerName = customer.customer_name || '';
                        this.customerSearchQuery = customer.customer_name;
                        this.applyShippingCost(customer.shipping_cost ?? 0);
                    } else {
                        this.selectedRegularCustomer = null;
                        this.selectedRegularCustomerId = '__manual';
                        this.buyerName = '';
                        this.customerSearchQuery = '';
                        this.applyShippingCost(0);
                    }
                },

                // LocalStorage persistence
                saveCartToStorage() {
                    const data = {
                        cart: this.cart,
                        customerType: this.customerType,
                        buyerName: this.buyerName,
                        shippingCost: this.shippingCost,
                        tip: this.tip,
                        selectedRegularCustomerId: this.selectedRegularCustomerId,
                        timestamp: new Date().toISOString()
                    };
                    localStorage.setItem('pos_cart', JSON.stringify(data));
                },

                loadCartFromStorage() {
                    try {
                        const stored = localStorage.getItem('pos_cart');
                        if (stored) {
                            const data = JSON.parse(stored);
                            // Only restore if less than 4 hours old
                            const storedTime = new Date(data.timestamp);
                            const now = new Date();
                            const hoursDiff = (now - storedTime) / (1000 * 60 * 60);
                            
                            if (hoursDiff < 4 && data.cart && data.cart.length > 0) {
                                this.cart = data.cart;
                                this.customerType = data.customerType || this.customertypes[0];
                                this.buyerName = data.buyerName || '';
                                this.shippingCost = data.shippingCost || 0;
                                this.shippingCostFormatted = this.shippingCost ? this.toCurrencyMask(this.shippingCost) : '';
                                this.tip = data.tip || 0;
                                this.tipFormatted = this.tip ? this.toCurrencyMask(this.tip) : '';
                                this.selectedRegularCustomerId = data.selectedRegularCustomerId || '__manual';
                                this.calculateTotal();
                                
                                if (this.selectedRegularCustomerId !== '__manual') {
                                    const customer = this.regularCustomers.find(c => String(c.id) === String(this.selectedRegularCustomerId));
                                    if (customer) {
                                        this.selectedRegularCustomer = customer;
                                        this.customerSearchQuery = customer.customer_name;
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to load cart from storage:', e);
                    }
                },

                clearCartStorage() {
                    localStorage.removeItem('pos_cart');
                },

                clearCart() {
                    this.cart = [];
                    this.clearCartStorage();
                    this.calculateTotal();
                },

                // Hold/Resume transactions
                holdTransaction() {
                    if (this.cart.length === 0) return;
                    
                    const held = {
                        cart: [...this.cart],
                        customerType: this.customerType,
                        buyerName: this.buyerName,
                        shippingCost: this.shippingCost,
                        tip: this.tip,
                        selectedRegularCustomerId: this.selectedRegularCustomerId,
                        selectedRegularCustomer: this.selectedRegularCustomer,
                        grandTotal: this.grandTotal(),
                        timestamp: new Date().toLocaleString('id-ID')
                    };
                    
                    this.heldTransactions.push(held);
                    this.saveHeldTransactions();
                    
                    // Clear current cart
                    this.cart = [];
                    this.buyerName = '';
                    this.shippingCost = 0;
                    this.shippingCostFormatted = '';
                    this.tip = 0;
                    this.tipFormatted = '';
                    this.paymentReceived = 0;
                    this.paymentReceivedFormatted = '';
                    this.selectedRegularCustomer = null;
                    this.selectedRegularCustomerId = '__manual';
                    this.customerSearchQuery = '';
                    this.clearCartStorage();
                    this.calculateTotal();
                },

                resumeTransaction(index) {
                    const held = this.heldTransactions[index];
                    if (!held) return;
                    
                    // Restore cart
                    this.cart = held.cart;
                    this.customerType = held.customerType;
                    this.buyerName = held.buyerName;
                    this.shippingCost = held.shippingCost;
                    this.shippingCostFormatted = held.shippingCost ? this.toCurrencyMask(held.shippingCost) : '';
                    this.tip = held.tip;
                    this.tipFormatted = held.tip ? this.toCurrencyMask(held.tip) : '';
                    this.selectedRegularCustomerId = held.selectedRegularCustomerId;
                    this.selectedRegularCustomer = held.selectedRegularCustomer;
                    if (held.selectedRegularCustomer) {
                        this.customerSearchQuery = held.selectedRegularCustomer.customer_name;
                    }
                    
                    this.calculateTotal();
                    
                    // Remove from held list
                    this.heldTransactions.splice(index, 1);
                    this.saveHeldTransactions();
                    this.showHeldTransactions = false;
                },

                removeHeldTransaction(index) {
                    this.heldTransactions.splice(index, 1);
                    this.saveHeldTransactions();
                },

                saveHeldTransactions() {
                    localStorage.setItem('pos_held_transactions', JSON.stringify(this.heldTransactions));
                },

                loadHeldTransactions() {
                    try {
                        const stored = localStorage.getItem('pos_held_transactions');
                        if (stored) {
                            this.heldTransactions = JSON.parse(stored);
                        }
                    } catch (e) {
                        console.warn('Failed to load held transactions:', e);
                        this.heldTransactions = [];
                    }
                },

                getPrice(product) {
                    if (!product || !Array.isArray(product.prices)) {
                        return 0;
                    }
                    const row = product.prices.find(pr => pr.customer_type === this.customerType);
                    return row ? Number(row.price) || 0 : 0;
                },

                addToCart(product) {
                    if (!product) {
                        return;
                    }
                    const price = this.roundCurrency(this.getPrice(product));
                    const unitLabel = this.resolveUnit(product);
                    const stockQty = Number(product.stock_quantity) || 0;
                    const existing = this.cart.find(item => item.id === product.id);

                    if (existing) {
                        // Cek stok sebelum tambah qty
                        if (existing.qty + 1 > stockQty) {
                            this.showToast(`Stok tidak cukup! Maks ${stockQty} unit.`);
                            return;
                        }
                        existing.qty += 1;
                        existing.satuan = unitLabel;
                        existing.stock_quantity = stockQty;
                        existing.subtotal = this.roundCurrency(existing.qty * existing.price);
                        this.showToast(`${product.name} (x${existing.qty})`);
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price,
                            qty: 1,
                            satuan: unitLabel,
                            stock_quantity: stockQty,
                            subtotal: this.roundCurrency(price),
                            category: product.category?.name || 'Umum',
                        });
                        this.showToast(`${product.name} ditambahkan`);
                        
                        // (Poin 4) Auto-scroll to cart
                        this.$nextTick(() => {
                            const container = this.$refs.cartContainer;
                            if (container) {
                                container.scrollTop = container.scrollHeight;
                            }
                        });
                    }

                    this.calculateTotal();
                },

                updateSubtotal(index) {
                    const item = this.cart[index];
                    if (!item) {
                        return;
                    }
                    if (!item.qty || item.qty < 1) {
                        item.qty = 1;
                    }
                    // Validasi terhadap stok
                    if (item.stock_quantity && item.qty > item.stock_quantity) {
                        item.qty = item.stock_quantity;
                        alert(`Stok tidak cukup! Maksimal ${item.stock_quantity} unit.`);
                    }
                    item.subtotal = this.roundCurrency(item.price * item.qty);
                    this.calculateTotal();
                },

                removeProduct(index) {
                    const removedItem = this.cart[index];
                    this.lastRemovedItem = { ...removedItem, originalIndex: index };
                    this.cart.splice(index, 1);
                    this.calculateTotal();
                    
                    // Show undo toast (Poin 3)
                    this.showUndoToast(`${removedItem.name} dihapus`);
                },
                
                // Undo remove item (Poin 3)
                undoRemove() {
                    if (this.lastRemovedItem) {
                        const item = this.lastRemovedItem;
                        this.cart.splice(item.originalIndex, 0, {
                            id: item.id,
                            name: item.name,
                            price: item.price,
                            qty: item.qty,
                            satuan: item.satuan,
                            stock_quantity: item.stock_quantity,
                            subtotal: item.subtotal,
                            category: item.category
                        });
                        this.calculateTotal();
                        this.lastRemovedItem = null;
                        this.undoToastVisible = false;
                        this.showToast('Item dikembalikan');
                    }
                },
                
                showUndoToast(message) {
                    this.undoToastMessage = message;
                    this.undoToastVisible = true;
                    clearTimeout(this.undoTimeout);
                    this.undoTimeout = setTimeout(() => {
                        this.undoToastVisible = false;
                        this.lastRemovedItem = null;
                    }, 5000);
                },

                calculateTotal() {
                    const total = this.cart.reduce((sum, item) => sum + (Number(item.subtotal) || 0), 0);
                    this.total = this.roundCurrency(total);
                },

                roundCurrency(value) {
                    const numeric = Number(value) || 0;
                    return Math.round(numeric);
                },

                formatCurrency(value) {
                    const numeric = Number(value) || 0;
                    return new Intl.NumberFormat('id-ID').format(numeric);
                },

                formatShippingCost() {
                    const value = this.extractNumber(this.shippingCostFormatted);
                    this.shippingCost = value;
                    this.shippingCostFormatted = value ? this.formatCurrency(value) : '';
                },

                formatTip() {
                    const value = this.extractNumber(this.tipFormatted);
                    this.tip = value;
                    this.tipFormatted = value ? this.formatCurrency(value) : '';
                },

                formatPaymentReceived() {
                    const value = this.extractNumber(this.paymentReceivedFormatted);
                    this.paymentReceived = value;
                    this.paymentReceivedFormatted = value ? this.formatCurrency(value) : '';
                },

                extractNumber(displayValue) {
                    const digits = String(displayValue ?? '').replace(/[^0-9]/g, '');
                    return digits ? Number(digits) : 0;
                },

                toCurrencyMask(value) {
                    return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
                },

                handleRegularCustomerChange() {
                    if (!this.selectedRegularCustomerId || this.selectedRegularCustomerId === '__manual') {
                        this.selectedRegularCustomer = null;
                        this.buyerName = '';
                        this.applyShippingCost(0);
                        return;
                    }

                    const selected = this.regularCustomers.find(customer => String(customer.id) === String(this.selectedRegularCustomerId));

                    if (selected) {
                        this.selectedRegularCustomer = selected;
                        this.buyerName = selected.customer_name || '';
                        this.applyShippingCost(selected.shipping_cost ?? 0);
                    } else {
                        this.selectedRegularCustomer = null;
                        this.buyerName = '';
                        this.applyShippingCost(0);
                    }
                },

                applyShippingCost(value) {
                    const numeric = Number(value) || 0;
                    this.shippingCost = numeric;
                    this.shippingCostFormatted = numeric ? this.toCurrencyMask(numeric) : '';
                },

                resolveUnit(product) {
                    if (product?.units?.name) {
                        return product.units.name;
                    }

                    if (product && Object.prototype.hasOwnProperty.call(product, 'unit_name') && product.unit_name) {
                        return product.unit_name;
                    }

                    const rawUnit = product && Object.prototype.hasOwnProperty.call(product, 'satuan')
                        ? product.satuan
                        : null;

                    if (typeof rawUnit === 'string' && rawUnit.trim() !== '') {
                        return Number.isNaN(Number(rawUnit)) ? rawUnit : 'pcs';
                    }

                    if (typeof rawUnit === 'number' && !Number.isNaN(rawUnit)) {
                        return 'pcs';
                    }

                    return 'pcs';
                },

                grandTotal() {
                    return this.roundCurrency(this.total + this.shippingCost + this.tip);
                },

                filteredProducts() {
                    let items = Array.isArray(this.products) ? [...this.products] : [];
                    const query = this.searchQuery.trim().toLowerCase();

                    if (query.length) {
                        items = items.filter(product => {
                            const name = (product.name || '').toLowerCase();
                            const sku = (product.sku || '').toLowerCase();
                            const description = (product.description || '').toLowerCase();
                            return name.includes(query) || sku.includes(query) || description.includes(query);
                        });
                    }

                    if (this.selectedCategory !== 'all') {
                        items = items.filter(product => String(this.extractCategoryId(product)) === String(this.selectedCategory));
                    }

                    if (this.showInStockOnly) {
                        items = items.filter(product => (Number(product.stock_quantity) || 0) > 0);
                    }

                    items.sort((a, b) => {
                        switch (this.sortBy) {
                            case 'price_asc':
                                return this.getPrice(a) - this.getPrice(b);
                            case 'price_desc':
                                return this.getPrice(b) - this.getPrice(a);
                            case 'name_asc':
                            default:
                                return (a.name || '').localeCompare(b.name || '', 'id', { sensitivity: 'base' });
                        }
                    });

                    return items;
                },

                paginatedProducts() {
                    const items = this.filteredProducts();
                    const totalPages = Math.max(1, Math.ceil(items.length / this.perPage));
                    if (this.currentPage > totalPages) {
                        this.currentPage = totalPages;
                    }
                    if (this.currentPage < 1) {
                        this.currentPage = 1;
                    }
                    const start = (this.currentPage - 1) * this.perPage;
                    return items.slice(start, start + this.perPage);
                },

                totalPages() {
                    const count = this.filteredProducts().length;
                    return Math.max(1, Math.ceil(count / this.perPage));
                },

                goToPage(page) {
                    const target = Number(page);
                    if (Number.isNaN(target)) {
                        return;
                    }
                    const total = this.totalPages();
                    if (target >= 1 && target <= total) {
                        this.currentPage = target;
                    }
                },

                goToPreviousPage() {
                    if (this.currentPage > 1) {
                        this.currentPage -= 1;
                    }
                },

                goToNextPage() {
                    if (this.currentPage < this.totalPages()) {
                        this.currentPage += 1;
                    }
                },

                pageNumbers() {
                    const total = this.totalPages();
                    const maxButtons = 5;
                    let start = Math.max(1, this.currentPage - Math.floor(maxButtons / 2));
                    let end = Math.min(total, start + maxButtons - 1);
                    start = Math.max(1, end - maxButtons + 1);

                    const pages = [];
                    for (let i = start; i <= end; i += 1) {
                        pages.push(i);
                    }
                    return pages;
                },

                paginationRangeLabel() {
                    const itemsCount = this.filteredProducts().length;
                    if (!itemsCount) {
                        return '0';
                    }
                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(start + this.perPage - 1, itemsCount);
                    return `${this.formatNumber(start)} – ${this.formatNumber(end)}`;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.selectedCategory = 'all';
                    this.showInStockOnly = false;
                    this.sortBy = 'name_asc';
                    this.perPage = 24;
                    this.currentPage = 1;
                },

                categoryLabel(product) {
                    if (product?.category?.name) {
                        return product.category.name;
                    }
                    const match = this.categories.find(category => String(category.id) === String(this.extractCategoryId(product)));
                    return match ? match.name : 'Umum';
                },

                extractCategoryId(product) {
                    if (!product) {
                        return null;
                    }
                    if (product?.category?.id) {
                        return product.category.id;
                    }
                    if (Object.prototype.hasOwnProperty.call(product, 'category_id')) {
                        return product.category_id;
                    }
                    return null;
                },

                stockLabel(product) {
                    const qty = Number(product?.stock_quantity);
                    if (!Number.isFinite(qty) || qty < 0) {
                        return 'Tidak diketahui';
                    }
                    if (qty === 0) {
                        return 'Habis';
                    }
                    if (qty <= 5) {
                        return `Tersisa ${qty}`;
                    }
                    return `Stok: ${qty}`;
                },
                
                stockBadgeClass(product) {
                    const qty = Number(product?.stock_quantity) || 0;
                    if (qty === 0) {
                        return 'bg-red-50 text-red-600 border-red-200';
                    }
                    if (qty <= 5) {
                        return 'bg-orange-50 text-orange-600 border-orange-200';
                    }
                    return 'bg-slate-50 text-slate-600 border-slate-200';
                },
                
                stockIconClass(product) {
                    const qty = Number(product?.stock_quantity) || 0;
                    if (qty === 0) {
                        return 'fa-exclamation-triangle';
                    }
                    if (qty <= 5) {
                        return 'fa-exclamation-circle';
                    }
                    return 'fa-box-open';
                },

                isOutOfStock(product) {
                    return (Number(product?.stock_quantity) || 0) <= 0;
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
                },

                // === SECOND ROUND: New helper functions ===

                // (Poin 1 & 5) Get qty of product in cart
                getCartQty(productId) {
                    const item = this.cart.find(i => i.id === productId);
                    return item ? item.qty : 0;
                },

                // (Poin 5) Decrement qty from product card
                decrementFromCard(productId) {
                    const index = this.cart.findIndex(i => i.id === productId);
                    if (index > -1) {
                        if (this.cart[index].qty > 1) {
                            this.cart[index].qty -= 1;
                            this.cart[index].subtotal = this.roundCurrency(this.cart[index].qty * this.cart[index].price);
                            this.showToast(`${this.cart[index].name} dikurangi`);
                        } else {
                            const name = this.cart[index].name;
                            this.cart.splice(index, 1);
                            this.showToast(`${name} dihapus dari keranjang`);
                        }
                        this.calculateTotal();
                    }
                },

                // (Poin 2) Increment qty in cart
                incrementQty(index) {
                    const item = this.cart[index];
                    if (item && item.qty < (item.stock_quantity || 999)) {
                        item.qty += 1;
                        item.subtotal = this.roundCurrency(item.qty * item.price);
                        this.calculateTotal();
                    }
                },

                // (Poin 2) Decrement qty in cart
                decrementQty(index) {
                    const item = this.cart[index];
                    if (item) {
                        if (item.qty > 1) {
                            item.qty -= 1;
                            item.subtotal = this.roundCurrency(item.qty * item.price);
                        } else {
                            this.cart.splice(index, 1);
                        }
                        this.calculateTotal();
                    }
                },

                // Get cart index by product ID
                getCartIndex(productId) {
                    return this.cart.findIndex(i => i.id === productId);
                },

                // (Poin 6) Get total units in cart
                getTotalUnits() {
                    return this.cart.reduce((sum, item) => sum + (item.qty || 0), 0);
                },

                // (Poin 7) Set exact payment amount
                setPaymentExact() {
                    this.paymentReceived = this.grandTotal();
                    this.paymentReceivedFormatted = this.toCurrencyMask(this.paymentReceived);
                },

                // (Poin 7) Add amount to payment
                addPaymentAmount(amount) {
                    this.paymentReceived += amount;
                    this.paymentReceivedFormatted = this.toCurrencyMask(this.paymentReceived);
                },

                // (Poin 3) Show toast notification
                showToast(message) {
                    this.toastMessage = message;
                    this.toastVisible = true;
                    setTimeout(() => {
                        this.toastVisible = false;
                    }, 2000);
                },

                // (Poin 13) Group cart items by category
                getCartGroupedByCategory() {
                    const groups = {};
                    this.cart.forEach(item => {
                        const product = this.products.find(p => p.id === item.id);
                        const categoryName = product?.category?.name || 'Umum';
                        if (!groups[categoryName]) {
                            groups[categoryName] = [];
                        }
                        groups[categoryName].push(item);
                    });
                    return groups;
                },

                // (Poin 12) Drag and drop handlers
                dragStart(event, item) {
                    this.draggedItem = item;
                    event.dataTransfer.effectAllowed = 'move';
                },

                dragOver(event) {
                    event.preventDefault();
                    event.dataTransfer.dropEffect = 'move';
                },

                drop(event, targetItem) {
                    event.preventDefault();
                    if (this.draggedItem && this.draggedItem.id !== targetItem.id) {
                        const fromIndex = this.cart.findIndex(i => i.id === this.draggedItem.id);
                        const toIndex = this.cart.findIndex(i => i.id === targetItem.id);
                        if (fromIndex > -1 && toIndex > -1) {
                            const [movedItem] = this.cart.splice(fromIndex, 1);
                            this.cart.splice(toIndex, 0, movedItem);
                        }
                    }
                    this.draggedItem = null;
                },
                
                // Print receipt (Poin 16)
                printReceipt() {
                    const printContent = document.createElement('div');
                    printContent.innerHTML = `
                        <div style="font-family: 'Courier New', monospace; width: 300px; padding: 20px;">
                            <div style="text-align: center; margin-bottom: 15px;">
                                <h2 style="margin: 0; font-size: 18px;">STRUK PENJUALAN</h2>
                                <p style="margin: 5px 0; font-size: 12px;">${new Date().toLocaleString('id-ID')}</p>
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="margin: 10px 0;">
                                <p style="margin: 3px 0; font-size: 12px;">Pembeli: ${this.buyerName || '-'}</p>
                                <p style="margin: 3px 0; font-size: 12px;">Tipe: ${this.customerType}</p>
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="margin: 10px 0;">
                                ${this.cart.map(item => `
                                    <div style="margin: 8px 0; font-size: 12px;">
                                        <p style="margin: 0; font-weight: bold;">${item.name}</p>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>${item.qty} x Rp ${this.formatCurrency(item.price)}</span>
                                            <span>Rp ${this.formatCurrency(item.subtotal)}</span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="margin: 10px 0; font-size: 12px;">
                                <div style="display: flex; justify-content: space-between;"><span>Subtotal</span><span>Rp ${this.formatCurrency(this.total)}</span></div>
                                ${this.shippingCost > 0 ? `<div style="display: flex; justify-content: space-between;"><span>Ongkir</span><span>Rp ${this.formatCurrency(this.shippingCost)}</span></div>` : ''}
                                ${this.tip > 0 ? `<div style="display: flex; justify-content: space-between;"><span>Tip</span><span>Rp ${this.formatCurrency(this.tip)}</span></div>` : ''}
                                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px;"><span>TOTAL</span><span>Rp ${this.formatCurrency(this.grandTotal())}</span></div>
                                <div style="display: flex; justify-content: space-between;"><span>Bayar</span><span>Rp ${this.formatCurrency(this.paymentReceived)}</span></div>
                                ${this.paymentReceived > this.grandTotal() ? `<div style="display: flex; justify-content: space-between;"><span>Kembalian</span><span>Rp ${this.formatCurrency(this.paymentReceived - this.grandTotal())}</span></div>` : ''}
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <p style="text-align: center; margin-top: 15px; font-size: 11px;">--- Terima Kasih ---</p>
                        </div>
                    `;
                    
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write('<html><head><title>Struk</title></head><body>');
                    printWindow.document.write(printContent.innerHTML);
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.print();
                }
            };
        }
    </script>
</x-app-layout>
