<x-app-layout>
    <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
    <i class="fas fa-truck mr-2"></i> Data Supplier
</h2>

    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <!-- Header atas -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Data Supplier</h1>
            <a href="{{ route('suppliers.create') }}" 
               class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow transition">
                <i class="fas fa-plus mr-2"></i> Tambah Supplier
            </a>
        </div>

        <!-- Form Cari -->
        <form action="{{ route('suppliers.index') }}" method="GET" class="mb-6 flex items-center space-x-2">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Cari supplier (nama / kode)..."
                   class="px-4 py-2 border rounded-lg w-1/3 focus:ring-2 focus:ring-blue-400">
            <button type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                <i class="fas fa-search mr-1"></i> Cari
            </button>

            @if(request('search'))
                <a href="{{ route('suppliers.index') }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-undo mr-1"></i> Kembali
                </a>
            @endif
        </form>

        <!-- Pesan sukses -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabel Supplier -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact Person</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telepon</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($suppliers as $supplier)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $supplier->supplier_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $supplier->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $supplier->contact_person ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $supplier->phone ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $supplier->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('suppliers.show', $supplier) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200">
                                            <i class="fas fa-trash mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data supplier</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
