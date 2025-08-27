<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-plus"></i>{{ __('Tambah Produk Baru') }}
            </h2>
            <a href="{{ route('products.index') }}"
                class="px-4 py-2 font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <!-- Left Column - Product Info -->
                    <div class="space-y-4">
                        <!-- Product Name -->
                        <div>
                            <label for="nama" class="block mb-1 text-sm font-medium text-gray-700">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text"
                                id="nama"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan nama produk">
                            @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="deskripsi" class="block mb-1 text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea id="deskripsi"
                                name="deskripsi"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Masukkan deskripsi produk">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
<h3 class="mt-6 mb-2 text-lg font-semibold">Harga per Kategori Customer</h3>
        <div class="grid grid-cols-1 gap-4">
            @foreach ($customertypes as $type)
                <div>
                    <label class="block mb-1 font-medium">Harga {{ ucfirst($type) }}</label>
                     <div x-data="{ harga: '' }">
                            <label for="harga" class="block mb-1 text-sm font-medium text-gray-700">
                            </label>
                            <div class="relative">
                                <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                                <input type="text"
                                    id="harga"
                                    name="prices[{{ $type }}]"
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
            @endforeach
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
                            <label for="kategori_id" class="block mb-1 text-sm font-medium text-gray-700">Kategori</label>
                            <select id="kategori_id" required
                                name="kategori_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($category as $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column - Image Upload -->
                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">Gambar Produk</label>
                            <div x-data="imageUploader()" class="space-y-4">
                                <!-- Image Preview -->
                                <div class="p-6 text-center border-2 border-gray-300 border-dashed rounded-lg"
                                    :class="{ 'border-blue-500 bg-blue-50': dragging }"
                                    @dragover.prevent="dragging = true"
                                    @dragleave.prevent="dragging = false"
                                    @drop.prevent="handleDrop($event)">

                                    <template x-if="!preview">
                                        <div>
                                            <i class="mb-4 text-4xl text-gray-400 fas fa-cloud-upload-alt"></i>
                                            <p class="mb-2 text-gray-600">Drag & drop gambar di sini</p>
                                            <p class="mb-4 text-sm text-gray-500">atau</p>
                                            <button type="button"
                                                @click="$refs.fileInput.click()"
                                                class="px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                                Pilih File
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="preview">
                                        <div class="relative">
                                            <img :src="preview" alt="Preview" class="object-cover h-48 max-w-full mx-auto rounded-lg">
                                            <button type="button"
                                                @click="preview = null; $refs.fileInput.value = ''"
                                                class="absolute flex items-center justify-center w-8 h-8 text-white bg-red-500 rounded-full top-2 right-2 hover:bg-red-600">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <input type="file"
                                    x-ref="fileInput"
                                    name="gambar"
                                    accept="image/*"
                                    @change="updatePreview($event.target.files[0])"
                                    class="hidden">

                                <p class="text-xs text-gray-500">
                                    Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                                </p>
                            </div>
                            @error('gambar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
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
