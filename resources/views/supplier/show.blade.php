<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-eye mr-2"></i>Detail Supplier
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Detail Supplier</h1>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('suppliers.edit', $supplier) }}" 
                       class="px-4 py-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Edit Supplier
                    </a>
                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus supplier ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Hapus Supplier
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">{{ $supplier->name }}</h2>
                    <p class="text-gray-600">Kode: {{ $supplier->supplier_code }}</p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Kontak</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                                    <dd class="text-sm text-gray-900">{{ $supplier->contact_person ?? 'Tidak ada data' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                                    <dd class="text-sm text-gray-900">{{ $supplier->phone ?? 'Tidak ada data' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="text-sm text-gray-900">{{ $supplier->email ?? 'Tidak ada data' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Lainnya</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">NPWP</dt>
                                    <dd class="text-sm text-gray-900">{{ $supplier->npwp ?? 'Tidak ada data' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Dibuat</dt>
                                    <dd class="text-sm text-gray-900">{{ $supplier->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Terakhir Diupdate</dt>
                                    <dd class="text-sm text-gray-900">{{ $supplier->updated_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Alamat</h3>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-gray-900">{{ $supplier->address ?? 'Tidak ada data alamat' }}</p>
                        </div>
                    </div>

                    @if($supplier->purchaseOrders->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Purchase Orders</h3>
                        <div class="bg-gray-50 rounded-md overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. PO</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($supplier->purchaseOrders->take(5) as $po)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $po->po_number ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $po->created_at->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $po->status ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $po->total_amount ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if($supplier->purchaseOrders->count() > 5)
                            <div class="px-4 py-3 bg-gray-50 text-center text-sm text-gray-500">
                                Menampilkan 5 dari {{ $supplier->purchaseOrders->count() }} purchase orders
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
