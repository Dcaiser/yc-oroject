<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-100 to-indigo-100">
                <i class="fas fa-plus-circle text-emerald-600"></i>
            </span>
            <h2 class="text-xl font-semibold leading-tight text-slate-700">Tambah Produk</h2>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['title' => 'Manajemen Produk', 'route' => 'products.index'], ['title' => 'Tambah Produk']]"/>
        <!-- Header Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Tambah Produk Baru</h1>
                    <p class="text-sm text-slate-500">
                        Lengkapi form di bawah untuk menambahkan produk baru ke dalam inventori sistem.
                    </p>
                </div>
                <a href="{{ route('products.index') }}" 
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-800">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        <!-- Form -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 gap-0 lg:grid-cols-2 lg:divide-x lg:divide-slate-100">
                    <!-- Left Column - Product Info -->
                    <div class="p-6 space-y-5">
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-500/10 text-emerald-600">
                                <i class="fas fa-info-circle text-sm"></i>
                            </span>
                            Informasi Produk
                        </h3>

                        <!-- Product Name -->
                        <div>
                            <label for="nama" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Nama Produk <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}" required
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Masukkan nama produk">
                            @error('nama')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Deskripsi
                            </label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500 resize-none"
                                placeholder="Deskripsi produk (opsional)">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Supplier (hidden) -->
                        <input type="hidden" name="supplier_id" value="{{ $supplier->first()?->id }}">

                        <!-- Category -->
                        <div>
                            <label for="kategori_id" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <select id="kategori_id" name="kategori_id" required
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Pilih kategori</option>
                                @foreach($category as $cat)
                                    <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Stock & Unit -->
                        <div class="grid grid-cols-2 gap-4" x-data="{ 
                            units: {{ Js::from($units->map(fn($u)=>['id'=>$u->id,'name'=>$u->name,'conversion'=>$u->conversion_to_base])) }},
                            selectedUnit: '{{ old('satuan') }}'
                        }">
                            <div>
                                <label for="stok" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Stok <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" id="stok" name="stok_user" value="{{ old('stok_user', 0) }}" min="0" step="0.01" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                    placeholder="0">
                                @error('stok_user')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="satuan" class="block text-sm font-semibold text-slate-700 mb-1.5">
                                    Satuan <span class="text-rose-500">*</span>
                                </label>
                                <select id="satuan" name="satuan" x-model="selectedUnit" required
                                    class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">Pilih satuan</option>
                                    <template x-for="unit in units" :key="unit.id">
                                        <option :value="unit.id" x-text="unit.name"></option>
                                    </template>
                                </select>
                                @error('satuan')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <p class="text-xs text-slate-500">
                            <i class="fas fa-circle-info mr-1"></i>
                            Stok akan disimpan sesuai satuan yang dipilih.
                        </p>
                    </div>

                    <!-- Right Column - Image & Prices -->
                    <div class="p-6 space-y-5 border-t border-slate-100 lg:border-t-0">
                        <!-- Image Upload -->
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 pb-2 border-b border-slate-100">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-600">
                                <i class="fas fa-image text-sm"></i>
                            </span>
                            Gambar Produk
                        </h3>

                        <div x-data="imageUploader()" class="space-y-3">
                            <div class="rounded-2xl border-2 border-dashed border-slate-200 p-6 text-center transition"
                                :class="{ 'border-emerald-400 bg-emerald-50': dragging }"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="handleDrop($event)">
                                
                                <template x-if="!preview">
                                    <div>
                                        <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                            <i class="fas fa-cloud-arrow-up text-xl"></i>
                                        </div>
                                        <p class="text-sm text-slate-600">Drag & drop gambar di sini</p>
                                        <p class="my-2 text-xs text-slate-400">atau</p>
                                        <button type="button" @click="triggerFileDialog"
                                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                                            <i class="fas fa-folder-open"></i>
                                            Pilih File
                                        </button>
                                    </div>
                                </template>
                                
                                <template x-if="preview">
                                    <div class="relative inline-block">
                                        <img :src="preview" alt="Preview" class="h-40 max-w-full rounded-xl object-cover mx-auto">
                                        <button type="button" @click="removeImage"
                                            class="absolute -top-2 -right-2 inline-flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-white shadow-lg transition hover:bg-rose-600">
                                            <i class="fas fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>

                            <input type="file" x-ref="fileInput" name="gambar" accept="image/*" @change="updatePreview($event.target.files[0])" class="hidden">
                            
                            <template x-if="fileName">
                                <p class="text-xs text-slate-600" x-text="fileName"></p>
                            </template>
                            <template x-if="errorMessage">
                                <p class="text-xs text-rose-600" x-text="errorMessage"></p>
                            </template>
                            
                            <p class="text-xs text-slate-500">Format: JPG, PNG, GIF. Maksimal 2MB.</p>
                        </div>

                        @error('gambar')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror

                        <!-- Prices -->
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2 pb-2 border-b border-slate-100 pt-4">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500/10 text-amber-600">
                                <i class="fas fa-tags text-sm"></i>
                            </span>
                            Harga per Tipe Customer
                        </h3>

                        <div class="space-y-3 rounded-xl border border-slate-200 bg-slate-50/80 p-4">
                            @foreach ($customertypes as $type)
                                <div x-data="{ harga: '', hargaRaw: '' }">
                                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ ucfirst($type) }}</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                                        <input type="text" x-model="harga"
                                            @input="
                                                let clean = $event.target.value.replace(/[^0-9]/g, '');
                                                hargaRaw = clean;
                                                harga = clean ? new Intl.NumberFormat('id-ID').format(parseInt(clean)) : '';
                                            "
                                            class="w-full rounded-xl border-slate-200 bg-white pl-10 pr-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                            placeholder="0">
                                        <input type="hidden" name="prices[{{ $type }}]" :value="hargaRaw">
                                    </div>
                                    @error("prices.{$type}")
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100 bg-slate-50/50 px-6 py-4">
                    <a href="{{ route('products.index') }}"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <i class="fas fa-check"></i>
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            window.imageUploader = function (config = {}) {
                return {
                    preview: config.initialUrl ?? null,
                    dragging: false,
                    fileName: config.initialName ?? '',
                    errorMessage: '',
                    init() {
                        if (this.preview && this.fileName === '' && config.initialName) {
                            this.fileName = config.initialName;
                        }
                    },
                    onDragOver() {
                        this.dragging = true;
                    },
                    onDragLeave() {
                        this.dragging = false;
                    },
                    handleDrop(event) {
                        this.dragging = false;
                        const file = event?.dataTransfer?.files?.[0];
                        if (file) {
                            this.setFile(file);
                        }
                    },
                    triggerFileDialog() {
                        this.$refs.fileInput?.click();
                    },
                    updatePreview(file) {
                        if (!file) {
                            this.reset();
                            return;
                        }
                        this.setFile(file);
                    },
                    setFile(file) {
                        if (!file.type.startsWith('image/')) {
                            this.errorMessage = 'File harus berupa gambar (JPG, PNG, GIF).';
                            this.reset(true);
                            return;
                        }

                        this.errorMessage = '';

                        if (this.preview) {
                            URL.revokeObjectURL(this.preview);
                        }

                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        if (this.$refs.fileInput) {
                            this.$refs.fileInput.files = dataTransfer.files;
                        }

                        this.preview = URL.createObjectURL(file);
                        this.fileName = file.name;
                    },
                    removeImage() {
                        if (this.preview) {
                            URL.revokeObjectURL(this.preview);
                        }
                        this.reset();
                    },
                    reset(keepError = false) {
                        if (!keepError) {
                            this.errorMessage = '';
                        }
                        this.preview = null;
                        this.dragging = false;
                        this.fileName = '';
                        if (this.$refs.fileInput) {
                            this.$refs.fileInput.value = '';
                            const emptyTransfer = new DataTransfer();
                            this.$refs.fileInput.files = emptyTransfer.files;
                        }
                    }
                };
            };
        });
    </script>
    @endpush
</x-app-layout>
