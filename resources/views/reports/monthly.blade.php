<x-app-layout>
    <x-slot name="header">
        <div class="reports-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                <i class="fas fa-calendar-alt mr-2 text-indigo-600"></i>{{ __('Laporan Bulanan') }}
            </h2>
            <div class="btn-group no-print">
                <a href="{{ route('reports.export-pdf', ['type' => 'monthly'] + request()->all()) }}" 
                   class="btn btn-secondary bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2 text-sm"></i>Export PDF
                </a>
                <a href="{{ route('reports.export-excel', ['type' => 'monthly'] + request()->all()) }}" 
                   class="btn btn-secondary bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2 text-sm"></i>Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 reports-main">
        <!-- Breadcrumb -->
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('reports.index') }}" class="text-gray-700 hover:text-indigo-600">
                        <i class="fas fa-chart-line mr-2"></i>Laporan
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-indigo-600 font-medium">Laporan Bulanan</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Month Selector -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-calendar mr-2 text-indigo-600"></i>Pilih Bulan
                </h3>
                
                <form method="GET" action="{{ route('reports.monthly') }}" class="flex items-center space-x-4">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                        <input type="month" name="month" id="month" value="{{ request('month', $month->format('Y-m')) }}"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-search mr-2"></i>Lihat Laporan
                        </button>
                    </div>
                </form>
                
                <div class="mt-4 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <p class="text-sm text-indigo-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Menampilkan laporan untuk bulan: 
                        <span class="font-semibold">{{ $month->format('F Y') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Aktivitas</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalActivities) }}</p>
                        </div>
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <i class="fas fa-activity text-indigo-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-emerald-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Produk Ditambah</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalProductsAdded) }}</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full">
                            <i class="fas fa-plus text-emerald-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Nilai Stok</p>
                            <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($totalStockValue, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-amber-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($lowStockCount) }}</p>
                        </div>
                        <div class="bg-amber-100 p-3 rounded-full">
                            <i class="fas fa-exclamation-triangle text-amber-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Breakdown Chart -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-bar mr-2 text-indigo-600"></i>Aktivitas per Minggu
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @php
                        $weekLabels = ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4', 'Minggu 5'];
                        $maxWeeklyActivities = $weeklyActivities->map->count()->max() ?: 1;
                    @endphp
                    
                    @foreach($weeklyActivities as $weekNumber => $weekActivities)
                        @php
                            $activityCount = $weekActivities->count();
                            $percentage = $maxWeeklyActivities > 0 ? ($activityCount / $maxWeeklyActivities) * 100 : 0;
                        @endphp
                        
                        <div class="text-center">
                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-600">{{ $weekLabels[$weekNumber - 1] ?? 'Minggu ' . $weekNumber }}</div>
                            </div>
                            <div class="h-32 flex items-end justify-center">
                                <div class="w-12 bg-indigo-200 rounded-t flex items-end justify-center" 
                                     style="height: {{ max($percentage, 15) }}%">
                                    <span class="text-sm font-medium text-indigo-800 mb-2">{{ $activityCount }}</span>
                                </div>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                {{ $activityCount }} aktivitas
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Products Added This Month -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-box mr-2 text-emerald-600"></i>Produk Baru Bulan Ini
                </h3>
                
                @if($productsThisMonth->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Produk
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Kategori
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stok
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Harga
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($productsThisMonth->take(10) as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                                        <i class="fas fa-box text-emerald-600 text-xs"></i>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $product->sku }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                {{ $product->category->name ?? 'Tidak ada kategori' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format($product->stock_quantity) }} {{ $product->satuan }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rp {{ number_format($product->getDefaultPrice(), 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $product->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($productsThisMonth->count() > 10)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600">
                                Menampilkan 10 dari {{ $productsThisMonth->count() }} produk baru. 
                                <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                    Lihat semua produk
                                </a>
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-box-open text-4xl mb-4"></i>
                        <p class="text-lg">Tidak ada produk baru bulan ini</p>
                        <p class="text-sm">Belum ada produk yang ditambahkan pada {{ $month->format('F Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Monthly Activity Summary -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>Ringkasan Aktivitas Bulan Ini
                </h3>
                
                @php
                    $activityTypes = $activities->groupBy(function($activity) {
                        if (str_contains(strtolower($activity->action), 'tambah')) return 'Penambahan';
                        if (str_contains(strtolower($activity->action), 'update')) return 'Perubahan';
                        if (str_contains(strtolower($activity->action), 'hapus')) return 'Penghapusan';
                        if (str_contains(strtolower($activity->action), 'login')) return 'Login';
                        return 'Lainnya';
                    });
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    @foreach($activityTypes as $type => $typeActivities)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <div class="mb-2">
                                @if($type == 'Penambahan')
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-plus text-green-600 text-xl"></i>
                                    </div>
                                @elseif($type == 'Perubahan')
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-edit text-blue-600 text-xl"></i>
                                    </div>
                                @elseif($type == 'Penghapusan')
                                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-trash text-red-600 text-xl"></i>
                                    </div>
                                @elseif($type == 'Login')
                                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-sign-in-alt text-indigo-600 text-xl"></i>
                                    </div>
                                @else
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto">
                                        <i class="fas fa-info text-gray-600 text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <h4 class="font-medium text-gray-900">{{ $type }}</h4>
                            <p class="text-2xl font-semibold text-gray-900">{{ $typeActivities->count() }}</p>
                            <p class="text-sm text-gray-600">
                                {{ number_format(($typeActivities->count() / $totalActivities) * 100, 1) }}%
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clock mr-2 text-indigo-600"></i>Aktivitas Terbaru Bulan Ini
                </h3>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aktivitas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Model
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Waktu
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($activities->take(15) as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-indigo-600 text-xs"></i>
                                                </div>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $activity->user }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $activity->action }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($activity->model)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ $activity->model }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span>{{ $activity->created_at->format('d M Y') }}</span>
                                            <span class="text-xs text-gray-500">{{ $activity->created_at->format('H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(str_contains(strtolower($activity->action), 'tambah'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-plus mr-1"></i>Penambahan
                                            </span>
                                        @elseif(str_contains(strtolower($activity->action), 'update'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-edit mr-1"></i>Perubahan
                                            </span>
                                        @elseif(str_contains(strtolower($activity->action), 'hapus'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-trash mr-1"></i>Penghapusan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-info mr-1"></i>Lainnya
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-4"></i>
                                        <p class="text-lg">Tidak ada aktivitas bulan ini</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($activities->count() > 15)
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            Menampilkan 15 dari {{ $activities->count() }} aktivitas. 
                            <a href="{{ route('reports.movement') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                Lihat semua aktivitas
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto submit form when month is changed
        document.getElementById('month').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
    @endpush
</x-app-layout>