<x-app-layout>
    <x-slot name="header">
        <div class="reports-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight">
                <i class="fas fa-dollar-sign mr-2 text-green-700"></i>{{ __('Laporan Nilai Stok') }}
            </h2>
            <div class="btn-group no-print">
                <a href="{{ route('reports.export-pdf', ['type' => 'stock-value'] + request()->all()) }}" 
                   class="btn btn-secondary bg-green-700 hover:bg-green-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2 text-sm"></i>Export PDF
                </a>
                <a href="{{ route('reports.export-excel', ['type' => 'stock-value'] + request()->all()) }}" 
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
                    <a href="{{ route('reports.index') }}" class="text-gray-700 hover:text-green-700">
                        <i class="fas fa-chart-line mr-2"></i>Laporan
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-green-700 font-medium">Nilai Stok</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Filter Section -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg filter-section">
            <div class="p-4 md:p-6">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-filter mr-2 text-green-700"></i>Filter Laporan
                </h3>
                
                <form method="GET" action="{{ route('reports.stock-value') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Category Filter -->
                        <div class="space-y-2">
                            <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                            <select name="category_id" id="category_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600 bg-white">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div class="space-y-2">
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
                        </div>

                        <!-- Date To -->
                        <div class="space-y-2">
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                   class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-green-600">
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-green-700 hover:bg-green-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-700">
                <div class="p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm font-medium text-gray-600 truncate">Total Produk</p>
                            <p class="text-xl md:text-2xl font-semibold text-gray-900 mt-1">{{ number_format($totalProducts) }}</p>
                        </div>
                        <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                            <i class="fas fa-box text-green-700 text-sm md:text-base"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-600">
                <div class="p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm font-medium text-gray-600 truncate">Total Stok</p>
                            <p class="text-xl md:text-2xl font-semibold text-gray-900 mt-1">{{ number_format($totalStock) }}</p>
                        </div>
                        <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                            <i class="fas fa-cubes text-green-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                <div class="p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs md:text-sm font-medium text-gray-600 truncate">Total Nilai</p>
                            <p class="text-lg md:text-xl font-semibold text-gray-900 mt-1">Rp {{ number_format($totalValue, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-100 p-2 md:p-3 rounded-full flex-shrink-0">
                            <i class="fas fa-dollar-sign text-green-600 text-sm md:text-base"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg border-l-4 border-green-500">
                <div class="p-4 md:p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stok Rendah</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $lowStockProducts->count() }}</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-exclamation-triangle text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-list mr-2 text-green-700"></i>Detail Produk & Nilai Stok
                    </h3>
                    <div class="text-sm text-gray-600">
                        Total: {{ $products->count() }} produk
                    </div>
                </div>

                <div class="table-responsive">
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
                                    SKU
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Stok
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Harga Satuan
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Nilai
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-box text-green-700"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($product->description, 30) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $product->category->name ?? 'Tidak ada kategori' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->sku }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <span class="mr-2">{{ number_format($product->stock_quantity) }}</span>
                                            <span class="text-gray-500">{{ $product->satuan }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        Rp {{ number_format($product->getDefaultPrice(), 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rp {{ number_format($product->stock_quantity * $product->getDefaultPrice(), 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->stock_quantity < 10)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Stok Rendah
                                            </span>
                                        @elseif($product->stock_quantity < 20)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <i class="fas fa-exclamation-circle mr-1"></i>Perlu Perhatian
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>Stok Aman
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-inbox text-4xl mb-4"></i>
                                        <p class="text-lg">Tidak ada data produk yang ditemukan</p>
                                        <p class="text-sm">Coba ubah filter atau tambahkan produk baru</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert Section -->
        @if($lowStockProducts->count() > 0)
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-600">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>Peringatan Stok Rendah
                </h3>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Produk dengan Stok Rendah
                            </h3>
                            <p class="mt-1 text-sm text-red-700">
                                Terdapat {{ $lowStockProducts->count() }} produk dengan stok di bawah 10 unit. 
                                Segera lakukan restok untuk menghindari kehabisan stok.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($lowStockProducts as $product)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $product->category->name ?? 'Tidak ada kategori' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-red-600">{{ $product->stock_quantity }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->satuan }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Auto submit form when dates are selected
        document.getElementById('date_from').addEventListener('change', function() {
            if (document.getElementById('date_to').value) {
                this.form.submit();
            }
        });
        
        document.getElementById('date_to').addEventListener('change', function() {
            if (document.getElementById('date_from').value) {
                this.form.submit();
            }
        });

        // Auto submit when category is changed
        document.getElementById('category_id').addEventListener('change', function() {
            this.form.submit();
        });
    </script>
    @endpush
</x-app-layout>