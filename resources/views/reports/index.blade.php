<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            <i class="mr-2 fas fa-file-alt"></i>Laporan Ringkas
        </h2>
    </x-slot>

    <div class="px-4 py-6 mx-auto max-w-7xl">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-4">
            <div class="p-4 bg-white border rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Produk</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalProducts) }}</p>
            </div>
            <div class="p-4 bg-white border rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Supplier</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalSuppliers) }}</p>
            </div>
            <div class="p-4 bg-white border rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Kategori</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalCategories) }}</p>
            </div>
            <div class="p-4 bg-white border rounded-lg shadow">
                <p class="text-sm text-gray-500">Total Stok Barang</p>
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalStockItems) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Inventory Value -->
            <div class="p-4 bg-white border rounded-lg shadow">
                <p class="text-sm text-gray-500">Perkiraan Nilai Persediaan</p>
                <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($inventoryValue, 0, ',', '.') }}</p>
            </div>

            <!-- Recent Products -->
            <div class="p-4 bg-white border rounded-lg shadow lg:col-span-2">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-gray-900">Produk Terbaru</h3>
                    <a href="{{ route('products.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 bg-gray-50">
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2">SKU</th>
                                <th class="px-3 py-2">Kategori</th>
                                <th class="px-3 py-2">Stok</th>
                                <th class="px-3 py-2">Satuan</th>
                                <th class="px-3 py-2">Harga</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($recentProducts as $p)
                                <tr>
                                    <td class="px-3 py-2 text-gray-900">{{ $p->name }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $p->sku ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $p->category?->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $p->stock_quantity ?? 0 }}</td>
                                    <td class="px-3 py-2 text-gray-700">{{ $p->satuan ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-900">Rp {{ number_format($p->price ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada data produk</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Suppliers -->
        <div class="p-4 mt-6 bg-white border rounded-lg shadow">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-gray-900">Supplier Terbaru</h3>
                <a href="{{ route('suppliers.index') }}" class="text-sm text-blue-600 hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 bg-gray-50">
                            <th class="px-3 py-2">Kode</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Contact</th>
                            <th class="px-3 py-2">Telepon</th>
                            <th class="px-3 py-2">Email</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($recentSuppliers as $s)
                            <tr>
                                <td class="px-3 py-2 text-gray-900">{{ $s->supplier_code }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $s->name }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $s->contact_person ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $s->phone ?? '-' }}</td>
                                <td class="px-3 py-2 text-gray-700">{{ $s->email ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 py-4 text-center text-gray-500">Belum ada data supplier</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>


