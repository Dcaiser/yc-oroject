<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="flex items-center gap-2 sm:gap-3 text-lg sm:text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-100 text-green-700">
                    <i class="fas fa-chart-line text-sm sm:text-base"></i>
                </span>
                <span class="truncate">Laporan Inventori</span>
            </h1>
            <div class="flex items-center px-3 py-1.5 sm:px-4 sm:py-2 font-medium text-white rounded-lg shadow transition bg-linear-to-r from-green-500 to-green-700">
                <i class="mr-1.5 sm:mr-2 fas fa-calendar-alt text-xs sm:text-sm"></i>
                <span class="text-xs sm:text-sm font-semibold truncate max-w-[150px] sm:max-w-none">{{ $periodDescription }}</span>
            </div>
        </div>
    </x-slot>

    @php
        $summary = array_merge([
            'totalActivities' => 0,
            'stockIn' => 0,
            'stockOut' => 0,
            'endingStock' => 0,
            'uniqueUsers' => 0,
        ], $summary ?? []);

        $summaryCards = [
            [
                'label' => 'Total Aktivitas',
                'value' => $summary['totalActivities'],
                'icon' => 'fa-wave-square',
                'icon_bg' => 'bg-green-100',
                'icon_color' => 'text-green-600',
            ],
            [
                'label' => 'Stok Masuk',
                'value' => $summary['stockIn'],
                'icon' => 'fa-arrow-down',
                'icon_bg' => 'bg-blue-100',
                'icon_color' => 'text-blue-600',
            ],
            [
                'label' => 'Stok Keluar',
                'value' => $summary['stockOut'],
                'icon' => 'fa-arrow-up',
                'icon_bg' => 'bg-yellow-100',
                'icon_color' => 'text-yellow-600',
            ],
            [
                'label' => 'Stok Akhir',
                'value' => $summary['endingStock'],
                'icon' => 'fa-boxes-stacked',
                'icon_bg' => 'bg-indigo-100',
                'icon_color' => 'text-indigo-600',
            ],
            [
                'label' => 'Pengguna Aktif',
                'value' => $summary['uniqueUsers'],
                'icon' => 'fa-users',
                'icon_bg' => 'bg-gray-100',
                'icon_color' => 'text-gray-600',
            ],
        ];

        $chartPayload = $chartData ?? ['labels' => [], 'datasets' => []];

        $years = $availableYears ?? [];
        if (is_object($years) && method_exists($years, 'all')) {
            $years = $years->all();
        }
        // Filter out invalid years (0 or negative)
        $years = array_filter($years, fn($year) => $year > 0);
        if (empty($years) && !empty($filters['year']) && $filters['year'] > 0) {
            $years = [$filters['year']];
        }

    $weekOptions = $weekOptions ?? [];
    $weekMonthOptions = $weekMonthOptions ?? [];

        $selectedWeekMonthLabel = 'Pilih Bulan';
        foreach ($weekMonthOptions as $option) {
            if ((int) ($option['value'] ?? -1) === (int) ($filters['week_month'] ?? -1)) {
                $selectedWeekMonthLabel = $option['label'] ?? $selectedWeekMonthLabel;
                break;
            }
        }
        if (empty($weekMonthOptions)) {
            $selectedWeekMonthLabel = 'Tidak ada bulan tersedia';
        }

        $selectedWeekYearLabel = 'Pilih Tahun';
        foreach ($years as $yearOption) {
            if ($yearOption > 0 && (int) $yearOption === (int) ($filters['week_year'] ?? -1)) {
                $selectedWeekYearLabel = (string) $yearOption;
                break;
            }
        }

        $selectedWeekLabel = 'Pilih Minggu';
        foreach ($weekOptions as $option) {
            if (($option['value'] ?? null) === ($filters['week'] ?? null)) {
                $selectedWeekLabel = $option['label'] ?? $selectedWeekLabel;
                break;
            }
        }
        if (empty($weekOptions)) {
            $selectedWeekLabel = 'Tidak ada minggu tersedia';
        }

        $selectedYearLabel = 'Pilih Tahun';
        foreach ($years as $yearOption) {
            if ($yearOption > 0 && (int) $yearOption === (int) ($filters['year'] ?? -1)) {
                $selectedYearLabel = (string) $yearOption;
                break;
            }
        }
        if (empty($years)) {
            $selectedWeekYearLabel = 'Tidak ada tahun tersedia';
            $selectedYearLabel = 'Tidak ada tahun tersedia';
        }

        $tableRows = array_reverse($tableData ?? []);
        $chunkSize = 10;
        $totalRows = count($tableRows);
        $totalSlides = $totalRows > 0 ? (int) ceil($totalRows / $chunkSize) : 1;

        $salesData = $salesTransactions ?? [];
        $totalSalesRows = count($salesData);
        $totalSalesSlides = $totalSalesRows > 0 ? (int) ceil($totalSalesRows / $chunkSize) : 1;

        $rangeSummary = null;
        $activeRangePreset = '';
        if (($filters['mode'] ?? 'range') === 'range' && !empty($filters['date_from']) && !empty($filters['date_to'])) {
            try {
                $dateFrom = \Illuminate\Support\Carbon::parse($filters['date_from'])->locale(app()->getLocale());
                $dateTo = \Illuminate\Support\Carbon::parse($filters['date_to'])->locale(app()->getLocale());

                $rangeSummary = $dateFrom->equalTo($dateTo)
                    ? $dateFrom->translatedFormat('d M Y')
                    : sprintf('%s – %s', $dateFrom->translatedFormat('d M Y'), $dateTo->translatedFormat('d M Y'));

                $today = \Illuminate\Support\Carbon::today();
                if ($dateFrom->isSameDay($today) && $dateTo->isSameDay($today)) {
                    $activeRangePreset = 'today';
                } elseif ($dateTo->isSameDay($today) && $dateFrom->isSameDay($today->copy()->subDays(6))) {
                    $activeRangePreset = 'last7';
                } elseif ($dateTo->isSameDay($today) && $dateFrom->isSameDay($today->copy()->subDays(29))) {
                    $activeRangePreset = 'last30';
                } elseif ($dateFrom->isSameDay($today->copy()->startOfMonth()) && $dateTo->isSameDay($today->copy()->endOfMonth())) {
                    $activeRangePreset = 'thisMonth';
                } elseif ($dateFrom->isSameDay($today->copy()->subMonth()->startOfMonth()) && $dateTo->isSameDay($today->copy()->subMonth()->endOfMonth())) {
                    $activeRangePreset = 'lastMonth';
                }
            } catch (\Throwable $th) {
                $rangeSummary = null;
                $activeRangePreset = '';
            }
        }

        $periodOptions = [
            'range' => [
                'label' => 'Rentang Tanggal',
                'description' => 'Analisis menggunakan rentang tanggal khusus',
                'icon' => 'fas fa-sliders-h',
                'accent' => 'emerald',
            ],
            'week' => [
                'label' => 'Mingguan',
                'description' => 'Bandingkan performa per minggu',
                'icon' => 'fas fa-calendar-week',
                'accent' => 'sky',
            ],
            'year' => [
                'label' => 'Tahunan',
                'description' => 'Lihat ringkasan per tahun',
                'icon' => 'fas fa-calendar-alt',
                'accent' => 'indigo',
            ],
        ];

        $activePeriodKey = $filters['mode'] ?? 'range';
        $activePeriod = $periodOptions[$activePeriodKey] ?? reset($periodOptions);
    @endphp

    <div class="space-y-6" x-data="{
        mode: '{{ $filters['mode'] }}',
        selectedWeek: '{{ $filters['week'] }}',
        selectedWeekMonth: {{ max(1, (int)$filters['week_month']) }},
        selectedWeekYear: {{ max(1900, (int)$filters['week_year']) }},
            rangePreset: '{{ $activeRangePreset }}',
            activeTab: 'sales',
            currentSlide: 0,
            slidesCount: {{ $totalSlides }},
            salesSlide: 0,
            salesSlidesCount: {{ $totalSalesSlides }},
            hasData: {{ json_encode($totalRows > 0) }},
            hasSalesData: {{ json_encode($totalSalesRows > 0) }},
            setRangePreset(key) {
                if (!key) {
                    return;
                }

                const today = new Date();
                let start = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                let end = new Date(today.getFullYear(), today.getMonth(), today.getDate());

                switch (key) {
                    case 'today':
                        break;
                    case 'last7':
                        start.setDate(end.getDate() - 6);
                        break;
                    case 'last30':
                        start.setDate(end.getDate() - 29);
                        break;
                    case 'thisMonth':
                        start = new Date(today.getFullYear(), today.getMonth(), 1);
                        end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                        break;
                    case 'lastMonth':
                        start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        end = new Date(today.getFullYear(), today.getMonth(), 0);
                        break;
                    default:
                        break;
                }

                this.applyRangePreset(key, start, end);
            },
            applyRangePreset(key, startDate, endDate) {
                if (!(startDate instanceof Date) || isNaN(startDate.valueOf()) || !(endDate instanceof Date) || isNaN(endDate.valueOf())) {
                    return;
                }

                if (this.mode !== 'range') {
                    this.mode = 'range';
                }

                const setInputValue = (input, value) => {
                    if (!input) {
                        return;
                    }

                    if (input._flatpickr) {
                        input._flatpickr.setDate(value, true);
                    } else {
                        input.value = value;
                    }

                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                };

                const formatDate = (date) => {
                    const year = date.getFullYear();
                    const month = `${date.getMonth() + 1}`.padStart(2, '0');
                    const day = `${date.getDate()}`.padStart(2, '0');
                    return `${year}-${month}-${day}`;
                };

                const from = formatDate(startDate);
                const to = formatDate(endDate);

                setInputValue(this.$refs.dateFromInput, from);
                setInputValue(this.$refs.dateToInput, to);

                this.rangePreset = key;
                this.submitFilters();
            },
            submitFilters() {
                const form = this.$refs.filtersForm;
                if (!form) {
                    return;
                }

                if (this.mode !== 'range') {
                    this.rangePreset = '';
                }

                setTimeout(() => {
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }, 0);
            }
        }">
        <x-breadcrumb :items="[['title' => 'Laporan Inventori']]" />

    <section class="overflow-visible bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
            <form method="GET" x-ref="filtersForm" class="p-6 bg-emerald-50/40 border-b border-emerald-100">
                <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                    <!-- Jenis Periode -->
                    <div class="flex flex-col gap-3 w-full">
                        <label class="text-sm font-semibold text-slate-700">Jenis Periode</label>
                        @php
                            $periodStyles = [
                                'emerald' => [
                                    'button' => 'border border-slate-200 bg-white hover:border-emerald-300 hover:bg-emerald-50 focus-visible:ring-emerald-200',
                                    'button_active' => 'border-emerald-300 bg-emerald-50 ring-1 ring-emerald-200 shadow-sm focus-visible:ring-emerald-200',
                                    'icon' => 'border-emerald-100 bg-emerald-50 text-emerald-600 group-hover:border-emerald-200 group-hover:bg-emerald-100',
                                    'icon_active' => 'border-emerald-400 bg-emerald-500 text-white',
                                    'label_active' => 'text-emerald-700',
                                    'desc_active' => 'text-emerald-600',
                                    'check_active' => 'border-emerald-300 bg-white text-emerald-600',
                                ],
                                'sky' => [
                                    'button' => 'border border-slate-200 bg-white hover:border-sky-300 hover:bg-sky-50 focus-visible:ring-sky-200',
                                    'button_active' => 'border-sky-300 bg-sky-50 ring-1 ring-sky-200 shadow-sm focus-visible:ring-sky-200',
                                    'icon' => 'border-sky-100 bg-sky-50 text-sky-600 group-hover:border-sky-200 group-hover:bg-sky-100',
                                    'icon_active' => 'border-sky-400 bg-sky-500 text-white',
                                    'label_active' => 'text-sky-700',
                                    'desc_active' => 'text-sky-600',
                                    'check_active' => 'border-sky-300 bg-white text-sky-600',
                                ],
                                'indigo' => [
                                    'button' => 'border border-slate-200 bg-white hover:border-indigo-300 hover:bg-indigo-50 focus-visible:ring-indigo-200',
                                    'button_active' => 'border-indigo-300 bg-indigo-50 ring-1 ring-indigo-200 shadow-sm focus-visible:ring-indigo-200',
                                    'icon' => 'border-indigo-100 bg-indigo-50 text-indigo-600 group-hover:border-indigo-200 group-hover:bg-indigo-100',
                                    'icon_active' => 'border-indigo-400 bg-indigo-500 text-white',
                                    'label_active' => 'text-indigo-700',
                                    'desc_active' => 'text-indigo-600',
                                    'check_active' => 'border-indigo-300 bg-white text-indigo-600',
                                ],
                            ];
                        @endphp
                        <div class="grid gap-2 sm:grid-cols-3">
                            @foreach($periodOptions as $value => $option)
                                @php
                                    $isActive = $activePeriodKey === $value;
                                    $palette = $periodStyles[$option['accent'] ?? 'emerald'] ?? $periodStyles['emerald'];
                                @endphp
                                <button type="button"
                                        @click="mode = '{{ $value }}'; submitFilters();"
                                        class="group relative flex items-start gap-3 rounded-xl px-4 py-3 text-left transition-all duration-300 ease-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 {{ $isActive ? $palette['button_active'] : $palette['button'] }}"
                                        :aria-pressed="mode === '{{ $value }}'">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border text-base font-semibold transition-all duration-300 {{ $isActive ? $palette['icon_active'] : $palette['icon'] }}">
                                        <i class="{{ $option['icon'] }}"></i>
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold leading-5 {{ $isActive ? $palette['label_active'] : 'text-slate-700 group-hover:text-slate-900' }}">{{ $option['label'] }}</p>
                                        <p class="mt-1 text-xs leading-relaxed {{ $isActive ? $palette['desc_active'] : 'text-slate-500 group-hover:text-slate-600' }}">{{ $option['description'] }}</p>
                                    </div>
                                    <span class="mt-1 hidden h-6 w-6 items-center justify-center rounded-full border text-xs font-semibold transition-all duration-300 {{ $isActive ? 'flex ' . $palette['check_active'] : 'text-slate-400 border-transparent group-hover:border-slate-200 group-hover:text-slate-500' }}">
                                        <i class="fas fa-check"></i>
                                    </span>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="mode" x-model="mode">
                    </div>
                    <!-- Rentang Tanggal Mode -->
                    <template x-if="mode === 'range'">
                        <div class="flex flex-col flex-1 gap-4">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700" for="date_from">Rentang Tanggal</label>
                                    <p class="text-xs text-slate-500">Pilih rentang waktu yang ingin dianalisis.</p>
                                </div>
                            </div>

                            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-5">
                                <template x-for="preset in [
                                    { key: 'today', label: 'Hari Ini', icon: 'fas fa-calendar-day' },
                                    { key: 'last7', label: '7 Hari Terakhir', icon: 'fas fa-calendar-week' },
                                    { key: 'last30', label: '30 Hari Terakhir', icon: 'fas fa-chart-line' },
                                    { key: 'thisMonth', label: 'Bulan Ini', icon: 'fas fa-calendar' },
                                    { key: 'lastMonth', label: 'Bulan Lalu', icon: 'fas fa-calendar-minus' },
                                ]" :key="preset.key">
                                    <button type="button"
                                            class="group inline-flex items-center justify-between gap-3 rounded-xl border px-3 py-2 text-xs font-semibold transition shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-1"
                                            @click="setRangePreset(preset.key)"
                                            :aria-pressed="rangePreset === preset.key"
                                            :class="rangePreset === preset.key
                                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg'
                                                : 'bg-white/90 text-slate-600 border-emerald-100 hover:border-emerald-300 hover:text-emerald-700'">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 transition"
                                              :class="rangePreset === preset.key ? 'bg-white/20 text-white' : ''">
                                            <i :class="preset.icon"></i>
                                        </span>
                                        <span class="flex-1 text-left" x-text="preset.label"></span>
                                        <span class="hidden text-emerald-200" :class="rangePreset === preset.key ? 'block' : 'hidden'">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    </button>
                                </template>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-xl border border-emerald-100/80 bg-white/70 p-3 shadow-sm ring-1 ring-emerald-100/60">
                                    <label for="date_from" class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Dari</label>
                                    <div class="relative mt-2">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                                            <i class="fas fa-calendar-day"></i>
                                        </span>
                                        <input id="date_from" x-ref="dateFromInput" type="text" name="date_from" value="{{ $filters['date_from'] }}" @change="rangePreset = ''; submitFilters()" data-datepicker
                                               placeholder="yyyy-mm-dd" autocomplete="off"
                                               class="w-full py-2.5 pl-12 pr-4 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>
                                </div>
                                <div class="rounded-xl border border-emerald-100/80 bg-white/70 p-3 shadow-sm ring-1 ring-emerald-100/60">
                                    <label for="date_to" class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Sampai</label>
                                    <div class="relative mt-2">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                                            <i class="fas fa-calendar-check"></i>
                                        </span>
                                        <input id="date_to" x-ref="dateToInput" type="text" name="date_to" value="{{ $filters['date_to'] }}" @change="rangePreset = ''; submitFilters()" data-datepicker
                                               placeholder="yyyy-mm-dd" autocomplete="off"
                                               class="w-full py-2.5 pl-12 pr-4 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Mingguan Mode -->
                    <template x-if="mode === 'week'">
                        <div class="flex flex-col gap-4 flex-1 sm:flex-row sm:flex-wrap">
                            <div class="flex flex-col gap-2 flex-1 min-w-[200px] sm:max-w-[260px]">
                                <label for="week_month" class="text-sm font-semibold text-slate-700">Pilih Bulan</label>
                                <div class="relative" data-styled-select>
                                    <select id="week_month" name="week_month" x-model="selectedWeekMonth" @change="submitFilters()"
                                            class="sr-only" tabindex="-1" aria-hidden="true">
                                        @foreach($weekMonthOptions as $option)
                                            <option value="{{ $option['value'] }}" @selected((int) $option['value'] === (int) $filters['week_month'])>
                                                {{ $option['label'] }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <button type="button"
                                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                            data-select-trigger
                                            aria-haspopup="listbox"
                                            aria-expanded="false">
                                        <span class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600">
                                                <i class="fas fa-calendar-days"></i>
                                            </span>
                                            <span class="flex flex-col text-left leading-tight">
                                                <span class="text-sm font-semibold text-slate-900" data-select-label>{{ $selectedWeekMonthLabel }}</span>
                                                <span class="text-xs font-medium text-slate-500">Pilih bulan analisis</span>
                                            </span>
                                        </span>
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 rounded-xl transition" data-select-arrow>
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </button>

                                    <div class="absolute left-0 right-0 z-50 hidden mt-2 origin-top transform scale-95 opacity-0 pointer-events-none transition-all duration-150 overflow-hidden bg-white border border-emerald-100 rounded-2xl shadow-xl"
                                         data-select-menu
                                         role="listbox">
                                        <div class="py-2 max-h-64 overflow-y-auto">
                                            @foreach($weekMonthOptions as $option)
                                                @php
                                                    $isActive = (int) $option['value'] === (int) $filters['week_month'];
                                                @endphp
                        <button type="button"
                            class="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-sm text-left transition rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0 {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                                                        data-select-option
                                                        data-value="{{ $option['value'] }}"
                                                        data-label="{{ $option['label'] }}"
                                                        data-option-base="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-sm text-left transition rounded-xl text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0">
                                                    <span class="flex items-center gap-3">
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500">
                                                            <i class="fas fa-calendar-week"></i>
                                                        </span>
                                                        <span>{{ $option['label'] }}</span>
                                                    </span>
                                                    <span class="text-emerald-500">
                                                        <i class="fas fa-check {{ $isActive ? '' : 'opacity-0' }}" data-active-icon></i>
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 flex-1 min-w-[200px]">
                                <label for="week_year" class="text-sm font-semibold text-slate-700">Pilih Tahun</label>
                                <div class="relative" data-styled-select>
                                    <select id="week_year" name="week_year" x-model="selectedWeekYear" @change="submitFilters()"
                                            class="sr-only" tabindex="-1" aria-hidden="true">
                                        @foreach($years as $year)
                                            @if($year > 0)
                                                <option value="{{ $year }}" @selected((int) $year === (int) $filters['week_year'])>{{ $year }}</option>
                                            @endif
                                        @endforeach
                                    </select>

                                    <button type="button"
                                            class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                            data-select-trigger
                                            aria-haspopup="listbox"
                                            aria-expanded="false">
                                        <span class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                        	                <span class="flex flex-col text-left leading-tight">
                                                <span class="text-sm font-semibold text-slate-900" data-select-label>{{ $selectedWeekYearLabel }}</span>
                                                <span class="text-xs font-medium text-slate-500">Tentukan tahun laporan</span>
                                            </span>
                                        </span>
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 rounded-xl transition" data-select-arrow>
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </button>

                                    <div class="absolute left-0 right-0 z-50 hidden mt-2 origin-top transform scale-95 opacity-0 pointer-events-none transition-all duration-150 overflow-hidden bg-white border border-emerald-100 rounded-2xl shadow-xl"
                                         data-select-menu
                                         role="listbox">
                                        <div class="py-2 max-h-64 overflow-y-auto">
                                            @foreach($years as $year)
                                                @continue($year <= 0)
                                                @php
                                                    $isActive = (int) $year === (int) $filters['week_year'];
                                                @endphp
                        <button type="button"
                            class="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-sm text-left transition rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0 {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                                                        data-select-option
                                                        data-value="{{ $year }}"
                                                        data-label="{{ $year }}"
                                                        data-option-base="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-sm text-left transition rounded-xl text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0">
                                                    <span class="flex items-center gap-3">
                                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500">
                                                            <i class="fas fa-calendar-alt"></i>
                                                        </span>
                                                        <span>{{ $year }}</span>
                                                    </span>
                                                    <span class="text-emerald-500">
                                                        <i class="fas fa-check {{ $isActive ? '' : 'opacity-0' }}" data-active-icon></i>
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 flex-1 min-w-[200px] sm:max-w-[280px]">
                                <label for="week" class="text-sm font-semibold text-slate-700">Rentang Minggu</label>
                                <div class="relative" data-styled-select>
                                    <select id="week" name="week" x-model="selectedWeek" @change="submitFilters()"
                                            class="sr-only" tabindex="-1" aria-hidden="true">
                                        @forelse($weekOptions as $option)
                                            <option value="{{ $option['value'] }}" @selected($option['value'] === $filters['week'])>
                                                {{ $option['label'] }}
                                            </option>
                                        @empty
                                            <option value="">Tidak ada minggu tersedia</option>
                                        @endforelse
                                    </select>

                                    <button type="button"
                                            class="w-full flex items-center justify-between gap-2.5 px-3 py-2 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                            data-select-trigger
                                            aria-haspopup="listbox"
                                            aria-expanded="false">
                                        <span class="flex items-center gap-3">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600">
                                                <i class="fas fa-calendar-week"></i>
                                            </span>
                                            <span class="flex flex-col text-left leading-snug">
                                                <span class="text-sm font-semibold text-slate-900 whitespace-normal break-words" data-select-label>{{ $selectedWeekLabel }}</span>
                                                <span class="text-xs font-medium text-slate-500">Pilih rentang minggu aktif</span>
                                            </span>
                                        </span>
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 rounded-xl transition" data-select-arrow>
                                            <i class="fas fa-chevron-down"></i>
                                        </span>
                                    </button>

                                    <div class="absolute left-0 right-0 z-50 hidden mt-2 origin-top transform scale-95 opacity-0 pointer-events-none transition-all duration-150 overflow-hidden bg-white border border-emerald-100 rounded-2xl shadow-xl"
                                         data-select-menu
                                         role="listbox">
                                        <div class="py-2 max-h-64 overflow-y-auto">
                                            @forelse($weekOptions as $option)
                                                @php
                                                    $isActive = ($option['value'] ?? null) === ($filters['week'] ?? null);
                                                @endphp
                        <button type="button"
                            class="w-full px-4 py-2.5 flex items-start justify-between gap-3 text-sm text-left transition rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0 {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                                                        data-select-option
                                                        data-value="{{ $option['value'] }}"
                                                        data-label="{{ $option['label'] }}"
                                                        data-option-base="w-full px-4 py-2.5 flex items-start justify-between gap-3 text-sm text-left transition rounded-xl text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0">
                                                    <span class="flex flex-col gap-1">
                                                        <span class="inline-flex items-center gap-2">
                                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500">
                                                                <i class="fas fa-chart-line"></i>
                                                            </span>
                                                            <span class="font-semibold">{{ $option['label'] }}</span>
                                                        </span>
                                                        @if(!empty($option['description']))
                                                            <span class="pl-10 text-xs font-medium text-slate-500">{{ $option['description'] }}</span>
                                                        @endif
                                                    </span>
                                                    <span class="text-emerald-500">
                                                        <i class="fas fa-check {{ $isActive ? '' : 'opacity-0' }}" data-active-icon></i>
                                                    </span>
                                                </button>
                                            @empty
                                                <div class="px-4 py-2.5 text-sm font-medium text-slate-500">Tidak ada minggu tersedia</div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Tahunan Mode -->
                    <template x-if="mode === 'year'">
                        <div class="flex flex-col gap-2 flex-1 min-w-[220px]">
                            <label for="year" class="text-sm font-semibold text-slate-700">Pilih Tahun</label>
                            <div class="relative" data-styled-select>
                                <select id="year" name="year" @change="submitFilters()"
                                        class="sr-only" tabindex="-1" aria-hidden="true">
                                    @foreach($years as $year)
                                        @if($year > 0)
                                            <option value="{{ $year }}" @selected($year == $filters['year'])>{{ $year }}</option>
                                        @endif
                                    @endforeach
                                </select>

                                <button type="button"
                                        class="w-full flex items-center justify-between gap-3 px-3 py-2.5 text-sm font-semibold text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                        data-select-trigger
                                        aria-haspopup="listbox"
                                        aria-expanded="false">
                                    <span class="flex items-center gap-3">
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600">
                                            <i class="fas fa-business-time"></i>
                                        </span>
                                        <span class="flex flex-col text-left leading-tight">
                                            <span class="text-sm font-semibold text-slate-900" data-select-label>{{ $selectedYearLabel }}</span>
                                            <span class="text-xs font-medium text-slate-500">Lihat ringkasan tahunan</span>
                                        </span>
                                    </span>
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 rounded-xl transition" data-select-arrow>
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                </button>

                                <div class="absolute left-0 right-0 z-50 hidden mt-2 origin-top transform scale-95 opacity-0 pointer-events-none transition-all duration-150 overflow-hidden bg-white border border-emerald-100 rounded-2xl shadow-xl"
                                     data-select-menu
                                     role="listbox">
                                    <div class="py-2 max-h-64 overflow-y-auto">
                                        @foreach($years as $year)
                                            @continue($year <= 0)
                                            @php
                                                $isActive = (int) $year === (int) $filters['year'];
                                            @endphp
                        <button type="button"
                            class="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-sm text-left transition rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0 {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                                                    data-select-option
                                                    data-value="{{ $year }}"
                                                    data-label="{{ $year }}"
                                                    data-option-base="w-full px-4 py-2.5 flex items-center justify-between gap-3 text-sm text-left transition rounded-xl text-slate-600 hover:bg-emerald-50 hover:text-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-300 focus-visible:ring-offset-0">
                                                <span class="flex items-center gap-3">
                                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </span>
                                                    <span>{{ $year }}</span>
                                                </span>
                                                <span class="text-emerald-500">
                                                    <i class="fas fa-check {{ $isActive ? '' : 'opacity-0' }}" data-active-icon></i>
                                                </span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Tombol Reset -->
                    <div class="shrink-0 flex flex-col gap-2">
                        <label class="text-sm font-semibold text-slate-700 opacity-0 pointer-events-none select-none" aria-hidden="true">Aksi</label>
                        <a href="{{ route('reports.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                            <i class="fas fa-rotate-right"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 items-stretch">
            @foreach($summaryCards as $card)
                <div class="p-5 bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100 h-full flex flex-col justify-center">
                    <div class="flex items-center gap-4">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-xl {{ $card['icon_bg'] }} {{ $card['icon_color'] }}">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </span>
                        <div class="space-y-1">
                            <p class="text-xs font-semibold tracking-wide uppercase text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-2xl font-bold text-slate-800">{{ number_format($card['value']) }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <div class="lg:col-span-2 overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex flex-col gap-1 p-6 sm:flex-row sm:items-center sm:justify-between bg-emerald-50/40 border-b border-emerald-100">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Aktivitas</h3>
                        <p class="text-sm text-slate-600">Grafik garis untuk aktivitas, stok masuk/keluar, dan stok akhir.</p>
                    </div>
                    <span class="text-xs font-semibold uppercase text-emerald-600">{{ $filters['mode'] === 'year' ? 'Per bulan' : 'Per hari' }}</span>
                </div>
                <div class="p-6 pb-4">
                    <div class="h-80">
                        <canvas id="reports-line-chart" aria-label="Grafik aktivitas" role="img"></canvas>
                    </div>
                </div>
            </div>

            <aside class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                <div class="flex items-center justify-between p-6 border-b border-emerald-100 bg-emerald-50/40">
                    <h3 class="text-lg font-semibold text-slate-900">Aktivitas Terbaru</h3>
                    <a href="{{ route('activities.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">Lihat Semua</a>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start gap-3 p-3 transition border border-emerald-100 rounded-xl bg-white hover:bg-emerald-50/60">
                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-emerald-100 text-emerald-600">
                                <i class="fas fa-clipboard-list"></i>
                            </span>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $activity->action }}</p>
                                <p class="text-xs text-slate-500">{{ optional($activity->created_at)->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada aktivitas pada periode ini.</p>
                    @endforelse
                </div>
            </aside>
        </section>

        <section class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
            <div class="flex flex-col gap-2 p-6 sm:flex-row sm:items-center sm:justify-between bg-emerald-50/40 border-b border-emerald-100">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900">Data Laporan</h3>
                    <p class="text-sm text-slate-600">Pilih tab untuk melihat transaksi penjualan atau pergerakan stok.</p>
                </div>
                <div class="flex gap-2">
                    <a :href="`{{ route('reports.export-sales') }}?` + new URLSearchParams({
                        mode: mode,
                        date_from: '{{ $filters['date_from'] }}',
                        date_to: '{{ $filters['date_to'] }}',
                        week: selectedWeek,
                        week_month: selectedWeekMonth,
                        week_year: selectedWeekYear,
                        year: '{{ $filters['year'] }}',
                        format: 'excel'
                    }).toString()" 
                    x-show="activeTab === 'sales'"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm">
                        <i class="fas fa-file-excel"></i>
                        Excel
                    </a>
                    <a :href="`{{ route('reports.export-sales') }}?` + new URLSearchParams({
                        mode: mode,
                        date_from: '{{ $filters['date_from'] }}',
                        date_to: '{{ $filters['date_to'] }}',
                        week: selectedWeek,
                        week_month: selectedWeekMonth,
                        week_year: selectedWeekYear,
                        year: '{{ $filters['year'] }}',
                        format: 'pdf'
                    }).toString()"
                    x-show="activeTab === 'sales'"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition bg-red-600 rounded-xl hover:bg-red-700 shadow-sm">
                        <i class="fas fa-file-pdf"></i>
                        PDF
                    </a>
                    
                    <a :href="`{{ route('reports.export-stock') }}?` + new URLSearchParams({
                        mode: mode,
                        date_from: '{{ $filters['date_from'] }}',
                        date_to: '{{ $filters['date_to'] }}',
                        week: selectedWeek,
                        week_month: selectedWeekMonth,
                        week_year: selectedWeekYear,
                        year: '{{ $filters['year'] }}',
                        format: 'excel'
                    }).toString()"
                    x-show="activeTab === 'stock'"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm">
                        <i class="fas fa-file-excel"></i>
                        Excel
                    </a>
                    <a :href="`{{ route('reports.export-stock') }}?` + new URLSearchParams({
                        mode: mode,
                        date_from: '{{ $filters['date_from'] }}',
                        date_to: '{{ $filters['date_to'] }}',
                        week: selectedWeek,
                        week_month: selectedWeekMonth,
                        week_year: selectedWeekYear,
                        year: '{{ $filters['year'] }}',
                        format: 'pdf'
                    }).toString()"
                    x-show="activeTab === 'stock'"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition bg-red-600 rounded-xl hover:bg-red-700 shadow-sm">
                        <i class="fas fa-file-pdf"></i>
                        PDF
                    </a>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="flex gap-1 px-6 pt-4 border-b border-emerald-100 bg-white">
                <button type="button" @click="activeTab = 'sales'; salesSlide = 0" class="relative px-6 py-3 text-sm font-semibold transition rounded-t-xl" :class="activeTab === 'sales' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-50'">
                    <span>Transaksi Penjualan</span>
                    <span x-show="activeTab === 'sales'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600"></span>
                </button>
                <button type="button" @click="activeTab = 'stock'; currentSlide = 0" class="relative px-6 py-3 text-sm font-semibold transition rounded-t-xl" :class="activeTab === 'stock' ? 'text-emerald-700 bg-emerald-50' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-50'">
                    <span>Pergerakan Stok</span>
                    <span x-show="activeTab === 'stock'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-600"></span>
                </button>
            </div>

            <!-- Transaksi Penjualan Tab -->
            <div x-show="activeTab === 'sales'"
                 x-cloak
                 x-transition:enter="transform transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transform transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-3 scale-95">
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-slate-700">
                            <thead class="text-xs font-semibold tracking-wide uppercase bg-slate-50 text-slate-600 ring-1 ring-slate-100">
                                <tr>
                                    <th class="px-3 py-4 w-16">No.</th>
                                    <th class="px-4 py-4">Tanggal</th>
                                    <th class="px-4 py-4">Customer</th>
                                    <th class="px-4 py-4">Tipe</th>
                                    <th class="px-4 py-4">Produk</th>
                                    <th class="px-4 py-4 text-right">Qty</th>
                                    <th class="px-4 py-4 text-right">Harga Satuan</th>
                                    <th class="px-4 py-4 text-right">Total Harga</th>
                                    <th class="px-4 py-4 text-right">Ongkir</th>
                                    <th class="px-4 py-4 text-right">Grand Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-100">
                                @forelse($salesData as $index => $sale)
                                    @php $chunkIndex = (int) floor($index / $chunkSize); @endphp
                                    <tr x-show="hasSalesData && salesSlide === {{ $chunkIndex }}" x-transition x-cloak class="transition hover:bg-emerald-50/70">
                                        <td class="px-3 py-3 font-semibold text-slate-600">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-slate-800">{{ $sale['date'] }}</td>
                                        <td class="px-4 py-3 font-semibold text-slate-900">{{ $sale['customer_name'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $sale['customer_type'] === 'agent' ? 'bg-blue-100 text-blue-700' : ($sale['customer_type'] === 'reseller' ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-700') }}">
                                                {{ ucfirst($sale['customer_type']) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-slate-800">{{ $sale['product_name'] }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($sale['qty']) }} {{ $sale['satuan'] }}</td>
                                        <td class="px-4 py-3 text-right">Rp {{ number_format($sale['price_per_unit'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-slate-900">Rp {{ number_format($sale['total_price'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right">Rp {{ number_format($sale['shipping_cost'], 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-emerald-700">Rp {{ number_format($sale['grand_total'], 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3 text-emerald-600">
                                                <span class="text-4xl"><i class="fas fa-shopping-cart"></i></span>
                                                <h3 class="text-lg font-semibold text-slate-800">Tidak ada transaksi penjualan</h3>
                                                <p class="text-sm text-slate-500">Belum ada transaksi pada periode ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="hidden" aria-hidden="true" x-effect="if (!hasSalesData) { salesSlide = 0; } else if (salesSlide >= salesSlidesCount) { salesSlide = Math.max(salesSlidesCount - 1, 0); }"></div>

                <div class="flex flex-col gap-3 px-6 py-4 border-t border-emerald-100 sm:flex-row sm:items-center sm:justify-between bg-white">
                    <p class="text-sm text-slate-500" x-text="hasSalesData ? `Halaman ${salesSlide + 1} dari ${salesSlidesCount}` : 'Tidak ada data untuk ditampilkan'"></p>
                    <div class="flex gap-2">
                        <button type="button" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100 disabled:opacity-50 disabled:cursor-not-allowed" @click="salesSlide = Math.max(salesSlide - 1, 0)" :disabled="!hasSalesData || salesSlide === 0">
                            <i class="fas fa-chevron-left"></i>
                            Sebelumnya
                        </button>
                        <button type="button" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100 disabled:opacity-50 disabled:cursor-not-allowed" @click="salesSlide = Math.min(salesSlide + 1, salesSlidesCount - 1)" :disabled="!hasSalesData || salesSlide >= salesSlidesCount - 1">
                            Berikutnya
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pergerakan Stok Tab -->
            <div x-show="activeTab === 'stock'"
                 x-cloak
                 x-transition:enter="transform transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transform transition ease-in duration-300"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-3 scale-95">
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-slate-700">
                            <thead class="text-xs font-semibold tracking-wide uppercase bg-slate-50 text-slate-600 ring-1 ring-slate-100">
                                <tr>
                                    <th class="px-3 py-4 w-16">No.</th>
                                    <th class="px-4 py-4">Periode</th>
                                    <th class="px-4 py-4 text-right">Total Aktivitas</th>
                                    <th class="px-4 py-4 text-right">Stok Masuk</th>
                                    <th class="px-4 py-4 text-right">Stok Keluar</th>
                                    <th class="px-4 py-4 text-right">Stok Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-100">
                                @forelse($tableRows as $index => $row)
                                    @php $chunkIndex = (int) floor($index / $chunkSize); @endphp
                                    <tr x-show="hasData && currentSlide === {{ $chunkIndex }}" x-transition x-cloak class="transition hover:bg-emerald-50/70">
                                        <td class="px-3 py-3 font-semibold text-slate-600">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-semibold text-slate-800">{{ $row['label'] }}</td>
                                        <td class="px-4 py-3 text-right font-semibold text-slate-900">{{ number_format($row['total']) }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($row['stock_in']) }}</td>
                                        <td class="px-4 py-3 text-right">{{ number_format($row['stock_out']) }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-indigo-700">{{ number_format($row['ending_stock']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3 text-emerald-600">
                                                <span class="text-4xl"><i class="fas fa-database"></i></span>
                                                <h3 class="text-lg font-semibold text-slate-800">Tidak ada data</h3>
                                                <p class="text-sm text-slate-500">Sesuaikan filter untuk menampilkan aktivitas.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="hidden" aria-hidden="true" x-effect="if (!hasData) { currentSlide = 0; } else if (currentSlide >= slidesCount) { currentSlide = Math.max(slidesCount - 1, 0); }"></div>

                <div class="flex flex-col gap-3 px-6 py-4 border-t border-emerald-100 sm:flex-row sm:items-center sm:justify-between bg-white">
                    <p class="text-sm text-slate-500" x-text="hasData ? `Halaman ${currentSlide + 1} dari ${slidesCount}` : 'Tidak ada data untuk ditampilkan'"></p>
                    <div class="flex gap-2">
                        <button type="button" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100 disabled:opacity-50 disabled:cursor-not-allowed" @click="currentSlide = Math.max(currentSlide - 1, 0)" :disabled="!hasData || currentSlide === 0">
                            <i class="fas fa-chevron-left"></i>
                            Sebelumnya
                        </button>
                        <button type="button" class="inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100 disabled:opacity-50 disabled:cursor-not-allowed" @click="currentSlide = Math.min(currentSlide + 1, slidesCount - 1)" :disabled="!hasData || currentSlide >= slidesCount - 1">
                            Berikutnya
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dropdowns = document.querySelectorAll('[data-styled-select]');
                const globalHiddenClasses = ['hidden', 'opacity-0', 'scale-95', 'pointer-events-none'];

                dropdowns.forEach((dropdown) => {
                    const select = dropdown.querySelector('select');
                    const trigger = dropdown.querySelector('[data-select-trigger]');
                    const menu = dropdown.querySelector('[data-select-menu]');
                    const labelEl = dropdown.querySelector('[data-select-label]');
                    const arrowEl = dropdown.querySelector('[data-select-arrow]');
                    const optionButtons = Array.from(dropdown.querySelectorAll('[data-select-option]'));

                    if (!select || !trigger || !menu) {
                        return;
                    }

                    const optionBaseClasses = (button) => button.dataset.optionBase || '';
                    let isOpen = false;

                    const closeMenu = (focusTrigger = false) => {
                        if (!isOpen) {
                            return;
                        }
                        isOpen = false;
                        globalHiddenClasses.forEach((cls) => menu.classList.add(cls));
                        trigger.setAttribute('aria-expanded', 'false');
                        if (arrowEl) {
                            arrowEl.classList.remove('rotate-180');
                            arrowEl.classList.remove('bg-emerald-100/80');
                            arrowEl.classList.remove('text-emerald-700');
                            arrowEl.classList.add('text-emerald-500');
                        }
                        if (focusTrigger && !trigger.disabled) {
                            trigger.focus();
                        }
                    };

                    const openMenu = () => {
                        if (isOpen || trigger.disabled) {
                            return;
                        }
                        isOpen = true;
                        menu.classList.remove('hidden');
                        requestAnimationFrame(() => {
                            menu.classList.remove('opacity-0');
                            menu.classList.remove('scale-95');
                            menu.classList.remove('pointer-events-none');
                        });
                        trigger.setAttribute('aria-expanded', 'true');
                        if (arrowEl) {
                            arrowEl.classList.add('rotate-180');
                            arrowEl.classList.add('bg-emerald-100/80');
                            arrowEl.classList.remove('text-emerald-500');
                            arrowEl.classList.add('text-emerald-700');
                        }
                    };

                    const setActiveOption = (button, shouldSubmit = true) => {
                        if (!button) {
                            return;
                        }

                        const value = button.dataset.value ?? '';
                        const label = button.dataset.label || button.textContent.trim();

                        if (labelEl) {
                            labelEl.textContent = label;
                        }

                        optionButtons.forEach((btn) => {
                            const base = optionBaseClasses(btn);
                            const activeIcon = btn.querySelector('[data-active-icon]');
                            if (btn === button) {
                                btn.className = `${base} bg-emerald-50 text-emerald-700 font-semibold`.trim();
                                if (activeIcon) {
                                    activeIcon.classList.remove('opacity-0');
                                }
                            } else {
                                btn.className = base.trim();
                                if (activeIcon) {
                                    activeIcon.classList.add('opacity-0');
                                }
                            }
                        });

                        if (select.value !== value) {
                            select.value = value;
                            select.dispatchEvent(new Event('input', { bubbles: true }));
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        if (shouldSubmit === false) {
                            return;
                        }
                    };

                    const optionCount = optionButtons.length;
                    trigger.disabled = optionCount === 0;
                    trigger.setAttribute('aria-disabled', optionCount === 0 ? 'true' : 'false');
                    trigger.classList.toggle('cursor-not-allowed', optionCount === 0);
                    trigger.classList.toggle('opacity-60', optionCount === 0);

                    trigger.addEventListener('click', (event) => {
                        if (trigger.disabled) {
                            return;
                        }
                        event.preventDefault();
                        if (isOpen) {
                            closeMenu();
                        } else {
                            openMenu();
                        }
                    });

                    trigger.addEventListener('keydown', (event) => {
                        if (trigger.disabled) {
                            return;
                        }
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            isOpen ? closeMenu() : openMenu();
                        } else if (event.key === 'ArrowDown') {
                            event.preventDefault();
                            if (!isOpen) {
                                openMenu();
                            }
                            optionButtons[0]?.focus();
                        }
                    });

                    optionButtons.forEach((button, index) => {
                        button.setAttribute('tabindex', '0');

                        button.addEventListener('click', () => {
                            setActiveOption(button);
                            closeMenu(true);
                        });

                        button.addEventListener('keydown', (event) => {
                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                setActiveOption(button);
                                closeMenu(true);
                            } else if (event.key === 'Escape') {
                                event.preventDefault();
                                closeMenu(true);
                            } else if (event.key === 'ArrowDown') {
                                event.preventDefault();
                                const next = optionButtons[index + 1] || optionButtons[0];
                                next?.focus();
                            } else if (event.key === 'ArrowUp') {
                                event.preventDefault();
                                const prev = optionButtons[index - 1] || optionButtons[optionButtons.length - 1];
                                prev?.focus();
                            }
                        });
                    });

                    document.addEventListener('click', (event) => {
                        if (!dropdown.contains(event.target)) {
                            closeMenu();
                        }
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeMenu(true);
                        }
                    });

                    select.addEventListener('change', () => {
                        const active = optionButtons.find((btn) => btn.dataset.value === select.value);
                        if (active) {
                            setActiveOption(active, false);
                        }
                    });

                    const initialOption = optionButtons.find((btn) => btn.dataset.value === select.value) || optionButtons[0];
                    if (initialOption) {
                        setActiveOption(initialOption, false);
                    }
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const canvas = document.getElementById('reports-line-chart');
                if (!canvas) {
                    return;
                }

                const ctx = canvas.getContext('2d');
                const payload = @json($chartPayload);
                const labels = payload.labels ?? [];
                const labelCount = labels.length;
                const denseThreshold = 40;
                const labelStep = labelCount > 18 ? Math.ceil(labelCount / 12) : 1;
                const hidePoints = labelCount > denseThreshold;

                const datasets = [
                    { key: 'total', label: 'Total Aktivitas', color: '#16a34a' },
                    { key: 'stock_in', label: 'Stok Masuk', color: '#0284c7' },
                    { key: 'stock_out', label: 'Stok Keluar', color: '#f59e0b' },
                    { key: 'ending_stock', label: 'Stok Akhir', color: '#6366f1' },
                ];

                const toRGBA = (hex, alpha) => {
                    const sanitized = hex.replace('#', '');
                    const bigint = parseInt(sanitized, 16);
                    const r = (bigint >> 16) & 255;
                    const g = (bigint >> 8) & 255;
                    const b = bigint & 255;
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                };

                const gradientCache = new Map();
                const getGradient = (color) => {
                    if (gradientCache.has(color)) {
                        return gradientCache.get(color);
                    }

                    const height = canvas.offsetHeight || canvas.clientHeight || 300;
                    const gradient = ctx.createLinearGradient(0, 0, 0, height);
                    gradient.addColorStop(0, toRGBA(color, 0.2));
                    gradient.addColorStop(1, toRGBA(color, 0.02));

                    gradientCache.set(color, gradient);
                    return gradient;
                };

                const formatNumber = (value) => new Intl.NumberFormat('id-ID').format(Number(value) || 0);

                let globalMax = 0;
                let globalMin = 0;

                const series = datasets.map((cfg) => {
                    const rawData = Array.isArray(payload.datasets?.[cfg.key]) ? payload.datasets[cfg.key] : [];
                    const parsedData = rawData.map((value) => Number(value) || 0);
                    const localMax = parsedData.length ? Math.max(...parsedData) : 0;
                    const localMin = parsedData.length ? Math.min(...parsedData) : 0;
                    globalMax = Math.max(globalMax, localMax);
                    globalMin = Math.min(globalMin, localMin);

                    const isFlat = parsedData.every((value) => value === 0);

                    return {
                        label: cfg.label,
                        data: parsedData,
                        meta: { localMax, localMin },
                        style: {
                            borderColor: cfg.color,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: cfg.color,
                            backgroundColor: getGradient(cfg.color),
                        },
                        hidden: isFlat,
                    };
                });

                const yPadding = globalMax > 0 ? Math.max(globalMax * 0.05, 2) : 2;
                const suggestedMax = globalMax > 0 ? globalMax + yPadding : 5;
                const suggestedMin = globalMin < 0 ? globalMin - Math.abs(globalMin) * 0.05 : 0;

                const hoverGuidePlugin = {
                    id: 'hoverGuide',
                    afterDraw(chart) {
                        const activeElements = chart.tooltip?.getActiveElements?.();
                        if (!activeElements || !activeElements.length) {
                            return;
                        }

                        const { ctx: chartCtx, chartArea } = chart;
                        const [{ element }] = activeElements;
                        chartCtx.save();
                        chartCtx.beginPath();
                        chartCtx.moveTo(element.x, chartArea.top);
                        chartCtx.lineTo(element.x, chartArea.bottom);
                        chartCtx.lineWidth = 1;
                        chartCtx.setLineDash([6, 4]);
                        chartCtx.strokeStyle = 'rgba(15, 118, 110, 0.35)';
                        chartCtx.stroke();
                        chartCtx.restore();
                    },
                };

                const chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: series.map((entry) => ({
                            label: entry.label,
                            data: entry.data,
                            borderColor: entry.style.borderColor,
                            backgroundColor: entry.style.backgroundColor,
                            pointBackgroundColor: entry.style.pointBackgroundColor,
                            pointBorderColor: entry.style.borderColor,
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: hidePoints ? 0 : 3,
                            pointHoverRadius: hidePoints ? 4 : 6,
                            pointBorderWidth: hidePoints ? 0 : 2,
                            hitRadius: hidePoints ? 6 : 10,
                            fill: entry.label === 'Total Aktivitas' ? 'origin' : 'start',
                            spanGaps: true,
                            hidden: entry.hidden,
                        })),
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { top: 8, right: 12, bottom: 0, left: 8 },
                        },
                        elements: {
                            line: {
                                capBezierPoints: true,
                            },
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    usePointStyle: true,
                                    padding: 12,
                                    boxWidth: 10,
                                },
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(15, 118, 110, 0.95)',
                                titleColor: '#f8fafc',
                                bodyColor: '#f8fafc',
                                borderColor: 'rgba(45, 212, 191, 0.4)',
                                borderWidth: 1,
                                padding: 14,
                                callbacks: {
                                    title(contexts) {
                                        return contexts?.[0]?.label ?? '';
                                    },
                                    label(context) {
                                        const value = formatNumber(context.parsed.y);
                                        return `${context.dataset.label}: ${value}`;
                                    },
                                    footer(contexts) {
                                        const sum = contexts.reduce((total, item) => total + (item.parsed?.y ?? 0), 0);
                                        return `Total: ${formatNumber(sum)}`;
                                    },
                                },
                            },
                        },
                        interaction: {
                            mode: 'nearest',
                            intersect: false,
                            axis: 'x',
                        },
                        scales: {
                            y: {
                                beginAtZero: suggestedMin >= 0,
                                suggestedMax,
                                suggestedMin,
                                ticks: {
                                    precision: 0,
                                    maxTicksLimit: 6,
                                    callback: (value) => formatNumber(value),
                                    padding: 4,
                                },
                                grid: {
                                    color: '#e2e8f0',
                                    drawTicks: false,
                                    borderDash: [4, 6],
                                },
                            },
                            x: {
                                ticks: {
                                    maxRotation: 0,
                                    autoSkip: false,
                                    padding: 4,
                                    callback(value, index, ticks) {
                                        if (labelStep <= 1) {
                                            return ticks[index]?.label ?? '';
                                        }
                                        return index % labelStep === 0 ? ticks[index]?.label ?? '' : '';
                                    },
                                },
                                grid: {
                                    color: '#f1f5f9',
                                    drawTicks: false,
                                    borderDash: [4, 6],
                                },
                            },
                        },
                        animation: {
                            duration: 750,
                            easing: 'easeOutQuart',
                        },
                    },
                    plugins: [hoverGuidePlugin],
                });

                window.addEventListener('beforeunload', () => chart.destroy());
            });
        </script>
    @endpush
</x-app-layout>