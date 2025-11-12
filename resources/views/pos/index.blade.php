<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-700 rounded-full">
                    <i class="fas fa-cash-register"></i>
                </span>
                Point of Sale
            </h1>
            <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold tracking-wide text-white uppercase rounded-full bg-gradient-to-r from-green-500 to-emerald-600">
                <i class="fas fa-star"></i>
                Versi Beta
            </span>
        </div>
    </x-slot>

    <div class="space-y-6"
         x-data="posApp({{ $product->toJson() }}, {{ json_encode($customertypes) }}, {{ json_encode($regularCustomers ?? []) }})">
        <x-breadcrumb :items="[['title' => 'Point of Sale']]" />

        <form method="POST" class="space-y-6">
            @csrf

            <div class="space-y-6">
                <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="flex flex-col justify-between p-4 bg-white border border-emerald-100 rounded-2xl shadow-sm">
                        <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Tipe Pelanggan</span>
                        <div class="mt-3 text-lg font-semibold text-slate-900" x-text="customerType || 'Belum dipilih'"></div>
                        <p class="mt-1 text-xs text-slate-500">Pilih tipe sebelum memasukkan produk.</p>
                    </article>
                    <article class="flex flex-col justify-between p-4 bg-white border border-emerald-100 rounded-2xl shadow-sm">
                        <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Produk Tersedia</span>
                        <div class="mt-3 text-2xl font-bold text-emerald-600" x-text="catalogCount"></div>
                        <p class="mt-1 text-xs text-slate-500">Mengikuti pencarian & filter aktif.</p>
                    </article>
                    <article class="flex flex-col justify-between p-4 bg-white border border-emerald-100 rounded-2xl shadow-sm">
                        <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Item di Keranjang</span>
                        <div class="mt-3 text-2xl font-bold text-emerald-600" x-text="cartQuantity"></div>
                        <p class="mt-1 text-xs text-slate-500">Jumlah total unit yang sudah dipilih.</p>
                    </article>
                    <article class="flex flex-col justify-between p-4 bg-white border border-emerald-100 rounded-2xl shadow-sm">
                        <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Grand Total</span>
                        <div class="mt-3 text-2xl font-bold text-emerald-600" x-text="currency(grandTotal)"></div>
                        <p class="mt-1 text-xs text-slate-500">Sudah termasuk ongkir & tip.</p>
                    </article>
                </section>

                <div class="grid gap-6 xl:grid-cols-12">
                    <section class="space-y-6 xl:col-span-8">
                        <section class="bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                            <div class="flex flex-col gap-6 p-6">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <h2 class="text-lg font-semibold text-slate-900">Kelola Katalog</h2>
                                        <p class="mt-1 text-sm text-slate-600">Gunakan pencarian, filter, dan tampilan untuk menemukan produk lebih cepat.</p>
                                    </div>
                                    <div class="inline-flex items-center gap-2 p-1 text-sm font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-xl">
                                        <button type="button"
                                                class="px-3 py-1 rounded-lg transition"
                                                :class="viewMode === 'grid' ? 'bg-white shadow text-emerald-600' : 'text-emerald-500 hover:text-emerald-600'"
                                                @click="toggleView('grid')">
                                            <i class="fas fa-border-all"></i>
                                            <span class="ml-2 hidden sm:inline">Grid</span>
                                        </button>
                                        <button type="button"
                                                class="px-3 py-1 rounded-lg transition"
                                                :class="viewMode === 'list' ? 'bg-white shadow text-emerald-600' : 'text-emerald-500 hover:text-emerald-600'"
                                                @click="toggleView('list')">
                                            <i class="fas fa-list"></i>
                                            <span class="ml-2 hidden sm:inline">Daftar</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid gap-4 lg:grid-cols-12">
                                    <div class="relative lg:col-span-6">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text"
                                               x-model="productSearch"
                                               placeholder="Cari nama produk atau SKU"
                                               class="w-full py-2.5 pl-12 pr-4 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>

                                    <div class="lg:col-span-4">
                                        <label class="flex items-center w-full gap-2 px-3 py-2 text-xs font-semibold tracking-wide text-emerald-600 uppercase bg-white border border-emerald-100 rounded-xl shadow-sm">
                                            <i class="fas fa-sort"></i>
                                            <span>Urutkan</span>
                                            <select x-model="sortOption"
                                                    class="flex-1 text-sm font-semibold text-emerald-700 bg-transparent border-none focus:ring-0 focus:outline-none">
                                                <option value="name-asc">Nama (A-Z)</option>
                                                <option value="name-desc">Nama (Z-A)</option>
                                                <option value="price-asc">Harga (Termurah)</option>
                                                <option value="price-desc">Harga (Termahal)</option>
                                            </select>
                                        </label>
                                    </div>

                                    <div class="lg:col-span-2 flex items-center justify-end">
                                        <button type="button"
                                                class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-xl shadow-sm disabled:opacity-40"
                                                :disabled="!hasActiveFilter"
                                                @click="resetProductFilters">
                                            Reset Filter
                                        </button>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    <span class="text-xs font-semibold uppercase text-slate-500">Filter satuan:</span>
                                    <template x-if="!units().length">
                                        <span class="text-xs text-slate-400">Tidak ada data satuan khusus</span>
                                    </template>
                                    <button type="button"
                                            class="px-3 py-1 text-xs font-semibold tracking-wide uppercase rounded-full border transition"
                                            :class="selectedUnit === 'all' ? 'bg-emerald-500 text-white border-emerald-500' : 'border-emerald-100 text-emerald-600 hover:bg-emerald-50'"
                                            @click="selectedUnit = 'all'">
                                        Semua
                                    </button>
                                    <template x-for="unit in units()" :key="unit">
                                        <button type="button"
                                                class="px-3 py-1 text-xs font-semibold tracking-wide uppercase rounded-full border transition"
                                                :class="selectedUnit === unit ? 'bg-emerald-500 text-white border-emerald-500' : 'border-emerald-100 text-emerald-600 hover:bg-emerald-50'"
                                                @click="selectedUnit = selectedUnit === unit ? 'all' : unit">
                                            <span x-text="unit"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </section>

                        <section class="bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                            <div class="flex items-center justify-between p-6 border-b border-emerald-100 bg-emerald-50/40 rounded-t-2xl">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Daftar Produk</h2>
                                    <p class="mt-1 text-sm text-slate-600">Klik untuk menambahkan beberapa item sekaligus.</p>
                                </div>
                                <span class="text-xs font-semibold tracking-wide text-emerald-600 uppercase" x-text="catalogCountLabel"></span>
                            </div>

                            <div class="p-6 space-y-5">
                                <template x-if="!catalogCount">
                                    <div class="flex flex-col items-center justify-center h-48 space-y-3 text-slate-500 bg-emerald-50/50 border border-dashed border-emerald-100 rounded-2xl">
                                        <span class="inline-flex items-center justify-center w-12 h-12 text-xl bg-white rounded-full shadow">
                                            <i class="fas fa-box-open"></i>
                                        </span>
                                        <p class="text-sm font-medium text-center">Tidak ada produk yang cocok. Ubah filter atau ketik kata kunci berbeda.</p>
                                    </div>
                                </template>

                                <div :class="viewMode === 'grid' ? 'grid grid-cols-1 gap-5 sm:grid-cols-2 2xl:grid-cols-3' : 'space-y-4'" x-show="catalogCount">
                                    <template x-for="product in paginatedProducts()" :key="product.id">
                                        <div :class="viewMode === 'grid' ? 'flex flex-col h-full p-5 bg-white border border-emerald-100 rounded-2xl shadow-sm transition hover:shadow-md focus-within:ring-2 focus-within:ring-emerald-400' : 'flex flex-col md:flex-row md:items-center md:justify-between gap-4 p-5 bg-white border border-emerald-100 rounded-2xl shadow-sm transition hover:shadow-md focus-within:ring-2 focus-within:ring-emerald-400'">
                                            <div class="flex items-start justify-between gap-4 w-full" :class="viewMode === 'list' ? 'md:w-2/3' : ''">
                                                <div>
                                                    <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase" x-text="product.satuan || 'Produk'"></span>
                                                    <h3 class="mt-1 text-base font-semibold text-slate-900" x-text="product.name"></h3>
                                                    <p class="mt-2 text-xs text-slate-500" x-text="product.description || 'Deskripsi belum tersedia.'"></p>
                                                </div>
                                                <span class="inline-flex items-center px-3 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 rounded-full">
                                                    <span x-text="product.sku || ('SKU-' + product.id)"></span>
                                                </span>
                                            </div>

                                            <div class="flex flex-col w-full gap-4 mt-4 md:mt-0" :class="viewMode === 'list' ? 'md:w-1/3 md:items-end' : ''">
                                                <div class="flex items-center justify-between">
                                                    <p class="text-xs font-semibold text-emerald-500">Harga untuk tipe ini</p>
                                                    <p class="text-lg font-bold text-emerald-600" x-text="currency(getPrice(product))"></p>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <div class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 border border-emerald-100 rounded-xl shadow-sm">
                                                        <button type="button"
                                                                class="text-emerald-500 transition hover:text-emerald-600"
                                                                @click="decreaseQuantity(product.id)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number"
                                                               min="1"
                                                               x-model.number="productQuantities[product.id]"
                                                               @input="normalizeQuantity(product.id)"
                                                               class="w-12 text-center bg-transparent focus:outline-none" />
                                                        <button type="button"
                                                                class="text-emerald-500 transition hover:text-emerald-600"
                                                                @click="increaseQuantity(product.id)">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>

                                                    <button type="button"
                                                            class="inline-flex items-center justify-center flex-1 px-4 py-2 text-sm font-semibold text-white transition bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow hover:shadow-lg"
                                                            @click="quickAdd(product)">
                                                        <i class="mr-2 fas fa-cart-plus"></i>
                                                        Tambah
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex flex-col gap-3 pt-4 border-t border-emerald-100" x-show="totalPages > 1">
                                    <div class="text-xs font-semibold text-slate-500">
                                        Menampilkan <span x-text="pageStart"></span> - <span x-text="pageEnd"></span> dari <span x-text="catalogCount"></span> produk
                                    </div>
                                    <div class="flex items-center justify-between gap-3">
                                        <button type="button"
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold uppercase tracking-wide rounded-xl border border-emerald-100 text-emerald-600 bg-white disabled:opacity-40"
                                                :disabled="!canGoPrev"
                                                @click="prevPage">
                                            <i class="fas fa-arrow-left"></i>
                                            Prev
                                        </button>
                                        <div class="flex items-center gap-1">
                                            <template x-for="page in visiblePages" :key="page">
                                                <button type="button"
                                                        class="w-9 h-9 text-xs font-semibold rounded-lg border transition"
                                                        :class="page === currentPage ? 'bg-emerald-500 text-white border-emerald-500' : 'border-emerald-100 text-emerald-600 hover:bg-emerald-50'"
                                                        @click="goToPage(page)"
                                                        x-text="page"></button>
                                            </template>
                                        </div>
                                        <button type="button"
                                                class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold uppercase tracking-wide rounded-xl border border-emerald-100 text-emerald-600 bg-white disabled:opacity-40"
                                                :disabled="!canGoNext"
                                                @click="nextPage">
                                            Next
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </section>

                    <aside class="space-y-6 xl:col-span-4">
                        <section class="bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                            <div class="p-6 border-b border-emerald-100 bg-emerald-50/40 rounded-t-2xl">
                                <h2 class="text-lg font-semibold text-slate-900">Detail Pembeli</h2>
                                <p class="mt-1 text-sm text-slate-600">Informasi kontak membantu saat pengantaran dan nota.</p>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="type in customertypes" :key="type">
                                        <button type="button"
                                                class="px-4 py-2 text-sm font-semibold rounded-xl border transition"
                                                :class="customerType === type
                                                    ? 'bg-gradient-to-r from-emerald-500 to-emerald-600 text-white shadow'
                                                    : 'border-emerald-100 text-emerald-600 bg-white hover:bg-emerald-50'"
                                                :disabled="cart.length"
                                                :aria-pressed="customerType === type"
                                                @click="selectCustomerType(type)">
                                            <i class="fas fa-user-tag mr-2"></i>
                                            <span class="capitalize" x-text="type"></span>
                                        </button>
                                    </template>
                                    <input type="hidden" name="customer_type" :value="customerType">
                                </div>

                                <div class="space-y-3">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-700" for="buyer-name">Nama Pembeli</label>
                                        <input id="buyer-name"
                                               type="text"
                                               name="customer_name"
                                               x-model="buyerName"
                                               placeholder="Masukkan nama atau kode pelanggan"
                                               required
                                               class="w-full px-4 py-2.5 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>

                                    <template x-if="regularCustomers.length">
                                        <div class="space-y-2">
                                            <label class="text-sm font-semibold text-slate-700" for="regular-customer">Pelanggan Terdaftar</label>
                                            <select id="regular-customer"
                                                    x-model="selectedRegularCustomer"
                                                    @change="handleRegularCustomerChange"
                                                    class="w-full px-4 py-2.5 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                                <option value="">Pilih dari daftar</option>
                                                <template x-for="name in regularCustomers" :key="name">
                                                    <option :value="name" x-text="name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </template>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-700" for="order-note">Catatan Pesanan</label>
                                        <textarea id="order-note"
                                                  name="note"
                                                  x-model="note"
                                                  rows="3"
                                                  placeholder="Tambahkan catatan khusus untuk tim kasir atau pengantar"
                                                  class="w-full px-4 py-2.5 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"></textarea>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                            <div class="flex items-start justify-between gap-3 p-6 border-b border-emerald-100 bg-emerald-50/40 rounded-t-2xl">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Keranjang Aktif</h2>
                                    <p class="mt-1 text-sm text-slate-600">Atur jumlah dan satuan setiap item sebelum checkout.</p>
                                </div>
                                <button type="button"
                                        class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-red-600 bg-white border border-red-100 rounded-xl shadow-sm hover:bg-red-50 disabled:opacity-40"
                                        :disabled="!cart.length"
                                        @click="resetCart">
                                    Kosongkan
                                </button>
                            </div>
                            <div class="p-6 space-y-5">
                                <template x-if="!cart.length">
                                    <div class="flex flex-col items-center justify-center h-40 space-y-3 text-slate-500 bg-emerald-50/40 border border-dashed border-emerald-100 rounded-2xl">
                                        <span class="inline-flex items-center justify-center w-10 h-10 text-lg bg-white rounded-full shadow">
                                            <i class="fas fa-basket-shopping"></i>
                                        </span>
                                        <p class="text-sm font-medium text-center">Keranjang masih kosong. Tambahkan produk dari daftar di sebelah kiri.</p>
                                    </div>
                                </template>

                                <ul class="space-y-4" x-show="cart.length" role="list">
                                    <template x-for="(item, index) in cart" :key="item.id">
                                        <li class="p-4 border border-emerald-100 rounded-xl shadow-sm bg-white">
                                            <input type="hidden" :name="`cart[id][${index}]`" :value="item.id" required>
                                            <input type="hidden" :name="`cart[price][${index}]`" :value="item.price" required>
                                            <input type="hidden" :name="`cart[subtotal][${index}]`" :value="item.subtotal" required>

                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-900" x-text="item.name"></p>
                                                    <p class="mt-1 text-xs font-semibold text-emerald-500">Rp <span x-text="currency(item.price)"></span> / <span x-text="item.satuan"></span></p>
                                                </div>
                                                <button type="button"
                                                        class="inline-flex items-center justify-center w-8 h-8 text-xs text-white transition bg-red-500 rounded-full shadow hover:bg-red-600"
                                                        @click="removeProduct(index)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>

                                            <div class="flex flex-wrap items-center gap-3 mt-4">
                                                <div class="flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 border border-emerald-100 rounded-xl shadow-sm">
                                                    <button type="button"
                                                            class="text-emerald-500 transition hover:text-emerald-600"
                                                            @click="decrementCartItem(index)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number"
                                                           min="1"
                                                           x-model.number="item.qty"
                                                           @input="updateSubtotal(index)"
                                                           :name="`cart[qty][${index}]`"
                                                           class="w-14 text-center bg-transparent focus:outline-none">
                                                    <button type="button"
                                                            class="text-emerald-500 transition hover:text-emerald-600"
                                                            @click="incrementCartItem(index)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>

                                                <select x-model="item.satuan"
                                                        :name="`cart[satuan][${index}]`"
                                                        class="px-3 py-2 text-sm font-medium bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                                    <option value="pcs">pcs</option>
                                                    <option value="box">dus</option>
                                                    <option value="lusin">lusin</option>
                                                </select>

                                                <p class="ml-auto text-sm font-semibold text-emerald-600">
                                                    Subtotal: <span x-text="currency(item.subtotal)"></span>
                                                </p>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </section>

                        <section class="bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                            <div class="p-6 border-b border-emerald-100 bg-emerald-50/40 rounded-t-2xl">
                                <h2 class="text-lg font-semibold text-slate-900">Pengiriman & Pembayaran</h2>
                                <p class="mt-1 text-sm text-slate-600">Masukkan biaya tambahan dan konfirmasi nominal pembayaran.</p>
                            </div>
                            <div class="p-6 space-y-5">
                                <div class="grid gap-4">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-700" for="shipping-cost">Ongkir</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-emerald-500">Rp</span>
                                            <input id="shipping-cost"
                                                   type="text"
                                                   name="shippingCost"
                                                   x-model="shippingCostDisplay"
                                                   @input="formatMoney('shippingCost')"
                                                   placeholder="0"
                                                   class="w-full py-2.5 pl-12 pr-4 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                            <input type="hidden" name="shippingCost_value" :value="shippingCost">
                                        </div>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-slate-700" for="tip">Tip</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-emerald-500">Rp</span>
                                            <input id="tip"
                                                   type="text"
                                                   name="tip"
                                                   x-model="tipDisplay"
                                                   @input="formatMoney('tip')"
                                                   placeholder="0"
                                                   class="w-full py-2.5 pl-12 pr-4 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                            <input type="hidden" name="tip_value" :value="tip">
                                        </div>
                                    </div>
                                </div>

                                <dl class="space-y-3 text-sm text-slate-600">
                                    <div class="flex items-center justify-between">
                                        <dt>Total Produk</dt>
                                        <dd class="font-semibold text-slate-900" x-text="cart.length + ' jenis'">
                                        </dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>Total Qty</dt>
                                        <dd class="font-semibold text-slate-900" x-text="cartQuantity"></dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>Total Belanja</dt>
                                        <dd class="font-semibold text-emerald-600" x-text="currency(total)"></dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>Ongkir</dt>
                                        <dd class="font-semibold text-emerald-600" x-text="currency(shippingCost)"></dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt>Tip</dt>
                                        <dd class="font-semibold text-emerald-600" x-text="currency(tip)"></dd>
                                    </div>
                                </dl>

                                <div class="pt-4 border-t border-emerald-100">
                                    <div class="flex items-center justify-between text-base font-bold text-slate-900">
                                        <span>Grand Total</span>
                                        <span x-text="currency(grandTotal)"></span>
                                    </div>
                                    <input type="hidden" name="grand_total" :value="grandTotal">
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-slate-700" for="payment-received">Pembayaran diterima</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-emerald-500">Rp</span>
                                        <input id="payment-received"
                                               type="text"
                                               name="paymentReceived"
                                               x-model="paymentDisplay"
                                               @input="formatMoney('payment')"
                                               placeholder="0"
                                               class="w-full py-2.5 pl-12 pr-4 text-sm bg-white border border-emerald-100 rounded-xl shadow-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                        <input type="hidden" name="paymentReceived_value" :value="paymentReceived">
                                    </div>
                                    <template x-if="paymentReceived > 0">
                                        <p class="text-sm font-semibold text-emerald-600">
                                            Kembalian: <span x-text="currency(change)"></span>
                                            <input type="hidden" name="change" :value="change">
                                        </p>
                                    </template>
                                </div>

                                <button type="submit"
                                        class="w-full px-5 py-3 text-sm font-semibold text-white transition rounded-xl shadow-lg bg-gradient-to-r from-emerald-500 to-emerald-600 hover:shadow-xl disabled:opacity-40 disabled:cursor-not-allowed"
                                        :disabled="!cart.length">
                                    <i class="mr-2 fas fa-credit-card"></i>
                                    Proses Checkout
                                </button>
                            </div>
                        </section>

                        <section class="p-5 space-y-3 text-sm bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold bg-white rounded-full shadow">
                                    <i class="fas fa-lightbulb"></i>
                                </span>
                                <div>
                                    <h3 class="text-sm font-semibold text-emerald-900">Tips Penggunaan</h3>
                                    <p class="text-xs text-emerald-700">Tipe pelanggan dapat diganti selama keranjang kosong.</p>
                                </div>
                            </div>
                            <p class="text-xs leading-relaxed">
                                Gunakan pencarian dan filter satuan untuk mempercepat input. Setelah pembayaran diterima, cek kembali kembalian sebelum menekan tombol checkout.
                            </p>
                        </section>
                    </aside>
                </div>
            </div>
        </form>
    </div>

    <script>
        function posApp(productsData, customerTypes = [], regularCustomers = []) {
            return {
                customertypes: customerTypes,
                products: productsData,
                regularCustomers,
                customerType: customerTypes.length ? customerTypes[0] : '',
                productSearch: '',
                sortOption: 'name-asc',
                selectedUnit: 'all',
                viewMode: 'grid',
                productQuantities: {},
                pageSize: 9,
                currentPage: 1,
                lastCatalogSignature: '',
                catalogCount: productsData.length,
                cart: [],
                buyerName: '',
                selectedRegularCustomer: '',
                note: '',

                shippingCost: 0,
                shippingCostDisplay: '',
                tip: 0,
                tipDisplay: '',
                paymentReceived: 0,
                paymentDisplay: '',

                get total() {
                    return this.cart.reduce((sum, item) => sum + item.subtotal, 0);
                },

                get cartQuantity() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },

                get catalogCountLabel() {
                    return this.catalogCount === 1
                        ? '1 produk ditemukan'
                        : `${this.catalogCount} produk ditemukan`;
                },

                get totalPages() {
                    return this.catalogCount === 0 ? 1 : Math.ceil(this.catalogCount / this.pageSize);
                },

                get canGoPrev() {
                    return this.currentPage > 1;
                },

                get canGoNext() {
                    return this.currentPage < this.totalPages;
                },

                get pageStart() {
                    if (!this.catalogCount) return 0;
                    return (this.currentPage - 1) * this.pageSize + 1;
                },

                get pageEnd() {
                    if (!this.catalogCount) return 0;
                    return Math.min(this.pageStart + this.pageSize - 1, this.catalogCount);
                },

                get visiblePages() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const windowSize = 5;
                    if (total <= windowSize) {
                        return Array.from({ length: total }, (_, i) => i + 1);
                    }
                    let start = Math.max(current - 2, 1);
                    let end = start + windowSize - 1;
                    if (end > total) {
                        end = total;
                        start = end - windowSize + 1;
                    }
                    return Array.from({ length: end - start + 1 }, (_, i) => start + i);
                },

                get grandTotal() {
                    return this.total + this.shippingCost + this.tip;
                },

                get change() {
                    const change = this.paymentReceived - this.grandTotal;
                    return change > 0 ? change : 0;
                },

                currency(value) {
                    return new Intl.NumberFormat('id-ID').format(value || 0);
                },

                get hasActiveFilter() {
                    return !!this.productSearch.trim() || this.selectedUnit !== 'all';
                },

                units() {
                    const unitSet = new Set();
                    this.products.forEach(product => {
                        if (product.satuan) {
                            unitSet.add(product.satuan);
                        }
                    });
                    return Array.from(unitSet);
                },

                catalogProducts() {
                    let list = [...this.products];

                    if (this.productSearch.trim()) {
                        const term = this.productSearch.toLowerCase();
                        list = list.filter(product => {
                            return [product.name, product.sku]
                                .filter(Boolean)
                                .some(field => field.toLowerCase().includes(term));
                        });
                    }

                    if (this.selectedUnit !== 'all') {
                        list = list.filter(product => {
                            const unit = (product.satuan || '').toString();
                            return unit && unit === this.selectedUnit;
                        });
                    }

                    switch (this.sortOption) {
                        case 'name-desc':
                            list.sort((a, b) => (b.name || '').localeCompare(a.name || ''));
                            break;
                        case 'price-asc':
                            list.sort((a, b) => this.getPrice(a) - this.getPrice(b));
                            break;
                        case 'price-desc':
                            list.sort((a, b) => this.getPrice(b) - this.getPrice(a));
                            break;
                        default:
                            list.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
                    }

                    list.forEach(product => {
                        if (!this.productQuantities[product.id]) {
                            this.productQuantities[product.id] = 1;
                        }
                    });

                    const signature = `${this.productSearch}|${this.sortOption}|${this.selectedUnit}|${this.customerType}`;
                    if (signature !== this.lastCatalogSignature) {
                        this.currentPage = 1;
                        this.lastCatalogSignature = signature;
                    }

                    this.catalogCount = list.length;
                    const totalPages = this.totalPages;
                    if (this.currentPage > totalPages) {
                        this.currentPage = totalPages;
                    }
                    return list;
                },

                paginatedProducts() {
                    const products = this.catalogProducts();
                    const start = (this.currentPage - 1) * this.pageSize;
                    return products.slice(start, start + this.pageSize);
                },

                nextPage() {
                    if (this.canGoNext) {
                        this.currentPage += 1;
                    }
                },

                prevPage() {
                    if (this.canGoPrev) {
                        this.currentPage -= 1;
                    }
                },

                goToPage(page) {
                    if (page >= 1 && page <= this.totalPages) {
                        this.currentPage = page;
                    }
                },

                toggleView(mode) {
                    this.viewMode = mode;
                },

                resetProductFilters() {
                    this.productSearch = '';
                    this.selectedUnit = 'all';
                    this.catalogProducts();
                },

                selectCustomerType(type) {
                    if (this.cart.length) return;
                    this.customerType = type;
                },

                getPrice(product) {
                    if (!product.prices || !product.prices.length) return 0;
                    const match = product.prices.find(item => item.customer_type === this.customerType);
                    return match ? parseFloat(match.price) : 0;
                },

                addToCart(product, quantity = 1) {
                    const price = this.getPrice(product);
                    if (!price) return;

                    const qty = Number.isFinite(quantity) ? parseInt(quantity, 10) : 1;
                    const safeQty = qty > 0 ? qty : 1;

                    const existing = this.cart.find(item => item.id === product.id);
                    if (existing) {
                        existing.qty += safeQty;
                        existing.subtotal = existing.qty * existing.price;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price,
                            qty: safeQty,
                            satuan: product.satuan || 'pcs',
                            subtotal: price * safeQty,
                        });
                    }
                },

                updateSubtotal(index) {
                    const item = this.cart[index];
                    if (!item) return;
                    const qty = parseInt(item.qty, 10) || 1;
                    item.qty = qty < 1 ? 1 : qty;
                    item.subtotal = item.qty * item.price;
                },

                removeProduct(index) {
                    this.cart.splice(index, 1);
                },

                resetCart() {
                    this.cart = [];
                    this.shippingCost = 0;
                    this.shippingCostDisplay = '';
                    this.tip = 0;
                    this.tipDisplay = '';
                    this.paymentReceived = 0;
                    this.paymentDisplay = '';
                },

                handleRegularCustomerChange() {
                    this.buyerName = this.selectedRegularCustomer || '';
                },

                formatMoney(field) {
                    const map = {
                        shippingCost: 'shippingCostDisplay',
                        tip: 'tipDisplay',
                        payment: 'paymentDisplay',
                    };
                    const rawField = map[field];
                    if (!rawField) return;

                    const rawValue = this[rawField].replace(/[^0-9]/g, '');
                    const numeric = rawValue ? parseInt(rawValue, 10) : 0;

                    if (field === 'shippingCost') {
                        this.shippingCost = numeric;
                    } else if (field === 'tip') {
                        this.tip = numeric;
                    } else if (field === 'payment') {
                        this.paymentReceived = numeric;
                    }

                    this[rawField] = numeric ? `Rp ${this.currency(numeric)}` : '';
                },

                increaseQuantity(id) {
                    const current = this.productQuantities[id] || 1;
                    this.productQuantities[id] = current + 1;
                },

                decreaseQuantity(id) {
                    const current = this.productQuantities[id] || 1;
                    this.productQuantities[id] = current > 1 ? current - 1 : 1;
                },

                normalizeQuantity(id) {
                    const raw = parseInt(this.productQuantities[id], 10);
                    this.productQuantities[id] = raw > 0 ? raw : 1;
                },

                quickAdd(product) {
                    const qty = this.productQuantities[product.id] || 1;
                    this.addToCart(product, qty);
                    this.productQuantities[product.id] = 1;
                },

                incrementCartItem(index) {
                    const item = this.cart[index];
                    if (!item) return;
                    item.qty += 1;
                    item.subtotal = item.qty * item.price;
                },

                decrementCartItem(index) {
                    const item = this.cart[index];
                    if (!item) return;
                    if (item.qty > 1) {
                        item.qty -= 1;
                        item.subtotal = item.qty * item.price;
                    } else {
                        this.removeProduct(index);
                    }
                },
            };
        }
    </script>
</x-app-layout>
