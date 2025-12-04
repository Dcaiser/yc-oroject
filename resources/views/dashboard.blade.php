<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <i class="fas fa-gauge-high text-xl"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Dashboard Utama</h1>
                    <p class="text-sm text-slate-500">Lihat sekilas kegiatan hari ini, stok, dan pesanan.</p>
                </div>
            </div>
        </div>
    </x-slot>

    @php
        $statCards = [
            [
                'label' => 'Total Produk',
                'value' => number_format($stats['total_products'] ?? 0, 0, ',', '.'),
                'icon' => 'fa-box',
                'accent' => 'emerald',
                'subtext' => 'Total produk aktif',
            ],
            [
                'label' => 'Stok Tersedia',
                'value' => number_format($stats['total_stock'] ?? 0, 0, ',', '.'),
                'icon' => 'fa-warehouse',
                'accent' => 'indigo',
                'subtext' => 'Siap dikirim',
            ],
            [
                'label' => 'Stok Menipis',
                'value' => number_format($stats['low_stock'] ?? 0, 0, ',', '.'),
                'icon' => 'fa-exclamation-triangle',
                'accent' => 'amber',
                'subtext' => 'Di bawah ' . $lowStockThreshold . ' unit',
            ],
            [
                'label' => 'Stok Habis',
                'value' => number_format($stats['out_of_stock'] ?? 0, 0, ',', '.'),
                'icon' => 'fa-times-circle',
                'accent' => 'rose',
                'subtext' => 'Butuh restok segera',
            ],
            [
                'label' => 'Pelanggan Terdaftar',
                'value' => number_format($stats['total_customers'] ?? 0, 0, ',', '.'),
                'icon' => 'fa-users',
                'accent' => 'purple',
                'subtext' => 'Pelanggan terdaftar',
            ],
        ];

        $palette = [
            'emerald' => [
                'border' => 'border-emerald-200/70',
                'iconBg' => 'bg-emerald-50',
                'iconFg' => 'text-emerald-600',
                'icon' => 'bg-emerald-50 text-emerald-600',
                'glow' => 'from-emerald-50/70',
            ],
            'indigo' => [
                'border' => 'border-indigo-200/70',
                'iconBg' => 'bg-indigo-50',
                'iconFg' => 'text-indigo-600',
                'icon' => 'bg-indigo-50 text-indigo-600',
                'glow' => 'from-indigo-50/70',
            ],
            'amber' => [
                'border' => 'border-amber-200/70',
                'iconBg' => 'bg-amber-50',
                'iconFg' => 'text-amber-600',
                'icon' => 'bg-amber-50 text-amber-600',
                'glow' => 'from-amber-50/70',
            ],
            'rose' => [
                'border' => 'border-rose-200/70',
                'iconBg' => 'bg-rose-50',
                'iconFg' => 'text-rose-600',
                'icon' => 'bg-rose-50 text-rose-600',
                'glow' => 'from-rose-50/70',
            ],
            'purple' => [
                'border' => 'border-purple-200/70',
                'iconBg' => 'bg-purple-50',
                'iconFg' => 'text-purple-600',
                'icon' => 'bg-purple-50 text-purple-600',
                'glow' => 'from-purple-50/70',
            ],
            'indigo-soft' => [
                'border' => 'border-indigo-200/70',
                'icon' => 'bg-indigo-100 text-indigo-600',
                'glow' => 'from-indigo-100'
            ],
            'amber-soft' => [
                'border' => 'border-amber-200/70',
                'icon' => 'bg-amber-100 text-amber-600',
                'glow' => 'from-amber-100'
            ],
            'rose-soft' => [
                'border' => 'border-rose-200/70',
                'icon' => 'bg-rose-100 text-rose-600',
                'glow' => 'from-rose-100'
            ],
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 space-y-4 sm:space-y-6 md:space-y-8">
        <!-- Hero -->
        <section class="rounded-2xl sm:rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-emerald-50/35 to-emerald-50/60 px-4 py-5 sm:px-6 sm:py-6 md:px-8 md:py-8 shadow-[0_15px_35px_-22px_rgba(16,185,129,0.45)]">
            <div class="flex flex-col gap-5 sm:gap-6 md:gap-8 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex-1 space-y-4 sm:space-y-6">
                    <div class="space-y-1 sm:space-y-2">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-slate-900">Halo, {{ $user->name ?? 'Tim' }}</h1>
                        <p class="text-sm sm:text-base text-slate-600">Anda masuk sebagai <span class="font-semibold text-emerald-700">{{ \Illuminate\Support\Str::title($user->role ?? '-') }}</span>.</p>
                    </div>

                    @if(!empty($opsPulse))
                        @php
                            $pulsePalette = [
                                'amber' => [
                                    'badge' => 'bg-amber-50 text-amber-600',
                                    'value' => 'text-amber-600'
                                ],
                                'rose' => [
                                    'badge' => 'bg-rose-50 text-rose-600',
                                    'value' => 'text-rose-600'
                                ],
                                'indigo' => [
                                    'badge' => 'bg-indigo-50 text-indigo-600',
                                    'value' => 'text-indigo-600'
                                ],
                                'emerald' => [
                                    'badge' => 'bg-emerald-50 text-emerald-600',
                                    'value' => 'text-emerald-600'
                                ],
                            ];
                        @endphp
                        <div class="rounded-2xl border border-slate-100 bg-white/85 shadow-sm">
                            <ul class="divide-y divide-slate-100">
                                @foreach($opsPulse as $pulse)
                                    @php $style = $pulsePalette[$pulse['accent']] ?? $pulsePalette['emerald']; @endphp
                                    <li class="flex items-center justify-between gap-4 px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl {{ $style['badge'] }}">
                                                <i class="fas {{ $pulse['icon'] }} text-base"></i>
                                            </span>
                                            <div class="leading-tight">
                                                <p class="text-sm font-semibold text-slate-900">{{ $pulse['label'] }}</p>
                                                <p class="text-xs text-slate-500">{{ $pulse['hint'] }}</p>
                                            </div>
                                        </div>
                                        <span class="text-2xl font-bold tabular-nums {{ $style['value'] }}">{{ $pulse['value'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="w-full lg:max-w-sm lg:self-stretch">
                    <div class="flex h-full flex-col overflow-hidden rounded-2xl sm:rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-emerald-50/60 to-emerald-100/40 text-center shadow-[0_18px_40px_-22px_rgba(16,185,129,0.55)]">
                        <div class="px-4 py-6 sm:px-6 sm:py-8 md:px-8 md:py-10 space-y-3 sm:space-y-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700/80">Jam Saat Ini</p>
                            <div class="text-4xl sm:text-5xl font-black text-slate-900 tabular-nums" id="dashboard-clock">
                                {{ now()->format('H:i') }}
                            </div>
                            <div class="space-y-1 text-slate-600">
                                <p class="text-base sm:text-lg font-semibold">{{ now()->locale('id')->translatedFormat('l') }}</p>
                                <p class="text-xs sm:text-sm">{{ now()->locale('id')->translatedFormat('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="mt-auto border-t border-emerald-100/60 bg-white/80 px-4 py-3 sm:px-6 sm:py-4 text-xs font-medium text-slate-500">
                            <p>Zona waktu: <span class="font-semibold text-slate-700">WIB</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- KPI Cards -->
        <section class="grid gap-3 sm:gap-4 grid-cols-2 md:grid-cols-4">
            @foreach (array_slice($statCards, 0, 4) as $card)
                @php $colors = $palette[$card['accent']] ?? $palette['emerald']; @endphp
                <article class="relative overflow-hidden rounded-2xl sm:rounded-3xl border {{ $colors['border'] }} bg-white shadow-[0_15px_35px_-20px_rgba(16,185,129,0.45)]">
                    <div class="flex flex-col items-start gap-2 sm:gap-3 px-4 py-4 sm:px-6 sm:py-6 md:px-8 md:py-8">
                        <span class="inline-flex h-10 w-10 sm:h-12 sm:w-12 md:h-16 md:w-16 items-center justify-center rounded-xl sm:rounded-2xl {{ $colors['iconBg'] }} {{ $colors['iconFg'] }}">
                            <i class="fas {{ $card['icon'] }} text-base sm:text-lg md:text-2xl"></i>
                        </span>
                        <div class="space-y-0.5 sm:space-y-1">
                            <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-[0.08em] sm:tracking-[0.12em] text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-2xl sm:text-3xl md:text-4xl font-black text-slate-900 tabular-nums">{{ $card['value'] }}</p>
                        </div>
                    </div>
                    <div class="h-1.5 sm:h-2 bg-gradient-to-r {{ $colors['glow'] }}"></div>
                </article>
            @endforeach
        </section>

        <section class="grid gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Quick Actions -->
            <div class="md:col-span-2 rounded-2xl sm:rounded-3xl border border-slate-100 bg-white shadow-sm">
                <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Quick Menu</p>
                        <h3 class="mt-1 text-lg sm:text-xl font-bold text-slate-900">Pintasan {{ \Illuminate\Support\Str::title($user->role ?? '-') }}</h3>
                        <p class="mt-0.5 text-xs sm:text-sm text-slate-500">Beberapa pintasan yang mungkin berguna.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 p-4 sm:gap-4 sm:p-6 sm:grid-cols-2">
                    @forelse ($quickActions as $action)
                        <a href="{{ $action['url'] }}" class="group flex items-start gap-3 sm:gap-4 rounded-xl sm:rounded-2xl border border-slate-100 bg-slate-50/50 px-3 py-3 sm:px-5 sm:py-5 shadow-sm transition transform hover:-translate-y-1 hover:border-emerald-200 hover:bg-emerald-50/50 hover:shadow-lg">
                            <span class="flex h-10 w-10 sm:h-12 sm:w-12 flex-shrink-0 items-center justify-center rounded-lg sm:rounded-xl {{ $action['style'] }} transition group-hover:scale-105">
                                <i class="fas {{ $action['icon'] }} text-base sm:text-lg"></i>
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm sm:text-base font-bold text-slate-900">{{ $action['label'] }}</p>
                                <p class="mt-0.5 text-xs sm:text-sm text-slate-500 line-clamp-2">{{ $action['description'] }}</p>
                            </div>
                            <i class="fas fa-arrow-right mt-1 flex-shrink-0 text-slate-300 transition group-hover:translate-x-1 group-hover:text-emerald-600 hidden sm:block"></i>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada saran khusus untuk peran ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sales Summary -->
            <div class="rounded-2xl sm:rounded-3xl border border-slate-100 bg-white/95 shadow-sm">
                <div class="flex items-start justify-between border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Penjualan Hari Ini</p>
                        <h3 class="mt-1.5 sm:mt-2 text-2xl sm:text-3xl font-bold text-slate-900">Rp{{ number_format($salesSummary['total'] ?? 0, 0, ',', '.') }}</h3>
                        <p class="mt-0.5 sm:mt-1 text-xs text-slate-500">Total pemasukan sampai sekarang</p>
                    </div>
                    <span class="flex h-10 w-10 sm:h-12 sm:w-12 flex-shrink-0 items-center justify-center rounded-xl sm:rounded-2xl border border-emerald-100 bg-emerald-500/10 text-emerald-600">
                        <i class="fas fa-receipt text-base sm:text-lg"></i>
                    </span>
                </div>
                <div class="px-4 py-4 sm:px-6 sm:py-5 space-y-4 sm:space-y-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase">Transaksi selesai</p>
                            <p class="mt-0.5 sm:mt-1 text-xl sm:text-2xl font-bold text-slate-900">{{ $salesSummary['orders'] ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs font-semibold text-slate-400 uppercase">Menunggu proses</p>
                            <p class="mt-0.5 sm:mt-1 text-xl sm:text-2xl font-bold text-slate-900">{{ $salesSummary['pending'] ?? 0 }}</p>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Pesanan lunas</span>
                            <span class="font-semibold">{{ $salesSummary['paid_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="mt-1.5 sm:mt-2 h-1.5 sm:h-2 rounded-full bg-slate-100">
                            <div class="h-1.5 sm:h-2 rounded-full bg-emerald-500 transition-all" style="width: {{ $salesSummary['paid_rate'] ?? 0 }}%"></div>
                        </div>
                    </div>
                    
                    <!-- Customer stat moved here -->
                    @php $customerCard = $statCards[4] ?? null; @endphp
                    @if($customerCard)
                        <div class="rounded-xl sm:rounded-2xl border border-purple-100 bg-purple-50/30 px-3 py-3 sm:px-4 sm:py-4">
                            <div class="flex items-center gap-2 sm:gap-3">
                                <span class="inline-flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-lg sm:rounded-xl bg-purple-100 text-purple-600">
                                    <i class="fas {{ $customerCard['icon'] }} text-sm sm:text-base"></i>
                                </span>
                                <div>
                                    <p class="text-[10px] sm:text-xs font-semibold uppercase tracking-wide text-slate-400">{{ $customerCard['label'] }}</p>
                                    <p class="mt-0.5 text-xl sm:text-2xl font-bold text-slate-900">{{ $customerCard['value'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- Recent Activity -->
            <div class="md:col-span-1 lg:col-span-2 rounded-2xl sm:rounded-3xl border border-slate-100 bg-white/95 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 px-4 py-4 sm:px-6 sm:py-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Aktivitas Terbaru</p>
                        <h3 class="mt-1 text-lg sm:text-xl font-bold text-slate-900">Catatan Aktivitas</h3>
                    </div>
                    <a href="{{ route('activities.index') }}" class="text-sm font-semibold text-emerald-600 underline hover:text-emerald-700 transition">Lihat selengkapnya</a>
                </div>
                <ul class="relative divide-y divide-slate-50">
                    @forelse ($recentActivities as $activity)
                        <li class="relative px-4 py-3 pl-10 sm:px-6 sm:py-4 sm:pl-12 transition hover:bg-slate-50/60">
                            <span class="absolute left-4 sm:left-6 top-4 sm:top-5 flex h-2.5 w-2.5 sm:h-3 sm:w-3 items-center justify-center">
                                <span class="absolute h-full w-full animate-ping rounded-full bg-emerald-300 opacity-60"></span>
                                <span class="relative h-2.5 w-2.5 sm:h-3 sm:w-3 rounded-full bg-emerald-500"></span>
                            </span>
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-slate-900 truncate">{{ $activity->action ?? 'Aktivitas' }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $activity->user ?? 'Sistem' }} • {{ optional($activity->created_at)->locale('id')->diffForHumans() ?? '-' }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-4 py-6 sm:px-6 sm:py-10 text-center">
                            <div class="mx-auto flex max-w-xs flex-col items-center justify-center rounded-xl sm:rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 p-5 sm:p-8 text-sm text-slate-500">
                                <i class="fas fa-clipboard-list mb-2 sm:mb-3 text-xl sm:text-2xl text-slate-300"></i>
                                <p class="font-semibold text-slate-600">Tidak ada aktivitas baru.</p>
                                <p class="mt-1 text-xs text-slate-400">Catatan baru muncul otomatis ketika ada aktivitas.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Inventory Alerts -->
            <div class="rounded-2xl sm:rounded-3xl border border-orange-100 bg-white/95 shadow-sm">
                <div class="border-b border-orange-100 bg-orange-50/40 px-4 py-4 sm:px-6 sm:py-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-orange-600">Peringatan stok</p>
                    <h3 class="mt-1 text-lg sm:text-xl font-bold text-slate-900">Produk yang perlu direstock</h3>
                    <p class="mt-0.5 text-xs sm:text-sm text-slate-500">Segera tambahkan stok untuk produk ini.</p>
                </div>
                <div class="p-4 sm:p-6 space-y-2 sm:space-y-3">
                    @forelse ($inventoryAlerts as $item)
                        <article class="group rounded-xl sm:rounded-2xl border border-orange-100 bg-white/80 p-3 sm:p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-200 hover:bg-orange-50/50">
                            <div class="flex items-start justify-between gap-2 sm:gap-3">
                                <div class="flex items-start gap-2 sm:gap-3 min-w-0">
                                    <span class="inline-flex h-8 w-8 sm:h-10 sm:w-10 flex-shrink-0 items-center justify-center rounded-lg sm:rounded-xl bg-orange-100 text-orange-600">
                                        <i class="fas {{ ($item->stock_quantity ?? 0) <= 0 ? 'fa-triangle-exclamation' : 'fa-box-open' }} text-sm sm:text-base"></i>
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-semibold text-slate-900 truncate">{{ $item->name }}</p>
                                        <p class="mt-0.5 text-[10px] sm:text-xs text-slate-500">Satuan: {{ $item->satuan ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="text-lg sm:text-xl font-bold {{ ($item->stock_quantity ?? 0) <= 0 ? 'text-rose-600' : 'text-orange-600' }}">{{ $item->stock_quantity ?? 0 }}</span>
                            </div>
                            <div class="mt-2 sm:mt-3 h-1 sm:h-1.5 rounded-full bg-slate-100">
                                @php
                                    $alertPercentage = min(100, (($item->stock_quantity ?? 0) / max(1, $lowStockThreshold)) * 100);
                                @endphp
                                <div class="h-1 sm:h-1.5 rounded-full transition-all {{ ($item->stock_quantity ?? 0) <= 0 ? 'bg-rose-500' : 'bg-orange-500' }}" style="width: {{ $alertPercentage }}%"></div>
                            </div>
                            <p class="mt-1.5 sm:mt-2 text-[10px] sm:text-xs text-slate-400">Batas stok aman: {{ $lowStockThreshold }} unit</p>
                        </article>
                    @empty
                        <div class="rounded-xl sm:rounded-2xl border border-dashed border-orange-200 bg-orange-50/30 px-4 py-6 sm:px-6 sm:py-8 text-center text-sm text-orange-600">
                            <i class="fas fa-check-circle text-xl sm:text-2xl mb-2"></i>
                            <p class="font-semibold">Stok aman</p>
                            <p class="text-xs text-slate-500 mt-1">Belum ada produk yang mendekati batas stok.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </div>

    @push('scripts')
    <script>
        // Real-time clock update
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const clockElement = document.getElementById('dashboard-clock');
            if (clockElement) {
                clockElement.textContent = `${hours}:${minutes}`;
            }
        }
        
        // Update immediately and then every second
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @endpush
</x-app-layout>