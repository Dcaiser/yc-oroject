<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl shrink-0">
                    <i class="ti ti-history-toggle text-lg"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900">Riwayat Aktivitas</h1>
                    <p class="text-sm text-slate-600 mt-0.5">Pantau setiap perubahan data dalam sistem.</p>
                </div>
            </div>
            <div class="flex items-center px-4 py-2 font-medium text-white rounded-lg shadow transition bg-linear-to-r from-emerald-500 to-emerald-700">
                <i class="mr-2 ti ti-calendar-month"></i>
                <span class="text-sm font-semibold">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </x-slot>

    <style>
        /* Custom scrollbar untuk dropdown */
        .custom-dropdown-menu::-webkit-scrollbar {
            width: 6px;
        }
        .custom-dropdown-menu::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }
        .custom-dropdown-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .custom-dropdown-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    @php
        $actionPalette = function (string $action): array {
            $normalized = \Illuminate\Support\Str::lower($action);

            if (\Illuminate\Support\Str::contains($normalized, ['menambah', 'membuat', 'transaksi pos'])) {
                return [
                    'label' => 'Penambahan',
                    'icon' => 'ti ti-circle-plus',
                    'classes' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                    'dot' => 'bg-emerald-500'
                ];
            }

            if (\Illuminate\Support\Str::contains($normalized, 'menghapus')) {
                return [
                    'label' => 'Penghapusan',
                    'icon' => 'ti ti-trash',
                    'classes' => 'bg-red-50 text-red-700 border border-red-200',
                    'dot' => 'bg-red-500'
                ];
            }

            if (\Illuminate\Support\Str::contains($normalized, ['mengedit', 'memperbarui', 'mengubah'])) {
                return [
                    'label' => 'Pembaruan',
                    'icon' => 'ti ti-pencil',
                    'classes' => 'bg-amber-50 text-amber-700 border border-amber-200',
                    'dot' => 'bg-amber-500'
                ];
            }

            return [
                'label' => 'Aktivitas',
                'icon' => 'ti ti-clipboard-list',
                'classes' => 'bg-slate-100 text-slate-600 border border-slate-200',
                'dot' => 'bg-slate-400'
            ];
        };

        $filterOptions = [
            'all' => ['label' => 'Semua', 'icon' => 'ti ti-layers-intersect'],
            'add' => ['label' => 'Penambahan', 'icon' => 'ti ti-circle-plus'],
            'edit' => ['label' => 'Pembaruan', 'icon' => 'ti ti-pencil'],
            'delete' => ['label' => 'Penghapusan', 'icon' => 'ti ti-trash'],
        ];

        $activeFilter = request('filter', 'all');
        $searchQuery = request('search', '');
        $activeSource = request('source', 'all');
        $activeUser = request('user', 'all');
        $dateFrom = request('date_from', '');
        $dateTo = request('date_to', '');
        $perPage = request('per_page', 15);
        
        // Mapping nama sumber yang lebih ramah
        $sourceLabels = [
            'Produk' => 'Produk',
            'Transaksi' => 'Transaksi',
            'StockIn' => 'Stok Masuk',
            'StockOut' => 'Stok Keluar',
            'Supplier' => 'Supplier',
            'Kategori' => 'Kategori',
            'Customer' => 'Pelanggan',
            'User' => 'Pengguna',
            'PurchaseOrder' => 'Purchase Order',
        ];
    @endphp

    <div class="space-y-6 pb-12" x-data="activityPage()">
        <x-breadcrumb :items="[['title' => 'Aktivitas']]" />

        <!-- Success/Error Alert -->
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-start gap-3 p-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-emerald-100 text-emerald-600 rounded-full shrink-0">
                    <i class="ti ti-circle-check"></i>
                </span>
                <div class="flex-1">
                    <p class="text-sm font-semibold">Berhasil!</p>
                    <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                </div>
                <button type="button" @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition
                 class="flex items-start gap-3 p-4 text-red-800 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full shrink-0">
                    <i class="ti ti-alert-circle"></i>
                </span>
                <div class="flex-1">
                    <p class="text-sm font-semibold">Gagal!</p>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
                <button type="button" @click="show = false" class="text-red-500 hover:text-red-700 transition">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
            <div class="p-4 sm:p-5 bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-emerald-100 text-emerald-600 rounded-xl shrink-0">
                        <i class="text-base sm:text-lg ti ti-database"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] sm:text-xs font-semibold tracking-wide text-emerald-600 uppercase truncate">Total Aktivitas</p>
                        <p class="text-xl sm:text-2xl font-bold text-slate-800">{{ number_format($stats['total'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-sky-100 text-sky-600 rounded-xl shrink-0">
                        <i class="text-base sm:text-lg ti ti-calendar-day"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] sm:text-xs font-semibold tracking-wide text-sky-600 uppercase truncate">Hari Ini</p>
                        <p class="text-xl sm:text-2xl font-bold text-slate-800">{{ number_format($stats['today'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-amber-100 text-amber-600 rounded-xl shrink-0">
                        <i class="text-base sm:text-lg ti ti-calendar-week"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] sm:text-xs font-semibold tracking-wide text-amber-600 uppercase truncate">Minggu Ini</p>
                        <p class="text-xl sm:text-2xl font-bold text-slate-800">{{ number_format($stats['this_week'] ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex items-center gap-3 sm:gap-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-teal-100 text-teal-600 rounded-xl shrink-0">
                        <i class="text-base sm:text-lg ti ti-user-check"></i>
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] sm:text-xs font-semibold tracking-wide text-teal-600 uppercase truncate">Pengguna Aktif</p>
                        <p class="text-xl sm:text-2xl font-bold text-slate-800">{{ number_format($stats['actors'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section -->
        <section class="bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
            <div class="px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl">
                            <i class="ti ti-filter"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold text-slate-900">Filter & Pencarian</h2>
                            <p class="text-sm text-slate-600">Temukan aktivitas yang Anda cari dengan mudah.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('activities.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                            <i class="ti ti-rotate-clockwise"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                        <form action="{{ route('activities.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh riwayat aktivitas? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-linear-to-r from-red-500 to-red-600 rounded-xl hover:scale-[1.02] shadow-lg shadow-red-400/30 transition">
                                <i class="ti ti-trash"></i>
                                <span class="hidden sm:inline">Hapus Semua</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <form action="{{ route('activities.index') }}" method="GET" class="space-y-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                            <i class="ti ti-search"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ $searchQuery }}"
                            placeholder="Cari berdasarkan pengguna, aktivitas, atau sumber..."
                            class="w-full py-3 pl-12 pr-4 text-sm font-medium text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400"
                        >
                        @if($searchQuery)
                            <a href="{{ route('activities.index', array_filter(request()->except('search'))) }}" 
                               class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-red-500 transition">
                                <i class="ti ti-circle-x"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Advanced Filters Grid -->
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Filter by Source - Custom Dropdown -->
                        <div x-data="{ open: false, selected: '{{ $activeSource }}', label: '{{ $activeSource === 'all' ? 'Semua Sumber' : ($sourceLabels[$activeSource] ?? $activeSource) }}' }">
                            <label class="block mb-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                <i class="ti ti-folder-open mr-1 text-emerald-500"></i>Sumber Data
                            </label>
                            <input type="hidden" name="source" :value="selected">
                            <div class="relative">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-slate-700 bg-white border-2 border-slate-200 rounded-xl cursor-pointer hover:border-emerald-300 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none transition-all">
                                    <span x-text="label" class="truncate"></span>
                                    <i class="ti ti-chevron-down text-xs text-slate-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                                </button>
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden custom-dropdown-menu max-h-60 overflow-y-auto">
                                    <div @click="selected = 'all'; label = 'Semua Sumber'; open = false" 
                                         class="flex items-center gap-3 px-4 py-3 text-sm cursor-pointer transition-colors"
                                         :class="selected === 'all' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'">
                                        <i class="ti ti-layers-intersect w-4"></i>
                                        <span>Semua Sumber</span>
                                        <i x-show="selected === 'all'" class="ti ti-check ml-auto text-emerald-500"></i>
                                    </div>
                                    @foreach($sources as $source)
                                        <div @click="selected = '{{ $source }}'; label = '{{ $sourceLabels[$source] ?? $source }}'; open = false" 
                                             class="flex items-center gap-3 px-4 py-3 text-sm cursor-pointer transition-colors"
                                             :class="selected === '{{ $source }}' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'">
                                            <i class="ti ti-cube w-4 text-slate-400"></i>
                                            <span>{{ $sourceLabels[$source] ?? $source }}</span>
                                            <i x-show="selected === '{{ $source }}'" class="ti ti-check ml-auto text-emerald-500"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Filter by User - Custom Dropdown -->
                        <div x-data="{ open: false, selected: '{{ $activeUser }}', label: '{{ $activeUser === 'all' ? 'Semua Pengguna' : $activeUser }}' }">
                            <label class="block mb-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                <i class="ti ti-user mr-1 text-emerald-500"></i>Pengguna
                            </label>
                            <input type="hidden" name="user" :value="selected">
                            <div class="relative">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full flex items-center justify-between px-4 py-3 text-sm font-medium text-slate-700 bg-white border-2 border-slate-200 rounded-xl cursor-pointer hover:border-emerald-300 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none transition-all">
                                    <span x-text="label" class="truncate"></span>
                                    <i class="ti ti-chevron-down text-xs text-slate-400 transition-transform" :class="{ 'rotate-180': open }"></i>
                                </button>
                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden custom-dropdown-menu max-h-60 overflow-y-auto">
                                    <div @click="selected = 'all'; label = 'Semua Pengguna'; open = false" 
                                         class="flex items-center gap-3 px-4 py-3 text-sm cursor-pointer transition-colors"
                                         :class="selected === 'all' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'">
                                        <i class="ti ti-users w-4"></i>
                                        <span>Semua Pengguna</span>
                                        <i x-show="selected === 'all'" class="ti ti-check ml-auto text-emerald-500"></i>
                                    </div>
                                    @foreach($users as $user)
                                        <div @click="selected = '{{ $user }}'; label = '{{ $user }}'; open = false" 
                                             class="flex items-center gap-3 px-4 py-3 text-sm cursor-pointer transition-colors"
                                             :class="selected === '{{ $user }}' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50'">
                                            <i class="ti ti-user-circle w-4 text-slate-400"></i>
                                            <span>{{ $user }}</span>
                                            <i x-show="selected === '{{ $user }}'" class="ti ti-check ml-auto text-emerald-500"></i>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Date From - Custom Date Picker -->
                        <div x-data="datePicker('date_from', '{{ $dateFrom }}')" class="relative" @click.away="closeAll()">
                            <label class="block mb-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                <i class="ti ti-calendar mr-1 text-emerald-500"></i>Dari Tanggal
                            </label>
                            <input type="hidden" name="date_from" :value="selectedDate">
                            <!-- Input Field dengan Manual Entry -->
                            <div class="relative">
                                <input type="text" 
                                       x-model="inputValue"
                                       @focus="open = true"
                                       @input.debounce.500ms="handleManualInput()"
                                       @keydown.enter.prevent="validateAndClose()"
                                       @keydown.escape="open = false"
                                       placeholder="dd/mm/yyyy"
                                       class="w-full px-4 py-3 pr-10 text-sm font-medium bg-white border-2 rounded-xl transition-all"
                                       :class="inputError ? 'text-red-600 border-red-300 focus:ring-red-400 focus:border-red-400' : 'text-slate-700 border-slate-200 hover:border-emerald-300 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400'">
                                <button type="button" @click="open = !open" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500 hover:text-emerald-600 transition">
                                    <i class="ti ti-calendar-month"></i>
                                </button>
                            </div>
                            <!-- Error Message -->
                            <p x-show="inputError" x-cloak x-text="inputError" class="mt-1 text-xs font-medium text-red-500"></p>
                            <!-- Calendar Dropdown -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-[60] left-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-visible">
                                <!-- Calendar Header with Month & Year Dropdowns -->
                                <div class="bg-linear-to-r from-emerald-500 to-emerald-600 text-white rounded-t-2xl">
                                    <div class="flex items-center justify-between px-3 py-2.5">
                                        <button type="button" @click="prevMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                                            <i class="ti ti-chevron-left text-sm"></i>
                                        </button>
                                        
                                        <!-- Month Dropdown -->
                                        <div class="relative">
                                            <button type="button" @click="showMonthPicker = !showMonthPicker; showYearPicker = false"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-white/20 transition">
                                                <span class="text-sm font-bold" x-text="monthNames[currentMonth]"></span>
                                                <i class="ti ti-chevron-down text-[10px] transition-transform" :class="showMonthPicker ? 'rotate-180' : ''"></i>
                                            </button>
                                            <!-- Month Grid Dropdown -->
                                            <div x-show="showMonthPicker" x-cloak x-transition
                                                 class="absolute z-[70] top-full left-1/2 -translate-x-1/2 mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 p-2 w-44">
                                                <div class="grid grid-cols-3 gap-1">
                                                    <template x-for="(month, idx) in monthShortNames" :key="idx">
                                                        <button type="button" @click="currentMonth = idx; showMonthPicker = false"
                                                                class="px-2 py-2 text-xs font-semibold rounded-lg transition"
                                                                :class="currentMonth === idx ? 'bg-emerald-500 text-white' : 'text-slate-600 hover:bg-emerald-50'"
                                                                x-text="month">
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Year Dropdown -->
                                        <div class="relative">
                                            <button type="button" @click="showYearPicker = !showYearPicker; showMonthPicker = false"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-white/20 transition">
                                                <span class="text-sm font-bold" x-text="currentYear"></span>
                                                <i class="ti ti-chevron-down text-[10px] transition-transform" :class="showYearPicker ? 'rotate-180' : ''"></i>
                                            </button>
                                            <!-- Year Scrollable Dropdown -->
                                            <div x-show="showYearPicker" x-cloak x-transition
                                                 x-init="$watch('showYearPicker', value => { if(value) $nextTick(() => scrollToSelectedYear($el)) })"
                                                 class="absolute z-[70] top-full right-0 mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 max-h-52 overflow-y-auto w-24 custom-dropdown-menu">
                                                <template x-for="year in yearRange" :key="year">
                                                    <button type="button" @click="currentYear = year; showYearPicker = false"
                                                            class="w-full px-3 py-2 text-sm font-medium text-center transition"
                                                            :class="currentYear === year ? 'bg-emerald-500 text-white' : 'text-slate-600 hover:bg-emerald-50'"
                                                            :data-year="year"
                                                            x-text="year">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        
                                        <button type="button" @click="nextMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                                            <i class="ti ti-chevron-right text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Day Names -->
                                <div class="grid grid-cols-7 gap-0 bg-emerald-50 border-b border-emerald-100">
                                    <template x-for="day in dayNames" :key="day">
                                        <div class="py-2 text-center text-xs font-bold text-emerald-700" x-text="day"></div>
                                    </template>
                                </div>
                                <!-- Calendar Days -->
                                <div class="grid grid-cols-7 gap-0.5 p-2">
                                    <template x-for="blank in blankDays" :key="'blank-'+blank">
                                        <div class="p-1"></div>
                                    </template>
                                    <template x-for="day in daysInMonth" :key="day">
                                        <button type="button" @click="selectDate(day)"
                                                class="p-1 aspect-square flex items-center justify-center text-sm rounded-lg transition-all"
                                                :class="{
                                                    'bg-emerald-500 text-white font-bold shadow-md': isSelected(day),
                                                    'bg-emerald-100 text-emerald-700 font-semibold ring-2 ring-emerald-300': isToday(day) && !isSelected(day),
                                                    'hover:bg-emerald-50 text-slate-600': !isSelected(day) && !isToday(day)
                                                }" x-text="day">
                                        </button>
                                    </template>
                                </div>
                                <!-- Calendar Footer -->
                                <div class="flex items-center justify-between px-3 py-2.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl">
                                    <button type="button" @click="clearDate()" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="ti ti-x mr-1"></i>Hapus
                                    </button>
                                    <button type="button" @click="setToday()" class="px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                                        <i class="ti ti-calendar-day mr-1"></i>Hari Ini
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Date To - Custom Date Picker -->
                        <div x-data="datePicker('date_to', '{{ $dateTo }}')" class="relative" @click.away="closeAll()">
                            <label class="block mb-2 text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                <i class="ti ti-calendar-check mr-1 text-emerald-500"></i>Sampai Tanggal
                            </label>
                            <input type="hidden" name="date_to" :value="selectedDate">
                            <!-- Input Field dengan Manual Entry -->
                            <div class="relative">
                                <input type="text" 
                                       x-model="inputValue"
                                       @focus="open = true"
                                       @input.debounce.500ms="handleManualInput()"
                                       @keydown.enter.prevent="validateAndClose()"
                                       @keydown.escape="open = false"
                                       placeholder="dd/mm/yyyy"
                                       class="w-full px-4 py-3 pr-10 text-sm font-medium bg-white border-2 rounded-xl transition-all"
                                       :class="inputError ? 'text-red-600 border-red-300 focus:ring-red-400 focus:border-red-400' : 'text-slate-700 border-slate-200 hover:border-emerald-300 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400'">
                                <button type="button" @click="open = !open" class="absolute right-3 top-1/2 -translate-y-1/2 text-emerald-500 hover:text-emerald-600 transition">
                                    <i class="ti ti-calendar-month"></i>
                                </button>
                            </div>
                            <!-- Error Message -->
                            <p x-show="inputError" x-cloak x-text="inputError" class="mt-1 text-xs font-medium text-red-500"></p>
                            <!-- Calendar Dropdown -->
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-[60] left-0 mt-2 w-80 bg-white border border-slate-200 rounded-2xl shadow-2xl overflow-visible">
                                <!-- Calendar Header with Month & Year Dropdowns -->
                                <div class="bg-linear-to-r from-emerald-500 to-emerald-600 text-white rounded-t-2xl">
                                    <div class="flex items-center justify-between px-3 py-2.5">
                                        <button type="button" @click="prevMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                                            <i class="ti ti-chevron-left text-sm"></i>
                                        </button>
                                        
                                        <!-- Month Dropdown -->
                                        <div class="relative">
                                            <button type="button" @click="showMonthPicker = !showMonthPicker; showYearPicker = false"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-white/20 transition">
                                                <span class="text-sm font-bold" x-text="monthNames[currentMonth]"></span>
                                                <i class="ti ti-chevron-down text-[10px] transition-transform" :class="showMonthPicker ? 'rotate-180' : ''"></i>
                                            </button>
                                            <!-- Month Grid Dropdown -->
                                            <div x-show="showMonthPicker" x-cloak x-transition
                                                 class="absolute z-[70] top-full left-1/2 -translate-x-1/2 mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 p-2 w-44">
                                                <div class="grid grid-cols-3 gap-1">
                                                    <template x-for="(month, idx) in monthShortNames" :key="idx">
                                                        <button type="button" @click="currentMonth = idx; showMonthPicker = false"
                                                                class="px-2 py-2 text-xs font-semibold rounded-lg transition"
                                                                :class="currentMonth === idx ? 'bg-emerald-500 text-white' : 'text-slate-600 hover:bg-emerald-50'"
                                                                x-text="month">
                                                        </button>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Year Dropdown -->
                                        <div class="relative">
                                            <button type="button" @click="showYearPicker = !showYearPicker; showMonthPicker = false"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg hover:bg-white/20 transition">
                                                <span class="text-sm font-bold" x-text="currentYear"></span>
                                                <i class="ti ti-chevron-down text-[10px] transition-transform" :class="showYearPicker ? 'rotate-180' : ''"></i>
                                            </button>
                                            <!-- Year Scrollable Dropdown -->
                                            <div x-show="showYearPicker" x-cloak x-transition
                                                 x-init="$watch('showYearPicker', value => { if(value) $nextTick(() => scrollToSelectedYear($el)) })"
                                                 class="absolute z-[70] top-full right-0 mt-2 bg-white rounded-xl shadow-2xl border border-slate-200 max-h-52 overflow-y-auto w-24 custom-dropdown-menu">
                                                <template x-for="year in yearRange" :key="year">
                                                    <button type="button" @click="currentYear = year; showYearPicker = false"
                                                            class="w-full px-3 py-2 text-sm font-medium text-center transition"
                                                            :class="currentYear === year ? 'bg-emerald-500 text-white' : 'text-slate-600 hover:bg-emerald-50'"
                                                            :data-year="year"
                                                            x-text="year">
                                                    </button>
                                                </template>
                                            </div>
                                        </div>
                                        
                                        <button type="button" @click="nextMonth()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/20 transition">
                                            <i class="ti ti-chevron-right text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                                <!-- Day Names -->
                                <div class="grid grid-cols-7 gap-0 bg-emerald-50 border-b border-emerald-100">
                                    <template x-for="day in dayNames" :key="day">
                                        <div class="py-2 text-center text-xs font-bold text-emerald-700" x-text="day"></div>
                                    </template>
                                </div>
                                <!-- Calendar Days -->
                                <div class="grid grid-cols-7 gap-0.5 p-2">
                                    <template x-for="blank in blankDays" :key="'blank-'+blank">
                                        <div class="p-1"></div>
                                    </template>
                                    <template x-for="day in daysInMonth" :key="day">
                                        <button type="button" @click="selectDate(day)"
                                                class="p-1 aspect-square flex items-center justify-center text-sm rounded-lg transition-all"
                                                :class="{
                                                    'bg-emerald-500 text-white font-bold shadow-md': isSelected(day),
                                                    'bg-emerald-100 text-emerald-700 font-semibold ring-2 ring-emerald-300': isToday(day) && !isSelected(day),
                                                    'hover:bg-emerald-50 text-slate-600': !isSelected(day) && !isToday(day)
                                                }" x-text="day">
                                        </button>
                                    </template>
                                </div>
                                <!-- Calendar Footer -->
                                <div class="flex items-center justify-between px-3 py-2.5 bg-slate-50 border-t border-slate-100 rounded-b-2xl">
                                    <button type="button" @click="clearDate()" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <i class="ti ti-x mr-1"></i>Hapus
                                    </button>
                                    <button type="button" @click="setToday()" class="px-3 py-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition">
                                        <i class="ti ti-calendar-day mr-1"></i>Hari Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Type Buttons -->
                    <div class="flex flex-wrap items-center gap-2 pt-2">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide mr-2">Tipe:</span>
                        @foreach($filterOptions as $key => $option)
                            <button type="submit" name="filter" value="{{ $key }}"
                                class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-sm font-semibold rounded-xl transition
                                {{ $activeFilter === $key 
                                    ? 'bg-linear-to-r from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-400/30' 
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200 border border-slate-200' }}">
                                <i class="fas {{ $option['icon'] }}"></i>
                                <span class="hidden xs:inline sm:inline">{{ $option['label'] }}</span>
                            </button>
                        @endforeach
                        
                        <!-- Apply Filter Button -->
                        <button type="submit" class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 transition">
                            <i class="ti ti-search"></i>
                            <span>Terapkan Filter</span>
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Activity Table -->
        <section class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
            <div class="flex flex-col gap-3 px-6 py-5 border-b border-emerald-100 bg-emerald-50/40 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl">
                        <i class="ti ti-list"></i>
                    </span>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Daftar Aktivitas</h2>
                        <p class="text-sm text-slate-600">Riwayat lengkap perubahan data dalam sistem.</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Per Page Selector - Custom Dropdown -->
                    <div x-data="{ open: false, selected: {{ (int)$perPage }} }" class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl border border-slate-200">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide whitespace-nowrap">
                            <i class="ti ti-list-numbers mr-1 text-emerald-500"></i>Tampilkan:
                        </label>
                        <div class="relative">
                            <button type="button" @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-2 pl-3 pr-8 py-1.5 text-sm font-semibold text-slate-700 bg-white border-2 border-slate-200 rounded-lg cursor-pointer hover:border-emerald-300 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 focus:outline-none transition-all">
                                <span x-text="selected"></span>
                            </button>
                            <i class="ti ti-chevron-down text-[10px] text-slate-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none transition-transform" :class="{ 'rotate-180': open }"></i>
                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute z-50 right-0 mt-2 w-24 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden">
                                @foreach([10, 25, 50, 100] as $pp)
                                    <a href="{{ route('activities.index', array_merge(request()->query(), ['per_page' => $pp])) }}" 
                                       class="flex items-center justify-between px-4 py-2.5 text-sm cursor-pointer transition-colors {{ (int)$perPage === $pp ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-slate-50' }}">
                                        <span>{{ $pp }}</span>
                                        @if((int)$perPage === $pp)
                                            <i class="ti ti-check text-emerald-500 text-xs"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-100 rounded-xl border border-emerald-200">
                        <i class="ti ti-chart-line"></i>
                        <span>{{ $activities->total() }} Aktivitas</span>
                    </div>
                </div>
            </div>

            <!-- Bulk Delete Form -->
            <form action="{{ route('activities.bulk-delete') }}" method="POST" id="bulkDeleteForm">
                @csrf
                @method('DELETE')

                <!-- Bulk Actions Bar -->
                <div x-show="selectedIds.length > 0" x-cloak
                     class="px-6 py-3 bg-amber-50 border-b border-amber-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-amber-100 text-amber-600 rounded-full">
                            <i class="ti ti-checks"></i>
                        </span>
                        <span class="text-sm font-semibold text-amber-800" x-text="selectedIds.length + ' aktivitas dipilih'"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="clearSelection()" 
                                class="px-3 py-1.5 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                            Batal Pilih
                        </button>
                        <button type="submit" onclick="return confirm('Hapus aktivitas yang dipilih?')"
                                class="px-3 py-1.5 text-xs font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                            <i class="ti ti-trash mr-1"></i> Hapus Terpilih
                        </button>
                    </div>
                </div>

                <!-- Mobile Cards View -->
                <div class="block lg:hidden divide-y divide-slate-100">
                    @forelse ($activities as $index => $activity)
                        @php 
                            $palette = $actionPalette($activity->action);
                            $sourceModel = $activity->model;
                            $sourceLabel = $sourceLabels[$sourceModel] ?? $sourceModel ?? '-';
                            $hasRoute = $sourceModel && isset($sourceRoutes[$sourceModel]);
                        @endphp
                        <div class="p-4 {{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/40' }}">
                            <div class="flex items-start gap-3">
                                <!-- Checkbox -->
                                <label class="flex items-center pt-1">
                                    <input type="checkbox" name="ids[]" value="{{ $activity->id }}"
                                           x-model="selectedIds"
                                           class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                </label>
                                <div class="flex-1 min-w-0 space-y-2.5">
                                    <!-- Header: User & Badge -->
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-600 bg-emerald-50 rounded-lg shrink-0">
                                                <i class="ti ti-user text-xs"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $activity->user ?? 'Sistem' }}</p>
                                                <p class="text-xs text-slate-400">{{ $activity->created_at->format('d M Y, H:i') }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-[10px] font-semibold rounded-md {{ $palette['classes'] }} shrink-0">
                                            <i class="{{ $palette['icon'] }} text-[9px]"></i>
                                            <span class="hidden xs:inline">{{ $palette['label'] }}</span>
                                        </span>
                                    </div>
                                    
                                    <!-- Action Text -->
                                    <p class="text-sm text-slate-600 leading-relaxed">{{ $activity->action }}</p>
                                    
                                    <!-- Footer: Source & Time -->
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        @if($hasRoute)
                                            <a href="{{ route($sourceRoutes[$sourceModel]) }}" 
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md hover:bg-emerald-100 transition">
                                                {{ $sourceLabel }}
                                                <i class="ti ti-arrow-right text-[9px]"></i>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-md">
                                                {{ $sourceLabel }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-emerald-600 font-medium">{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8">
                            <div class="flex flex-col items-center justify-center gap-4 py-8 text-center">
                                <div class="w-20 h-20 flex items-center justify-center bg-emerald-50 rounded-2xl border-2 border-dashed border-emerald-200">
                                    <i class="ti ti-clipboard-list text-3xl text-emerald-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-slate-800">Belum ada aktivitas</h3>
                                    <p class="mt-1 text-sm text-slate-500 max-w-sm">Aktivitas sistem akan muncul di sini setelah ada perubahan data.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-slate-700 border-collapse">
                        <thead class="text-xs font-semibold tracking-wide uppercase">
                            <tr class="bg-emerald-600 text-white">
                                <th class="px-4 py-4 w-12 border border-emerald-500">
                                    <label class="flex items-center justify-center">
                                        <input type="checkbox" @change="toggleAll($event)" 
                                               class="w-4 h-4 text-emerald-600 bg-white border-white rounded focus:ring-white focus:ring-2 cursor-pointer">
                                    </label>
                                </th>
                                <th class="px-3 py-4 text-center w-14 border border-emerald-500">No.</th>
                                <th class="px-5 py-4 border border-emerald-500">
                                    <a href="{{ route('activities.index', array_merge(request()->query(), ['sort' => 'user', 'dir' => ($sortBy === 'user' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" 
                                       class="flex items-center gap-2 hover:text-emerald-100 transition">
                                        Pengguna
                                        @if($sortBy === 'user')
                                            <i class="ti {{ $sortDir === 'asc' ? 'ti-sort-ascending' : 'ti-sort-descending' }} text-emerald-200"></i>
                                        @else
                                            <i class="ti ti-arrows-sort text-emerald-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-5 py-4 border border-emerald-500">
                                    <a href="{{ route('activities.index', array_merge(request()->query(), ['sort' => 'action', 'dir' => ($sortBy === 'action' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" 
                                       class="flex items-center gap-2 hover:text-emerald-100 transition">
                                        Aktivitas
                                        @if($sortBy === 'action')
                                            <i class="ti {{ $sortDir === 'asc' ? 'ti-sort-ascending' : 'ti-sort-descending' }} text-emerald-200"></i>
                                        @else
                                            <i class="ti ti-arrows-sort text-emerald-300"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-5 py-4 border border-emerald-500 w-40">
                                    <a href="{{ route('activities.index', array_merge(request()->query(), ['sort' => 'model', 'dir' => ($sortBy === 'model' && $sortDir === 'asc') ? 'desc' : 'asc'])) }}" 
                                       class="flex items-center gap-2 hover:text-emerald-100 transition">
                                        Sumber
                                        @if($sortBy === 'model')
                                            <i class="ti {{ $sortDir === 'asc' ? 'ti-sort-ascending' : 'ti-sort-descending' }} text-emerald-200"></i>
                                        @else
                                            <i class="ti ti-arrows-sort text-emerald-300"></i>
                                        @endif
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activities as $index => $activity)
                                @php $palette = $actionPalette($activity->action); @endphp
                                <tr class="transition hover:bg-emerald-50/60 group {{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50' }}">
                                    <td class="px-4 py-4 align-middle border border-slate-200">
                                        <label class="flex items-center justify-center">
                                            <input type="checkbox" name="ids[]" value="{{ $activity->id }}"
                                                   x-model="selectedIds"
                                                   class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                        </label>
                                    </td>
                                    <td class="px-3 py-4 align-middle text-center border border-slate-200">
                                        <span class="text-sm text-slate-500">{{ $activities->firstItem() + $index }}</span>
                                    </td>
                                    <td class="px-5 py-4 align-middle border border-slate-200">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-9 h-9 text-emerald-600 bg-emerald-50 rounded-lg group-hover:scale-105 transition shrink-0">
                                                <i class="ti ti-user text-sm"></i>
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 truncate">{{ $activity->user ?? 'Sistem' }}</p>
                                                <p class="text-xs text-slate-500">{{ $activity->created_at->format('d M Y, H:i') }}</p>
                                                <p class="text-xs text-emerald-600 font-medium">{{ $activity->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 align-middle border border-slate-200">
                                        <div class="space-y-1.5">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-md {{ $palette['classes'] }}">
                                                <i class="{{ $palette['icon'] }} text-[10px]"></i>
                                                {{ $palette['label'] }}
                                            </span>
                                            <p class="text-sm text-slate-600 leading-relaxed">{{ $activity->action }}</p>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 align-middle border border-slate-200">
                                        @php
                                            $sourceModel = $activity->model;
                                            $sourceLabel = $sourceLabels[$sourceModel] ?? $sourceModel ?? '-';
                                            $hasRoute = $sourceModel && isset($sourceRoutes[$sourceModel]);
                                        @endphp
                                        @if($hasRoute)
                                            <a href="{{ route($sourceRoutes[$sourceModel]) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md hover:bg-emerald-100 hover:border-emerald-300 transition">
                                                {{ $sourceLabel }}
                                                <i class="ti ti-arrow-right text-[10px]"></i>
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-md">
                                                {{ $sourceLabel }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-16 border border-slate-200">
                                        <div class="flex flex-col items-center justify-center gap-4 text-center">
                                            <div class="w-20 h-20 flex items-center justify-center bg-emerald-50 rounded-2xl border-2 border-dashed border-emerald-200">
                                                <i class="ti ti-clipboard-list text-3xl text-emerald-400"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-slate-800">Belum ada aktivitas</h3>
                                                <p class="mt-2 text-sm text-slate-500 max-w-sm">
                                                    Aktivitas sistem akan muncul di sini setelah ada perubahan data yang terekam.
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            @if ($activities->hasPages())
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-emerald-100 bg-white">
                    <p class="text-sm text-slate-500">
                        Menampilkan <span class="font-semibold text-slate-700">{{ $activities->firstItem() ?? 0 }}-{{ $activities->lastItem() ?? 0 }}</span> dari 
                        <span class="font-semibold text-slate-700">{{ $activities->total() }}</span> aktivitas
                    </p>
                    <nav class="flex items-center gap-1">
                        {{-- Previous --}}
                        @if ($activities->onFirstPage())
                            <span class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-100 rounded-lg cursor-not-allowed">
                                <i class="ti ti-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $activities->previousPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition">
                                <i class="ti ti-chevron-left text-xs"></i>
                            </a>
                        @endif

                        {{-- Page Numbers --}}
                        @php
                            $currentPage = $activities->currentPage();
                            $lastPage = $activities->lastPage();
                            $start = max(1, $currentPage - 1);
                            $end = min($lastPage, $currentPage + 1);
                        @endphp

                        @if ($start > 1)
                            <a href="{{ $activities->url(1) }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition">1</a>
                            @if ($start > 2)
                                <span class="inline-flex items-center justify-center w-9 h-9 text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span class="inline-flex items-center justify-center w-9 h-9 text-sm font-bold text-white bg-linear-to-r from-emerald-500 to-emerald-600 rounded-lg shadow-md shadow-emerald-400/30">{{ $page }}</span>
                            @else
                                <a href="{{ $activities->url($page) }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition">{{ $page }}</a>
                            @endif
                        @endfor

                        @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                                <span class="inline-flex items-center justify-center w-9 h-9 text-slate-400">...</span>
                            @endif
                            <a href="{{ $activities->url($lastPage) }}" class="inline-flex items-center justify-center w-9 h-9 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition">{{ $lastPage }}</a>
                        @endif

                        {{-- Next --}}
                        @if ($activities->hasMorePages())
                            <a href="{{ $activities->nextPageUrl() }}" class="inline-flex items-center justify-center w-9 h-9 text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600 transition">
                                <i class="ti ti-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-9 h-9 text-slate-300 bg-slate-100 rounded-lg cursor-not-allowed">
                                <i class="ti ti-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </nav>
                </div>
            @endif
        </section>
    </div>

    <script>
        function datePicker(fieldName, initialValue) {
            const today = new Date();
            let selectedYear = today.getFullYear();
            let selectedMonth = today.getMonth();
            let selectedDay = null;
            let initialInputValue = '';
            
            // Parse initial value (format: yyyy-mm-dd from database)
            if (initialValue) {
                const parts = initialValue.split('-');
                if (parts.length === 3) {
                    selectedYear = parseInt(parts[0]);
                    selectedMonth = parseInt(parts[1]) - 1;
                    selectedDay = parseInt(parts[2]);
                    // Format as dd/mm/yyyy for display
                    initialInputValue = `${String(selectedDay).padStart(2, '0')}/${String(selectedMonth + 1).padStart(2, '0')}/${selectedYear}`;
                }
            }
            
            return {
                open: false,
                showMonthPicker: false,
                showYearPicker: false,
                selectedDate: initialValue || '',
                inputValue: initialInputValue,
                inputError: '',
                currentYear: selectedYear,
                currentMonth: selectedMonth,
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                monthShortNames: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                
                // Generate year range: 50 years back, 10 years forward
                get yearRange() {
                    const currentYear = new Date().getFullYear();
                    const startYear = currentYear - 50;
                    const endYear = currentYear + 10;
                    return Array.from({ length: endYear - startYear + 1 }, (_, i) => endYear - i);
                },
                
                get blankDays() {
                    const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                    return Array.from({ length: firstDay }, (_, i) => i);
                },
                
                get daysInMonth() {
                    const days = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                    return Array.from({ length: days }, (_, i) => i + 1);
                },
                
                // Close all dropdowns
                closeAll() {
                    this.open = false;
                    this.showMonthPicker = false;
                    this.showYearPicker = false;
                },
                
                // Handle manual input (supports: dd/mm/yyyy, dd-mm-yyyy, dd.mm.yyyy, yyyy-mm-dd)
                handleManualInput() {
                    this.inputError = '';
                    const value = this.inputValue.trim();
                    
                    if (!value) {
                        this.selectedDate = '';
                        return;
                    }
                    
                    // Patterns for different formats
                    const ddmmyyyy = /^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/;
                    const yyyymmdd = /^(\d{4})[\/\-\.](\d{1,2})[\/\-\.](\d{1,2})$/;
                    
                    let day, month, year;
                    
                    if (ddmmyyyy.test(value)) {
                        const match = value.match(ddmmyyyy);
                        day = parseInt(match[1]);
                        month = parseInt(match[2]);
                        year = parseInt(match[3]);
                    } else if (yyyymmdd.test(value)) {
                        const match = value.match(yyyymmdd);
                        year = parseInt(match[1]);
                        month = parseInt(match[2]);
                        day = parseInt(match[3]);
                    } else {
                        this.inputError = 'Format: dd/mm/yyyy';
                        return;
                    }
                    
                    // Validate
                    if (!this.validateDate(day, month, year)) {
                        return;
                    }
                    
                    // Set the date
                    this.currentYear = year;
                    this.currentMonth = month - 1;
                    this.setDateInternal(day);
                },
                
                // Validate date values
                validateDate(day, month, year) {
                    // Validate month
                    if (month < 1 || month > 12) {
                        this.inputError = 'Bulan tidak valid (1-12)';
                        return false;
                    }
                    
                    // Validate year range
                    if (year < 1975 || year > 2035) {
                        this.inputError = 'Tahun harus 1975-2035';
                        return false;
                    }
                    
                    // Validate day for the given month
                    const daysInMonth = new Date(year, month, 0).getDate();
                    if (day < 1 || day > daysInMonth) {
                        this.inputError = `Tanggal tidak valid (1-${daysInMonth})`;
                        return false;
                    }
                    
                    return true;
                },
                
                // Validate and close on Enter
                validateAndClose() {
                    this.handleManualInput();
                    if (!this.inputError && this.selectedDate) {
                        this.open = false;
                    }
                },
                
                // Set date internally without closing
                setDateInternal(day) {
                    const month = String(this.currentMonth + 1).padStart(2, '0');
                    const dayStr = String(day).padStart(2, '0');
                    this.selectedDate = `${this.currentYear}-${month}-${dayStr}`;
                    this.inputValue = `${dayStr}/${month}/${this.currentYear}`;
                    this.inputError = '';
                },
                
                prevMonth() {
                    this.showMonthPicker = false;
                    this.showYearPicker = false;
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                },
                
                nextMonth() {
                    this.showMonthPicker = false;
                    this.showYearPicker = false;
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                },
                
                selectDate(day) {
                    this.setDateInternal(day);
                    this.closeAll();
                },
                
                isSelected(day) {
                    if (!this.selectedDate) return false;
                    const parts = this.selectedDate.split('-');
                    return parseInt(parts[0]) === this.currentYear && 
                           parseInt(parts[1]) - 1 === this.currentMonth && 
                           parseInt(parts[2]) === day;
                },
                
                isToday(day) {
                    const today = new Date();
                    return today.getFullYear() === this.currentYear && 
                           today.getMonth() === this.currentMonth && 
                           today.getDate() === day;
                },
                
                clearDate() {
                    this.selectedDate = '';
                    this.inputValue = '';
                    this.inputError = '';
                    this.closeAll();
                },
                
                setToday() {
                    const today = new Date();
                    this.currentYear = today.getFullYear();
                    this.currentMonth = today.getMonth();
                    this.selectDate(today.getDate());
                },
                
                // Scroll to selected year in dropdown
                scrollToSelectedYear(el) {
                    const selectedBtn = el.querySelector(`[data-year="${this.currentYear}"]`);
                    if (selectedBtn) {
                        selectedBtn.scrollIntoView({ block: 'center', behavior: 'instant' });
                    }
                }
            };
        }
        
        function activityPage() {
            return {
                selectedIds: [],
                
                toggleAll(event) {
                    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
                    if (event.target.checked) {
                        this.selectedIds = Array.from(checkboxes).map(cb => cb.value);
                    } else {
                        this.selectedIds = [];
                    }
                    checkboxes.forEach(cb => cb.checked = event.target.checked);
                },
                
                clearSelection() {
                    this.selectedIds = [];
                    document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = false);
                    document.querySelector('thead input[type="checkbox"]').checked = false;
                }
            }
        }
    </script>
</x-app-layout>
