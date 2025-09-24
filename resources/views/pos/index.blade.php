<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-3xl font-extrabold text-gray-900 tracking-tight">
                <span class="inline-flex items-center justify-center w-10 h-10 mr-2 text-2xl bg-green-100 text-green-700 rounded-full">🛒</span>
                POS (Point of Sale)
            </h2>
            <span class="px-4 py-1 text-sm font-bold text-white bg-gradient-to-r from-green-500 to-green-700 rounded-full shadow">
                Versi Beta
            </span>
        </div>
    </x-slot>

    <div x-data="posApp({{ $product->toJson() }}, {{ json_encode($customertypes) }}, {{ json_encode($regularCustomers ?? []) }})"
         class="min-h-screen p-8 bg-gradient-to-br from-green-50 via-white to-green-100">

        <div class="mb-10">
            <h3 class="mb-3 text-xl font-semibold text-green-800">👤 Pilih Tipe pembeli</h3>
            <div class="flex flex-wrap gap-4">
                <template x-for="type in customertypes" :key="type">
                    <button
                        class="px-6 py-2 text-base font-semibold rounded-xl shadow focus:outline-none transition"
                        :class="customerType === type
                            ? 'bg-gradient-to-r from-green-600 to-green-400 text-white shadow-lg scale-105'
                            : 'bg-white text-green-700 border-2 border-green-200 hover:bg-green-50'"
                        @click="customerType = type">
                        <span class="capitalize" x-text="type"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Input Nama Pembeli (hilang jika pelanggan tetap dipilih) --}}
        <div class="mb-8" x-show="!selectedRegularCustomer">
            <label class="block mb-2 text-lg font-semibold text-green-700">Nama Pembeli</label>
            <input type="text" x-model="buyerName" placeholder="Masukkan nama pembeli"
                   class="w-full p-3 border-2 border-green-200 rounded-xl shadow focus:ring-2 focus:ring-green-400 transition bg-white">
        </div>

        {{-- Input Ongkir --}}
        <div class="mb-8">
            <label class="block mb-2 text-lg font-semibold text-green-700">Ongkir</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-green-700 font-bold">Rp</span>
                <input type="text"
                       x-model="shippingCostFormatted"
                       @input="formatShippingCost"
                       placeholder="Masukkan ongkir"
                       class="w-full pl-12 p-3 border-2 border-green-200 rounded-xl shadow focus:ring-2 focus:ring-green-400 transition bg-white">
            </div>
        </div>


        {{-- Grid Produk --}}
        <h3 class="mb-5 text-2xl font-bold text-green-900">📦 Pilih Produk</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-7">
            <template x-for="product in products" :key="product.id">
                <div class="relative p-6 bg-white rounded-2xl shadow-lg cursor-pointer transition hover:shadow-xl hover:-translate-y-1 border border-green-100"
                     @click="addToCart(product)">
                    <div class="absolute top-3 right-3 px-2 py-0.5 text-xs font-bold text-green-600 bg-green-50 rounded">
                        <span x-text="product.sku"></span>
                    </div>
                    <h4 class="mb-2 text-lg font-bold text-green-800 truncate" x-text="product.name"></h4>
                    <p class="mt-4 text-xl font-extrabold text-green-600" x-text="formatCurrency(getPrice(product))"></p>
                    <span class="absolute bottom-3 right-3 px-3 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Tambah</span>
                </div>
            </template>
        </div>

        {{-- Keranjang & Ringkasan --}}
        <div class="grid gap-8 mt-14 md:grid-cols-3">
            {{-- Keranjang --}}
            <div class="md:col-span-2">
                <h3 class="mb-5 text-2xl font-bold text-green-900">🛍️ Keranjang</h3>
                <template x-if="cart.length === 0">
                    <div class="flex items-center justify-center h-32 text-green-400 bg-white rounded-xl shadow-inner">
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
                                <th class="px-5 py-3 border-b">Satuan</th> <!-- Tambahkan kolom satuan -->
                                <th class="px-5 py-3 border-b">Subtotal</th>
                                <th class="px-5 py-3 border-b">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in cart" :key="index">
                                <tr class="transition hover:bg-green-50">
                                    <td class="px-5 py-3 font-medium text-green-900 border-b" x-text="item.name"></td>
                                    <td class="px-5 py-3 text-green-700 font-bold border-b" x-text="formatCurrency(item.price)"></td>
                                    <td class="px-5 py-3 border-b">
                                        <input type="number" min="1" x-model.number="item.qty"
                                               @input="updateSubtotal(index)"
                                               class="w-20 px-3 py-2 text-center border-2 border-green-200 rounded-xl focus:ring-2 focus:ring-green-300 transition bg-white">
                                    </td>
                                    <td class="px-5 py-3 border-b">
                                        <select x-model="item.satuan" class="w-full p-2 border-2 border-green-200 rounded-xl bg-white">
                                            <option value="pcs">pcs</option>
                                            <option value="box">box</option>
                                            <option value="lusin">lusin</option>
                                            <!-- Tambahkan satuan lain sesuai kebutuhan -->
                                        </select>
                                    </td>
                                    <td class="px-5 py-3 font-semibold text-green-700 border-b" x-text="formatCurrency(item.subtotal)"></td>
                                    <td class="px-5 py-3 text-center border-b">
                                        <button @click="removeProduct(index)"
                                                class="px-4 py-2 text-white bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow hover:scale-110 transition">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Ringkasan Belanja --}}
            <div class="p-8 bg-white shadow-xl rounded-2xl h-fit" x-show="cart.length > 0">
                <h3 class="mb-5 text-xl font-bold text-green-900">📊 Ringkasan Belanja</h3>
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
                </div>
                <hr class="my-4">
                <div class="flex justify-between text-xl font-extrabold text-green-900">
                    <span>Grand Total</span>
                    <span x-text="formatCurrency(total + shippingCost)"></span>
                </div>
                <button @click="checkout()"
                        class="w-full px-8 py-4 mt-6 font-bold text-white bg-gradient-to-r from-green-500 to-green-700 rounded-xl shadow-lg hover:scale-105 transition">
                    💳 Proses Checkout
                </button>
            </div>
        </div>
    </div>

    <script>
    function posApp(productsData, customertypesData, regularCustomersData) {
        return {
            customerType: customertypesData[0],
            customertypes: customertypesData,
            products: productsData,
            cart: [],
            total: 0,
            shippingCost: 0,
            shippingCostFormatted: '',
            regularCustomers: regularCustomersData,
            selectedRegularCustomer: '',
            buyerName: '',

            getPrice(product) {
                if (!product.prices) return 0;
                let p = product.prices.find(pr => pr.customer_type === this.customerType);
                return p ? parseFloat(p.price) : 0;
            },

            addToCart(product) {
                let price = this.getPrice(product);
                let existing = this.cart.find(i => i.id === product.id);
                if (existing) {
                    existing.qty++;
                    existing.subtotal = existing.qty * existing.price;
                } else {
                    this.cart.push({
                        id: product.id,
                        name: product.name,
                        price: price,
                        qty: 1,
                        subtotal: price
                    });
                }
                this.calculateTotal();
            },

            updateSubtotal(index) {
                let item = this.cart[index];
                item.subtotal = item.price * item.qty;
                this.calculateTotal();
            },

            removeProduct(index) {
                this.cart.splice(index, 1);
                this.calculateTotal();
            },

            calculateTotal() {
                this.total = this.cart.reduce((sum, item) => sum + item.subtotal, 0);
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            },

            formatShippingCost(e) {
                let val = this.shippingCostFormatted.replace(/[^0-9]/g, '');
                this.shippingCost = val ? parseInt(val) : 0;
                this.shippingCostFormatted = val ?  + new Intl.NumberFormat('id-ID').format(val) : '';
            },

            handleRegularCustomerChange() {
                if (this.selectedRegularCustomer) {
                    this.buyerName = this.selectedRegularCustomer;
                } else {
                    this.buyerName = '';
                }
            },

            checkout() {
                if (this.cart.length === 0) {
                    alert('Keranjang kosong!');
                    return;
                }
                alert('Total pembayaran: ' + this.formatCurrency(this.total + this.shippingCost));
            }
        }
    }
    </script>
</x-app-layout>
