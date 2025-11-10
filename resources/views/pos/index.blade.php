<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-3xl font-extrabold tracking-tight text-gray-900">
                <span class="inline-flex items-center justify-center w-10 h-10 mr-2 text-2xl text-green-700 bg-green-100 rounded-full">🛒</span>
                POS (Point of Sale)
            </h2>
            <span class="px-4 py-1 text-sm font-bold text-white rounded-full shadow bg-gradient-to-r from-green-500 to-green-700">
                Versi Beta
            </span>
        </div>
    </x-slot>
<form action="{{ route('pos.checkout') }}" method="POST" onsubmit="return confirm('Yakin ingin checkout?')">
    @csrf
    <div x-data="posApp({{ $product->toJson() }}, {{ json_encode($customertypes) }}, {{ json_encode($regularCustomers ?? []) }})" x-init="init()"
         class="min-h-screen p-8 bg-gradient-to-br from-green-50 via-white to-green-100">

        <!-- Tipe Pembeli -->
        <div class="mb-10">
            <h3 class="mb-3 text-xl font-semibold text-green-800"><i class="fa-solid fa-person"></i> Pilih Tipe pembeli</h3>
            <div class="flex flex-wrap gap-4">
                <template x-for="type in customertypes" :key="type">
                    <button required
                        type="button"
                        name="customer_type"
                        class="px-6 py-2 text-base font-semibold transition shadow rounded-xl focus:outline-none"
                        :class="customerType === type
                            ? 'bg-gradient-to-r from-green-600 to-green-400 text-white shadow-lg scale-105'
                            : 'bg-white text-green-700 border-2 border-green-200 hover:bg-green-50'"
                        @click="cart.length === 0 ? customerType = type : null"
                        :disabled="cart.length > 0"
                        :style="cart.length > 0 ? 'opacity:0.5;cursor:not-allowed;' : ''">
                        <span class="capitalize" x-text="type"></span>
                    </button>
                </template>
                <input type="hidden" name="customer_type" :value="customerType">
            </div>
        </div>

        <!-- ID Pesanan -->
        <div class="mb-8">
            <label class="block mb-2 text-lg font-semibold text-green-700">ID Pesanan</label>
            <input type="text" name="order_id" x-model="orderId" placeholder="Masukkan ID pesanan" required
                   class="w-full p-3 transition bg-white border-2 border-green-200 shadow rounded-xl focus:ring-2 focus:ring-green-400">
        </div>

        <!-- Nama Pembeli -->
        <div class="mb-8" x-show="!selectedRegularCustomer">
            <label class="block mb-2 text-lg font-semibold text-green-700">Nama Pembeli</label>
            <input type="text" name="customer_name" x-model="buyerName" placeholder="Masukkan nama pembeli" required
                   class="w-full p-3 transition bg-white border-2 border-green-200 shadow rounded-xl focus:ring-2 focus:ring-green-400">
        </div>

        <!-- Grid Produk -->
        <h3 class="mb-5 text-2xl font-bold text-green-900"><i class="fa-solid fa-shop"></i> Pilih Produk</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-7">
            <template x-for="product in products" :key="product.id">
                <div class="relative p-6 transition bg-white border border-green-100 shadow-lg cursor-pointer rounded-2xl hover:shadow-xl hover:-translate-y-1"
                     @click="addToCart(product)">
                    <div class="absolute top-3 right-3 px-2 py-0.5 text-xs font-bold text-green-600 bg-green-50 rounded">
                        <span x-text="product.sku"></span>
                    </div>
                    <h4 class="mb-2 text-lg font-bold text-green-800 truncate" x-text="product.name"></h4>
                    <p class="mt-4 text-xl font-extrabold text-green-600" x-text="formatCurrency(getPrice(product))"></p>
                    <span class="absolute px-3 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full bottom-3 right-3">Tambah</span>
                </div>
            </template>
        </div>

        <div class="md:col-span-2">
            <div class="flex justify-end mb-4" x-show="cart.length > 0">
                <button type="button" @click="cart = []"
                    class="px-5 py-2 text-white transition shadow bg-gradient-to-r from-red-500 to-red-700 rounded-xl hover:scale-105">
                    <i class="fa-solid fa-ban"></i> Batalkan Semua
                </button>
            </div>
        </div>

        <!-- Keranjang & Ringkasan -->
        <div class="grid gap-8 mt-14 md:grid-cols-3">
            <!-- Keranjang -->
            <div class="md:col-span-2">
                <h3 class="mb-5 text-2xl font-bold text-green-900"><i class="fa-solid fa-cart-shopping"></i> Keranjang</h3>
                <template x-if="cart.length === 0">
                    <div class="flex items-center justify-center h-32 text-green-400 bg-white shadow-inner rounded-xl">
                        <span class="text-lg italic">Keranjang masih kosong, silakan pilih produk.</span>
                    </div>
                </template>

                <div class="overflow-x-auto" x-show="cart.length > 0">
                    <table class="w-full bg-white border-collapse shadow-lg rounded-2xl">
                        <thead class="text-green-700 bg-green-50">
                            <tr>
                                <th class="px-5 py-3 border-b">Produk</th>
                                <th class="px-5 py-3 border-b">Harga</th>
                                <th class="px-5 py-3 border-b">Qty</th>
                                <th class="px-5 py-3 border-b">Satuan</th>
                                <th class="px-5 py-3 border-b">Subtotal</th>
                                <th class="px-5 py-3 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="transition hover:bg-green-50">
                                    <td class="px-5 py-3 font-medium text-green-900 border-b">
                                        <span x-text="item.name"></span>
                                        <input type="hidden" :name="'cart[id]['+index+']'" :value="item.id" required>
                                        <input type="hidden" :name="'cart[name]['+index+']'" :value="item.name" required>
                                    </td>
                                    <td class="px-5 py-3 font-bold text-green-700 border-b" x-text="formatCurrency(item.price)">
                                        <input type="hidden" :name="'cart[price]['+index+']'" :value="item.price" required>
                                    </td>
                                    <td class="px-5 py-3 border-b">
                                        <input required
                                               type="number" min="1" x-model.number="item.qty"
                                               @input="updateSubtotal(index)"
                                               :name="'cart[qty]['+index+']'"
                                               class="w-20 px-3 py-2 text-center transition bg-white border-2 border-green-200 rounded-xl focus:ring-2 focus:ring-green-300">
                                    </td>
                                    <td class="px-5 py-3 border-b">
                                        <select x-model="item.satuan"
                                                :name="'cart[satuan]['+index+']'"
                                                class="w-full p-2 bg-white border-2 border-green-200 rounded-xl" required>
                                            <option value="pcs">pcs</option>
                                            <option value="box">dus</option>
                                            <option value="lusin">lusin</option>
                                        </select>
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-green-700 border-b" x-text="formatCurrency(item.subtotal)">
                                        <input type="hidden" :name="'cart[subtotal]['+index+']'" :value="item.subtotal" required>
                                    </td>
                                    <td class="px-5 py-3 text-center border-b">
                                        <button type="button" @click="removeProduct(index)"
                                                class="px-4 py-2 text-white transition shadow bg-gradient-to-r from-green-500 to-green-700 rounded-xl hover:scale-110">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <!-- Input Ongkir -->
                    <div class="mb-8">
                        <label class="block mb-2 text-lg font-semibold text-green-700">Ongkir</label>
                        <div class="relative">
                            <span class="absolute font-bold text-green-700 -translate-y-1/2 left-4 top-1/2">Rp</span>
                            <input type="text"
                                   x-model="shippingCostFormatted"
                    @input="formatShippingCost()"
                                   name="shippingCost"
                                   placeholder="Masukkan ongkir"
                                   class="w-full p-3 pl-12 transition bg-white border-2 border-green-200 shadow rounded-xl focus:ring-2 focus:ring-green-400" >
                        </div>
                    </div>
                    <!-- Input Tip -->
                    <div class="mb-8">
                        <label class="block mb-2 text-lg font-semibold text-green-700">Tip</label>
                        <div class="relative">
                            <span class="absolute font-bold text-green-700 -translate-y-1/2 left-4 top-1/2">Rp</span>
                            <input type="text"
                                   x-model="tipFormatted"
                    @input="formatTip()"
                                   name="tip"
                                   placeholder="Masukkan tip"
                                   class="w-full p-3 pl-12 transition bg-white border-2 border-green-200 shadow rounded-xl focus:ring-2 focus:ring-green-400">
                        </div>
                    </div>
                    <div class="mb-8">
                        <label class="block mb-2 text-lg font-semibold text-green-700">Catatan</label>
                        <textarea name="note"
                        placeholder="Tambahkan catatan (opsional)"
                        class="w-full p-3 transition bg-white border-2 border-green-200 shadow rounded-xl focus:ring-2 focus:ring-green-400"></textarea>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Belanja -->
            <div class="p-8 bg-white shadow-xl rounded-2xl h-fit" x-show="cart.length > 0">
                <h3 class="mb-5 text-xl font-bold text-green-900"><i class="fa-solid fa-cash-register"></i> Ringkasan Belanja</h3>
                <div class="space-y-3 text-green-700">
                    <div class="flex justify-between">
                        <span>Total Item</span>
                        <span class="font-semibold" x-text="cart.length + ' produk'"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Belanja</span>
                        <span class="font-bold text-green-700" x-text="formatCurrency(total)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkir</span>
                        <span class="font-bold text-green-700" x-text="formatCurrency(shippingCost)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Tip</span>
                        <span class="font-bold text-green-700" x-text="formatCurrency(tip)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pembayaran Diterima</span>
                        <span class="font-bold text-green-700" x-text="formatCurrency(paymentReceived)"></span>
                    </div>
                </div>
                <template x-if="balanceDue() > 0">
                    <div class="flex justify-between px-3 py-2 mt-3 text-sm font-semibold text-yellow-700 bg-yellow-50 rounded-xl">
                        <span>Sisa Pembayaran</span>
                        <span x-text="'Rp ' + formatCurrency(balanceDue())"></span>
                    </div>
                </template>
                <template x-if="changeDue() > 0">
                    <div class="flex justify-between px-3 py-2 mt-3 text-sm font-semibold text-green-700 bg-green-50 rounded-xl">
                        <span>Kembalian</span>
                        <span x-text="'Rp ' + formatCurrency(changeDue())"></span>
                    </div>
                </template>
                <hr class="my-4">
                <div class="flex justify-between text-xl font-extrabold text-green-900">
                    <span>Grand Total</span>
                    <span x-text="formatCurrency(grandTotal())"></span>
                    <input type="hidden" name="grand_total" :value="grandTotal()">
                </div>
                <div class="mt-6">
                    <label class="block mb-2 text-sm font-semibold text-green-700 uppercase">Pembayaran Diterima</label>
                    <div class="relative">
                        <span class="absolute font-bold text-green-700 -translate-y-1/2 left-4 top-1/2">Rp</span>
                        <input type="text"
                               name="payment_received"
                               x-model="paymentReceivedFormatted"
                               @input="formatPaymentReceived()"
                               placeholder="Masukkan nominal pembayaran"
                               class="w-full p-3 pl-12 transition bg-white border-2 border-green-200 shadow rounded-xl focus:ring-2 focus:ring-green-400">
                    </div>
                </div>
                <div class="flex justify-between mt-4 text-sm font-semibold" x-show="paymentReceived > 0">
                    <span>Status</span>
                    <span :class="balanceDue() === 0 ? 'text-green-700' : 'text-yellow-700'"
                          x-text="balanceDue() === 0 ? 'Sudah Dibayar' : 'Belum Dibayar'"></span>
                </div>
                <button type="submit"
                        class="w-full px-8 py-4 mt-6 font-bold text-white transition shadow-lg bg-gradient-to-r from-green-500 to-green-700 rounded-xl hover:scale-105">
                     Checkout
                </button>
            </div>
        </div>
    </div>
</form>
    <script>
    function posApp(productsData, customertypesData, regularCustomersData) {
        return {
            customerType: customertypesData.length ? customertypesData[0] : '',
            customertypes: customertypesData,
            products: productsData,
            cart: [],
            total: 0,
            shippingCost: 0,
            shippingCostFormatted: '',
            tip: 0,
            tipFormatted: '',
            paymentReceived: 0,
            paymentReceivedFormatted: '',
            orderId: '',
            regularCustomers: regularCustomersData,
            selectedRegularCustomer: '',
            buyerName: '',

            init() {
                if (!this.orderId) {
                    this.orderId = this.generateOrderId();
                }
            },

            generateOrderId() {
                const now = new Date();
                const pad = (value) => String(value).padStart(2, '0');
                return `ORD-${now.getFullYear()}${pad(now.getMonth() + 1)}${pad(now.getDate())}-${pad(now.getHours())}${pad(now.getMinutes())}${pad(now.getSeconds())}`;
            },

            getPrice(product) {
                if (!product?.prices?.length) {
                    return 0;
                }

                const matched = product.prices.find((pr) => pr.customer_type === this.customerType);
                return matched ? parseFloat(matched.price) : 0;
            },

            addToCart(product) {
                const price = this.getPrice(product);
                const existing = this.cart.find((item) => item.id === product.id);

                if (existing) {
                    existing.qty += 1;
                    existing.subtotal = existing.qty * existing.price;
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        price: price,
                        qty: 1,
                        satuan: 'pcs',
                        subtotal: price,
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

                item.subtotal = item.price * item.qty;
                this.calculateTotal();
            },

            removeProduct(index) {
                this.cart.splice(index, 1);
                this.calculateTotal();
            },

            calculateTotal() {
                this.total = this.cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
            },

            formatCurrency(value) {
                const numeric = Number(value) || 0;
                return new Intl.NumberFormat('id-ID').format(numeric);
            },

            formatShippingCost() {
                const digits = this.shippingCostFormatted.replace(/[^0-9]/g, '');
                this.shippingCost = digits ? parseInt(digits, 10) : 0;
                this.shippingCostFormatted = digits ? 'Rp ' + this.formatCurrency(this.shippingCost) : '';
            },

            formatTip() {
                const digits = this.tipFormatted.replace(/[^0-9]/g, '');
                this.tip = digits ? parseInt(digits, 10) : 0;
                this.tipFormatted = digits ? 'Rp ' + this.formatCurrency(this.tip) : '';
            },

            formatPaymentReceived() {
                const digits = this.paymentReceivedFormatted.replace(/[^0-9]/g, '');
                this.paymentReceived = digits ? parseInt(digits, 10) : 0;
                this.paymentReceivedFormatted = digits ? 'Rp ' + this.formatCurrency(this.paymentReceived) : '';
            },

            grandTotal() {
                return this.total + this.shippingCost + this.tip;
            },

            balanceDue() {
                return Math.max(this.grandTotal() - this.paymentReceived, 0);
            },

            changeDue() {
                return Math.max(this.paymentReceived - this.grandTotal(), 0);
            },

            handleRegularCustomerChange() {
                this.buyerName = this.selectedRegularCustomer || '';
            },
        };
    }
    </script>
</x-app-layout>
