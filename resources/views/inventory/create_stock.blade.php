<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full"><i class="fas fa-plus"></i></span>
                {{ __('Tambah Stok Produk Baru') }}
            </h2>
            <a href="{{ route('invent') }}"
                class="px-4 py-2 font-medium text-white transition rounded-lg shadow bg-linear-to-r from-green-500 to-green-700 hover:scale-105">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="mt-8 overflow-hidden bg-white shadow-lg rounded-2xl">
        <div class="p-8">
            <form method="POST" action="{{ route('addstock') }}" enctype="multipart/form-data"
      class="space-y-8"
      onsubmit="return confirm('Simpan data terbaru?')"
            x-data="{
                stokUser: Number(@json(old('stok', 0))) || 0,
                hargaRaw: Number(@json(old('harga_p', 0))) || 0,
        hargaFormatted: '',
        hargaTotal: 0,
        products: {{ Js::from($produk->mapWithKeys(fn($item) => [
            (string) $item->id => [
                'unit_id' => $item->satuan,
                'unit_name' => $item->units->name ?? null,
            ]
        ])) }},
    selectedProduct: String(@json(old('name_p', $produk->first()->id ?? ''))),
        selectedUnitId: null,
        selectedUnitName: '',
        formatRupiah(value) { return new Intl.NumberFormat('id-ID').format(value || 0); },
        updateHargaTotal() {
            this.hargaTotal = (this.hargaRaw * this.stokUser) || 0;
        },
        syncUnit() {
            const productData = this.products[this.selectedProduct] || null;
            if (productData) {
                this.selectedUnitId = productData.unit_id || '';
                this.selectedUnitName = productData.unit_name || 'Belum diatur';
            } else {
                this.selectedUnitId = '';
                this.selectedUnitName = 'Belum diatur';
            }
        }
      }"
      x-init="hargaFormatted = formatRupiah(hargaRaw); updateHargaTotal(); syncUnit();">
    @csrf

    <!-- Pilih Produk -->
    <div>
        <label for="name-p" class="block mb-1 text-sm font-semibold text-green-700">Pilih Produk</label>
        <select id="name-p" required
            name="name_p"
            x-model="selectedProduct"
            @change="syncUnit()"
            class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
            @foreach($produk as $cat)
            <option value="{{$cat->id}}" {{ (string) old('name_p', $produk->first()->id ?? '') === (string) $cat->id ? 'selected' : '' }}>{{$cat->name}}</option>
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
                    updateHargaTotal();
                "
                class="w-full py-2 pl-12 pr-3 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50"
                placeholder="0">
            <input type="hidden" name="harga_p" :value="hargaRaw">
        </div>
    </div>

    <!-- Jumlah Stok -->
    <div class="mt-4">
        <label for="stok" class="block mb-1 text-sm font-semibold text-green-700">
            Jumlah <span class="text-red-500">*</span>
        </label>
        <input type="number"
            id="stok"
            name="stok"
            x-model.number="stokUser"
            min="1"
            @input="updateHargaTotal"
            class="w-full px-3 py-2 border-2 border-green-200 rounded-lg bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400"
            placeholder="0">
    </div>

    <!-- Satuan -->
    <div class="mt-4">
        <label class="block mb-1 text-sm font-semibold text-green-700">
            Satuan <span class="text-red-500">*</span>
        </label>
        <div class="w-full px-3 py-2 text-green-800 bg-green-100 border-2 border-green-200 rounded-lg">
            <span x-text="selectedUnitName"></span>
        </div>
        <input type="hidden" name="satuan" :value="selectedUnitId">
        <template x-if="!selectedUnitId">
            <p class="mt-1 text-sm text-red-600">Satuan produk belum diatur. Silakan perbarui data produk terlebih dahulu.</p>
        </template>
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
                :value="formatRupiah(hargaTotal)"
                readonly
                class="w-full py-2 pl-12 pr-3 bg-green-100 border-2 border-green-200 rounded-lg focus:outline-none"
                placeholder="0">
            <input type="hidden" name="harga_t" :value="hargaTotal">
        </div>
        <div class="mt-2 text-lg font-bold text-green-700">
            Total: Rp <span x-text="formatRupiah(hargaTotal)"></span>
        </div>
    </div>

    <!-- Upload Bukti -->
    <div class="space-y-6">
        <label class="block mb-1 text-sm font-semibold text-green-700">Upload Bukti / Foto (Opsional)</label>
        <input type="file" name="bukti" accept="image/*"
            class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
        @error('bukti')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Buttons -->
    <div class="flex items-center justify-end pt-8 space-x-4 border-t border-green-200">
        <a href="{{ route('invent') }}"
            class="px-6 py-2 font-medium text-green-900 transition bg-green-100 rounded-lg hover:bg-green-200">
            Batal
        </a>
        <button type="submit"
            class="px-6 py-2 font-medium text-white transition rounded-lg shadow bg-linear-to-r from-green-500 to-green-700 hover:scale-105">
            <i class="mr-2 fas fa-save"></i>Simpan
        </button>
    </div>
</form>
        </div>
    </div>
</x-app-layout>
