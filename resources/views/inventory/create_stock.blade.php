<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-extrabold text-green-900 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-700 rounded-full"><i class="fas fa-plus"></i></span>
                {{ __('Tambah Stok Produk Baru') }}
            </h2>
            <a href="{{ route('invent') }}"
                class="px-4 py-2 font-medium text-white bg-gradient-to-r from-green-500 to-green-700 rounded-lg shadow hover:scale-105 transition">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="overflow-hidden bg-white shadow-lg rounded-2xl mt-8">
        <div class="p-8">
            <form method="POST" action="{{ route('addstock') }}" enctype="multipart/form-data" class="space-y-8" onsubmit="return confirm('Simpan data terbaru?')" x-data="{ hargaRaw: 0, hargaFormatted: '', stok: 0, formatRupiah(value) { return new Intl.NumberFormat('id-ID').format(value); } }" x-init="hargaFormatted = formatRupiah(hargaRaw)">
                @csrf

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Left Column - Product Info -->
                    <div class="space-y-6">
                        <!-- Product Name -->
                        <div>
                            <label for="name-p" class="block mb-1 text-sm font-semibold text-green-700">Pilih Produk</label>
                            <select id="name-p" required
                                name="name_p"
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
                                @foreach($produk as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                                @endforeach
                            </select>
                            @error('name-p')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harga Distributor -->
                        <div>
                            <label class="block mb-1 font-semibold text-green-700">Harga Distributor</label>
                            <div class="relative">
                                <span class="absolute text-green-500 left-3 top-2">Rp</span>
                                <input type="text"
                                    id="harga_display"
                                    x-model="hargaFormatted"
                                    @input="
                                        let raw = $event.target.value.replace(/\D/g, '');
                                        hargaRaw = parseInt(raw || 0);
                                        hargaFormatted = formatRupiah(hargaRaw);
                                    "
                                    class="w-full py-2 pl-12 pr-3 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50"
                                    placeholder="0">
                                <input type="hidden" name="harga_p" :value="hargaRaw">
                            </div>
                        </div>

                        <!-- Stok Barang -->
                        <div>
                            <label for="stok" class="block mb-1 text-sm font-semibold text-green-700">
                                Stok Barang <span class="text-red-500">*</span>
                            </label>
                            <div class="flex w-full overflow-hidden border-2 border-green-200 rounded-lg bg-green-50">
                                <button type="button"
                                    @click="if(stok > 0) stok--"
                                    class="px-3 py-2 text-green-700 bg-green-100 hover:bg-green-200">-</button>
                                <input type="number"
                                    id="stok"
                                    name="stok"
                                    x-model="stok"
                                    min="0"
                                    class="w-full text-center bg-transparent focus:outline-none"
                                    placeholder="0">
                                <button type="button"
                                    @click="stok++"
                                    class="px-3 py-2 text-green-700 bg-green-100 hover:bg-green-200">+</button>
                            </div>
                        </div>

                        <!-- Satuan -->
                        <div>
                            <label for="satuan" class="block mb-1 text-sm font-semibold text-green-700">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                id="satuan"
                                name="satuan"
                                value="{{ old('satuan') }}"
                                required
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50"
                                placeholder="Masukkan satuan stok produk (kg/liter/...)">
                            @error('satuan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Harga Total -->
                        <div>
                            <label class="block mb-1 font-semibold text-green-700">Harga Modal Total</label>
                            <div class="relative">
                                <span class="absolute text-green-500 left-3 top-2">Rp</span>
                                <input type="text"
                                    id="harga-total"
                                    :value="formatRupiah((hargaRaw * stok) || 0)"
                                    readonly
                                    class="w-full py-2 pl-12 pr-3 bg-green-100 border-2 border-green-200 rounded-lg focus:outline-none"
                                    placeholder="0">
                                <input type="hidden" name="harga_t" :value="(hargaRaw * stok) || 0">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Image Upload (optional, modern style) -->
                    <div class="space-y-6">
                        <label class="block mb-1 text-sm font-semibold text-green-700">Upload Bukti / Foto (Opsional)</label>
                        <input type="file" name="bukti" accept="image/*"
                            class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
                        @error('bukti')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end pt-8 space-x-4 border-t border-green-200">
                    <a href="{{ route('invent') }}"
                        class="px-6 py-2 font-medium text-green-900 bg-green-100 rounded-lg hover:bg-green-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 font-medium text-white bg-gradient-to-r from-green-500 to-green-700 rounded-lg shadow hover:scale-105 transition">
                        <i class="mr-2 fas fa-save"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
