<x-app-layout>
    @php
        $todayLabel = \Illuminate\Support\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
        $userName = Auth::user()->name ?? 'Kasir';
        $systemCurrency = config('app.currency', 'IDR');
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl shrink-0">
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

    <form action="{{ route('pos.checkout') }}" method="POST"
      id="pos-form"
      x-data="posApp({{ $product->toJson() }}, {{ json_encode($customertypes) }}, {{ json_encode($regularCustomers ?? []) }}, {{ json_encode($categories ?? []) }})"
      x-ref="checkoutForm"
      @submit.prevent="processCheckout">
    @csrf
        <div
            x-data="posApp({{ $product->toJson() }}, {{ json_encode($customertypes) }}, {{ json_encode($regularCustomers ?? []) }}, {{ json_encode($categories ?? []) }}, '{{ $systemCurrency }}')"
            x-init="initApp()"
            class="space-y-6 pb-12 max-w-full overflow-x-hidden">

            <!-- Toast Notification -->
            <div x-show="toastVisible" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform translate-y-2"
                 class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50">
                <div class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl"
                     :class="toastType === 'error' ? 'bg-red-600 text-white' : 'bg-slate-900 text-white'">
                    <i class="fas" :class="toastType === 'error' ? 'fa-exclamation-circle text-red-200' : 'fa-check-circle text-emerald-400'"></i>
                    <span class="font-medium" x-text="toastMessage"></span>
                </div>
            </div>

            <!-- Undo Toast -->
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

            <!-- Mobile Cart Floating Button -->
            <div x-show="cart.length > 0" x-cloak class="fixed bottom-4 right-4 z-40 lg:hidden">
                <button type="button"
                        @click="mobileCartOpen = true"
                        class="relative flex items-center gap-3 px-5 py-4 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white rounded-2xl shadow-xl hover:shadow-2xl active:scale-95 transition-all">
                    <div class="relative">
                        <i class="fas fa-shopping-cart text-lg"></i>
                        <span class="absolute -top-2 -right-2 w-5 h-5 flex items-center justify-center text-[10px] font-bold bg-white text-emerald-600 rounded-full"
                              x-text="cart.length"></span>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] uppercase tracking-wide opacity-80">Keranjang</p>
                        <p class="text-base font-bold" x-text="formatCurrency(grandTotal())"></p>
                    </div>
                    <i class="fas fa-chevron-up ml-2 text-sm opacity-70"></i>
                </button>
            </div>

            <!-- Mobile Cart Drawer -->
            <div x-show="mobileCartOpen" x-cloak
                 class="fixed inset-0 z-50 lg:hidden"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="mobileCartOpen = false"></div>
                <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl max-h-[85vh] overflow-hidden flex flex-col"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="translate-y-full"
                     x-transition:enter-end="translate-y-0"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="translate-y-0"
                     x-transition:leave-end="translate-y-full">
                    <!-- Drawer Handle -->
                    <div class="flex justify-center pt-3 pb-2">
                        <div class="w-10 h-1 bg-slate-300 rounded-full"></div>
                    </div>
                    <!-- Header -->
                    <div class="flex items-center justify-between px-5 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Keranjang</h3>
                            <p class="text-sm text-emerald-600" x-text="cart.length + ' produk • ' + getTotalUnits() + ' unit'"></p>
                        </div>
                        <button type="button" @click="mobileCartOpen = false" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <!-- Cart Items -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-3">
                        <template x-for="(item, index) in cart" :key="'mobile-cart-'+item.id">
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-slate-900 truncate" x-text="item.name"></h4>
                                    <p class="text-sm text-slate-500">
                                        <span x-text="formatCurrency(item.price)"></span>
                                        <span class="mx-1">×</span>
                                        <span class="font-semibold" x-text="item.qty"></span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="decrementQty(index)" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <span class="w-8 text-center font-bold text-slate-900" x-text="item.qty"></span>
                                    <button type="button" @click="incrementQty(index)" :disabled="item.qty >= item.stock_quantity" class="w-8 h-8 flex items-center justify-center bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 transition">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-emerald-600" x-text="formatCurrency(item.subtotal)"></p>
                                </div>
                                <button type="button" @click="removeProduct(index)" class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <!-- Footer - Summary & Actions -->
                    <div class="border-t border-slate-100 bg-white">
                        <!-- Summary -->
                        <div class="px-5 py-3 space-y-2 text-sm bg-slate-50">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="font-semibold" x-text="formatCurrency(total)"></span>
                            </div>
                            <div class="flex justify-between" x-show="shippingCost > 0">
                                <span class="text-slate-500">Ongkir</span>
                                <span class="font-semibold" x-text="formatCurrency(shippingCost)"></span>
                            </div>
                            <div class="flex justify-between" x-show="tip > 0">
                                <span class="text-slate-500">Tip</span>
                                <span class="font-semibold" x-text="formatCurrency(tip)"></span>
                            </div>
                            <div class="flex justify-between" x-show="expense > 0">
                                <span class="text-slate-500">Pengeluaran</span>
                                <span class="font-semibold" x-text="formatCurrency(expense)"></span>
                            </div>
                            <div class="flex justify-between" x-show="discount > 0">
                                <span class="text-slate-500">Diskon</span>
                                <span class="font-semibold text-red-600" x-text="'-' + formatCurrency(discount)"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-slate-200">
                                <span class="font-bold text-slate-900">Grand Total</span>
                                <span class="font-bold text-emerald-600 text-lg" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                        </div>

                        <!-- Payment in Mobile Drawer -->
                        <div class="px-5 py-4 space-y-3">
                            <!-- Diskon Quick Buttons -->
                            <div x-show="cart.length > 0">
                                <label class="block mb-1.5 text-xs font-semibold text-slate-500 uppercase">Diskon</label>
                                <div class="grid grid-cols-4 gap-2">
                                    <button type="button" @click="applyDiscount(5)" class="px-2 py-2 text-xs font-semibold text-purple-600 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100">5%</button>
                                    <button type="button" @click="applyDiscount(10)" class="px-2 py-2 text-xs font-semibold text-purple-600 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100">10%</button>
                                    <button type="button" @click="applyDiscount(15)" class="px-2 py-2 text-xs font-semibold text-purple-600 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100">15%</button>
                                    <button type="button" @click="removeDiscount()" class="px-2 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100">Hapus</button>
                                </div>
                            </div>

                            <!-- Metode Pembayaran Mobile -->
                            <div>
                                <label class="block mb-1.5 text-xs font-semibold text-slate-500 uppercase">Metode Pembayaran</label>
                                <div class="flex gap-2">
                                    <button type="button"
                                            @click="paymentMethod = 'cash'"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold transition border rounded-lg"
                                            :class="paymentMethod === 'cash'
                                                ? 'bg-emerald-600 border-emerald-600 text-white'
                                                : 'bg-white border-slate-200 text-slate-600 hover:border-emerald-300'">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Cash</span>
                                    </button>
                                    <button type="button"
                                            @click="paymentMethod = 'transfer'"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-xs font-semibold transition border rounded-lg"
                                            :class="paymentMethod === 'transfer'
                                                ? 'bg-blue-600 border-blue-600 text-white'
                                                : 'bg-white border-slate-200 text-slate-600 hover:border-blue-300'">
                                        <i class="fas fa-university"></i>
                                        <span>Transfer</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="paymentMethod === 'cash'">
                                <label class="block mb-1.5 text-xs font-semibold text-slate-500 uppercase">Uang Diterima</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-500 font-bold">{{ $systemCurrency === 'IDR' ? 'Rp' : $systemCurrency }}</span>
                                    <input type="text" inputmode="numeric"
                                           x-model="paymentReceivedFormatted"
                                           @input="formatPaymentReceived"
                                           placeholder="0"
                                           class="w-full py-3 pl-10 pr-4 text-lg font-bold text-slate-900 bg-white border-2 border-emerald-200 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                </div>
                                <!-- Quick Amount Grid -->
                                <div class="grid grid-cols-4 gap-2 mt-2">
                                    <button type="button" @click="setPaymentExact()" class="col-span-2 px-3 py-2.5 text-xs font-bold text-emerald-700 bg-emerald-100 rounded-xl hover:bg-emerald-200 transition">
                                        <i class="fas fa-check mr-1"></i> Uang Pas
                                    </button>
                                    <button type="button" @click="addPaymentAmount(50000)" class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">+50rb</button>
                                    <button type="button" @click="addPaymentAmount(100000)" class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">+100rb</button>
                                </div>
                            </div>

                            <!-- Change/Balance Display -->
                            <div x-show="paymentReceived > 0 && paymentMethod === 'cash'" class="p-3 rounded-xl"
                                 :class="paymentReceived >= grandTotal() ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200'">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium" :class="paymentReceived >= grandTotal() ? 'text-emerald-700' : 'text-amber-700'"
                                          x-text="paymentReceived >= grandTotal() ? 'Kembalian' : 'Kurang'"></span>
                                    <span class="text-xl font-extrabold" :class="paymentReceived >= grandTotal() ? 'text-emerald-700' : 'text-red-600'"
                                          x-text="formatCurrency(Math.abs(paymentReceived - grandTotal()))"></span>
                                </div>
                            </div>

                            <!-- Process Button -->
                            <button type="button"
                                    @click="mobileCartOpen = false; validateAndShowConfirmModal()"
                                    :disabled="cart.length === 0 || (paymentMethod === 'cash' && paymentReceived < grandTotal())"
                                    class="w-full py-4 text-base font-bold text-white bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl shadow-lg disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98] transition">
                                <i class="fas fa-check-circle mr-2"></i>
                                Proses Pembayaran
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <x-breadcrumb :items="[['title' => 'POS Kasir']]" />

            <!-- Error Messages -->
            <div x-show="validationErrors.length > 0" x-cloak
                 class="flex items-start gap-3 p-4 text-red-800 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-500 rounded-full">
                    <i class="fas fa-circle-exclamation"></i>
                </span>
                <ul class="space-y-1 text-sm list-disc list-inside">
                    <template x-for="error in validationErrors" :key="error">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>

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
                            <!-- Pelanggan -->
                            <button type="button"
                                    class="group inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition border rounded-xl focus:outline-none"
                                    :class="customerType === 'pelanggan'
                                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg'
                                        : 'bg-white border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-600'"
                                    @click="cart.length === 0 ? customerType = 'pelanggan' : null"
                                    :disabled="cart.length > 0"
                                    :style="cart.length > 0 && customerType !== 'pelanggan' ? 'opacity:0.5;cursor:not-allowed;' : ''">
                                <span class="inline-flex items-center justify-center w-7 h-7 text-xs rounded-lg"
                                      :class="customerType === 'pelanggan' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-600'">
                                    <i class="fas fa-user"></i>
                                </span>
                                <span>Pelanggan</span>
                            </button>

                            <!-- Reseller -->
                            <button type="button"
                                    class="group inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition border rounded-xl focus:outline-none"
                                    :class="customerType === 'reseller'
                                        ? 'bg-red-500 border-red-500 text-white shadow-lg'
                                        : 'bg-white border-slate-200 text-slate-600 hover:border-red-300 hover:text-red-600'"
                                    @click="cart.length === 0 ? customerType = 'reseller' : null"
                                    :disabled="cart.length > 0"
                                    :style="cart.length > 0 && customerType !== 'reseller' ? 'opacity:0.5;cursor:not-allowed;' : ''">
                                <span class="inline-flex items-center justify-center w-7 h-7 text-xs rounded-lg"
                                      :class="customerType === 'reseller' ? 'bg-white/20 text-white' : 'bg-red-50 text-red-600'">
                                    <i class="fas fa-store"></i>
                                </span>
                                <span>Reseller</span>
                            </button>

                            <!-- Agent -->
                            <button type="button"
                                    class="group inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold transition border rounded-xl focus:outline-none"
                                    :class="customerType === 'agent'
                                        ? 'bg-blue-600 border-blue-600 text-white shadow-lg'
                                        : 'bg-white border-slate-200 text-slate-600 hover:border-blue-300 hover:text-blue-600'"
                                    @click="cart.length === 0 ? customerType = 'agent' : null"
                                    :disabled="cart.length > 0"
                                    :style="cart.length > 0 && customerType !== 'agent' ? 'opacity:0.5;cursor:not-allowed;' : ''">
                                <span class="inline-flex items-center justify-center w-7 h-7 text-xs rounded-lg"
                                      :class="customerType === 'agent' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-600'">
                                    <i class="fas fa-user-tie"></i>
                                </span>
                                <span>Agent</span>
                            </button>
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
                                    @input="debounceFilterCustomers()"
                                    @focus="showCustomerDropdown = true"
                                    @blur="setTimeout(() => showCustomerDropdown = false, 200)"
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

            <div class="grid gap-5 lg:grid-cols-[1fr_320px] xl:grid-cols-[1fr_350px] 2xl:grid-cols-[1fr_380px] lg:items-start max-w-full overflow-hidden">
                <div class="space-y-5 min-w-0 overflow-hidden">
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
                                <!-- Barcode Scanner Button -->
                                <button type="button"
                                        @click="toggleBarcodeScanner()"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition">
                                    <i class="fas fa-barcode"></i>
                                    <span>Scan Barcode</span>
                                </button>
                            </div>
                        </div>

                        <!-- Barcode Scanner Modal -->
                        <div x-show="showBarcodeScanner" x-cloak class="p-5 border-b border-slate-200 bg-slate-50">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-semibold text-slate-900">Scan Barcode</h3>
                                <button type="button" @click="showBarcodeScanner = false" class="text-slate-400 hover:text-slate-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="relative">
                                <input type="text"
                                       x-model="barcodeInput"
                                       @input="processBarcodeInput"
                                       @keydown.enter="handleBarcodeEnter"
                                       placeholder="Klik di sini lalu scan barcode..."
                                       class="w-full px-4 py-3 text-center font-semibold text-lg border-2 border-blue-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 bg-white">
                                <div class="text-center mt-2">
                                    <p class="text-xs text-slate-500">Tekan Enter setelah scan, atau ketik kode manual</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 space-y-5">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                                <div class="relative flex-1">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input
                                        type="search"
                                        x-ref="productSearch"
                                        x-model="searchQuery"
                                        @input="debounceSearch()"
                                        placeholder="Cari nama produk, kode, atau deskripsi…"
                                        class="w-full py-2.5 pl-11 pr-3 text-[13px] font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                </div>
                                <button
                                    type="button"
                                    @click="resetFilters()"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-[13px] font-semibold text-emerald-600 transition border border-slate-200 rounded-lg hover:border-emerald-300 hover:bg-emerald-50">
                                    <i class="fas fa-rotate-left"></i> <span>Bersihkan filter</span>
                                </button>
                            </div>

                            <div class="grid gap-1.5 sm:grid-cols-2 xl:grid-cols-4">
                                <div class="h-full rounded-[10px] border border-slate-200 bg-white p-2">
                                    <p class="mb-1 text-[10px] font-semibold tracking-[0.08em] text-slate-500 uppercase">Urutkan</p>
                                    <div class="relative">
                                        <select
                                            x-model="sortBy"
                                            @change="currentPage = 1"
                                            class="w-full h-9 rounded-lg border border-slate-200 bg-white pl-3 pr-7 text-[12px] font-semibold text-slate-800 appearance-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                                            <option value="name_asc">Nama A-Z</option>
                                            <option value="price_asc">Harga terendah</option>
                                            <option value="price_desc">Harga tertinggi</option>
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400"><i class="fas fa-chevron-down text-[10px]"></i></span>
                                    </div>
                                </div>

                                <div class="h-full rounded-[10px] border border-slate-200 bg-white p-2">
                                    <p class="mb-1 text-[10px] font-semibold tracking-[0.08em] text-slate-500 uppercase">Jumlah halaman</p>
                                    <div class="relative">
                                        <select
                                            x-model.number="perPage"
                                            @change="currentPage = 1"
                                            class="w-full h-9 rounded-lg border border-slate-200 bg-white pl-3 pr-7 text-[12px] font-semibold text-slate-800 appearance-none focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100">
                                            <template x-for="size in perPageOptions" :key="'per-page-' + size">
                                                <option :value="size" x-text="size + ' produk'"></option>
                                            </template>
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2 text-slate-400"><i class="fas fa-chevron-down text-[10px]"></i></span>
                                    </div>
                                </div>

                                <div class="h-full rounded-[10px] border border-slate-200 bg-white p-2 sm:col-span-2 xl:col-span-1">
                                    <p class="mb-1 text-[10px] font-semibold tracking-[0.08em] text-slate-500 uppercase">Kategori</p>
                                    <div class="relative overflow-hidden">
                                        <button type="button"
                                                @click="$refs.categoryScroller.scrollBy({ left: -150, behavior: 'smooth' })"
                                                x-show="categoryScrollLeft > 0"
                                                class="absolute left-0 top-0 bottom-0 z-10 flex w-6 cursor-pointer items-center justify-start bg-gradient-to-r from-white via-white/95 to-transparent pl-1 opacity-0 transition-opacity hover:opacity-100"
                                                :class="{ 'opacity-80': categoryScrollLeft > 0 }">
                                            <i class="fas fa-chevron-left text-xs text-emerald-600"></i>
                                        </button>

                                        <div class="scrollbar-hide flex gap-1.5 overflow-x-auto px-1 pb-1 scroll-smooth"
                                             x-ref="categoryScroller"
                                             @scroll="categoryScrollLeft = $refs.categoryScroller.scrollLeft; categoryScrollRight = $refs.categoryScroller.scrollWidth - $refs.categoryScroller.clientWidth - $refs.categoryScroller.scrollLeft">
                                            <button
                                                type="button"
                                                class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-1 text-[12px] font-semibold transition"
                                                :class="selectedCategory === 'all' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-emerald-300 hover:bg-emerald-50'"
                                                @click="selectedCategory = 'all'; currentPage = 1">
                                                <i class="fas fa-layer-group mr-2 text-[11px]"></i> Semua
                                            </button>
                                            <template x-for="category in categories" :key="'chip-'+category.id">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-1 text-[12px] font-semibold transition"
                                                    :class="String(selectedCategory) === String(category.id)
                                                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm'
                                                        : 'bg-white text-slate-700 border-slate-200 hover:border-emerald-300 hover:bg-emerald-50'"
                                                    @click="selectedCategory = category.id; currentPage = 1"
                                                    x-text="category.name"></button>
                                            </template>
                                        </div>

                                        <button type="button"
                                                @click="$refs.categoryScroller.scrollBy({ left: 150, behavior: 'smooth' })"
                                                x-show="categoryScrollRight > 5"
                                                class="absolute right-0 top-0 bottom-0 z-10 flex w-6 cursor-pointer items-center justify-end bg-gradient-to-l from-white via-white/95 to-transparent pr-1 opacity-0 transition-opacity hover:opacity-100"
                                                :class="{ 'opacity-80': categoryScrollRight > 5 }">
                                            <i class="fas fa-chevron-right text-xs text-emerald-600"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="h-full">
                                    <label class="flex h-full items-start gap-2 rounded-[10px] border border-slate-200 bg-white px-2.5 py-1.5 text-[12px] font-semibold text-slate-800 transition hover:border-emerald-300 hover:bg-emerald-50">
                                        <input type="checkbox" class="mt-0.5 h-3.5 w-3.5 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500" x-model="showInStockOnly" @change="currentPage = 1">
                                        <div class="leading-tight">
                                            <span class="block text-[12.5px] font-semibold text-slate-900">Stok tersedia saja</span>
                                            <span class="text-[10.5px] font-normal text-slate-500">Sembunyikan produk stok habis</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Loading Skeleton -->
                            <div x-show="isLoading && filteredProducts().length === 0" x-cloak class="grid gap-3 grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4">
                                <template x-for="i in 8" :key="'skeleton-'+i">
                                    <div class="animate-pulse bg-slate-100 rounded-xl border border-slate-200 h-48"></div>
                                </template>
                            </div>

                            <!-- Empty State -->
                            <template x-if="!isLoading && filteredProducts().length === 0">
                                <div class="flex flex-col items-center gap-3 py-12 text-center text-emerald-700 bg-emerald-50/80 border border-emerald-100 rounded-2xl">
                                    <span class="text-4xl"><i class="fas fa-search-minus"></i></span>
                                    <p class="text-sm font-semibold">Produk tidak ditemukan</p>
                                    <p class="text-xs text-emerald-600">Coba ubah kata kunci atau pilih kategori lain.</p>
                                    <button @click="resetFilters()" class="mt-2 px-4 py-2 text-xs font-semibold text-emerald-600 bg-emerald-100 rounded-lg hover:bg-emerald-200 transition">
                                        Reset Filter
                                    </button>
                                </div>
                            </template>

                            <div class="grid gap-3 grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 max-w-full" x-show="filteredProducts().length > 0 && !isLoading">
                                <template x-for="product in paginatedProducts()" :key="product.id">
                                    <div class="group relative flex flex-col bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-lg hover:border-emerald-300"
                                         :class="[isOutOfStock(product) ? 'opacity-60' : '', getCartQty(product.id) > 0 ? 'ring-2 ring-emerald-500 border-emerald-500' : '']">

                                        <!-- Product Image -->
                                        <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden cursor-pointer"
                                             @click="!isOutOfStock(product) && addToCart(product)">
                                            <template x-if="product.image_url">
                                                <img :src="product.image_url"
                                                     :alt="product.name"
                                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                                     loading="lazy"
                                                     @load="product.imageLoaded = true"
                                                     x-init="product.imageLoaded = false">
                                            </template>
                                            <!-- Placeholder -->
                                            <div x-show="!product.image_url || !product.imageLoaded"
                                                 class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-gradient-to-br from-slate-50 to-slate-100">
                                                <i class="fas fa-box-open text-3xl"></i>
                                            </div>

                                            <!-- Qty Badge -->
                                            <span x-show="getCartQty(product.id) > 0"
                                                  class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-6 h-6 text-[10px] font-bold text-white bg-emerald-600 rounded-full shadow-lg z-10 animate-bounce-once">
                                                <span x-text="getCartQty(product.id)"></span>
                                            </span>

                                            <!-- Out of Stock Badge -->
                                            <div x-show="isOutOfStock(product)"
                                                 class="absolute inset-0 flex items-center justify-center bg-black/50">
                                                <span class="px-2 py-1 text-[10px] font-bold text-white bg-red-500 rounded-full">
                                                    HABIS
                                                </span>
                                            </div>

                                            <!-- Category Tag -->
                                            <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 text-[9px] font-semibold text-emerald-700 bg-white/90 rounded"
                                                  x-text="categoryLabel(product)"></span>
                                        </div>

                                        <!-- Product Info -->
                                        <div class="flex flex-col p-2 h-[88px]">
                                            <h3 class="text-xs font-medium text-slate-800 line-clamp-2 h-8 leading-4 cursor-pointer hover:text-emerald-600"
                                                @click="!isOutOfStock(product) && addToCart(product)"
                                                x-text="product.name"></h3>

                                            <!-- Price -->
                                            <p class="text-sm font-bold text-emerald-600 mt-auto" x-text="formatCurrency(getPrice(product))"></p>

                                            <!-- Action Button -->
                                            <div class="mt-1.5">
                                                <!-- Qty Controls -->
                                                <div x-show="getCartQty(product.id) > 0" class="flex items-center justify-center gap-2 py-1 bg-emerald-50 rounded">
                                                    <button type="button"
                                                            @click.stop="decrementFromCard(product.id)"
                                                            class="w-6 h-6 flex items-center justify-center bg-white border border-emerald-200 rounded text-emerald-600 hover:bg-red-50 hover:text-red-500 text-xs">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <span class="text-xs font-bold text-emerald-700 w-5 text-center" x-text="getCartQty(product.id)"></span>
                                                    <button type="button"
                                                            @click.stop="addToCart(product)"
                                                            :disabled="getCartQty(product.id) >= (product.stock_quantity || 999)"
                                                            class="w-6 h-6 flex items-center justify-center bg-white border border-emerald-200 rounded text-emerald-600 hover:bg-emerald-100 text-xs disabled:opacity-50">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                </div>

                                                <!-- Add to Cart Button -->
                                                <button type="button"
                                                        x-show="getCartQty(product.id) === 0"
                                                        @click="addToCart(product)"
                                                        :disabled="isOutOfStock(product)"
                                                        class="w-full py-1.5 text-[11px] font-semibold rounded transition-all"
                                                        :class="isOutOfStock(product) ? 'bg-slate-100 text-slate-400' : 'bg-emerald-500 text-white hover:bg-emerald-600'">
                                                    <i class="fas fa-cart-plus mr-1"></i>
                                                    <span>Tambah</span>
                                                </button>
                                            </div>
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
                    <!-- Keranjang & Ringkasan -->
                    <section class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                        <div class="flex items-center justify-between gap-3 px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">
                            <div class="flex items-center gap-3">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-900">Keranjang</h2>
                                    <p class="text-sm text-slate-600" x-show="cart.length === 0">Kelola item yang akan dibayar.</p>
                                    <p class="text-sm text-emerald-600 font-medium" x-show="cart.length > 0" x-cloak>
                                        <span x-text="cart.length"></span> produk
                                        (<span x-text="getTotalUnits()"></span> unit)
                                    </p>
                                </div>
                            </div>
                            <!-- Consolidated action buttons -->
                            <div class="flex items-center gap-2">
                                <!-- Held transactions badge -->
                                <button
                                    type="button"
                                    @click="showHeldTransactions = true"
                                    class="relative inline-flex items-center justify-center w-10 h-10 text-blue-600 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition"
                                    x-show="heldTransactions.length > 0"
                                    title="Transaksi Tertunda">
                                    <i class="fas fa-history"></i>
                                    <span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-blue-600 rounded-full" x-text="heldTransactions.length"></span>
                                </button>

                                <!-- Dropdown menu -->
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
                                                @click="showClearCartModal = true; open = false"
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

                        <!-- Quick Total Bar -->
                        <div x-show="cart.length > 0"
                             class="px-4 py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 flex items-center justify-between">
                            <div class="text-white">
                                <p class="text-[10px] uppercase tracking-wide opacity-80">Total Belanja</p>
                                <p class="text-lg font-bold" x-text="formatCurrency(total)"></p>
                            </div>
                            <div class="text-white text-right">
                                <span class="text-sm font-medium" x-text="cart.length + ' item'"></span>
                            </div>
                        </div>

                        <div class="p-3 space-y-2 max-h-[50vh] overflow-y-auto scrollbar-thin" x-show="cart.length > 0" x-ref="cartContainer">
                            <!-- Compact Card Layout -->
                            <template x-for="(item, index) in cart" :key="'cart-'+item.id">
                                <div class="relative p-3 bg-white rounded-xl border border-slate-200 hover:border-emerald-200 transition-all group"
                                     draggable="true"
                                     @dragstart="dragStart($event, item)"
                                     @dragover.prevent
                                     @drop="drop($event, item)">
                                    <!-- Delete Button -->
                                    <button type="button"
                                            @click="removeProduct(index)"
                                            class="absolute -top-1.5 -right-1.5 w-6 h-6 flex items-center justify-center bg-red-500 text-white rounded-full shadow hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100 z-10">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>

                                    <!-- Product Info Row -->
                                    <div class="flex items-start justify-between gap-3 mb-2">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-sm font-semibold text-slate-800 leading-tight truncate" x-text="item.name"></h4>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                <span class="text-emerald-600 font-medium" x-text="formatCurrency(item.price)"></span>
                                                <span class="mx-1">×</span>
                                                <span class="font-semibold" x-text="item.qty"></span>
                                            </p>
                                        </div>
                                        <!-- Subtotal -->
                                        <div class="text-right shrink-0">
                                            <p class="text-sm font-bold text-emerald-600" x-text="formatCurrency(item.subtotal)"></p>
                                        </div>
                                    </div>

                                    <!-- Compact Qty Control -->
                                    <div class="flex items-center gap-2">
                                        <button type="button"
                                                @click="decrementQty(index)"
                                                class="w-8 h-8 flex items-center justify-center bg-slate-100 text-slate-600 rounded-lg hover:bg-red-100 hover:text-red-600 transition text-sm">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number"
                                               inputmode="numeric"
                                               min="1"
                                               :max="item.stock_quantity"
                                               x-model.number="item.qty"
                                               @input="updateSubtotal(index)"
                                               @blur="validateItemQty(index)"
                                               class="w-12 h-8 text-sm font-bold text-center bg-white border border-slate-200 rounded-lg focus:border-emerald-400 focus:ring-1 focus:ring-emerald-200">
                                        <button type="button"
                                                @click="incrementQty(index)"
                                                :disabled="item.qty >= item.stock_quantity"
                                                class="w-8 h-8 flex items-center justify-center bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition text-sm disabled:opacity-50 disabled:bg-slate-300">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <!-- Stock warning -->
                                        <span x-show="item.stock_quantity && item.stock_quantity <= 5"
                                              class="ml-auto text-[10px] font-medium"
                                              :class="item.stock_quantity <= 2 ? 'text-red-500' : 'text-orange-500'"
                                              x-text="'Sisa ' + item.stock_quantity"></span>
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
                            <input type="hidden" name="discount" :value="discount">
                            <input type="hidden" name="discount_percent" :value="discountPercent">
                        </div>

                        <!-- ========== INTEGRATED SUMMARY SECTION ========== -->

                        <!-- Compact Summary -->
                        <div x-show="cart.length > 0" class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 space-y-2 text-sm">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Subtotal</span>
                                <span class="font-semibold" x-text="formatCurrency(total)"></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600" x-show="shippingCost > 0">
                                <span>Ongkir</span>
                                <span class="font-semibold" x-text="formatCurrency(shippingCost)"></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600" x-show="tip > 0">
                                <span>Tip</span>
                                <span class="font-semibold" x-text="formatCurrency(tip)"></span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600" x-show="expense > 0">
                                <span>Pengeluaran</span>
                                <span class="font-semibold" x-text="formatCurrency(expense)"></span>
                            </div>
                            <div class="flex items-center justify-between text-orange-600" x-show="discount > 0">
                                <span>Diskon (<span x-text="discountPercent"></span>%)</span>
                                <span class="font-semibold" x-text="'-' + formatCurrency(discount)"></span>
                            </div>
                        </div>

                        <!-- Grand Total Bar -->
                        <div x-show="cart.length > 0"
                             class="px-4 py-4 bg-gradient-to-r from-emerald-600 to-emerald-500">
                            <div class="flex items-center justify-between text-white">
                                <span class="text-base font-bold">Grand Total</span>
                                <span class="text-2xl font-extrabold" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                        </div>

                        <!-- Diskon Section -->
                        <div x-show="cart.length > 0" class="border-t border-slate-100 px-4 py-3 bg-orange-50/30">
                            <label class="block mb-1.5 text-xs font-semibold tracking-wide text-orange-600 uppercase">Diskon (Opsional)</label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="number"
                                           x-model="discountPercent"
                                           @input="applyDiscountFromPercent()"
                                           min="0"
                                           max="100"
                                           step="0.1"
                                           placeholder="Persen"
                                           class="w-full py-2 pl-3 pr-8 text-sm font-semibold text-orange-700 bg-white border border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-orange-400 font-bold">%</span>
                                </div>
                                <button type="button" @click="applyDiscount(10)" class="px-3 py-2 text-xs font-semibold text-orange-600 bg-orange-100 border border-orange-200 rounded-lg hover:bg-orange-200">10%</button>
                                <button type="button" @click="removeDiscount()" class="px-3 py-2 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100">Hapus</button>
                            </div>
                        </div>

                        <!-- Ongkir, Alamat, Tip, Pengeluaran, Catatan - Collapsible -->
                        <div x-show="cart.length > 0" class="border-t border-slate-100">
                            <button type="button"
                                    @click="summaryExpanded = !summaryExpanded"
                                    class="w-full px-4 py-3 flex items-center justify-between text-sm font-medium text-slate-600 hover:bg-slate-50 transition">
                                <span class="flex items-center gap-2">
                                    <i class="fas fa-sliders-h text-emerald-500"></i>
                                    <span>Alamat, Ongkir & Lainnya</span>
                                </span>
                                <i class="fas transition-transform duration-200" :class="summaryExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                            </button>

                            <div x-show="summaryExpanded" x-collapse class="px-4 pb-4 space-y-3">
                                <div class="grid gap-3">
                                    <!-- Alamat Pengiriman -->
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Alamat Pengiriman</label>
                                        <textarea
                                            name="shipping_address"
                                            rows="3"
                                            x-model="shippingAddress"
                                            placeholder="Masukkan alamat lengkap pengiriman..."
                                            class="w-full px-3 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400"></textarea>
                                    </div>

                                    <!-- Ongkir -->
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Ongkir (opsional)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400 font-semibold text-sm">{{ $systemCurrency === 'IDR' ? 'Rp' : $systemCurrency }}</span>
                                            <input
                                                type="text"
                                                x-model="shippingCostFormatted"
                                                @input="formatShippingCost"
                                                placeholder="0"
                                                class="w-full py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                            <input type="hidden" name="shipping_cost" :value="shippingCost">
                                        </div>
                                        <p class="mt-1 text-xs text-emerald-600">Biaya pengiriman ke alamat di atas</p>
                                    </div>

                                    <!-- Tip -->
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Tip (opsional)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400 font-semibold text-sm">{{ $systemCurrency === 'IDR' ? 'Rp' : $systemCurrency }}</span>
                                            <input
                                                type="text"
                                                x-model="tipFormatted"
                                                @input="formatTip"
                                                placeholder="0"
                                                class="w-full py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                                            <input type="hidden" name="tip" :value="tip">
                                        </div>
                                    </div>

                                    <!-- Pengeluaran -->
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Pengeluaran (Harga/Karton)</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-amber-400 font-semibold text-sm">{{ $systemCurrency === 'IDR' ? 'Rp' : $systemCurrency }}</span>
                                            <input
                                                type="text"
                                                x-model="expenseFormatted"
                                                @input="formatExpense"
                                                placeholder="0"
                                                class="w-full py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 bg-white border border-amber-200 rounded-lg focus:ring-2 focus:ring-amber-400 focus:border-amber-400 placeholder:font-normal placeholder:text-slate-400">
                                            <input type="hidden" name="expense_amount" :value="expense">
                                        </div>
                                        <p class="mt-1 text-xs text-amber-600">Biaya tambahan seperti pembelian karton, packing, dll.</p>
                                    </div>

                                    <!-- Catatan -->
                                    <div>
                                        <label class="block mb-1.5 text-xs font-semibold tracking-wide text-slate-500 uppercase">Catatan</label>
                                        <textarea
                                            name="note"
                                            rows="2"
                                            x-model="note"
                                            placeholder="Catatan opsional untuk transaksi..."
                                            class="w-full px-3 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Section -->
                        <div x-show="cart.length > 0" class="px-4 py-4 border-t border-slate-100 bg-slate-50/80 space-y-3">
                            <input type="hidden" name="grand_total" :value="grandTotal()">

                            <!-- Metode Pembayaran -->
                            <div>
                                <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase tracking-wide">Metode Pembayaran</label>
                                <div class="flex gap-3">
                                    <button type="button"
                                            @click="paymentMethod = 'cash'"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold transition border rounded-xl"
                                            :class="paymentMethod === 'cash'
                                                ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg'
                                                : 'bg-white border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-600'">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Cash</span>
                                    </button>
                                    <button type="button"
                                            @click="paymentMethod = 'transfer'"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold transition border rounded-xl"
                                            :class="paymentMethod === 'transfer'
                                                ? 'bg-blue-600 border-blue-600 text-white shadow-lg'
                                                : 'bg-white border-slate-200 text-slate-600 hover:border-blue-300 hover:text-blue-600'">
                                        <i class="fas fa-university"></i>
                                        <span>Transfer</span>
                                    </button>
                                </div>
                                <input type="hidden" name="payment_method" :value="paymentMethod">
                            </div>

                            <!-- Uang Diterima (cash) -->
                            <div x-show="paymentMethod === 'cash'">
                                <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase tracking-wide">Uang Diterima</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-500 font-bold">{{ $systemCurrency === 'IDR' ? 'Rp' : $systemCurrency }}</span>
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

                                <!-- Quick Amount Buttons -->
                                <div class="grid grid-cols-4 gap-2 mt-3">
                                    <button type="button" @click="setPaymentExact()"
                                            class="col-span-2 px-3 py-2.5 text-xs font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 rounded-xl hover:bg-emerald-200 active:scale-95 transition">
                                        <i class="fas fa-check mr-1"></i> Uang Pas
                                    </button>
                                    <button type="button" @click="addPaymentAmount(5000)"
                                            class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 active:scale-95 transition">
                                        +5rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(10000)"
                                            class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 active:scale-95 transition">
                                        +10rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(20000)"
                                            class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 active:scale-95 transition">
                                        +20kb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(50000)"
                                            class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 active:scale-95 transition">
                                        +50rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(100000)"
                                            class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 active:scale-95 transition">
                                        +100rb
                                    </button>
                                    <button type="button" @click="addPaymentAmount(200000)"
                                            class="px-3 py-2.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-100 active:scale-95 transition">
                                        +200rb
                                    </button>
                                </div>

                                <!-- Kembalian Display -->
                                <div x-show="paymentReceived > 0" class="p-3 rounded-xl mt-3"
                                     :class="paymentReceived >= grandTotal() ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200'">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium"
                                              :class="paymentReceived >= grandTotal() ? 'text-emerald-700' : 'text-amber-700'"
                                              x-text="paymentReceived >= grandTotal() ? 'Kembalian' : 'Kurang'"></span>
                                        <span class="text-lg font-extrabold"
                                              :class="paymentReceived >= grandTotal() ? 'text-emerald-700' : 'text-red-600'"
                                              x-text="formatCurrency(Math.abs(paymentReceived - grandTotal()))"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Transfer -->
                            <div x-show="paymentMethod === 'transfer'" class="space-y-3">
                                <div>
                                    <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase tracking-wide">Nama Bank</label>
                                    <input type="text"
                                           name="bank_name"
                                           x-model="bankName"
                                           placeholder="Contoh: BCA, BRI, Mandiri"
                                           class="w-full px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                                </div>
                                <div>
                                    <label class="block mb-1.5 text-xs font-semibold text-slate-600 uppercase tracking-wide">Nomor Rekening</label>
                                    <input type="text"
                                           name="account_number"
                                           x-model="accountNumber"
                                           placeholder="Contoh: 1234567890"
                                           class="w-full px-4 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400">
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-2 pt-2">
    <button type="button"
            @click="processPayment"
            :disabled="cart.length === 0 || (paymentMethod === 'cash' && paymentReceived < grandTotal()) || isSubmitting"
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
                                <span class="font-semibold" x-text="cart.length + ' produk (' + getTotalUnits() + ' unit)'"></span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-500">Grand Total:</span>
                                <span class="font-bold text-emerald-600" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-500">Metode Pembayaran:</span>
                                <span class="font-semibold text-capitalize" x-text="paymentMethod === 'cash' ? 'Cash' : 'Transfer'"></span>
                            </div>
                            <div class="flex justify-between text-sm" x-show="paymentMethod === 'cash'">
                                <span class="text-slate-500">Pembayaran:</span>
                                <span class="font-semibold" x-text="formatCurrency(paymentReceived)"></span>
                            </div>
                            <div class="flex justify-between text-sm" x-show="paymentMethod === 'cash'">
                                <span class="text-slate-500">Kembalian:</span>
                                <span class="font-semibold text-emerald-600" x-text="formatCurrency(Math.max(0, paymentReceived - grandTotal()))"></span>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-200" x-show="shippingAddress">
                                <p class="text-xs text-slate-500 mb-1">Alamat Pengiriman:</p>
                                <p class="text-xs text-slate-700 font-medium" x-text="shippingAddress.substring(0, 50) + (shippingAddress.length > 50 ? '...' : '')"></p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="button"
                                    @click="showConfirmModal = false"
                                    class="flex-1 px-4 py-3 text-sm font-semibold text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                                Batal
                            </button>
    <button type="button"
            @click="processPayment"
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
                            <div class="text-sm">
                                <span class="text-slate-500">Pembayaran:</span>
                                <span class="font-medium ml-2 capitalize" x-text="paymentMethod === 'cash' ? 'Cash' : 'Transfer'"></span>
                            </div>
                            <div class="text-sm mt-2" x-show="shippingAddress">
                                <span class="text-slate-500">Alamat:</span>
                                <span class="font-medium ml-2" x-text="shippingAddress.substring(0, 30) + (shippingAddress.length > 30 ? '...' : '')"></span>
                            </div>
                        </div>

                        <div class="border-t border-dashed border-slate-300 pt-4 mb-4 space-y-2">
                            <template x-for="item in cart" :key="'receipt-'+item.id">
                                <div class="flex justify-between text-sm">
                                    <div class="flex-1">
                                        <p class="font-medium text-slate-900" x-text="item.name"></p>
                                        <p class="text-xs text-slate-500" x-text="item.qty + ' x ' + formatCurrency(item.price)"></p>
                                    </div>
                                    <span class="font-semibold text-slate-700" x-text="formatCurrency(item.subtotal)"></span>
                                </div>
                            </template>
                        </div>

                        <div class="border-t border-dashed border-slate-300 pt-4 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Subtotal</span>
                                <span class="font-medium" x-text="formatCurrency(total)"></span>
                            </div>
                            <div class="flex justify-between" x-show="shippingCost > 0">
                                <span class="text-slate-500">Ongkir</span>
                                <span class="font-medium" x-text="formatCurrency(shippingCost)"></span>
                            </div>
                            <div class="flex justify-between" x-show="tip > 0">
                                <span class="text-slate-500">Tip</span>
                                <span class="font-medium" x-text="formatCurrency(tip)"></span>
                            </div>
                            <div class="flex justify-between" x-show="expense > 0">
                                <span class="text-slate-500">Pengeluaran</span>
                                <span class="font-medium" x-text="formatCurrency(expense)"></span>
                            </div>
                            <div class="flex justify-between" x-show="discount > 0">
                                <span class="text-slate-500">Diskon (<span x-text="discountPercent"></span>%)</span>
                                <span class="font-medium text-red-600" x-text="'-' + formatCurrency(discount)"></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-slate-200 text-base">
                                <span class="font-bold text-slate-900">TOTAL</span>
                                <span class="font-bold text-emerald-600" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                            <div class="flex justify-between" x-show="paymentMethod === 'cash'">
                                <span class="text-slate-500">Bayar</span>
                                <span class="font-medium" x-text="formatCurrency(paymentReceived)"></span>
                            </div>
                            <div class="flex justify-between" x-show="paymentMethod === 'cash' && paymentReceived > grandTotal()">
                                <span class="text-slate-500">Kembalian</span>
                                <span class="font-medium text-emerald-600" x-text="formatCurrency(paymentReceived - grandTotal())"></span>
                            </div>
                            <div class="flex justify-between" x-show="paymentMethod === 'transfer'">
                                <span class="text-slate-500">Metode</span>
                                <span class="font-medium text-blue-600">Transfer Bank</span>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-xs text-slate-400">--- Terima Kasih ---</p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 space-y-2">
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
                                    <span class="font-bold text-emerald-600" x-text="formatCurrency(held.grandTotal)"></span>
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

            <!-- Clear Cart Confirmation Modal -->
            <div x-show="showClearCartModal"
                 x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-2xl shadow-xl max-w-sm w-full overflow-hidden"
                     @click.away="showClearCartModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="p-6 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-red-100 text-red-600 rounded-full">
                            <i class="fas fa-trash-alt text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Bersihkan Keranjang?</h3>
                        <p class="text-slate-600 mb-6">Semua item di keranjang akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>

                        <div class="bg-slate-50 rounded-xl p-4 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Total Item:</span>
                                <span class="font-semibold" x-text="cart.length + ' produk'"></span>
                            </div>
                            <div class="flex justify-between text-sm mt-2">
                                <span class="text-slate-500">Total Nilai:</span>
                                <span class="font-bold text-emerald-600" x-text="formatCurrency(grandTotal())"></span>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="button"
                                    @click="showClearCartModal = false"
                                    class="flex-1 px-4 py-3 text-sm font-semibold text-slate-700 border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                                Batal
                            </button>
                            <button type="button"
                                    @click="clearCart(); showClearCartModal = false"
                                    class="flex-1 px-4 py-3 text-sm font-bold text-white bg-red-500 rounded-xl hover:bg-red-600 transition">
                                Ya, Hapus Semua
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        /* Animation untuk badge qty */
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

        /* Custom scrollbar */
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Scrollbar hide utility */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Performance optimizations */
        .will-change-transform {
            will-change: transform;
        }

        /* Hide elements with x-cloak */
        [x-cloak] { display: none !important; }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('posCart', {
                count: 0,
                totalUnits: 0,
                grandTotal: 0
            });
        });

        function posApp(productsData, customerTypesData, regularCustomersData, categoriesData, systemCurrency) {
            return {
                // Data properties
                customertypes: ['pelanggan', 'reseller', 'agent'],
                customerType: 'pelanggan',
                products: [],
                regularCustomers: Array.isArray(regularCustomersData) ? regularCustomersData : [],
                categories: Array.isArray(categoriesData) ? categoriesData : [],
                currencySymbol: systemCurrency === 'IDR' ? 'Rp' : systemCurrency,

                // State
                cart: [],
                total: 0,
                shippingAddress: '',
                shippingCost: 0,
                shippingCostFormatted: '',
                tip: 0,
                tipFormatted: '',
                expense: 0,
                expenseFormatted: '',
                discount: 0,
                discountPercent: 0,
                paymentMethod: 'cash',
                paymentReceived: 0,
                paymentReceivedFormatted: '',
                bankName: '',
                accountNumber: '',
                note: '',
                selectedRegularCustomerId: '__manual',
                selectedRegularCustomer: null,
                buyerName: '',

                // Search & Filter
                searchQuery: '',
                searchQueryDebounced: '',
                selectedCategory: 'all',
                showInStockOnly: false,
                sortBy: 'name_asc',
                perPageOptions: [12, 20, 24, 36],
                perPage: 24,
                currentPage: 1,

                // UI State
                isLoading: false,
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
                summaryExpanded: true,

                // Notifications
                toastMessage: '',
                toastVisible: false,
                toastType: 'success',
                draggedItem: null,

                // Undo & History
                lastRemovedItem: null,
                undoToastVisible: false,
                undoToastMessage: '',
                undoTimeout: null,

                // Barcode Scanner
                showBarcodeScanner: false,
                barcodeInput: '',
                barcodeTimeout: null,

                // Mobile
                mobileCartOpen: false,
                showClearCartModal: false,

                // Validation
                validationErrors: [],

                // ========== INITIALIZATION ==========
                async initApp() {
                    // Initialize products with optimization
                    this.products = Array.isArray(productsData) ?
                        productsData.map(p => ({
                            ...p,
                            imageLoaded: false,
                            prices: p.prices || []
                        })) : [];

                    this.filteredCustomerResults = this.regularCustomers;

                    // Load saved state
                    this.loadCartFromStorage();
                    this.loadHeldTransactions();

                    // Load summary expanded state
                    const savedSummaryState = localStorage.getItem('pos_summary_expanded');
                    if (savedSummaryState !== null) {
                        this.summaryExpanded = savedSummaryState === 'true';
                    }

                    // Set up watchers
                    this.setupWatchers();

                    // Set up keyboard shortcuts
                    this.setupKeyboardShortcuts();

                    // Auto focus on product search
                    setTimeout(() => {
                        this.$refs.productSearch?.focus();
                    }, 100);
                },

                setupWatchers() {
                    // Watch cart for Alpine store updates
                    this.$watch('cart', (value) => {
                        Alpine.store('posCart').count = value.length;
                        Alpine.store('posCart').totalUnits = this.getTotalUnits();
                        Alpine.store('posCart').grandTotal = this.grandTotal();
                        this.saveCartToStorage();
                    }, { deep: true });

                    // Watch summary expanded state
                    this.$watch('summaryExpanded', (value) => {
                        localStorage.setItem('pos_summary_expanded', value.toString());
                    });

                    // Watch debounced search
                    this.$watch('searchQuery', Alpine.debounce((value) => {
                        this.searchQueryDebounced = value;
                        this.currentPage = 1;
                    }, 300));
                },

                setupKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        const isTyping = ['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName);

                        // F2: Focus product search
                        if (e.key === 'F2' && !isTyping) {
                            e.preventDefault();
                            this.$refs.productSearch?.focus();
                        }

                        // F6: Toggle barcode scanner
                        if (e.key === 'F6' && !isTyping) {
                            e.preventDefault();
                            this.toggleBarcodeScanner();
                        }

                        // F8: Process payment
                        if (e.key === 'F8' && !isTyping && this.cart.length > 0) {
                            e.preventDefault();
                            this.validateAndShowConfirmModal();
                        }

                        // Escape: Close modals
                        if (e.key === 'Escape') {
                            this.closeAllModals();
                        }

                        // Ctrl+Z: Undo remove
                        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !isTyping) {
                            e.preventDefault();
                            if (this.lastRemovedItem) {
                                this.undoRemove();
                            }
                        }

                        // Barcode scanner detection
                        if (e.target === document.body && this.showBarcodeScanner) {
                            if (e.key === 'Enter') {
                                this.handleBarcodeEnter();
                            }
                        }
                    });
                },

                closeAllModals() {
                    this.showConfirmModal = false;
                    this.showReceiptPreview = false;
                    this.showHeldTransactions = false;
                    this.showClearCartModal = false;
                    this.mobileCartOpen = false;
                    this.showBarcodeScanner = false;
                },

                // ========== CUSTOMER MANAGEMENT ==========
                debounceFilterCustomers: Alpine.debounce(function() {
                    const query = this.customerSearchQuery.toLowerCase().trim();
                    if (!query) {
                        this.filteredCustomerResults = this.regularCustomers;
                    } else {
                        this.filteredCustomerResults = this.regularCustomers.filter(c =>
                            c.customer_name?.toLowerCase().includes(query) ||
                            (c.address && c.address.toLowerCase().includes(query)) ||
                            (c.phone && c.phone.includes(query))
                        );
                    }
                }, 200),

                selectCustomer(customer) {
                    if (customer) {
                        this.selectedRegularCustomer = customer;
                        this.selectedRegularCustomerId = customer.id;
                        this.buyerName = customer.customer_name || '';
                        this.customerSearchQuery = customer.customer_name;
                        if (customer.address) {
                            this.shippingAddress = customer.address;
                        }
                        this.applyShippingCost(customer.shipping_cost ?? 0);
                    } else {
                        this.selectedRegularCustomer = null;
                        this.selectedRegularCustomerId = '__manual';
                        this.buyerName = '';
                        this.customerSearchQuery = '';
                        this.shippingAddress = '';
                        this.applyShippingCost(0);
                    }
                },

                // ========== CART MANAGEMENT ==========
                addToCart(product) {
                    if (!product || this.isOutOfStock(product)) return;

                    const price = this.roundCurrency(this.getPrice(product));
                    const unitLabel = this.resolveUnit(product);
                    const stockQty = Number(product.stock_quantity) || 0;
                    const existingIndex = this.cart.findIndex(item => item.id === product.id);

                    if (existingIndex > -1) {
                        const existing = this.cart[existingIndex];
                        if (existing.qty + 1 > stockQty) {
                            this.showErrorToast(`Stok tidak cukup! Maks ${stockQty} unit.`);
                            return;
                        }
                        existing.qty += 1;
                        existing.subtotal = this.roundCurrency(existing.qty * existing.price);
                        this.showToast(`${product.name} (x${existing.qty})`);

                        // Animate the qty badge
                        const badge = document.querySelector(`[data-product-id="${product.id}"] .qty-badge`);
                        if (badge) {
                            badge.classList.remove('animate-bounce-once');
                            void badge.offsetWidth; // Trigger reflow
                            badge.classList.add('animate-bounce-once');
                        }
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price,
                            qty: 1,
                            satuan: unitLabel,
                            stock_quantity: stockQty,
                            subtotal: price,
                            category: product.category?.name || 'Umum',
                        });
                        this.showToast(`${product.name} ditambahkan`);
                    }

                    this.calculateTotal();

                    // Auto-scroll to cart
                    this.$nextTick(() => {
                        const container = this.$refs.cartContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },

                removeProduct(index) {
                    if (index < 0 || index >= this.cart.length) return;

                    const removedItem = this.cart[index];
                    this.lastRemovedItem = { ...removedItem, originalIndex: index };
                    this.cart.splice(index, 1);
                    this.calculateTotal();

                    // Show undo toast
                    this.showUndoToast(`${removedItem.name} dihapus`);
                },

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

                decrementQty(index) {
                    if (index < 0 || index >= this.cart.length) return;

                    const item = this.cart[index];
                    if (item.qty > 1) {
                        item.qty -= 1;
                        item.subtotal = this.roundCurrency(item.qty * item.price);
                        this.calculateTotal();
                    } else {
                        this.removeProduct(index);
                    }
                },

                incrementQty(index) {
                    if (index < 0 || index >= this.cart.length) return;

                    const item = this.cart[index];
                    if (item.qty < (item.stock_quantity || 999)) {
                        item.qty += 1;
                        item.subtotal = this.roundCurrency(item.qty * item.price);
                        this.calculateTotal();
                    }
                },

                decrementFromCard(productId) {
                    const index = this.cart.findIndex(i => i.id === productId);
                    if (index > -1) {
                        this.decrementQty(index);
                    }
                },

                updateSubtotal(index) {
                    const item = this.cart[index];
                    if (!item) return;

                    if (!item.qty || item.qty < 1) {
                        item.qty = 1;
                    }

                    if (item.stock_quantity && item.qty > item.stock_quantity) {
                        item.qty = item.stock_quantity;
                        this.showErrorToast(`Stok tidak cukup! Maksimal ${item.stock_quantity} unit.`);
                    }

                    item.subtotal = this.roundCurrency(item.price * item.qty);
                    this.calculateTotal();
                },

                validateItemQty(index) {
                    const item = this.cart[index];
                    if (!item) return;

                    if (!item.qty || item.qty < 1) {
                        item.qty = 1;
                        item.subtotal = this.roundCurrency(item.price * item.qty);
                        this.calculateTotal();
                    }
                },

                clearCart() {
                    this.cart = [];
                    this.total = 0;
                    this.discount = 0;
                    this.discountPercent = 0;
                    this.clearCartStorage();
                },

                getCartQty(productId) {
                    const item = this.cart.find(i => i.id === productId);
                    return item ? item.qty : 0;
                },

                getTotalUnits() {
                    return this.cart.reduce((sum, item) => sum + (item.qty || 0), 0);
                },

                calculateTotal() {
                    const total = this.cart.reduce((sum, item) => sum + (Number(item.subtotal) || 0), 0);
                    this.total = this.roundCurrency(total);
                },

                // ========== DISCOUNT MANAGEMENT ==========
                applyDiscount(percent) {
                    this.discountPercent = percent;
                    this.discount = this.roundCurrency(this.total * (percent / 100));
                },

                applyDiscountFromPercent() {
                    const percent = Math.min(Math.max(0, this.discountPercent), 100);
                    this.discountPercent = percent;
                    this.discount = this.roundCurrency(this.total * (percent / 100));
                },

                removeDiscount() {
                    this.discount = 0;
                    this.discountPercent = 0;
                },

                // ========== PAYMENT MANAGEMENT ==========
                setPaymentExact() {
                    this.paymentReceived = this.grandTotal();
                    this.paymentReceivedFormatted = this.formatCurrency(this.paymentReceived);
                },

                addPaymentAmount(amount) {
                    this.paymentReceived += amount;
                    this.paymentReceivedFormatted = this.formatCurrency(this.paymentReceived);
                },

                grandTotal() {
                    return this.roundCurrency(
                        this.total +
                        this.shippingCost +
                        this.tip +
                        this.expense -
                        this.discount
                    );
                },

                // ========== FORMATTING & VALIDATION ==========
                formatCurrency(value) {
                    const numeric = Number(value) || 0;
                    if (this.currencySymbol === 'Rp') {
                        return new Intl.NumberFormat('id-ID').format(numeric);
                    }
                    return new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(numeric);
                },

                roundCurrency(value) {
                    const numeric = Number(value) || 0;
                    return Math.round(numeric);
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

                formatExpense() {
                    const value = this.extractNumber(this.expenseFormatted);
                    this.expense = value;
                    this.expenseFormatted = value ? this.formatCurrency(value) : '';
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

                // ========== PRODUCT FILTERING & SORTING ==========
                debounceSearch: Alpine.debounce(function() {
                    this.currentPage = 1;
                }, 300),

                filteredProducts() {
                    if (!Array.isArray(this.products)) return [];

                    let items = [...this.products];
                    const query = this.searchQueryDebounced.trim().toLowerCase();

                    if (query) {
                        items = items.filter(product => {
                            const name = (product.name || '').toLowerCase();
                            const sku = (product.sku || '').toLowerCase();
                            const description = (product.description || '').toLowerCase();
                            const barcode = (product.barcode || '').toLowerCase();
                            return name.includes(query) ||
                                   sku.includes(query) ||
                                   description.includes(query) ||
                                   barcode.includes(query);
                        });
                    }

                    if (this.selectedCategory !== 'all') {
                        items = items.filter(product =>
                            String(this.extractCategoryId(product)) === String(this.selectedCategory)
                        );
                    }

                    if (this.showInStockOnly) {
                        items = items.filter(product => (Number(product.stock_quantity) || 0) > 0);
                    }

                    // Sorting
                    items.sort((a, b) => {
                        const priceA = this.getPrice(a);
                        const priceB = this.getPrice(b);

                        switch (this.sortBy) {
                            case 'price_asc': return priceA - priceB;
                            case 'price_desc': return priceB - priceA;
                            case 'name_asc':
                            default: return (a.name || '').localeCompare(b.name || '', 'id', { sensitivity: 'base' });
                        }
                    });

                    return items;
                },

                paginatedProducts() {
                    const items = this.filteredProducts();
                    const totalPages = Math.max(1, Math.ceil(items.length / this.perPage));

                    if (this.currentPage > totalPages) this.currentPage = totalPages;
                    if (this.currentPage < 1) this.currentPage = 1;

                    const start = (this.currentPage - 1) * this.perPage;
                    return items.slice(start, start + this.perPage);
                },

                totalPages() {
                    const count = this.filteredProducts().length;
                    return Math.max(1, Math.ceil(count / this.perPage));
                },

                goToPage(page) {
                    const target = Number(page);
                    if (!Number.isNaN(target)) {
                        const total = this.totalPages();
                        if (target >= 1 && target <= total) {
                            this.currentPage = target;
                        }
                    }
                },

                goToPreviousPage() {
                    if (this.currentPage > 1) this.currentPage -= 1;
                },

                goToNextPage() {
                    if (this.currentPage < this.totalPages()) this.currentPage += 1;
                },

                pageNumbers() {
                    const total = this.totalPages();
                    const maxButtons = 5;
                    let start = Math.max(1, this.currentPage - Math.floor(maxButtons / 2));
                    let end = Math.min(total, start + maxButtons - 1);
                    start = Math.max(1, end - maxButtons + 1);

                    const pages = [];
                    for (let i = start; i <= end; i++) pages.push(i);
                    return pages;
                },

                paginationRangeLabel() {
                    const itemsCount = this.filteredProducts().length;
                    if (!itemsCount) return '0';

                    const start = (this.currentPage - 1) * this.perPage + 1;
                    const end = Math.min(start + this.perPage - 1, itemsCount);
                    return `${this.formatNumber(start)} – ${this.formatNumber(end)}`;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.searchQueryDebounced = '';
                    this.selectedCategory = 'all';
                    this.showInStockOnly = false;
                    this.sortBy = 'name_asc';
                    this.currentPage = 1;
                },

                // ========== PRODUCT HELPERS ==========
                getPrice(product) {
                    if (!product || !Array.isArray(product.prices)) return 0;
                    const row = product.prices.find(pr => pr.customer_type === this.customerType);
                    return row ? Number(row.price) || 0 : 0;
                },

                resolveUnit(product) {
                    if (product?.units?.name) return product.units.name;
                    if (product?.unit_name) return product.unit_name;
                    if (product?.satuan && typeof product.satuan === 'string') {
                        return product.satuan.trim() || 'pcs';
                    }
                    return 'pcs';
                },

                extractCategoryId(product) {
                    return product?.category?.id || product?.category_id || null;
                },

                categoryLabel(product) {
                    if (product?.category?.name) return product.category.name;
                    const match = this.categories.find(cat =>
                        String(cat.id) === String(this.extractCategoryId(product))
                    );
                    return match ? match.name : 'Umum';
                },

                isOutOfStock(product) {
                    return (Number(product?.stock_quantity) || 0) <= 0;
                },

                formatNumber(value) {
                    return new Intl.NumberFormat('id-ID').format(Number(value) || 0);
                },

                // ========== BARCODE SCANNER ==========
                toggleBarcodeScanner() {
                    this.showBarcodeScanner = !this.showBarcodeScanner;
                    this.barcodeInput = '';

                    if (this.showBarcodeScanner) {
                        this.$nextTick(() => {
                            const input = document.querySelector('[x-model="barcodeInput"]');
                            input?.focus();
                        });
                    }
                },

                processBarcodeInput() {
                    clearTimeout(this.barcodeTimeout);
                    this.barcodeTimeout = setTimeout(() => {
                        if (this.barcodeInput.length >= 3) {
                            this.scanBarcode(this.barcodeInput);
                        }
                    }, 100);
                },

                handleBarcodeEnter() {
                    if (this.barcodeInput.trim()) {
                        this.scanBarcode(this.barcodeInput.trim());
                        this.barcodeInput = '';
                    }
                },

                scanBarcode(code) {
                    const product = this.products.find(p =>
                        p.barcode === code ||
                        p.sku === code ||
                        String(p.id) === code
                    );

                    if (product) {
                        this.addToCart(product);
                        this.showToast(`Produk ditemukan: ${product.name}`);
                        this.barcodeInput = '';

                        if (this.showBarcodeScanner) {
                            setTimeout(() => {
                                const input = document.querySelector('[x-model="barcodeInput"]');
                                input?.focus();
                            }, 100);
                        }
                    } else {
                        this.showErrorToast(`Produk dengan kode "${code}" tidak ditemukan`);
                    }
                },

                // ========== LOCALSTORAGE PERSISTENCE ==========
                saveCartToStorage() {
                    const data = {
                        cart: this.cart,
                        customerType: this.customerType,
                        buyerName: this.buyerName,
                        shippingAddress: this.shippingAddress,
                        shippingCost: this.shippingCost,
                        tip: this.tip,
                        expense: this.expense,
                        discount: this.discount,
                        discountPercent: this.discountPercent,
                        paymentMethod: this.paymentMethod,
                        paymentReceived: this.paymentReceived,
                        bankName: this.bankName,
                        accountNumber: this.accountNumber,
                        note: this.note,
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
                            const storedTime = new Date(data.timestamp);
                            const now = new Date();
                            const hoursDiff = (now - storedTime) / (1000 * 60 * 60);

                            if (hoursDiff < 12 && data.cart && data.cart.length > 0) {
                                this.cart = data.cart;
                                this.customerType = data.customerType || 'pelanggan';
                                this.buyerName = data.buyerName || '';
                                this.shippingAddress = data.shippingAddress || '';
                                this.shippingCost = data.shippingCost || 0;
                                this.shippingCostFormatted = this.shippingCost ? this.formatCurrency(this.shippingCost) : '';
                                this.tip = data.tip || 0;
                                this.tipFormatted = this.tip ? this.formatCurrency(this.tip) : '';
                                this.expense = data.expense || 0;
                                this.expenseFormatted = this.expense ? this.formatCurrency(this.expense) : '';
                                this.discount = data.discount || 0;
                                this.discountPercent = data.discountPercent || 0;
                                this.paymentMethod = data.paymentMethod || 'cash';
                                this.paymentReceived = data.paymentReceived || 0;
                                this.paymentReceivedFormatted = this.paymentReceived ? this.formatCurrency(this.paymentReceived) : '';
                                this.bankName = data.bankName || '';
                                this.accountNumber = data.accountNumber || '';
                                this.note = data.note || '';
                                this.selectedRegularCustomerId = data.selectedRegularCustomerId || '__manual';

                                this.calculateTotal();

                                if (this.selectedRegularCustomerId !== '__manual') {
                                    const customer = this.regularCustomers.find(c =>
                                        String(c.id) === String(this.selectedRegularCustomerId)
                                    );
                                    if (customer) {
                                        this.selectedRegularCustomer = customer;
                                        this.customerSearchQuery = customer.customer_name;
                                        if (customer.address && !this.shippingAddress) {
                                            this.shippingAddress = customer.address;
                                        }
                                    }
                                }
                            }
                        }
                    } catch (e) {
                        console.warn('Failed to load cart:', e);
                        localStorage.removeItem('pos_cart');
                    }
                },

                clearCartStorage() {
                    localStorage.removeItem('pos_cart');
                },

                // ========== HELD TRANSACTIONS ==========
                holdTransaction() {
                    if (this.cart.length === 0) return;

                    const held = {
                        cart: [...this.cart],
                        customerType: this.customerType,
                        buyerName: this.buyerName,
                        shippingAddress: this.shippingAddress,
                        shippingCost: this.shippingCost,
                        tip: this.tip,
                        expense: this.expense,
                        discount: this.discount,
                        discountPercent: this.discountPercent,
                        paymentMethod: this.paymentMethod,
                        selectedRegularCustomerId: this.selectedRegularCustomerId,
                        selectedRegularCustomer: this.selectedRegularCustomer,
                        grandTotal: this.grandTotal(),
                        timestamp: new Date().toLocaleString('id-ID')
                    };

                    this.heldTransactions.push(held);
                    this.saveHeldTransactions();

                    // Reset current cart
                    this.clearCart();
                    this.buyerName = '';
                    this.shippingAddress = '';
                    this.shippingCost = 0;
                    this.shippingCostFormatted = '';
                    this.tip = 0;
                    this.tipFormatted = '';
                    this.expense = 0;
                    this.expenseFormatted = '';
                    this.discount = 0;
                    this.discountPercent = 0;
                    this.paymentMethod = 'cash';
                    this.paymentReceived = 0;
                    this.paymentReceivedFormatted = '';
                    this.bankName = '';
                    this.accountNumber = '';
                    this.note = '';
                    this.selectedRegularCustomer = null;
                    this.selectedRegularCustomerId = '__manual';
                    this.customerSearchQuery = '';

                    this.showToast('Transaksi ditunda');
                },

                resumeTransaction(index) {
                    const held = this.heldTransactions[index];
                    if (!held) return;

                    // Restore data
                    this.cart = held.cart;
                    this.customerType = held.customerType;
                    this.buyerName = held.buyerName;
                    this.shippingAddress = held.shippingAddress || '';
                    this.shippingCost = held.shippingCost;
                    this.shippingCostFormatted = held.shippingCost ? this.formatCurrency(held.shippingCost) : '';
                    this.tip = held.tip;
                    this.tipFormatted = held.tip ? this.formatCurrency(held.tip) : '';
                    this.expense = held.expense || 0;
                    this.expenseFormatted = held.expense ? this.formatCurrency(held.expense) : '';
                    this.discount = held.discount || 0;
                    this.discountPercent = held.discountPercent || 0;
                    this.paymentMethod = held.paymentMethod || 'cash';
                    this.selectedRegularCustomerId = held.selectedRegularCustomerId;
                    this.selectedRegularCustomer = held.selectedRegularCustomer;

                    if (held.selectedRegularCustomer) {
                        this.customerSearchQuery = held.selectedRegularCustomer.customer_name;
                        if (held.selectedRegularCustomer.address && !this.shippingAddress) {
                            this.shippingAddress = held.selectedRegularCustomer.address;
                        }
                    }

                    this.calculateTotal();

                    // Remove from held list
                    this.heldTransactions.splice(index, 1);
                    this.saveHeldTransactions();
                    this.showHeldTransactions = false;

                    this.showToast('Transaksi dilanjutkan');
                },

                removeHeldTransaction(index) {
                    this.heldTransactions.splice(index, 1);
                    this.saveHeldTransactions();
                    this.showToast('Transaksi tertunda dihapus');
                },

                saveHeldTransactions() {
                    try {
                        localStorage.setItem('pos_held_transactions', JSON.stringify(this.heldTransactions));
                    } catch (e) {
                        console.warn('Failed to save held transactions:', e);
                    }
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

                // ========== VALIDATION ==========
                processPayment() {
                    if (!this.showConfirmModal) {
                        this.validateAndShowConfirmModal();
                    } else {
                        this.submitForm();
                    }
                },

                validateForm() {
                    this.validationErrors = [];

                    if (this.cart.length === 0) {
                        this.validationErrors.push('Keranjang tidak boleh kosong');
                    }

                    if (!this.buyerName && !this.selectedRegularCustomer) {
                        this.validationErrors.push('Nama pembeli harus diisi');
                    }

                    if (this.paymentMethod === 'cash' && this.paymentReceived < this.grandTotal()) {
                        this.validationErrors.push('Uang diterima kurang dari total pembayaran');
                    }

                    if (this.paymentMethod === 'transfer') {
                        if (!this.bankName.trim()) {
                            this.validationErrors.push('Nama bank harus diisi untuk pembayaran transfer');
                        }
                        if (!this.accountNumber.trim()) {
                            this.validationErrors.push('Nomor rekening harus diisi untuk pembayaran transfer');
                        }
                    }

                    // Check stock availability
                    this.cart.forEach((item, index) => {
                        const product = this.products.find(p => p.id === item.id);
                        if (product && item.qty > (product.stock_quantity || 0)) {
                            this.validationErrors.push(
                                `Stok ${item.name} tidak cukup. Tersedia: ${product.stock_quantity}, Diminta: ${item.qty}`
                            );
                        }
                    });

                    return this.validationErrors.length === 0;
                },

                validateAndShowConfirmModal() {
                    if (this.validateForm()) {
                        this.showConfirmModal = true;
                    } else {
                        this.showErrorToast('Ada kesalahan dalam formulir');
                    }
                },

                validateAndSubmit() {
                    if (this.validateForm()) {
                        this.submitForm();
                    }
                },

                // ========== FORM SUBMISSION ==========
                async submitForm() {
                    this.isSubmitting = true;

                    try {
                        // Prepare form data
                        const formData = new FormData(document.getElementById('pos-form'));

                        // Add cart items
                        this.cart.forEach((item, index) => {
                            formData.append(`cart[${index}][id]`, item.id);
                            formData.append(`cart[${index}][name]`, item.name);
                            formData.append(`cart[${index}][qty]`, item.qty);
                            formData.append(`cart[${index}][price]`, item.price);
                            formData.append(`cart[${index}][satuan]`, item.satuan);
                            formData.append(`cart[${index}][subtotal]`, item.subtotal);
                        });

                        // Submit via fetch for better UX
                        const response = await fetch('{{ route("pos.checkout") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.showToast('Transaksi berhasil disimpan!');
                            this.resetFormAfterPayment();

                            // Redirect or show success message
                            if (result.redirect) {
                                setTimeout(() => {
                                    window.location.href = result.redirect;
                                }, 1500);
                            }
                        } else {
                            throw new Error(result.message || 'Terjadi kesalahan');
                        }
                    } catch (error) {
                        console.error('Submission error:', error);
                        this.showErrorToast(error.message || 'Gagal menyimpan transaksi');
                    } finally {
                        this.isSubmitting = false;
                        this.showConfirmModal = false;
                    }
                },

                resetFormAfterPayment() {
                    // Reset all form fields
                    this.cart = [];
                    this.customerType = 'pelanggan';
                    this.buyerName = '';
                    this.shippingAddress = '';
                    this.shippingCost = 0;
                    this.shippingCostFormatted = '';
                    this.tip = 0;
                    this.tipFormatted = '';
                    this.expense = 0;
                    this.expenseFormatted = '';
                    this.discount = 0;
                    this.discountPercent = 0;
                    this.paymentMethod = 'cash';
                    this.paymentReceived = 0;
                    this.paymentReceivedFormatted = '';
                    this.bankName = '';
                    this.accountNumber = '';
                    this.note = '';
                    this.selectedRegularCustomer = null;
                    this.selectedRegularCustomerId = '__manual';
                    this.customerSearchQuery = '';
                    this.validationErrors = [];

                    // Clear form inputs
                    document.getElementById('pos-form')?.reset();

                    // Clear storage
                    this.clearCartStorage();

                    // Focus back to product search
                    setTimeout(() => {
                        this.$refs.productSearch?.focus();
                    }, 300);
                },

                // ========== NOTIFICATIONS ==========
                showToast(message) {
                    this.toastMessage = message;
                    this.toastType = 'success';
                    this.toastVisible = true;

                    setTimeout(() => {
                        this.toastVisible = false;
                    }, 2000);
                },

                showErrorToast(message) {
                    this.toastMessage = message;
                    this.toastType = 'error';
                    this.toastVisible = true;

                    setTimeout(() => {
                        this.toastVisible = false;
                        this.toastType = 'success';
                    }, 3000);
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

                // ========== DRAG & DROP ==========
                dragStart(event, item) {
                    event.dataTransfer.setData('text/plain', item.id);
                    event.dataTransfer.effectAllowed = 'move';
                    this.draggedItem = item;
                },

                drop(event, targetItem) {
                    event.preventDefault();

                    const draggedId = event.dataTransfer.getData('text/plain');
                    const fromIndex = this.cart.findIndex(i => i.id === draggedId);
                    const toIndex = this.cart.findIndex(i => i.id === targetItem.id);

                    if (fromIndex > -1 && toIndex > -1 && fromIndex !== toIndex) {
                        const [movedItem] = this.cart.splice(fromIndex, 1);
                        this.cart.splice(toIndex, 0, movedItem);
                        this.showToast('Urutan keranjang diubah');
                    }

                    this.draggedItem = null;
                },

                // ========== PRINT RECEIPT ==========
                printReceipt() {
                    const printContent = document.createElement('div');
                    printContent.innerHTML = `
                        <div style="font-family: 'Courier New', monospace; width: 300px; padding: 20px;">
                            <div style="text-align: center; margin-bottom: 15px;">
                                <h2 style="margin: 0; font-size: 18px;">STRUK PENJUALAN</h2>
                                <p style="margin: 5px 0; font-size: 12px;">${new Date().toLocaleString('id-ID')}</p>
                                <p style="margin: 3px 0; font-size: 11px;">Kasir: {{ $userName }}</p>
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="margin: 10px 0; font-size: 12px;">
                                <p style="margin: 3px 0;"><strong>Pembeli:</strong> ${this.buyerName || '-'}</p>
                                <p style="margin: 3px 0;"><strong>Tipe:</strong> ${this.customerType}</p>
                                <p style="margin: 3px 0;"><strong>Pembayaran:</strong> ${this.paymentMethod === 'cash' ? 'Cash' : 'Transfer'}</p>
                                ${this.shippingAddress ? `<p style="margin: 3px 0;"><strong>Alamat:</strong> ${this.shippingAddress}</p>` : ''}
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="margin: 10px 0;">
                                ${this.cart.map(item => `
                                    <div style="margin: 8px 0; font-size: 12px;">
                                        <p style="margin: 0; font-weight: bold;">${item.name}</p>
                                        <div style="display: flex; justify-content: space-between;">
                                            <span>${item.qty} x ${this.formatCurrency(item.price)}</span>
                                            <span>${this.formatCurrency(item.subtotal)}</span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="margin: 10px 0; font-size: 12px;">
                                <div style="display: flex; justify-content: space-between;"><span>Subtotal</span><span>${this.formatCurrency(this.total)}</span></div>
                                ${this.shippingCost > 0 ? `<div style="display: flex; justify-content: space-between;"><span>Ongkir</span><span>${this.formatCurrency(this.shippingCost)}</span></div>` : ''}
                                ${this.tip > 0 ? `<div style="display: flex; justify-content: space-between;"><span>Tip</span><span>${this.formatCurrency(this.tip)}</span></div>` : ''}
                                ${this.expense > 0 ? `<div style="display: flex; justify-content: space-between;"><span>Pengeluaran</span><span>${this.formatCurrency(this.expense)}</span></div>` : ''}
                                ${this.discount > 0 ? `<div style="display: flex; justify-content: space-between; color: #dc2626;"><span>Diskon (${this.discountPercent}%)</span><span>-${this.formatCurrency(this.discount)}</span></div>` : ''}
                                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px; border-top: 1px solid #000; padding-top: 5px;">
                                    <span>TOTAL</span><span>${this.formatCurrency(this.grandTotal())}</span>
                                </div>
                                ${this.paymentMethod === 'cash' ? `<div style="display: flex; justify-content: space-between;"><span>Bayar</span><span>${this.formatCurrency(this.paymentReceived)}</span></div>` : ''}
                                ${this.paymentMethod === 'cash' && this.paymentReceived > this.grandTotal() ? `<div style="display: flex; justify-content: space-between;"><span>Kembalian</span><span>${this.formatCurrency(this.paymentReceived - this.grandTotal())}</span></div>` : ''}
                                ${this.paymentMethod === 'transfer' ? `<div style="display: flex; justify-content: space-between;"><span>Metode</span><span>Transfer Bank</span></div>` : ''}
                            </div>
                            <hr style="border: 1px dashed #000;">
                            <div style="text-align: center; margin-top: 15px; font-size: 11px; color: #666;">
                                <p>--- Terima Kasih ---</p>
                                <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
                            </div>
                        </div>
                    `;

                    const printWindow = window.open('', '_blank', 'width=350,height=600');
                    printWindow.document.write(`
                        <html>
                            <head>
                                <title>Struk Penjualan</title>
                                <style>
                                    @media print {
                                        @page { margin: 0; size: auto; }
                                        body { margin: 0; padding: 10px; }
                                    }
                                </style>
                            </head>
                            <body>
                                ${printContent.innerHTML}
                                <script>
                                    window.onload = function() {
                                        window.print();
                                        setTimeout(function() {
                                            window.close();
                                        }, 500);
                                    }
                                <\/script>
                            </body>
                        </html>
                    `);
                    printWindow.document.close();

                    this.showReceiptPreview = false;
                },

                // ========== SHIPPING COST ==========
                applyShippingCost(value) {
                    const numeric = Number(value) || 0;
                    this.shippingCost = numeric;
                    this.shippingCostFormatted = numeric ? this.formatCurrency(numeric) : '';
                }
            };
        }
    </script>
</x-app-layout>
