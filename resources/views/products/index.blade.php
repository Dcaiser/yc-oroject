<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full"><i class="fas fa-box"></i></span>
                {{ __('Daftar Produk') }}
            </h2>
            <a href="{{ route('products.create') }}"
                class="flex items-center px-4 py-2 font-medium text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                <i class="mr-2 fas fa-plus"></i>Tambah Produk
            </a>
        </div>
    </x-slot>
    @if(isset($products) && count($products) > 0)

    <div class="space-y-6">
        <!-- Search and Filter Section -->
        <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
            <div class="p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <!-- Search Input -->
                    <div class="flex-1 max-w-md">
                        <form method="GET" action="{{ route('products.index') }}" class="flex">
                            <div class="relative flex-1">
                                <input type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Cari produk..."
                                    class="w-full py-2 pl-10 pr-4 border-2 border-green-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="text-green-400 fas fa-search"></i>
                                </div>
                            </div>
                            <button type="submit"
                                class="px-4 py-2 text-white transition bg-gradient-to-r from-green-500 to-green-700 rounded-r-xl hover:scale-105">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Filter Options -->
                    <div class="flex items-center gap-4 space-x-2">
                        <form action="{{ route('deleteall') }}" method="post" onsubmit="return confirm('Yakin hapus semua data?')">
                            @csrf
                            @method('DELETE')
                            <button class="p-3 text-white transition rounded-lg shadow bg-gradient-to-r from-red-500 to-red-700 hover:scale-105" type="submit">
                                <i class="fa-solid fa-trash"></i> Hapus Semua
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if (session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            class="p-4 mb-4 text-green-800 border border-green-500 rounded-lg bg-green-50">
            {{ session('success') }}
        </div>
        @endif

        <!-- Products Grid -->
        <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                    <div class="overflow-hidden transition-shadow duration-300 bg-white border border-green-100 rounded-lg hover:shadow-lg">
                        <!-- Product Image -->
                        <div class="flex items-center justify-center bg-green-100 aspect-w-16 aspect-h-12">
                            <img src="{{ $product['gambar'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}"
                                alt="{{ $product['nama'] }}"
                                class="object-cover w-full h-48 rounded-t-lg">
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="mb-2 text-lg font-bold text-green-900 line-clamp-2">{{ $product['name'] }}</h3>
                            <p class="mb-1 text-sm text-green-700">{{$product['sku']}}</p>
                            <p class="mb-2 text-sm text-green-700">{{ $product->category ? $product->category->name : '-' }}</p>

                            <!-- Price and Stock -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="flex text-sm text-green-700">
                                 @php
                                $unit = $product->units; // relasi units di model Produk
                                $stok = $product['stock_quantity'] ?? 0;
                                $conversion = $unit ? $unit->conversion_to_base : 1;
                                $selectedUnitId = old("produk.{$product->id}.satuan", $product->satuan);
                                $selectedUnit = $units->firstWhere('id', $selectedUnitId);
                                $isDus = $selectedUnit && $selectedUnit->name == 'dus';
                                $isPcs = $selectedUnit && $selectedUnit->name == 'pcs';
                                if ($isDus) {
                                    $displayStok = $conversion > 0 ? floor($stok / $conversion) : $stok;
                                    $displaySatuan = 'dus';
                                } elseif ($isPcs) {
                                    $displayStok = $stok;
                                    $displaySatuan = 'pcs';
                                } else {
                                    $displayStok = $stok;
                                    $displaySatuan = $selectedUnit ? $selectedUnit->name : '';
                                }
                            @endphp
                            <span class="font-semibold text-green-700">
                                {{ $displayStok }} {{ $displaySatuan }}
                            </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2" x-data="{ op: false }">
                                <!-- Tombol buka modal -->
                                <button
                                    @click="op = true"
                                    type="button"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-center text-green-900 transition bg-green-100 rounded hover:bg-green-200">
                                    <i class="mr-1 fas fa-edit"></i>Detail
                                </button>

                                <!-- Modal -->
                                <div
                                    x-show="op"
                                    x-transition
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                    <!-- Konten Modal -->
                                    <div
                                        class="bg-white w-[90%] md:w-[72%] gap-2 max-h-[90%] rounded-lg shadow-lg overflow-auto p-6 relative"
                                        @click.away="op = false">
                                        <!-- Tombol Close -->
                                        <button
                                            @click="op = false"
                                            class="absolute text-xl text-gray-600 top-3 right-3 hover:text-black">&times;</button>

                                        <!-- Judul Modal -->
                                        <h2 class="mb-4 text-2xl font-semibold text-green-900">Edit Data Produk</h2>

                                        <!-- Form -->
                                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="grid-cols-2 space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <!-- Gambar Produk -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-green-700">Gambar Produk</label>
                                                <div x-data="imageUploader()" class="space-y-4">
                                                    <!-- Image Preview -->
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
                                                        name="gambar-edit"
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

                                            <!-- Judul -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-green-700">Judul</label>
                                                <input type="text" name="title1" value="{{ $product['name']  }}" class="w-full p-2 border border-green-200 rounded">
                                            </div>

                                            <!-- Deskripsi -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-green-700">Deskripsi</label>
                                                <textarea name="description1" rows="4" class="w-full p-2 border border-green-200 rounded">{{ $product['description'] }}</textarea>
                                            </div>
                                             <div>
                                                <label for="kategori_id" class="block mb-1 text-sm font-medium text-green-700">Supplier</label>
                                                <select id="kategori_id" required
                                                    name="supplier_id1"
                                                    class="w-full px-3 py-2 border border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400">
                                                    @foreach($supplier as $supp)
                                                    <option value="{{$supp->id}}" {{ $product->supplier_id == $supp->id ? 'selected' : '' }}>{{$supp->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('supp_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Harga -->
                                            <h3 class="mt-6 mb-2 text-lg font-semibold text-green-900">Harga per Kategori Customer</h3>
                                            <div class="grid grid-cols-1 gap-4">
                                                @foreach ($customertypes as $type)
                                                    @php
                                                        $existing = optional($product->prices->firstWhere('customer_type', $type))->price;
                                                        $formatted = $existing !== null ? number_format($existing, 0, ',', '.') : '';
                                                    @endphp
                                                    <div>
                                                        <label class="block mb-1 font-medium text-green-700">Harga {{ ucfirst($type) }}</label>
                                                        <div
                                                            x-data="{
                                                                harga: '{{ $formatted }}',
                                                                numericVal: '{{ $existing ?? '' }}'
                                                            }"
                                                        >
                                                            <div class="relative">
                                                                <span class="absolute text-green-500 left-3 top-2">Rp</span>
                                                                <input type="text"
                                                                    x-model="harga"
                                                                    x-on:input="
                                                                        let clean = $event.target.value.replace(/[^0-9]/g,'');
                                                                        numericVal = clean;
                                                                        harga = clean ? new Intl.NumberFormat('id-ID').format(parseInt(clean)) : '';
                                                                    "
                                                                    class="w-full py-2 pl-12 pr-3 border border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400"
                                                                >
                                                                <input type="hidden" name="prices[{{ $type }}]" :value="numericVal">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <div>
                                                <label for="kategori_id" class="block mb-1 text-sm font-medium text-green-700">Kategori</label>
                                                <select id="kategori_id" required
                                                    name="kategori_id1"
                                                    class="w-full px-3 py-2 border border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400">
                                                    @foreach($category as $cat)
                                                    <option value="{{$cat->id}}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>{{$cat->name}}</option>
                                                    @endforeach
                                                </select>
                                                @error('kategori_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- SKU -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-green-700">SKU</label>
                                                <input type="text" name="sku1" value="{{ $product['sku'] ?? '' }}" class="w-full p-2 border border-green-200 rounded">
                                            </div>

                                            <!-- Stok -->
                                           <div>
                                                <label class="block mb-1 text-sm font-medium text-green-700">Jumlah Stok</label>
                                                @php
                                                    // Ambil relasi satuan dari produk
                                                    $unit = $product->units;
                                                    $stok = $product->stock_quantity ?? 0;
                                                    $conversion = $unit?->conversion_to_base ?? 1;

                                                    // Ambil satuan yang sedang dipilih (dari input lama atau default produk)
                                                    $selectedUnitId = old("produk.{$product->id}.satuan", $product->satuan);
                                                    $selectedUnit = $units->firstWhere('id', $selectedUnitId);

                                                    // Hitung stok yang akan ditampilkan berdasarkan satuan terpilih
                                                    if ($selectedUnit) {
                                                        $displaySatuan = $selectedUnit->name;
                                                        $displayStok = $conversion > 0 ? floor($stok / $conversion) : $stok;
                                                    } else {
                                                        // fallback jika tidak ada satuan
                                                        $displaySatuan = $unit?->name ?? '';
                                                        $displayStok = $stok;
                                                    }
                                                @endphp

                                                <div class="flex items-center gap-2">
                                                    <input
                                                        type="number"
                                                        name="stock1"
                                                        value="{{ $displayStok }}"
                                                        class="w-full p-2 border border-green-200 rounded focus:ring-2 focus:ring-green-400 focus:outline-none"
                                                    >
                                                    <span class="px-3 py-2 text-sm font-semibold text-green-800 bg-green-100 rounded">
                                                        {{ $displaySatuan }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-green-700">Satuan</label>
                                                <input type="text" name="satuan1" value="{{ $product->units->name }}" class="w-full p-2 border border-green-200 rounded">
                                            </div>

                                            <!-- Tombol Submit -->
                                            <div class="flex gap-2 pt-4 text-right">
                                                <button type="submit" class="px-4 py-2 text-white transition rounded shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                                                    Simpan Perubahan
                                                </button>
                                        </form>
                                                <form action="{{ route('products.destroy', $product->id) }}" onsubmit="return confirm('yakin ingin hapus?')" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 text-white transition rounded shadow bg-gradient-to-r from-red-500 to-red-700 hover:scale-105">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination (if needed) -->
                <div class="flex justify-center mt-6">
                    {{-- {{ $products->links() }} --}}
                </div>
                @else
                <!-- Empty State -->
                <div class="py-12 text-center">
                    <div class="w-24 h-24 mx-auto mb-4 text-green-200">
                        <i class="text-6xl fas fa-box"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-medium text-green-900">Produk tidak ada</h3>
                    <p class="mb-4 text-green-500">Mulai dengan menambahkan produk.</p>
                    <a href="{{ route('products.create') }}"
                        class="inline-flex items-center px-4 py-2 text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                        <i class="mr-2 fas fa-plus"></i>Tambah Produk
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
