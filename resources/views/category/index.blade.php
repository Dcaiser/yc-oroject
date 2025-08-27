<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-tags mr-2"></i>{{ __('Kategori') }}
            </h2>
            <a href="{{route('addcategory')}}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Kategori
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold">Daftar Kategori</h1>
        </div>

        <div x-data="{ open: false, id: '', name: '', description: '' }">

            <!-- Tabel -->
            <table class="w-full text-gray-700 border">
                <thead>
                    <tr>
                        <th class="border px-4 py-2">ID</th>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Deskripsi</th>
                        <th class="border px-4 py-2" colspan="2">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category as $cate)
                    <tr class="border-b bg-white hover:bg-gray-50 text-center">
                        <td class="px-6 py-4">{{ $cate->id }}</td>
                        <td class="px-6 py-4">{{ $cate->name }}</td>
                        <td class="px-6 py-4">{{ $cate->description }}</td>
                        <td class=" py-4">
                            <button
                                @click="open = true; id = '{{ $cate->id }}'; name = '{{ $cate->name }}'; description = '{{ $cate->description }}'"
                                class="bg-blue-500 text-white px-3 py-1 rounded">
                                Edit
                            </button>
                        </td>
                        <td class="">
                        <form action="{{route('deletecategory', $cate->id)}}" method="POST" class="">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-800 text-white px-4 py-2 rounded shadow">
                                    hapus
                                </button>
                            </form> 

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Modal -->
            <div
                x-show="open"
                x-transition
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                style="display: none;">

                <div class="bg-white w-[90%] md:w-[72%] rounded-lg shadow-lg p-6 relative" @click.away="open = false">
                    <!-- Tombol Close -->
                    <button @click="open = false" class="absolute top-3 right-3 text-gray-600 hover:text-black text-xl">&times;</button>

                    <h2 class="text-2xl font-semibold mb-4">Edit Data Kategori</h2>

                    <form method="POST" :action="'/categories/' + id">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Nama</label>
                            <input type="text" name="name" x-model="name" class="border border-gray-300 rounded w-full p-2">
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium mb-1">Deskripsi</label>
                            <textarea name="description" x-model="description" rows="3" class="border border-gray-300 rounded w-full p-2"></textarea>
                        </div>

                        <div class="flex  gap-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    </div>
</x-app-layout>