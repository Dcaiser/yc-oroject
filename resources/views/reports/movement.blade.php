<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-arrows-alt mr-2"></i>{{ __('Laporan Pergerakan Stok') }}
        </h2>
    </x-slot>

    <div class="space-y-6 reports-main">
        <!-- Header Section -->
        <div class="reports-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Laporan Pergerakan Stok') }}
            </h2>
            <div class="btn-group no-print">
                <a href="{{ route('reports.movement.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                   class="btn btn-secondary bg-green-700 hover:bg-green-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="{{ route('reports.movement.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                   class="btn btn-primary bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
            </div>
        </div>

        <div class="space-y-6 reports-main">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg filter-section">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-filter mr-2 text-green-700"></i>Filter Laporan
                    </h3>
                    
                    <form method="GET" action="{{ route('reports.movement') }}" class="filter-form">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Mulai
                                </label>
                                <input type="date" 
                                       name="date_from" 
                                       id="date_from"
                                       value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                                       class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 text-sm">
                            </div>

                            <div class="form-group">
                                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">
                                    Tanggal Selesai
                                </label>
                                <input type="date" 
                                       name="date_to" 
                                       id="date_to"
                                       value="{{ request('date_to', now()->format('Y-m-d')) }}"
                                       class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 text-sm">
                            </div>

                            <div class="form-group">
                                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Produk
                                </label>
                                <select name="product_id" 
                                        id="product_id"
                                        class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 text-sm">
                                    <option value="">Semua Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="btn btn-primary bg-green-700 hover:bg-green-800 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>Filter Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-600">
                    <div class="p-4 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-600">Total Aktivitas</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($totalActivities) }}</p>
                            </div>
                            <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-list text-green-700 text-sm md:text-base"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-700">
                    <div class="p-4 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-600">Aktivitas Produk</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($productActivities) }}</p>
                            </div>
                            <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-box text-green-600 text-sm md:text-base"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                    <div class="p-4 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-600">Aktivitas User</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($userActivities) }}</p>
                            </div>
                            <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-users text-green-500 text-sm md:text-base"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-600">
                    <div class="p-4 md:p-6">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-600">Total Produk</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($products->count()) }}</p>
                            </div>
                            <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                                <i class="fas fa-cubes text-green-600 text-sm md:text-base"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Report Data -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tipe
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Record ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Aktivitas
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($activities as $activity)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $activity->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <div class="font-medium">{{ $activity->user }}</div>
                                            <div class="text-gray-500">{{ $activity->model ?? 'System' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if(str_contains($activity->action, 'tambah'))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-plus-circle mr-1"></i>Tambah
                                                </span>
                                            @elseif(str_contains($activity->action, 'update'))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-edit mr-1"></i>Update
                                                </span>
                                            @elseif(str_contains($activity->action, 'hapus'))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-minus-circle mr-1"></i>Hapus
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-info-circle mr-1"></i>Info
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                            ID: {{ $activity->record_id ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $activity->action }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                                <p class="text-gray-600 font-medium">Tidak ada data pergerakan stok</p>
                                                <p class="text-gray-500">Coba ubah filter atau rentang tanggal</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($activities->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            {{ $activities->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
