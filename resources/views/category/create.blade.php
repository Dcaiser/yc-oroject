<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('Tambah Kategori') }}
            </h2>
            <a href="{{ route('category') }}"
               class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                <i class="fas fa-arrow-left text-slate-500"></i>
                Kembali ke daftar
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto bg-white overflow-hidden shadow-sm sm:rounded-2xl ring-1 ring-slate-100">
        <div class="p-6 sm:p-8">
            <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column - Product Info -->
                    <div class="space-y-4">
                        <!-- Product Name -->
                        <div>
                            <label for="nama" class="block text-sm font-semibold text-slate-700 mb-1">Tambahkan kategori baru <span class="text-rose-500">*</span></label>
                            <input type="text" 
                                   id="nama_kategori" 
                                   name="nama-kategori" 
                                   value="{{ old('nama') }}" 
                                   required
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50"
                                   placeholder="Masukkan nama kategori">
                            @error('nama')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="deskripsi" class="block text-sm font-semibold text-slate-700 mb-1">Tambahkan deskripsi</label>
                            <textarea id="deskripsi" 
                                      name="deskripsi-kategori" 
                                      rows="4"
                                      class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50"
                                      placeholder="Masukkan deskripsi ">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                    <a href="{{ route('category') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                        <i class="fas fa-save"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>