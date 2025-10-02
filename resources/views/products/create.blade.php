<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full"><i class="fas fa-plus"></i></span>
                {{ __('Tambah Produk Baru') }}
            </h2>
            <a href="{{ route('products.index') }}"
                class="px-4 py-2 font-medium text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="mt-8 overflow-hidden bg-white shadow-lg rounded-2xl">
        <div class="p-8">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <!-- Left Column - Product Info -->
                    <div class="space-y-6">
                        <!-- Product Name -->
                        <div>
                            <label for="nama" class="block mb-1 text-sm font-semibold text-green-700">Nama Produk <span class="text-red-500">*</span></label>
                            <input type="text"
                                id="nama"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50"
                                placeholder="Masukkan nama produk">
                            @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="deskripsi" class="block mb-1 text-sm font-semibold text-green-700">Deskripsi</label>
                            <textarea id="deskripsi"
                                name="deskripsi"
                                rows="4"
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50"
                                placeholder="Masukkan deskripsi produk">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="kategori_id" class="block mb-1 text-sm font-semibold text-green-700">Supplier</label>
                            <select id="kategori_id" required
                                name="supplier_id"
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
                                @foreach($supplier as $supp)
                                <option value="{{$supp->id}}">{{$supp->name}}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <h3 class="mt-6 mb-2 text-lg font-semibold text-green-900">Harga jual per Kategori Customer</h3>
                        <div class="grid grid-cols-1 gap-4 p-5 rounded bg-green-50">
                            @foreach ($customertypes as $type)
                                <div>
                                    <label class="block mb-1 font-medium text-green-700">Harga {{ ucfirst($type) }}</label>
                                    <div x-data="{ harga: '', hargaRaw: '' }">
                                        <div class="relative">
                                            <span class="absolute text-green-500 left-3 top-2">Rp</span>
                                            <input type="text"
                                                x-model="harga"
                                                @input="
                                                    let clean = $event.target.value.replace(/[^0-9]/g, '');
                                                    hargaRaw = clean;
                                                    harga = clean ? new Intl.NumberFormat('id-ID').format(parseInt(clean)) : '';
                                                "
                                                class="w-full py-2 pl-12 pr-3 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50"
                                                placeholder="0">
                                            <input type="hidden" name="prices[{{ $type }}]" :value="hargaRaw">
                                        </div>
                                        @error('harga')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Stock -->
                        
                        <div
                            x-data="{
                                units: {{ Js::from($units->map(fn($u)=>['id'=>$u->id,'name'=>$u->name,'conversion'=>$u->conversion_to_base])) }},
                                stokUser: 0,
                                selectedUnit: '',
                                stokFinal: 0,
                                hargaRaw: 0,
                                hargaFormatted: '',
                                hargaTotal: 0,

                                formatRupiah(value) {
                                    return new Intl.NumberFormat('id-ID').format(value);
                                },

                                updateFinalQty() {
                                    let unit = this.units.find(u => u.id == this.selectedUnit);
                                    this.stokFinal = (unit && unit.conversion && this.stokUser > 0)
                                        ? this.stokUser * unit.conversion
                                        : 0;
                                    this.updateHargaTotal();
                                },

                                updateHargaTotal() {
                                    this.hargaTotal = (this.hargaRaw * this.stokFinal) || 0;
                                }
                            }"
                            x-init="hargaFormatted = formatRupiah(hargaRaw)"
                        >
                            <div class="mt-4">
                                <label for="stok" class="block mb-1 text-sm font-semibold text-green-700">
                                    stok barang <span class="text-red-500">*</span>
                                </label>
                                <input type="number"
                                    id="stok"
                                    name="stok_user"
                                    x-model.number="stokUser"
                                    min="1"
                                    @input="updateFinalQty"
                                    class="w-full px-3 py-2 border-2 border-green-200 rounded-lg bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400"
                                    placeholder="0">
                            </div>

                            <label for="satuan" class="block mt-4 mb-1 text-sm font-semibold text-green-700">
                                Satuan <span class="text-red-500">*</span>
                            </label>
                            <select id="satuan"
                                name="satuan"
                                x-model="selectedUnit"
                                @change="updateFinalQty"
                                required
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
                                <option value="">Pilih satuan</option>
                                <template x-for="unit in units" :key="unit.id">
                                    <option :value="unit.id" x-text="unit.name"></option>
                                </template>
                            </select>

                            <!-- Hidden input untuk stok final (dalam pcs) -->
                            <input type="hidden" name="stok" :value="stokFinal">

                            <!-- Tampilkan hasil konversi -->
                            <template x-if="stokFinal > 0 && selectedUnit">
                                <div class="mt-2 text-sm text-green-700">
                                    <span x-text="stokUser"></span>
                                    <span x-text="units.find(u => u.id == selectedUnit)?.name"></span>
                                    = <span x-text="stokFinal"></span> pcs
                                </div>
                            </template>
                        </div>
                        <!-- Category -->
                        <div>
                            <label for="kategori_id" class="block mb-1 text-sm font-semibold text-green-700">Kategori</label>
                            <select id="kategori_id" required
                                name="kategori_id"
                                class="w-full px-3 py-2 border-2 border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
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
                    <div class="space-y-6">
                        <label class="block mb-1 text-sm font-semibold text-green-700">Gambar Produk</label>
                        <div x-data="imageUploader()" class="space-y-4">
                            <div class="p-6 text-center border-2 border-green-200 border-dashed rounded-lg"
                                :class="{ 'border-green-500 bg-green-50': dragging }"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="handleDrop($event)">
                                <template x-if="!preview">
                                    <div>
                                        <i class="mb-4 text-4xl text-green-400 fas fa-cloud-upload-alt"></i>
                                        <p class="mb-2 text-green-600">Drag & drop gambar di sini</p>
                                        <p class="mb-4 text-sm text-green-500">atau</p>
                                        <button type="button"
                                            @click="$refs.fileInput.click()"
                                            class="px-4 py-2 text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
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
                            <p class="text-xs text-green-500">
                                Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                            </p>
                        </div>
                        @error('gambar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end pt-8 space-x-4 border-t border-green-200">
                    <a href="{{ route('products.index') }}"
                        class="px-6 py-2 font-medium text-green-900 transition bg-green-100 rounded-lg hover:bg-green-200">
                        Batal
                    </a>
                    <button type="submit" onclick="return confirm('yakin?')"
                        class="px-6 py-2 font-medium text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                        <i class="mr-2 fas fa-save"></i>Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
