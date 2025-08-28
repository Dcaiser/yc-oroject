<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-box"></i>{{ __('Daftar Produk') }}
            </h2>
            <a href="{{ route('products.create') }}"
                class="flex items-center px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="mr-2 fas fa-plus"></i>Tambah Produk
            </a>
        </div>
    </x-slot>
    @if(isset($products) && count($products) > 0)

    <div class="space-y-6">
        <!-- Search and Filter Section -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
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
                                    class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="text-gray-400 fas fa-search"></i>
                                </div>
                            </div>
                            <button type="submit"
                                class="px-4 py-2 text-white transition-colors bg-blue-600 rounded-r-lg hover:bg-blue-700">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Filter Options -->
                    <div class="flex items-center gap-4 space-x-2">
                        <form action="{{ route('deleteall') }}" method="post" class=""  onsubmit="return confirm('Yakin hapus semua data?')">
                            @csrf
                            @method('DELETE')
                            <button class="p-3 text-white bg-red-600 rounded active:bg-red-800" type="submit">
                                <i class="fa-solid fa-trash"></i> hapus semua
                            </button>
                        </form>
                        <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Semua Kategori</option>
                            <select name="" id=""></select>
                        </select>
                        <select name="sort" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="nama">Nama A-Z</option>
                            <option value="harga_asc">Harga Terendah</option>
                            <option value="harga_desc">Harga Tertinggi</option>
                            <option value="terbaru">Terbaru</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                    <div class="overflow-hidden transition-shadow duration-300 bg-white border border-gray-200 rounded-lg hover:shadow-lg">
                        <!-- Product Image -->
                        <div class="bg-gray-200 aspect-w-16 aspect-h-12">
                            <img src="{{ $product['gambar'] ?? 'https://via.placeholder.com/300x200?text=No+Image' }}"
                                alt="{{ $product['nama'] }}"
                                class="object-cover w-full h-48">
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="mb-2 text-lg font-semibold line-clamp-2">{{ $product['name'] }}</h3>
                            <p class="mb-3 text-sm text-gray-600 line-clamp-2">{{$product['sku']}}</p>
                            <p class="mb-3 text-sm text-gray-600 line-clamp-2">{{ $product->category ? $product->category->name : '-' }}</p>

                            <!-- Price and Stock -->
                            <div class="flex items-center justify-between mb-3">
                                <span class="flex text-sm text-gray-500">
                                    <p class="">
                                        Stok: {{ $product['stock_quantity'] ?? 0  }}
                                    </p>&nbsp; {{ $product['satuan'] }}
                                </span>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-2" x-data="{ op: false }">
                                <!-- Tombol buka modal -->
                                <button
                                    @click="op = true"
                                    type="button"
                                    class="flex-1 px-3 py-2 text-sm font-medium text-center text-gray-800 transition-colors bg-gray-100 rounded hover:bg-gray-200">
                                    <i class="mr-1 fas fa-edit"></i>detail
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
                                        <h2 class="mb-4 text-2xl font-semibold">Edit Data Produk</h2>

                                        <!-- Form -->
                                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="grid-cols-2 space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <!-- Gambar Produk -->
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
                                                        name="gambar-edit"
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

                                            <!-- Judul -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-700">Judul</label>
                                                <input type="text" name="title1" value="{{ $product['name']  }}" class="w-full p-2 border border-gray-300 rounded">
                                            </div>

                                            <!-- Deskripsi -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-700">Deskripsi</label>
                                                <textarea name="description1" rows="4" class="w-full p-2 border border-gray-300 rounded">{{ $product['description'] }}</textarea>
                                            </div>

                                            <!-- Harga -->
<h3 class="mt-6 mb-2 text-lg font-semibold">Harga per Kategori Customer</h3>
        <div class="grid grid-cols-1 gap-4">
            @foreach ($customertypes as $type)
                @php
                    $existing = optional($product->prices->firstWhere('customer_type', $type))->price;
                    $formatted = $existing !== null ? number_format($existing, 0, ',', '.') : '';
                @endphp
                <div>
                    <label class="block mb-1 font-medium">Harga {{ ucfirst($type) }}</label>
                     <div x-data="{ harga: '{{ $formatted }}', numericVal: '{{ $existing ?? '' }}' }">
                            <div class="relative">
                                <span class="absolute text-gray-500 left-3 top-2">Rp</span>
                                <input type="text"
                                    x-model="harga"
                                    x-on:input="numericVal = harga.replace(/[^0-9]/g,''); harga = new Intl.NumberFormat('id-ID').format(numericVal)"
                                    class="w-full py-2 pl-12 pr-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <input type="hidden" name="prices[{{ $type }}]" :value="numericVal">
                            </div>
                </div>
                </div>
            @endforeach
        </div>
                                            <div>
                                                <label for="kategori_id" class="block mb-1 text-sm font-medium text-gray-700">Kategori</label>
                                                <select id="kategori_id" required
                                                    name="kategori_id1"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                                                <label class="block mb-1 text-sm font-medium text-gray-700">SKU</label>
                                                <input type="text" name="sku1" value="{{ $product['sku'] ?? '' }}" class="w-full p-2 border border-gray-300 rounded">
                                            </div>

                                            <!-- Stok -->
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-700">Jumlah Stok</label>
                                                <input type="number" name="stock1" value="{{ $product['stock_quantity'] ?? '' }}" class="w-full p-2 border border-gray-300 rounded">
                                            </div>
                                            <div>
                                                <label class="block mb-1 text-sm font-medium text-gray-700">satuan</label>
                                                <input type="text" name="satuan1" value="{{ $product['satuan'] }}" class="w-full p-2 border border-gray-300 rounded">
                                            </div>

                                            <!-- Tombol Submit -->
                                            <div class="flex gap-2 pt-4 text-right">
                                                <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded shadow hover:bg-blue-700">
                                                    Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                                <form action="{{ route('products.destroy', $product->id) }}" onsubmit="return confirm('yakin ingin hapus?')" class="" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-4 py-2 text-white bg-red-500 rounded shadow hover:bg-red-800">
                                                        hapus
                                                    </button>
                                                </form>

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
                    <div class="w-24 h-24 mx-auto mb-4 text-gray-300">
                        <i class="text-6xl fas fa-box"></i>
                    </div>
                    <h3 class="mb-2 text-lg font-medium text-gray-900">produk tersebut tidak ada</h3>
                    <p class="mb-4 text-gray-500">Mulai dengan menambahkan produk.</p>
                    <a href="{{ route('products.create') }}"
                        class="inline-flex items-center px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                        <i class="mr-2 fas fa-plus"></i>Tambah Produk
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
