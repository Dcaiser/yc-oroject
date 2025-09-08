<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-plus"></i>{{ __('Tambah stok Produk Baru') }}
            </h2>
            <a href="{{ route('invent') }}"
                class="px-4 py-2 font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('addstock') }}" enctype="multipart/form-data" class="space-y-6" onsubmit="return confirm('simpan data terbaru?')"  x-data="{ harga: 0, stok: 0 }" >
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Left Column - Product Info -->
                    <div class="space-y-4">
                        <!-- Product Name -->
                        <div>
                            <label for="name-p" class="block mb-1 text-sm font-medium text-gray-700">pilih produk</label>
                            <select id="name-p" required
                                name="name_p"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($produk as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                                @endforeach
                            </select>
                            @error('name-p')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->

                        <!-- Price -->
<div x-data="{
        hargaRaw: 0,
        hargaFormatted: '',
        stok: 0,
        formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }
    }"
    x-init="hargaFormatted = formatRupiah(hargaRaw)"
    class="space-y-4"
>

    <!-- Harga Distributor -->
    <div>
        <label class="block mb-1 font-medium">Harga distributor</label>
        <div class="relative">
            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
            <input type="text"
                id="harga_display"
                x-model="hargaFormatted"
                @input="
                    let raw = $event.target.value.replace(/\D/g, '');
                    hargaRaw = parseInt(raw || 0);
                    hargaFormatted = formatRupiah(hargaRaw);
                "
                class="w-full py-2 pl-12 pr-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="0">

            <!-- Hidden input untuk dikirim ke server -->
            <input type="hidden" name="harga_p" :value="hargaRaw">
        </div>
    </div>

    <!-- Stok Barang -->
    <div class="w-48">
        <label for="stok" class="block mb-1 text-sm font-medium text-gray-700">
            Stok Barang <span class="text-red-500">*</span>
        </label>
        <div class="flex w-full overflow-hidden border border-gray-300 rounded-lg">
            <!-- Tombol Minus -->
            <button type="button"
                @click="if(stok > 0) stok--"
                class="px-3 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200">-</button>

            <!-- Input Angka -->
            <input type="number"
                id="stok"
                name="stok"
                x-model="stok"
                min="0"
                class="w-full text-center focus:outline-none"
                placeholder="0">

            <!-- Tombol Plus -->
            <button type="button"
                @click="stok++"
                class="px-3 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200">+</button>
        </div>
    </div>

    <!-- Satuan -->
    <div>
        <label for="satuan" class="block mb-1 text-sm font-medium text-gray-700">
            Satuan <span class="text-red-500">*</span>
        </label>
        <input type="text"
            id="satuan"
            name="satuan"
            value="{{ old('satuan') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Masukkan satuan stok produk (kg/liter/..)">
        @error('satuan')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Harga Total -->
    <div>
        <label class="block mb-1 font-medium">Harga modal total</label>
        <div class="relative">
            <span class="absolute text-gray-500 left-3 top-2">Rp</span>
            <input type="text"
                id="harga-total"
                :value="new Intl.NumberFormat('id-ID').format((hargaRaw * stok) || 0)"
                readonly
                class="w-full py-2 pl-12 pr-3 bg-gray-100 border border-gray-300 rounded-lg focus:outline-none"
                placeholder="0">

            <!-- Hidden input untuk database -->
            <input type="hidden" name="harga_t" :value="(hargaRaw * stok) || 0">
        </div>
    </div>

</div>

                    <!-- Right Column - Image Upload -->
                    <diva class="space-y-4">
                    </diva>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end pt-6 space-x-4 border-t border-gray-200">
                    <a href="{{ route('invent') }}"
                        class="px-6 py-2 font-medium text-gray-800 transition-colors bg-gray-300 rounded-lg hover:bg-gray-400">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="mr-2 fas fa-save"></i>Simpan
                    </button>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
