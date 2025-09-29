<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-chart-line mr-2"></i>{{ __('Laporan & Analytics') }}
        </h2>
    </x-slot>

    <div class="space-y-6 reports-main">
        <!-- Welcome Section -->
        <div class="welcome-section overflow-hidden shadow-lg sm:rounded-xl">
            <div class="relative p-6 lg:p-8 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-xl lg:text-2xl font-bold mb-2">Pusat Laporan Al-Ruhamaa'</h3>
                        <p class="text-emerald-100 text-sm lg:text-base">Pantau dan analisis performa inventori dengan laporan yang komprehensif</p>
                    </div>
                    <div class="hidden lg:block">
                        <i class="fas fa-chart-bar text-5xl xl:text-6xl text-emerald-200 opacity-40"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <!-- Total Products -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card border-emerald-500 hover:shadow-lg hover:-translate-y-1 hover:border-emerald-400 transition-all duration-300 ease-in-out group cursor-pointer">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-emerald-700 transition-colors duration-300">Total Produk</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors duration-300">{{ $totalProducts ?? 0 }}</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full group-hover:bg-emerald-200 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-box text-emerald-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card border-emerald-500 hover:shadow-lg hover:-translate-y-1 hover:border-emerald-600 transition-all duration-300 ease-in-out group cursor-pointer">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-emerald-700 transition-colors duration-300">Stok Rendah</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors duration-300">{{ $lowStockProducts ?? 0 }}</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full group-hover:bg-emerald-200 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-exclamation-triangle text-emerald-600 text-lg group-hover:animate-pulse"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Suppliers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card border-emerald-500 hover:shadow-lg hover:-translate-y-1 hover:border-emerald-600 transition-all duration-300 ease-in-out group cursor-pointer">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-emerald-700 transition-colors duration-300">Total Supplier</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors duration-300">{{ $totalSuppliers ?? 0 }}</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full group-hover:bg-emerald-200 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-truck text-emerald-600 text-lg group-hover:translate-x-1 transition-transform duration-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Activities -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg stats-card border-emerald-500 hover:shadow-lg hover:-translate-y-1 hover:border-emerald-700 transition-all duration-300 ease-in-out group cursor-pointer">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 group-hover:text-emerald-700 transition-colors duration-300">Aktivitas Bulan Ini</p>
                            <p class="text-2xl lg:text-3xl font-bold text-gray-900 group-hover:text-emerald-600 transition-colors duration-300">{{ $monthlyActivities ?? 0 }}</p>
                        </div>
                        <div class="bg-emerald-100 p-3 rounded-full group-hover:bg-emerald-200 group-hover:scale-110 transition-all duration-300">
                            <i class="fas fa-activity text-emerald-600 text-lg group-hover:animate-bounce"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Categories -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
            <!-- Stock Value Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-in-out group report-card">
                <div class="p-4 md:p-6">
                    <!-- Header Section -->
                    <div class="flex items-start mb-4">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4 flex-shrink-0 group-hover:bg-emerald-200 transition-colors duration-300">
                            <i class="fas fa-dollar-sign text-emerald-600 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight mb-1">Laporan Nilai Stok</h3>
                            <p class="text-sm text-gray-600">Analisis nilai inventori dan stok</p>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="flex-1 mb-4">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Lihat total nilai stok, produk dengan stok rendah, dan distribusi nilai berdasarkan kategori.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                        <a href="{{ route('reports.stock-value') }}" 
                           class="flex-1 bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-eye mr-2 transition-transform duration-300 hover:scale-110"></i>Lihat Laporan
                        </a>
                        <a href="{{ route('reports.export-pdf', ['type' => 'stock-value']) }}" 
                           class="bg-red-600 hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-download mr-1 transition-transform duration-300 hover:scale-110"></i>PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Movement Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-in-out group report-card">
                <div class="p-4 md:p-6">
                    <!-- Header Section -->
                    <div class="flex items-start mb-4">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4 flex-shrink-0 group-hover:bg-emerald-200 transition-colors duration-300">
                            <i class="fas fa-arrows-alt text-emerald-600 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight mb-1">Laporan Pergerakan</h3>
                            <p class="text-sm text-gray-600">Riwayat aktivitas dan pergerakan stok</p>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="flex-1 mb-4">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Track semua aktivitas sistem, perubahan stok, dan transaksi yang terjadi.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                        <a href="{{ route('reports.movement') }}" 
                           class="flex-1 bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-eye mr-2 transition-transform duration-300 hover:scale-110"></i>Lihat Laporan
                        </a>
                        <a href="{{ route('reports.export-pdf', ['type' => 'movement']) }}" 
                           class="bg-red-600 hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-download mr-1 transition-transform duration-300 hover:scale-110"></i>PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Supplier Performance -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-in-out group report-card">
                <div class="p-4 md:p-6">
                    <!-- Header Section -->
                    <div class="flex items-start mb-4">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4 flex-shrink-0 group-hover:bg-emerald-200 transition-colors duration-300">
                            <i class="fas fa-truck text-emerald-600 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight mb-1">Performa Supplier</h3>
                            <p class="text-sm text-gray-600">Evaluasi kinerja supplier</p>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="flex-1 mb-4">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Analisis performa supplier berdasarkan frekuensi order dan kualitas layanan.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                        <a href="{{ route('reports.supplier-performance') }}" 
                           class="flex-1 bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-eye mr-2 transition-transform duration-300 hover:scale-110"></i>Lihat Laporan
                        </a>
                        <a href="{{ route('reports.export-pdf', ['type' => 'supplier-performance']) }}" 
                           class="bg-red-600 hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-download mr-1 transition-transform duration-300 hover:scale-110"></i>PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Weekly Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-in-out group report-card">
                <div class="p-4 md:p-6">
                    <!-- Header Section -->
                    <div class="flex items-start mb-4">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4 flex-shrink-0 group-hover:bg-emerald-200 transition-colors duration-300">
                            <i class="fas fa-calendar-week text-emerald-600 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight mb-1">Laporan Mingguan</h3>
                            <p class="text-sm text-gray-600">Ringkasan aktivitas per minggu</p>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="flex-1 mb-4">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Laporan komprehensif aktivitas inventori dalam periode satu minggu.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                        <a href="{{ route('reports.weekly') }}" 
                           class="flex-1 bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-eye mr-2 transition-transform duration-300 hover:scale-110"></i>Lihat Laporan
                        </a>
                        <a href="{{ route('reports.export-pdf', ['type' => 'weekly']) }}" 
                           class="bg-red-600 hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-download mr-1 transition-transform duration-300 hover:scale-110"></i>PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Monthly Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-xl hover:-translate-y-2 transition-all duration-300 ease-in-out group report-card">
                <div class="p-4 md:p-6">
                    <!-- Header Section -->
                    <div class="flex items-start mb-4">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4 flex-shrink-0 group-hover:bg-emerald-200 transition-colors duration-300">
                            <i class="fas fa-calendar-alt text-emerald-600 text-lg group-hover:scale-110 transition-transform duration-300"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight mb-1">Laporan Bulanan</h3>
                            <p class="text-sm text-gray-600">Ringkasan komprehensif per bulan</p>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="flex-1 mb-4">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Analisis mendalam aktivitas dan performa inventori dalam periode satu bulan.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-2 mt-auto">
                        <a href="{{ route('reports.monthly') }}" 
                           class="flex-1 bg-emerald-600 hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-eye mr-2 transition-transform duration-300 hover:scale-110"></i>Lihat Laporan
                        </a>
                        <a href="{{ route('reports.export-pdf', ['type' => 'monthly']) }}" 
                           class="bg-red-600 hover:bg-red-700 hover:shadow-lg hover:-translate-y-0.5 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-300 ease-out text-center flex items-center justify-center">
                            <i class="fas fa-download mr-1 transition-transform duration-300 hover:scale-110"></i>PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Custom Report -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out group report-card border-2 border-dashed border-gray-300 hover:border-gray-400">
                <div class="p-4 md:p-6">
                    <!-- Header Section -->
                    <div class="flex items-start mb-4">
                        <div class="bg-emerald-100 p-3 rounded-full mr-4 flex-shrink-0 group-hover:bg-emerald-200 transition-colors duration-300">
                            <i class="fas fa-cog text-emerald-600 text-lg group-hover:rotate-45 transition-transform duration-300"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 leading-tight mb-1">Laporan Custom</h3>
                            <p class="text-sm text-gray-600">Buat laporan sesuai kebutuhan</p>
                        </div>
                    </div>
                    
                    <!-- Description Section -->
                    <div class="flex-1 mb-4">
                        <p class="text-gray-700 text-sm leading-relaxed">
                            Fitur ini akan segera tersedia untuk membuat laporan dengan filter dan parameter khusus.
                        </p>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-center mt-auto">
                        <button class="bg-emerald-400 text-white px-4 py-2.5 rounded-lg text-sm font-medium cursor-not-allowed flex items-center justify-center transition-all duration-300" disabled>
                            <i class="fas fa-clock mr-2 animate-pulse"></i>Segera Hadir
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow duration-300">
            <div class="p-4 md:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-history mr-2 text-emerald-600"></i>Aktivitas Terbaru
                    </h3>
                    <a href="{{ route('activities.index') }}" class="text-emerald-600 hover:text-emerald-700 hover:underline hover:scale-105 text-sm font-medium transition-all duration-300 ease-out">
                        Lihat Semua <i class="fas fa-arrow-right ml-1 transition-transform duration-300 hover:translate-x-1"></i>
                    </a>
                </div>
                
                <div class="space-y-3">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 ease-out cursor-pointer">
                            <div class="flex-shrink-0 mr-3">
                                @if(str_contains($activity->action, 'tambah'))
                                    <div class="bg-green-100 p-2 rounded-full hover:bg-green-200 hover:scale-110 transition-all duration-300">
                                        <i class="fas fa-plus text-green-600 text-xs"></i>
                                    </div>
                                @elseif(str_contains($activity->action, 'update'))
                                    <div class="bg-blue-100 p-2 rounded-full hover:bg-blue-200 hover:scale-110 transition-all duration-300">
                                        <i class="fas fa-edit text-blue-600 text-xs"></i>
                                    </div>
                                @elseif(str_contains($activity->action, 'hapus'))
                                    <div class="bg-red-100 p-2 rounded-full hover:bg-red-200 hover:scale-110 transition-all duration-300">
                                        <i class="fas fa-trash text-red-600 text-xs"></i>
                                    </div>
                                @else
                                    <div class="bg-gray-100 p-2 rounded-full hover:bg-gray-200 hover:scale-110 transition-all duration-300">
                                        <i class="fas fa-info text-gray-600 text-xs"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 hover:text-gray-700 transition-colors duration-200">{{ $activity->user }}</p>
                                <p class="text-sm text-gray-600 hover:text-gray-700 transition-colors duration-200">{{ $activity->action }}</p>
                            </div>
                            <div class="text-sm text-gray-500 hover:text-gray-600 transition-colors duration-200">
                                {{ $activity->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500 hover:text-gray-600 transition-colors duration-300">
                            <i class="fas fa-inbox text-2xl mb-2 hover:animate-pulse"></i>
                            <p class="hover:scale-105 transition-transform duration-300">Belum ada aktivitas terbaru</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Performance Optimization -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to report links
            const reportLinks = document.querySelectorAll('a[href*="/reports/"]');
            
            reportLinks.forEach(link => {
                if (link.href.includes('/reports/') && !link.href.includes('export') && !link.href.includes('#')) {
                    link.addEventListener('click', function(e) {
                        // Add loading state
                        const icon = this.querySelector('i');
                        const originalIcon = icon.className;
                        const originalText = this.innerHTML;
                        
                        // Show loading
                        icon.className = 'fas fa-spinner fa-spin mr-2';
                        this.querySelector('.transition-transform').textContent = 'Memuat...';
                        this.style.pointerEvents = 'none';
                        this.style.opacity = '0.7';
                        
                        // Restore after timeout (fallback)
                        setTimeout(() => {
                            icon.className = originalIcon;
                            this.innerHTML = originalText;
                            this.style.pointerEvents = '';
                            this.style.opacity = '';
                        }, 5000);
                    });
                }
            });
            
            // Preload critical resources
            const preloadLinks = [
                '{{ route("reports.stock-value") }}',
                '{{ route("reports.movement") }}',
                '{{ route("reports.supplier-performance") }}',
                '{{ route("reports.weekly") }}'
            ];
            
            // Add prefetch hints for faster navigation
            preloadLinks.forEach(url => {
                const link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = url;
                document.head.appendChild(link);
            });
            
            console.log('Reports page optimized and loaded');
        });
    </script>
    @endpush
</x-app-layout>