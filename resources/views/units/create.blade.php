<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-weight"></i>{{ __('Tambah Satuan') }}
            </h2>
            <a href="{{ route('category') }}" class="px-4 py-2 text-white bg-gray-600 rounded">Kembali</a>
        </div>
    </x-slot>

    <div class="max-w-4xl px-4 py-8 mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <form action="{{ route('units.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Nama Satuan</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full p-2 border rounded" required>
                    @error('name') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block mb-1 text-sm font-medium">Konversi ke dasar (contoh: 1 untuk dasar, 0.001 untuk gram jika dasar kg)</label>
                    <input type="number" step="any" name="conversion_to_base" value="{{ old('conversion_to_base') }}" class="w-full p-2 border rounded" required>
                    @error('conversion_to_base') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded">Simpan</button>
                    <a href="{{ route('category') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
