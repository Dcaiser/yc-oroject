<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-calendar-week mr-2"></i>{{ __('Laporan Mingguan') }}
        </h2>
    </x-slot>

    <div class="space-y-6 reports-main">
        <!-- Header Section -->
        <div class="reports-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                {{ __('Laporan Mingguan') }}
            </h2>
            <div class="btn-group no-print">
                <a href="{{ route('reports.weekly.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                   class="btn btn-secondary bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="{{ route('reports.weekly.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                   class="btn btn-primary bg-green-700 hover:bg-green-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
            </div>
        </div>

        <div class="space-y-6 reports-main">
            <!-- Filter Section -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg filter-section">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-calendar-week mr-2 text-green-600"></i>Filter Mingguan
                    </h3>
                    
                    <form method="GET" action="{{ route('reports.weekly') }}" class="filter-form">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="form-group">
                                <label for="week" class="block text-sm font-medium text-gray-700 mb-1">
                                    Pilih Minggu
                                </label>
                                <input type="week" 
                                       name="week" 
                                       id="week"
                                       value="{{ request('week', now()->format('Y-\WW')) }}"
                                       class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                            </div>

                            <div class="form-group">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Kategori
                                </label>
                                <select name="category_id" 
                                        id="category_id"
                                        class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Supplier
                                </label>
                                <select name="supplier_id" 
                                        id="supplier_id"
                                        class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm">
                                    <option value="">Semua Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->nama_supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end mt-4 pt-4 border-t border-gray-200">
                            <button type="submit" class="btn btn-primary bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-search mr-2"></i>Filter Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="summary-grid">
                <div class="summary-card bg-gradient-to-r from-green-600 to-green-700">
                    <div class="summary-icon">
                        <i class="fas fa-list-alt text-white text-2xl"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Total Aktivitas</div>
                        <div class="summary-value">{{ number_format($totalActivities) }}</div>
                    </div>
                </div>

                <div class="summary-card bg-gradient-to-r from-green-500 to-green-600">
                    <div class="summary-icon">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Produk Ditambah</div>
                        <div class="summary-value">{{ number_format($productsAdded) }}</div>
                    </div>
                </div>

                <div class="summary-card bg-gradient-to-r from-green-700 to-green-800">
                    <div class="summary-icon">
                        <i class="fas fa-edit text-white text-2xl"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Update Stok</div>
                        <div class="summary-value">{{ number_format($stockUpdates) }}</div>
                    </div>
                </div>

                <div class="summary-card bg-gradient-to-r from-green-800 to-green-900">
                    <div class="summary-icon">
                        <i class="fas fa-calendar-day text-white text-2xl"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-title">Hari Aktif</div>
                        <div class="summary-value">{{ $dailyActivities->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Weekly Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Daily Activities Chart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-line mr-2 text-indigo-600"></i>Aktivitas Harian
                        </h3>
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                            <canvas id="dailyActivitiesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Distribution Chart -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-purple-600"></i>Distribusi Kategori
                        </h3>
                        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Breakdown Table -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-table mr-2 text-green-600"></i>Rincian Harian
                    </h3>
                    
                    <div class="table-responsive">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Barang Masuk
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Barang Keluar
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Net Flow
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($dailyData as $daily)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($daily->date)->format('l, d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            <span class="text-green-600 font-medium">
                                                {{ number_format($daily->stock_in) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            <span class="text-red-600 font-medium">
                                                {{ number_format($daily->stock_out) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                            @php
                                                $netFlow = $daily->stock_in - $daily->stock_out;
                                            @endphp
                                            <span class="font-medium {{ $netFlow > 0 ? 'text-green-600' : ($netFlow < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $netFlow > 0 ? '+' : '' }}{{ number_format($netFlow) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            @if($netFlow > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-arrow-up mr-1"></i>Surplus
                                                </span>
                                            @elseif($netFlow < 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-arrow-down mr-1"></i>Defisit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-minus mr-1"></i>Seimbang
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                                                <p class="text-gray-600 font-medium">Tidak ada data untuk minggu ini</p>
                                                <p class="text-gray-500">Pilih minggu yang berbeda atau coba filter lain</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Activities Chart
    const dailyCtx = document.getElementById('dailyActivitiesChart').getContext('2d');
    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: @json($dailyData->pluck('date')->map(function($date) { return \Carbon\Carbon::parse($date)->format('D, M j'); })),
            datasets: [{
                label: 'Barang Masuk',
                data: @json($dailyData->pluck('stock_in')),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4
            }, {
                label: 'Barang Keluar',
                data: @json($dailyData->pluck('stock_out')),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Category Distribution Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: @json($categoryData->pluck('name')),
            datasets: [{
                data: @json($categoryData->pluck('value')),
                backgroundColor: [
                    'rgb(59, 130, 246)',
                    'rgb(34, 197, 94)', 
                    'rgb(239, 68, 68)',
                    'rgb(245, 158, 11)',
                    'rgb(139, 92, 246)',
                    'rgb(236, 72, 153)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush
</x-app-layout>
