<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-layer-group"></i>
                </span>
                <h2 class="text-xl font-semibold leading-tight text-slate-700">Kategori & Satuan</h2>
            </div>
            <p class="text-sm text-slate-500">Kelola kategori produk dan satuan ukuran untuk inventori.</p>
        </div>
    </x-slot>

    <div class="space-y-6"
        x-data="categoryPage()"
        @keydown.escape.window="showCategoryModal = false; showUnitModal = false; showDeleteModal = false">

        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['title' => 'Kategori & Satuan']]" />

        <!-- Header Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-2">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                        <i class="fas fa-layer-group"></i>
                        Data Master
                    </span>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Kategori & Satuan</h1>
                    <p class="text-xs text-slate-500">
                        Kelola kategori produk dan satuan ukuran untuk inventori Anda.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="button" @click="openCategoryModal()"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Kategori
                    </button>
                    <button type="button" @click="openUnitModal()"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-400/30 transition hover:bg-indigo-600">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Satuan
                    </button>
                    <a href="{{ route('category') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800">
                        <i class="fas fa-arrow-rotate-left me-2"></i>
                        Reset
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $statCards = [
                [
                    'label' => 'Total Kategori',
                    'value' => $stats['total_categories'],
                    'sub' => $stats['categories_with_products'] . ' memiliki produk',
                    'icon' => 'fa-tags',
                    'accent' => 'bg-emerald-500/10 text-emerald-600',
                ],
                [
                    'label' => 'Kategori Kosong',
                    'value' => $stats['empty_categories'],
                    'sub' => 'Belum ada produk',
                    'icon' => 'fa-folder-open',
                    'accent' => $stats['empty_categories'] > 0 ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-500/10 text-slate-500',
                ],
                [
                    'label' => 'Total Satuan',
                    'value' => $stats['total_units'],
                    'sub' => $stats['units_in_use'] . ' sedang digunakan',
                    'icon' => 'fa-ruler',
                    'accent' => 'bg-indigo-500/10 text-indigo-600',
                ],
                [
                    'label' => 'Kategori Populer',
                    'value' => $stats['most_used_category']?->name ?? '-',
                    'sub' => ($stats['most_used_category']?->products_count ?? 0) . ' produk',
                    'icon' => 'fa-crown',
                    'accent' => 'bg-purple-500/10 text-purple-600',
                ],
            ];
        @endphp

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach($statCards as $card)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/50">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-xl font-semibold text-slate-900">{{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl {{ $card['accent'] }}">
                            <i class="fas {{ $card['icon'] }} text-base"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold">Berhasil</p>
                        <p class="text-sm text-emerald-700/80">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold">Error</p>
                        <p class="text-sm text-rose-700/80">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Content -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            {{-- Toolbar --}}
            <div class="flex flex-col gap-4 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                {{-- Tabs --}}
                <div class="flex rounded-xl bg-slate-100 p-1">
                    <a href="{{ route('category', ['tab' => 'category', 'search' => $search]) }}"
                        class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $tab === 'category' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fas fa-tags"></i>
                        <span>Kategori</span>
                        <span class="rounded-full {{ $tab === 'category' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} px-2 py-0.5 text-xs font-semibold">
                            {{ $categories->total() }}
                        </span>
                    </a>
                    <a href="{{ route('category', ['tab' => 'unit', 'search' => $search]) }}"
                        class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $tab === 'unit' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <i class="fas fa-ruler"></i>
                        <span>Satuan</span>
                        <span class="rounded-full {{ $tab === 'unit' ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-200 text-slate-600' }} px-2 py-0.5 text-xs font-semibold">
                            {{ $units->total() }}
                        </span>
                    </a>
                </div>

                {{-- Search & Actions --}}
                <div class="flex flex-1 items-center gap-3 sm:justify-end">
                    {{-- Search --}}
                    <form action="{{ route('category') }}" method="GET" class="relative flex-1 sm:max-w-xs">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" name="search" value="{{ $search }}" placeholder="Cari {{ $tab === 'category' ? 'kategori' : 'satuan' }}..."
                            class="w-full rounded-xl border-slate-200 py-2.5 pl-10 pr-4 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500">
                        @if($search)
                            <a href="{{ route('category', ['tab' => $tab]) }}" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>

                    {{-- Bulk Delete (shown when items selected) --}}
                    <template x-if="selectedItems.length > 0">
                        <button type="button" @click="confirmBulkDelete()"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-rose-700">
                            <i class="fas fa-trash"></i>
                            <span x-text="'Hapus (' + selectedItems.length + ')'"></span>
                        </button>
                    </template>

                    {{-- Refresh --}}
                    <a href="{{ route('category', ['tab' => $tab]) }}" 
                        class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700">
                        <i class="fas fa-rotate-right"></i>
                    </a>
                </div>
            </div>

            {{-- Content Area --}}
            <div class="p-4">
                @if($tab === 'category')
                    {{-- Categories Tab --}}
                    @if($categories->isEmpty())
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                <i class="fas fa-tags text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Belum ada kategori</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                @if($search)
                                    Tidak ditemukan kategori dengan kata kunci "{{ $search }}"
                                @else
                                    Mulai dengan menambahkan kategori produk pertama
                                @endif
                            </p>
                            @if($search)
                                <a href="{{ route('category', ['tab' => 'category']) }}" class="mt-4 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                                    <i class="fas fa-rotate-left mr-1"></i>Reset pencarian
                                </a>
                            @else
                                <button type="button" @click="openCategoryModal()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                                    <i class="fas fa-plus"></i>Tambah Kategori
                                </button>
                            @endif
                        </div>
                    @else
                        {{-- Select All --}}
                        <div class="mb-4 flex items-center gap-3">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" x-model="selectAllCategories" @change="toggleSelectAll('category')"
                                    class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                <span>Pilih Semua</span>
                            </label>
                            <span class="text-xs text-slate-400">{{ $categories->total() }} kategori</span>
                        </div>

                        {{-- Categories Grid --}}
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach($categories as $category)
                                <div class="group relative rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/50 transition hover:border-emerald-300 hover:shadow-md"
                                    :class="{ 'ring-2 ring-emerald-500 border-emerald-500': selectedItems.includes({{ $category->id }}) }">
                                    {{-- Checkbox --}}
                                    <div class="absolute left-3 top-3">
                                        <input type="checkbox" :value="{{ $category->id }}" x-model="selectedItems"
                                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                                    </div>

                                    {{-- Content --}}
                                    <div class="ml-6">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-semibold text-slate-900 truncate">{{ $category->name }}</h4>
                                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2">
                                                    {{ $category->description ?: 'Tidak ada deskripsi' }}
                                                </p>
                                            </div>
                                            <span class="shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600">
                                                <i class="fas fa-tags text-sm"></i>
                                            </span>
                                        </div>

                                        {{-- Stats --}}
                                        <div class="mt-3 flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-full {{ $category->products_count > 0 ? 'bg-emerald-500/10 text-emerald-700' : 'bg-slate-100 text-slate-500' }} px-2.5 py-1 text-xs font-medium">
                                                <i class="fas fa-box text-[10px]"></i>
                                                {{ $category->products_count }} produk
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="mt-3 flex items-center gap-2 border-t border-slate-100 pt-3">
                                            <button type="button" @click="editCategory({{ json_encode($category) }})"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                                                <i class="fas fa-pen-to-square"></i>Edit
                                            </button>
                                            <button type="button" @click="confirmDeleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}', {{ $category->products_count }})"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border px-3 py-2 text-xs font-semibold transition {{ $category->products_count > 0 ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300' }}"
                                                {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash-can"></i>Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($categories->hasPages())
                            <div class="mt-6 border-t border-slate-100 pt-4">
                                {{ $categories->links() }}
                            </div>
                        @endif
                    @endif
                @else
                    {{-- Units Tab --}}
                    @if($units->isEmpty())
                        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                            <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-100 text-indigo-500">
                                <i class="fas fa-ruler text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900">Belum ada satuan</h3>
                            <p class="mt-1 text-sm text-slate-500">
                                @if($search)
                                    Tidak ditemukan satuan dengan kata kunci "{{ $search }}"
                                @else
                                    Mulai dengan menambahkan satuan ukuran pertama
                                @endif
                            </p>
                            @if($search)
                                <a href="{{ route('category', ['tab' => 'unit']) }}" class="mt-4 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                                    <i class="fas fa-rotate-left mr-1"></i>Reset pencarian
                                </a>
                            @else
                                <button type="button" @click="openUnitModal()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-400/30 transition hover:bg-indigo-600">
                                    <i class="fas fa-plus"></i>Tambah Satuan
                                </button>
                            @endif
                        </div>
                    @else
                        {{-- Select All --}}
                        <div class="mb-4 flex items-center gap-3">
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" x-model="selectAllUnits" @change="toggleSelectAll('unit')"
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span>Pilih Semua</span>
                            </label>
                            <span class="text-xs text-slate-400">{{ $units->total() }} satuan</span>
                        </div>

                        {{-- Units Grid --}}
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            @foreach($units as $unit)
                                <div class="group relative rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/50 transition hover:border-indigo-300 hover:shadow-md"
                                    :class="{ 'ring-2 ring-indigo-500 border-indigo-500': selectedItems.includes({{ $unit->id }}) }">
                                    {{-- Checkbox --}}
                                    <div class="absolute left-3 top-3">
                                        <input type="checkbox" :value="{{ $unit->id }}" x-model="selectedItems"
                                            class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    </div>

                                    {{-- Content --}}
                                    <div class="ml-6">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-semibold text-slate-900 truncate">{{ $unit->name }}</h4>
                                                <p class="mt-0.5 text-xs text-slate-500">
                                                    Konversi: {{ $unit->conversion_to_base }}x base
                                                </p>
                                            </div>
                                            <span class="shrink-0 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600">
                                                <i class="fas fa-ruler text-sm"></i>
                                            </span>
                                        </div>

                                        {{-- Stats --}}
                                        <div class="mt-3 flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 rounded-full {{ $unit->produk_count > 0 ? 'bg-indigo-500/10 text-indigo-700' : 'bg-slate-100 text-slate-500' }} px-2.5 py-1 text-xs font-medium">
                                                <i class="fas fa-box text-[10px]"></i>
                                                {{ $unit->produk_count }} produk
                                            </span>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="mt-3 flex items-center gap-2 border-t border-slate-100 pt-3">
                                            <button type="button" @click="editUnit({{ json_encode($unit) }})"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                                                <i class="fas fa-pen-to-square"></i>Edit
                                            </button>
                                            <button type="button" @click="confirmDeleteUnit({{ $unit->id }}, '{{ addslashes($unit->name) }}', {{ $unit->produk_count }})"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border px-3 py-2 text-xs font-semibold transition {{ $unit->produk_count > 0 ? 'border-slate-200 text-slate-400 cursor-not-allowed' : 'border-rose-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300' }}"
                                                {{ $unit->produk_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash-can"></i>Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($units->hasPages())
                            <div class="mt-6 border-t border-slate-100 pt-4">
                                {{ $units->links() }}
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </section>

        {{-- Category Modal --}}
        <div x-show="showCategoryModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="showCategoryModal = false">
            
            <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showCategoryModal = false">
                
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600">
                            <i class="fas fa-tags"></i>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900" x-text="categoryModalMode === 'create' ? 'Tambah Kategori' : 'Edit Kategori'"></h3>
                    </div>
                    <button type="button" @click="showCategoryModal = false" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form :action="categoryFormAction" method="POST" class="p-6">
                    @csrf
                    <template x-if="categoryModalMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label for="category_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Kategori <span class="text-rose-500">*</span></label>
                            <input type="text" id="category_name" name="name" x-model="categoryForm.name" required maxlength="255"
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500"
                                placeholder="Contoh: Elektronik, Makanan, dll">
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="category_description" class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                            <textarea id="category_description" name="description" x-model="categoryForm.description" rows="3" maxlength="500"
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500 resize-none"
                                placeholder="Deskripsi singkat kategori (opsional)"></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="showCategoryModal = false"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600 disabled:opacity-50">
                            <template x-if="!isSubmitting">
                                <i class="fas fa-check"></i>
                            </template>
                            <template x-if="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Unit Modal --}}
        <div x-show="showUnitModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="showUnitModal = false">
            
            <div class="relative w-full max-w-md rounded-3xl bg-white shadow-2xl"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                @click.away="showUnitModal = false">
                
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600">
                            <i class="fas fa-ruler"></i>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900" x-text="unitModalMode === 'create' ? 'Tambah Satuan' : 'Edit Satuan'"></h3>
                    </div>
                    <button type="button" @click="showUnitModal = false" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                {{-- Form --}}
                <form :action="unitFormAction" method="POST" class="p-6">
                    @csrf
                    <template x-if="unitModalMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label for="unit_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Satuan <span class="text-rose-500">*</span></label>
                            <input type="text" id="unit_name" name="name" x-model="unitForm.name" required maxlength="255"
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Contoh: Kilogram, Liter, Pcs, dll">
                            @error('name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unit_conversion" class="block text-sm font-semibold text-slate-700 mb-1.5">Nilai Konversi ke Base <span class="text-rose-500">*</span></label>
                            <input type="number" id="unit_conversion" name="conversion_to_base" x-model="unitForm.conversion_to_base" required min="0.0001" step="0.0001"
                                class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="1">
                            <p class="mt-1 text-xs text-slate-500">
                                <i class="fas fa-circle-info mr-1"></i>Contoh: 1 Kilogram = 1000 gram, maka nilai = 1000
                            </p>
                            @error('conversion_to_base')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="showUnitModal = false"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-400/30 transition hover:bg-indigo-600 disabled:opacity-50">
                            <template x-if="!isSubmitting">
                                <i class="fas fa-check"></i>
                            </template>
                            <template x-if="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @keydown.escape.window="showDeleteModal = false">
            
            <div class="relative w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl text-center"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="showDeleteModal = false">
                
                <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <i class="fas fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="text-lg font-bold text-slate-900" x-text="deleteModalTitle"></h3>
                <p class="mt-2 text-sm text-slate-500" x-text="deleteModalMessage"></p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="showDeleteModal = false"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                        Batal
                    </button>
                    <form :action="deleteFormAction" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-rose-400/30 transition hover:bg-rose-600">
                            <i class="fas fa-trash-can"></i>Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Bulk Delete Form (hidden) --}}
        <form id="bulkDeleteForm" :action="bulkDeleteAction" method="POST" class="hidden">
            @csrf
            @method('DELETE')
            <template x-for="id in selectedItems" :key="id">
                <input type="hidden" name="ids[]" :value="id">
            </template>
        </form>
    </div>

    @push('scripts')
    <script>
        function categoryPage() {
            return {
                // Modal states
                showCategoryModal: false,
                showUnitModal: false,
                showDeleteModal: false,
                categoryModalMode: 'create',
                unitModalMode: 'create',
                isSubmitting: false,

                // Forms
                categoryForm: { id: null, name: '', description: '' },
                unitForm: { id: null, name: '', conversion_to_base: 1 },

                // Selection
                selectedItems: [],
                selectAllCategories: false,
                selectAllUnits: false,

                // Delete modal
                deleteModalTitle: '',
                deleteModalMessage: '',
                deleteFormAction: '',
                bulkDeleteAction: '',

                // Computed
                get categoryFormAction() {
                    return this.categoryModalMode === 'create' 
                        ? '{{ route("categories.store") }}'
                        : `/categories/${this.categoryForm.id}`;
                },
                get unitFormAction() {
                    return this.unitModalMode === 'create'
                        ? '{{ route("units.store") }}'
                        : `/units/${this.unitForm.id}`;
                },

                // Methods
                toggleSelectAll(type) {
                    if (type === 'category') {
                        if (this.selectAllCategories) {
                            this.selectedItems = @json($categories->pluck('id'));
                        } else {
                            this.selectedItems = [];
                        }
                    } else {
                        if (this.selectAllUnits) {
                            this.selectedItems = @json($units->pluck('id'));
                        } else {
                            this.selectedItems = [];
                        }
                    }
                },

                confirmBulkDelete() {
                    const count = this.selectedItems.length;
                    const type = '{{ $tab }}' === 'category' ? 'kategori' : 'satuan';
                    this.deleteModalTitle = `Hapus ${count} ${type}?`;
                    this.deleteModalMessage = `Apakah Anda yakin ingin menghapus ${count} ${type} yang dipilih? Tindakan ini tidak dapat dibatalkan.`;
                    this.bulkDeleteAction = '{{ $tab }}' === 'category' 
                        ? '{{ route("categories.bulk-destroy") }}'
                        : '{{ route("units.bulk-destroy") }}';
                    this.showDeleteModal = true;
                }
            }
        }

        // Global functions for onclick handlers
        function openCategoryModal() {
            const component = Alpine.$data(document.querySelector('[x-data]'));
            component.categoryModalMode = 'create';
            component.categoryForm = { id: null, name: '', description: '' };
            component.showCategoryModal = true;
        }

        function editCategory(category) {
            const component = Alpine.$data(document.querySelector('[x-data]'));
            component.categoryModalMode = 'edit';
            component.categoryForm = {
                id: category.id,
                name: category.name,
                description: category.description || ''
            };
            component.showCategoryModal = true;
        }

        function confirmDeleteCategory(id, name, productCount) {
            if (productCount > 0) {
                alert(`Tidak dapat menghapus kategori "${name}" karena masih memiliki ${productCount} produk.`);
                return;
            }
            const component = Alpine.$data(document.querySelector('[x-data]'));
            component.deleteModalTitle = 'Hapus Kategori?';
            component.deleteModalMessage = `Apakah Anda yakin ingin menghapus kategori "${name}"? Tindakan ini tidak dapat dibatalkan.`;
            component.deleteFormAction = `/categories/${id}`;
            component.showDeleteModal = true;
        }

        function openUnitModal() {
            const component = Alpine.$data(document.querySelector('[x-data]'));
            component.unitModalMode = 'create';
            component.unitForm = { id: null, name: '', conversion_to_base: 1 };
            component.showUnitModal = true;
        }

        function editUnit(unit) {
            const component = Alpine.$data(document.querySelector('[x-data]'));
            component.unitModalMode = 'edit';
            component.unitForm = {
                id: unit.id,
                name: unit.name,
                conversion_to_base: unit.conversion_to_base
            };
            component.showUnitModal = true;
        }

        function confirmDeleteUnit(id, name, productCount) {
            if (productCount > 0) {
                alert(`Tidak dapat menghapus satuan "${name}" karena masih digunakan ${productCount} produk.`);
                return;
            }
            const component = Alpine.$data(document.querySelector('[x-data]'));
            component.deleteModalTitle = 'Hapus Satuan?';
            component.deleteModalMessage = `Apakah Anda yakin ingin menghapus satuan "${name}"? Tindakan ini tidak dapat dibatalkan.`;
            component.deleteFormAction = `/units/${id}`;
            component.showDeleteModal = true;
        }
    </script>
    @endpush
</x-app-layout>
