<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-extrabold text-red-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-red-700 bg-red-100 rounded-full"><i class="fas fa-minus"></i></span>
                {{ __('Kurangi Stok (Stock Out)') }}
            </h2>
            <a href="{{ route('invent') }}"
                class="px-4 py-2 font-medium text-white transition rounded-lg shadow bg-gradient-to-r from-gray-500 to-gray-700 hover:scale-105">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-breadcrumb :items="[
            ['title' => 'Inventori', 'url' => route('invent')],
            ['title' => 'Kurangi Stok']
        ]" />

        <div class="mt-8 overflow-hidden bg-white shadow-lg rounded-2xl">
            <div class="p-8">
                <form method="POST" action="{{ route('storeStockOut') }}" class="space-y-8"
                    onsubmit="return confirm('Simpan data pengurangan stok?')"
                    x-data="{
                        stokUser: Number(@json(old('stok', 0))) || 0,
                        products: {{ Js::from($produk->mapWithKeys(fn($item) => [
                            (string) $item->id => [
                                'unit_id' => $item->satuan,
                                'unit_name' => $item->units->name ?? null,
                                'current_stock' => $item->stock_quantity
                            ]
                        ])) }},
                        selectedProduct: String(@json(old('name_p', $produk->first()->id ?? ''))),
                        selectedUnitId: null,
                        selectedUnitName: '',
                        currentStock: 0,
                        syncUnit() {
                            const productData = this.products[this.selectedProduct] || null;
                            if (productData) {
                                this.selectedUnitId = productData.unit_id || '';
                                this.selectedUnitName = productData.unit_name || 'Belum diatur';
                                this.currentStock = productData.current_stock || 0;
                            } else {
                                this.selectedUnitId = '';
                                this.selectedUnitName = 'Belum diatur';
                                this.currentStock = 0;
                            }
                        }
                    }"
                    x-init="syncUnit();">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <!-- Pilih Produk -->
                        <div class="space-y-2">
                            <label for="name_p" class="block text-sm font-semibold text-gray-700">Nama Produk</label>
                            <div class="relative">
                                <select name="name_p" id="name_p" x-model="selectedProduct" @change="syncUnit()"
                                    class="w-full py-3 pl-4 pr-10 transition border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500">
                                    @foreach ($produk as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-sm text-gray-500">Stok saat ini: <span class="font-bold text-gray-800" x-text="currentStock"></span> <span x-text="selectedUnitName"></span></p>
                        </div>

                        <!-- Jumlah Stok Keluar -->
                        <div class="space-y-2">
                            <label for="stok" class="block text-sm font-semibold text-gray-700">Jumlah Keluar</label>
                            <div class="relative">
                                <input type="number" name="stok" id="stok" x-model="stokUser" min="1" required
                                    class="w-full py-3 pl-4 pr-12 transition border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500"
                                    placeholder="0">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                    <span class="text-sm font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded" x-text="selectedUnitName"></span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                        </div>

                        <!-- Keterangan / Alasan -->
                        <div class="space-y-2 md:col-span-2">
                            <label for="note" class="block text-sm font-semibold text-gray-700">Keterangan / Alasan</label>
                            <textarea name="note" id="note" rows="3" required
                                class="w-full py-3 px-4 transition border-gray-300 rounded-xl focus:ring-red-500 focus:border-red-500"
                                placeholder="Contoh: Barang rusak, Kadaluarsa, Pemakaian sendiri..."></textarea>
                            <x-input-error :messages="$errors->get('note')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <button type="submit"
                            class="px-6 py-2 font-medium text-white transition rounded-lg shadow bg-gradient-to-r from-red-500 to-red-700 hover:scale-105">
                            <i class="mr-2 fas fa-save"></i>Simpan Pengurangan Stok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
