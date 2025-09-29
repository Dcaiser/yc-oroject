<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-truck mr-2"></i>{{ __('Laporan Performa Supplier') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center">
                    <div class="bg-emerald-100 p-3 rounded-full mr-4">
                        <i class="fas fa-truck text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Laporan Performa Supplier</h1>
                        <p class="text-gray-600 mt-1">Analisis dan evaluasi kinerja supplier berdasarkan berbagai metrik</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('reports.supplier-performance.export', array_merge(request()->all(), ['format' => 'pdf'])) }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-200 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </a>
                    <a href="{{ route('reports.supplier-performance.export', array_merge(request()->all(), ['format' => 'excel'])) }}" 
                       class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-file-excel mr-2"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-emerald-100 p-2 rounded-lg mr-3">
                        <i class="fas fa-filter text-emerald-600"></i>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Filter & Analisis</h2>
                </div>
                
                <form method="GET" action="{{ route('reports.supplier-performance') }}" class="space-y-4">
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Mulai
                            </label>
                            <input type="date" 
                                   name="start_date" 
                                   id="start_date"
                                   value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Selesai
                            </label>
                            <input type="date" 
                                   name="end_date" 
                                   id="end_date"
                                   value="{{ request('end_date', now()->format('Y-m-d')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                        </div>

                        <div>
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Supplier
                            </label>
                            <select name="supplier_id" 
                                    id="supplier_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="performance_metric" class="block text-sm font-medium text-gray-700 mb-2">
                                Metrik Performa
                            </label>
                            <select name="performance_metric" 
                                    id="performance_metric"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                <option value="total_orders" {{ request('performance_metric') == 'total_orders' ? 'selected' : '' }}>
                                    Total Pesanan
                                </option>
                                <option value="total_value" {{ request('performance_metric') == 'total_value' ? 'selected' : '' }}>
                                    Nilai Total
                                </option>
                                <option value="delivery_time" {{ request('performance_metric') == 'delivery_time' ? 'selected' : '' }}>
                                    Waktu Pengiriman
                                </option>
                                <option value="quality_score" {{ request('performance_metric') == 'quality_score' ? 'selected' : '' }}>
                                    Skor Kualitas
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-info-circle text-emerald-500 mr-2"></i>
                            Gunakan filter untuk menyesuaikan analisis sesuai kebutuhan
                        </p>
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-200 text-white font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-search mr-2"></i>Analisis Performa
                        </button>
                    </div>
                    </form>
                </div>
            </div>

        <!-- Performance Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 rounded-lg mr-4">
                        <i class="fas fa-truck text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">Total Supplier</p>
                        <p class="text-3xl font-bold">{{ number_format($summary['total_suppliers']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 rounded-lg mr-4">
                        <i class="fas fa-shopping-cart text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">Total Pesanan</p>
                        <p class="text-3xl font-bold">{{ number_format($summary['total_orders']) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 rounded-lg mr-4">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">Nilai Pesanan</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($summary['total_value'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-emerald-700 to-emerald-800 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 rounded-lg mr-4">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">Rata-rata Delivery</p>
                        <p class="text-3xl font-bold">{{ number_format($summary['avg_delivery_time'], 1) }}<span class="text-lg font-normal text-emerald-200 ml-1">hari</span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
            <!-- Top Suppliers by Value -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Top Supplier by Value</h3>
                        <p class="text-gray-600 text-sm">Berdasarkan nilai total pesanan</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    @foreach($topSuppliersByValue as $index => $supplier)
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center flex-1">
                                <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-full flex items-center justify-center font-bold text-lg mr-4">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $supplier->nama_supplier }}</h4>
                                    <p class="text-sm text-gray-600">{{ $supplier->orders_count }} pesanan</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg text-emerald-600">Rp {{ number_format($supplier->total_value, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500">{{ number_format($supplier->avg_delivery_time, 1) }} hari delivery</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-6">
                    <div class="bg-emerald-100 p-3 rounded-lg mr-4">
                        <i class="fas fa-chart-bar text-emerald-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Tren Performa</h3>
                        <p class="text-gray-600 text-sm">Visualisasi data performa supplier</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 h-80 flex items-center justify-center">
                    <canvas id="performanceChart" class="max-w-full max-h-full"></canvas>
                </div>
            </div>
        </div>

        <!-- Detailed Performance Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700">
                <div class="flex items-center">
                    <i class="fas fa-table text-white text-xl mr-3"></i>
                    <h3 class="text-xl font-semibold text-white">Detail Performa Supplier</h3>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Supplier
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Total Pesanan
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Nilai Total
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Delivery Time
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Performance
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($performanceData as $supplier)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                            {{ strtoupper(substr($supplier->nama_supplier, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $supplier->nama_supplier }}</h4>
                                            <p class="text-sm text-gray-500">{{ $supplier->products_count }} produk</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ number_format($supplier->orders_count) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-lg font-bold text-emerald-600">Rp {{ number_format($supplier->total_value, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-clock mr-1"></i>{{ number_format($supplier->avg_delivery_time, 1) }} hari
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $score = rand(70, 95); // Sample performance score
                                    @endphp
                                    <div class="flex items-center justify-center">
                                        <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 h-2 rounded-full" style="width: {{ $score }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700">{{ $score }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $performance = $supplier->avg_delivery_time <= 7 ? 'excellent' : ($supplier->avg_delivery_time <= 14 ? 'good' : 'average');
                                    @endphp
                                    @if($performance === 'excellent')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                            <i class="fas fa-check-circle mr-1"></i>Excellent
                                        </span>
                                    @elseif($performance === 'good')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-thumbs-up mr-1"></i>Good
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Average
                                        </span>
                                    @endif
                                </td>
                                    </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="bg-gray-100 rounded-full p-6 mb-4">
                                            <i class="fas fa-chart-line text-gray-400 text-4xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak ada data performa supplier</h3>
                                        <p class="text-gray-600 max-w-md">Coba ubah filter atau rentang tanggal untuk melihat data performa supplier</p>
                                        <button type="button" onclick="document.querySelector('form').reset(); document.querySelector('form').submit();" 
                                                class="mt-4 inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                                            <i class="fas fa-refresh mr-2"></i>Reset Filter
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($performanceData->hasPages())
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            {{ $performanceData->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Performance Trend Chart
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Nilai Pesanan (Juta Rp)',
                data: @json($chartData['values']),
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 1
            }, {
                label: 'Jumlah Pesanan',
                data: @json($chartData['orders']),
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
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
