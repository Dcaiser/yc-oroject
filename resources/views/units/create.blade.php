<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-slate-800">
                <i class="mr-2 text-emerald-600 fas fa-weight"></i>{{ __('Tambah Satuan') }}
            </h2>
            <a href="{{ route('category') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                <i class="mr-1 fas fa-arrow-left text-slate-500"></i>Kembali ke daftar
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl px-4 py-8 mx-auto">
        <div class="p-6 bg-white rounded-2xl shadow-sm ring-1 ring-slate-100">
            <form action="{{ route('units.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-semibold text-slate-700">Nama Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50" required>
                    @error('name') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-semibold text-slate-700">Konversi ke dasar <span class="text-rose-500">*</span></label>
                    <input type="number" step="any" name="conversion_to_base" value="{{ old('conversion_to_base') }}" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-slate-50" required>
                    <p class="mt-1 text-xs text-slate-500">Contoh: 1 untuk dasar, 0.001 untuk gram jika dasar kilogram.</p>
                    @error('conversion_to_base') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('category') }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">Batal</a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-1">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
