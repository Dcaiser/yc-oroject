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
            <form method="POST" action="" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Left Column - Product Info -->
                    <div class="space-y-4">
                        <!-- Product Name -->
                        <div>
                            <label for="name-p" class="block mb-1 text-sm font-medium text-gray-700">pilih produk</label>
                            <select id="name-p" required
                                name="name-p"
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
                <div>
                    <label class="block mb-1 font-medium">Harga distributor</label>
                     <div x-data="{ harga: '' }">
                            <label for="harga" class="block mb-1 text-sm font-medium text-gray-700">
                            </label>
                            <div class="relative">
                                <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                                <input type="text"
                                    id="harga"
                                    name="harga-p"
                                    x-model="harga"
                                    x-on:input="harga = new Intl.NumberFormat('id-ID').format(harga.replace(/[^0-9]/g, ''))"

                                    class="w-full py-2 pl-12 pr-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0">
                            </div>
                            @error('harga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                </div>
        </div>
                        <!-- Stock -->
                        <div x-data="{ qty: {{ old('stok', 0) }} }" class="w-48">
                            <label for="stok" class="block mb-1 text-sm font-medium text-gray-700">
                                Stok Barang <span class="text-red-500">*</span>
                            </label>

                            <div class="flex w-full overflow-hidden border border-gray-300 rounded-lg">
                                <!-- Tombol Minus -->
                                <button type="button"
                                    @click="if(qty > 0) qty--"
                                    class="px-3 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200">
                                    -
                                </button>

                                <!-- Input Angka -->
                                <input type="number"
                                    id="stok"
                                    name="stok"
                                    x-model="qty"
                                    min="0"
                                    class="w-full text-center focus:outline-none"
                                    placeholder="0">

                                <!-- Tombol Plus -->
                                <button type="button"
                                    @click="qty++"
                                    class="px-3 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200">
                                    +
                                </button>


                            </div>

                            @error('stok')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                        <label for="nama" class="block mb-1 text-sm font-medium text-gray-700">satuan<span class="text-red-500">*</span></label>
                                <input type="text"
                                    id="nama"
                                    name="satuan"
                                    value="{{ old('satuan') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Masukkan satuan stok produk (kg/liter/..)">
                                @error('nama')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                        </div>
                        <!-- Category -->
                                        <div>
                    <label class="block mb-1 font-medium">Harga total</label>
                     <div x-data="{ harga: '' }">
                            <label for="harga" class="block mb-1 text-sm font-medium text-gray-700">
                            </label>
                            <div class="relative">
                                <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                                <input type="text"
                                    id="harga"
                                    name="harga-t"
                                    x-model="harga"
                                    x-on:input="harga = new Intl.NumberFormat('id-ID').format(harga.replace(/[^0-9]/g, ''))"

                                    class="w-full py-2 pl-12 pr-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0">
                            </div>
                            @error('harga')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                </div>

                    </div>

                    <!-- Right Column - Image Upload -->
                    <diva class="space-y-4">
                    </diva>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end pt-6 space-x-4 border-t border-gray-200">
                    <a href="{{ route('products.index') }}"
                        class="px-6 py-2 font-medium text-gray-800 transition-colors bg-gray-300 rounded-lg hover:bg-gray-400">
                        Batal
                    </a>
                    <button type="submit" onclick="return confirm('yakin?')"
                        class="px-6 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="mr-2 fas fa-save"></i>Simpan Produk
                    </button>

                </div>
            </form>
        </div>
    </div>
</x-app-layout>
