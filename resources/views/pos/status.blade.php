<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                <i class="fas fa-receipt"></i>
            </span>
            <h2 class="text-xl font-semibold leading-tight text-slate-700">Status Pembayaran</h2>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="paymentPage()" x-init="initPage()">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['title' => 'POS', 'url' => route('pos')],
            ['title' => 'Status Pembayaran']
        ]" />

        <!-- Header Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Status Pembayaran</h1>
                    <p class="text-sm text-slate-500">
                        Pantau dan kelola status pembayaran transaksi dari Point of Sale.
                    </p>
                </div>

                <a href="{{ route('pos') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke POS
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        @php
            $totalTransactions = $payments->count();
            $paidCount = $payments->filter(fn($p) => in_array($p->status ?? '', ['paid', 'dibayar']))->count();
            $unpaidCount = $totalTransactions - $paidCount;
            $totalRevenue = $payments->sum('grand_total');
            $totalReceived = $payments->sum('payment_received');
            $totalBalance = $payments->sum('balance_due');
            
            $statCards = [
                [
                    'label' => 'Total Transaksi',
                    'value' => number_format($totalTransactions),
                    'sub' => $selectedDate ? 'Pada ' . \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') : 'Semua waktu',
                    'icon' => 'fa-receipt',
                    'accent' => 'bg-emerald-500/10 text-emerald-600',
                    'filter' => 'all',
                ],
                [
                    'label' => 'Sudah Dibayar',
                    'value' => number_format($paidCount),
                    'sub' => $totalTransactions > 0 ? round(($paidCount / $totalTransactions) * 100) . '% dari total' : '0%',
                    'icon' => 'fa-circle-check',
                    'accent' => 'bg-emerald-500/10 text-emerald-600',
                    'filter' => 'paid',
                ],
                [
                    'label' => 'Belum Dibayar',
                    'value' => number_format($unpaidCount),
                    'sub' => 'Perlu tindakan',
                    'icon' => 'fa-clock',
                    'accent' => $unpaidCount > 0 ? 'bg-amber-500/10 text-amber-600' : 'bg-slate-500/10 text-slate-500',
                    'filter' => 'pending',
                ],
                [
                    'label' => 'Total Diterima',
                    'value' => 'Rp ' . number_format($totalReceived, 0, ',', '.'),
                    'sub' => 'Dari Rp ' . number_format($totalRevenue, 0, ',', '.'),
                    'icon' => 'fa-wallet',
                    'accent' => 'bg-indigo-500/10 text-indigo-600',
                    'filter' => null,
                ],
                [
                    'label' => 'Total Sisa Tagihan',
                    'value' => 'Rp ' . number_format($totalBalance, 0, ',', '.'),
                    'sub' => $unpaidCount > 0 ? 'Dari ' . $unpaidCount . ' transaksi' : 'Semua lunas',
                    'icon' => 'fa-hand-holding-dollar',
                    'accent' => $totalBalance > 0 ? 'bg-rose-500/10 text-rose-600' : 'bg-slate-500/10 text-slate-500',
                    'filter' => 'pending',
                ],
            ];
        @endphp

        <div class="grid gap-3 grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
            @foreach($statCards as $card)
                @if($card['filter'])
                    <button type="button" 
                            @click="setStatusFilter('{{ $card['filter'] }}')"
                            class="group rounded-xl border border-slate-200/80 bg-white p-3 shadow-sm shadow-slate-200/50 text-left transition hover:border-emerald-200 hover:shadow-md xl:p-4"
                            :class="{ 'ring-2 ring-emerald-500 border-emerald-300': statusFilter === '{{ $card['filter'] }}' }">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide truncate">{{ $card['label'] }}</p>
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $card['accent'] }} transition group-hover:scale-105">
                                <i class="fas {{ $card['icon'] }} text-xs"></i>
                            </span>
                        </div>
                        <p class="mt-1 text-lg font-bold text-slate-900 xl:text-xl">{{ $card['value'] }}</p>
                        <p class="mt-1 text-[11px] text-slate-500 truncate">{{ $card['sub'] }}</p>
                    </button>
                @else
                    <div class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-sm shadow-slate-200/50 xl:p-4">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wide truncate">{{ $card['label'] }}</p>
                            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg {{ $card['accent'] }}">
                                <i class="fas {{ $card['icon'] }} text-xs"></i>
                            </span>
                        </div>
                        <p class="mt-1 text-lg font-bold text-slate-900 xl:text-xl">{{ $card['value'] }}</p>
                        <p class="mt-1 text-[11px] text-slate-500 truncate">{{ $card['sub'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check"></i>
                    </span>
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                    <p class="text-sm font-medium text-rose-800">{{ $errors->first() }}</p>
                </div>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <!-- Toolbar -->
            <div class="flex flex-col gap-4 border-b border-slate-100 p-4">
                <!-- Row 1: Search, Status Filter, Refresh -->
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Left: Search + Status Filter -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <!-- Search -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   x-model="searchQuery"
                                   @input.debounce.300ms="filterData()"
                                   placeholder="Cari customer, referensi..."
                                   class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-sm text-slate-700 placeholder-slate-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400 sm:w-64">
                            <button type="button" 
                                    x-show="searchQuery" 
                                    @click="searchQuery = ''; filterData()"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        
                        <!-- Status Filter Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                    class="inline-flex h-10 w-full items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-700 transition hover:bg-slate-50 sm:w-44"
                                    :class="{ 'border-emerald-300 ring-2 ring-emerald-100': statusFilter !== 'all' }">
                                <span class="flex items-center gap-2">
                                    <i class="fas" :class="{
                                        'fa-layer-group text-slate-400': statusFilter === 'all',
                                        'fa-circle-check text-emerald-500': statusFilter === 'paid',
                                        'fa-clock text-amber-500': statusFilter === 'pending'
                                    }"></i>
                                    <span x-text="statusFilter === 'all' ? 'Semua Status' : (statusFilter === 'paid' ? 'Sudah Dibayar' : 'Belum Bayar')"></span>
                                </span>
                                <i class="fas fa-chevron-down text-xs text-slate-400 transition" :class="{ 'rotate-180': open }"></i>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 z-20 mt-2 w-48 origin-top-left rounded-xl border border-slate-200 bg-white p-1 shadow-lg">
                                <button type="button" @click="setStatusFilter('all'); open = false"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
                                        :class="{ 'bg-emerald-50 text-emerald-700': statusFilter === 'all' }">
                                    <i class="fas fa-layer-group text-slate-400"></i>
                                    Semua Status
                                </button>
                                <button type="button" @click="setStatusFilter('paid'); open = false"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
                                        :class="{ 'bg-emerald-50 text-emerald-700': statusFilter === 'paid' }">
                                    <i class="fas fa-circle-check text-emerald-500"></i>
                                    Sudah Dibayar
                                </button>
                                <button type="button" @click="setStatusFilter('pending'); open = false"
                                        class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 transition hover:bg-slate-50"
                                        :class="{ 'bg-emerald-50 text-emerald-700': statusFilter === 'pending' }">
                                    <i class="fas fa-clock text-amber-500"></i>
                                    Belum Bayar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Refresh + Count -->
                    <div class="flex items-center gap-3">
                        <button type="button" @click="refreshPage()" 
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50 hover:text-slate-700"
                                :class="{ 'animate-spin': isLoading }"
                                title="Refresh">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                        <span class="text-sm text-slate-500">
                            <span class="font-medium text-slate-700" x-text="filteredCount"></span> 
                            <span x-text="filteredCount !== {{ $payments->count() }} ? 'dari {{ $payments->count() }}' : ''"></span> transaksi
                        </span>
                    </div>
                </div>

                <!-- Row 2: Date Filter + Quick Presets -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Date Presets -->
                    <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-1">
                        <button type="button" @click="setDatePreset('today')"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                :class="datePreset === 'today' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            Hari Ini
                        </button>
                        <button type="button" @click="setDatePreset('yesterday')"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                :class="datePreset === 'yesterday' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            Kemarin
                        </button>
                        <button type="button" @click="setDatePreset('week')"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                :class="datePreset === 'week' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            7 Hari
                        </button>
                        <button type="button" @click="setDatePreset('all')"
                                class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
                                :class="datePreset === 'all' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'">
                            Semua
                        </button>
                    </div>

                    <span class="text-slate-300">|</span>

                    <!-- Custom Date -->
                    <form action="{{ route('pos.payments') }}" method="GET" class="flex items-center gap-2">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <input type="date" name="date" value="{{ $selectedDate }}"
                                   class="h-10 rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-sm text-slate-700 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>
                        <button type="submit"
                                class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                        @if($selectedDate)
                            <a href="{{ route('pos.payments') }}"
                               class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Active Filters Indicator -->
                <div x-show="searchQuery || statusFilter !== 'all'" x-cloak
                     class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-500">Filter aktif:</span>
                    <template x-if="searchQuery">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                            <i class="fas fa-search text-slate-400"></i>
                            "<span x-text="searchQuery"></span>"
                            <button type="button" @click="searchQuery = ''; filterData()" class="ml-0.5 text-slate-400 hover:text-slate-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    </template>
                    <template x-if="statusFilter !== 'all'">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                              :class="statusFilter === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                            <i class="fas" :class="statusFilter === 'paid' ? 'fa-circle-check' : 'fa-clock'"></i>
                            <span x-text="statusFilter === 'paid' ? 'Sudah Dibayar' : 'Belum Bayar'"></span>
                            <button type="button" @click="setStatusFilter('all')" class="ml-0.5 opacity-70 hover:opacity-100">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    </template>
                    <button type="button" @click="resetFilters()" class="text-xs text-emerald-600 hover:text-emerald-700 hover:underline">
                        Reset semua filter
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-4">
                <!-- Loading Overlay -->
                <div x-show="isLoading" x-cloak class="flex items-center justify-center py-16">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-slate-200 border-t-emerald-500"></div>
                        <p class="text-sm text-slate-500">Memuat data...</p>
                    </div>
                </div>

                <!-- No Results from Filter -->
                <div x-show="!isLoading && filteredCount === 0 && {{ $payments->count() }} > 0" x-cloak
                     class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">Tidak ditemukan</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Tidak ada transaksi yang sesuai dengan filter Anda.
                    </p>
                    <button type="button" @click="resetFilters()" class="mt-4 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                        <i class="fas fa-rotate-left mr-1"></i>Reset filter
                    </button>
                </div>

                @if($payments->isEmpty())
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                        <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="fas fa-file-invoice text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">
                            {{ $selectedDate ? 'Tidak ada transaksi' : 'Belum ada transaksi' }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $selectedDate ? 'Tidak ditemukan transaksi pada tanggal ' . \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') : 'Transaksi yang diproses melalui POS akan muncul di sini.' }}
                        </p>
                        @if($selectedDate)
                            <a href="{{ route('pos.payments') }}" class="mt-4 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                                <i class="fas fa-rotate-left mr-1"></i>Tampilkan semua transaksi
                            </a>
                        @else
                            <a href="{{ route('pos') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                                <i class="fas fa-cash-register"></i>
                                Buka POS
                            </a>
                        @endif
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block" x-show="!isLoading && filteredCount > 0">
                        <div class="overflow-x-auto">
                            @foreach($groupedPayments as $dateKey => $transactions)
                                @php
                                    $dateLabel = $dateKey !== 'unknown'
                                        ? \Carbon\Carbon::createFromFormat('Y-m-d', $dateKey)->translatedFormat('l, d M Y')
                                        : 'Tanggal tidak diketahui';
                                @endphp
                                
                                <!-- Date Header -->
                                <div class="sticky top-0 z-10 mb-3 {{ !$loop->first ? 'mt-6' : '' }}"
                                     x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                    <div class="flex items-center gap-3 rounded-xl bg-slate-100 px-4 py-2.5">
                                        <i class="fas fa-calendar-day text-slate-400"></i>
                                        <span class="text-sm font-semibold text-slate-700">{{ $dateLabel }}</span>
                                        <span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-600"
                                              x-text="getVisibleCountInGroup('{{ $dateKey }}') + ' transaksi'">
                                            {{ count($transactions) }} transaksi
                                        </span>
                                    </div>
                                </div>

                                <table class="mb-4 w-full text-sm" x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                    <thead>
                                        <tr class="border-b border-slate-100">
                                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Waktu & Ref</th>
                                            <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Pembeli</th>
                                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500">Grand Total</th>
                                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500">Dibayar</th>
                                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500">Sisa/Kembali</th>
                                            <th class="px-3 py-2.5 text-center text-[11px] font-semibold uppercase tracking-wide text-slate-500">Status</th>
                                            <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($transactions as $payment)
                                            @php
                                                $createdAt = $payment->created_at;
                                                $grandTotal = $payment->grand_total ?? 0;
                                                $paid = $payment->payment_received ?? 0;
                                                $balance = $payment->balance_due ?? max($grandTotal - $paid, 0);
                                                $change = $payment->change_due ?? max($paid - $grandTotal, 0);
                                                $status = $payment->status ?? 'pending';
                                                $isPaid = in_array($status, ['paid', 'dibayar'], true);
                                            @endphp
                                            <tr class="group transition hover:bg-slate-50/50"
                                                x-show="isPaymentVisible({{ json_encode([
                                                    'id' => $payment->id,
                                                    'customer_name' => $payment->customer_name,
                                                    'reference' => $payment->reference,
                                                    'order_id' => $payment->order_id ?? null,
                                                    'status' => $status,
                                                    'date' => $dateKey
                                                ]) }})"
                                                data-payment-id="{{ $payment->id }}"
                                                data-date-group="{{ $dateKey }}">
                                                <!-- Time & Ref -->
                                                <td class="px-3 py-3">
                                                    <div class="font-medium text-slate-900">{{ $createdAt?->format('H:i') ?? '-' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $payment->reference ?? '-' }}</div>
                                                </td>
                                                <!-- Customer -->
                                                <td class="px-3 py-3">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="h-8 w-8 shrink-0 inline-flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 font-semibold text-xs">
                                                            {{ strtoupper(substr($payment->customer_name ?? 'G', 0, 2)) }}
                                                        </span>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-semibold text-slate-900 truncate max-w-[140px]">{{ $payment->customer_name ?? 'Guest' }}</p>
                                                            <p class="text-xs text-slate-500 capitalize">{{ $payment->customer_type ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <!-- Grand Total -->
                                                <td class="px-3 py-3 text-right">
                                                    <span class="font-semibold text-slate-900">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                                </td>
                                                <!-- Paid -->
                                                <td class="px-3 py-3 text-right">
                                                    <span class="font-medium text-slate-700">Rp {{ number_format($paid, 0, ',', '.') }}</span>
                                                </td>
                                                <!-- Balance/Change -->
                                                <td class="px-3 py-3 text-right">
                                                    @if($change > 0)
                                                        <span class="font-semibold text-emerald-600">+Rp {{ number_format($change, 0, ',', '.') }}</span>
                                                    @elseif($balance > 0)
                                                        <span class="font-semibold text-amber-600">-Rp {{ number_format($balance, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                                <!-- Status -->
                                                <td class="px-3 py-3 text-center">
                                                    @if($isPaid)
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                            <i class="fas fa-circle-check text-[10px]"></i>
                                                            Dibayar
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                            <i class="fas fa-clock text-[10px]"></i>
                                                            Belum Bayar
                                                        </span>
                                                    @endif
                                                </td>
                                                <!-- Actions -->
                                                <td class="px-3 py-3">
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        <button type="button" @click="showDetail({{ json_encode($payment) }})"
                                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-slate-50 hover:text-slate-600 hover:border-slate-300"
                                                            title="Lihat Detail">
                                                            <i class="fas fa-eye text-xs"></i>
                                                        </button>
                                                        @if(!$isPaid)
                                                            <button type="button" @click="openPayModal({{ json_encode($payment) }})"
                                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 text-emerald-500 transition hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300"
                                                                title="Tandai Dibayar">
                                                                <i class="fas fa-check text-xs"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="space-y-6 lg:hidden" x-show="!isLoading && filteredCount > 0">
                        @foreach($groupedPayments as $dateKey => $transactions)
                            @php
                                $dateLabel = $dateKey !== 'unknown'
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $dateKey)->translatedFormat('l, d M Y')
                                    : 'Tanggal tidak diketahui';
                            @endphp
                            
                            <!-- Date Header -->
                            <div class="flex items-center gap-3 rounded-xl bg-slate-100 px-4 py-2.5"
                                 x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                <i class="fas fa-calendar-day text-slate-400"></i>
                                <span class="text-sm font-semibold text-slate-700">{{ $dateLabel }}</span>
                                <span class="rounded-full bg-slate-200 px-2 py-0.5 text-xs font-medium text-slate-600"
                                      x-text="getVisibleCountInGroup('{{ $dateKey }}')">
                                    {{ count($transactions) }}
                                </span>
                            </div>

                            <div class="grid gap-4" x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                @foreach($transactions as $payment)
                                    @php
                                        $createdAt = $payment->created_at;
                                        $grandTotal = $payment->grand_total ?? 0;
                                        $paid = $payment->payment_received ?? 0;
                                        $balance = $payment->balance_due ?? max($grandTotal - $paid, 0);
                                        $change = $payment->change_due ?? max($paid - $grandTotal, 0);
                                        $status = $payment->status ?? 'pending';
                                        $isPaid = in_array($status, ['paid', 'dibayar'], true);
                                    @endphp
                                    <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm"
                                         x-show="isPaymentVisible({{ json_encode([
                                            'id' => $payment->id,
                                            'customer_name' => $payment->customer_name,
                                            'reference' => $payment->reference,
                                            'order_id' => $payment->order_id ?? null,
                                            'status' => $status,
                                            'date' => $dateKey
                                        ]) }})"
                                         data-payment-id="{{ $payment->id }}"
                                         data-date-group="{{ $dateKey }}">
                                        <div class="flex items-start justify-between gap-3">
                                            <!-- Customer Info -->
                                            <div class="flex items-center gap-3">
                                                <span class="h-10 w-10 shrink-0 inline-flex items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($payment->customer_name ?? 'G', 0, 2)) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="font-semibold text-slate-900 truncate">{{ $payment->customer_name ?? 'Guest' }}</p>
                                                    <p class="text-xs text-slate-500">{{ $createdAt?->format('H:i') }} · {{ $payment->reference ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <!-- Status -->
                                            @if($isPaid)
                                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                                    <i class="fas fa-circle-check text-[10px]"></i>
                                                    Dibayar
                                                </span>
                                            @else
                                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                                    <i class="fas fa-clock text-[10px]"></i>
                                                    Belum
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Amount Info -->
                                        <div class="mt-4 grid grid-cols-3 gap-2 rounded-xl bg-slate-50 p-3 text-center">
                                            <div>
                                                <p class="text-[10px] font-medium uppercase text-slate-400">Total</p>
                                                <p class="text-sm font-bold text-slate-900">{{ number_format($grandTotal, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-medium uppercase text-slate-400">Dibayar</p>
                                                <p class="text-sm font-bold text-slate-700">{{ number_format($paid, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-medium uppercase text-slate-400">
                                                    {{ $change > 0 ? 'Kembali' : ($balance > 0 ? 'Sisa' : 'Sisa') }}
                                                </p>
                                                <p class="text-sm font-bold {{ $change > 0 ? 'text-emerald-600' : ($balance > 0 ? 'text-amber-600' : 'text-slate-400') }}">
                                                    {{ $change > 0 ? number_format($change, 0, ',', '.') : ($balance > 0 ? number_format($balance, 0, ',', '.') : '-') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="mt-3 flex items-center gap-2 border-t border-slate-100 pt-3">
                                            <button type="button" @click="showDetail({{ json_encode($payment) }})"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                                                <i class="fas fa-eye"></i>
                                                Detail
                                            </button>
                                            @if(!$isPaid)
                                                <button type="button" @click="openPayModal({{ json_encode($payment) }})"
                                                    class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-600">
                                                    <i class="fas fa-check"></i>
                                                    Bayar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Modal -->
        <div x-show="showDetailModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl"
                 @click.away="showDetailModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <!-- Header -->
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-5 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Detail Transaksi</h3>
                        <p class="text-xs text-slate-500" x-text="selectedPayment?.reference || '-'"></p>
                    </div>
                    <button type="button" @click="showDetailModal = false"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-5 space-y-5">
                    <!-- Customer & Status -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="h-12 w-12 inline-flex items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 font-bold">
                                <span x-text="(selectedPayment?.customer_name || 'G').substring(0, 2).toUpperCase()"></span>
                            </span>
                            <div>
                                <p class="font-semibold text-slate-900" x-text="selectedPayment?.customer_name || 'Guest'"></p>
                                <p class="text-sm text-slate-500 capitalize" x-text="selectedPayment?.customer_type || '-'"></p>
                            </div>
                        </div>
                        <template x-if="isPaymentPaid(selectedPayment)">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1.5 text-sm font-semibold text-emerald-700">
                                <i class="fas fa-circle-check"></i>
                                Dibayar
                            </span>
                        </template>
                        <template x-if="!isPaymentPaid(selectedPayment)">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-3 py-1.5 text-sm font-semibold text-amber-700">
                                <i class="fas fa-clock"></i>
                                Belum Bayar
                            </span>
                        </template>
                    </div>

                    <!-- Items -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-3">Rincian Item</p>
                        <div class="space-y-2">
                            <template x-for="item in (selectedPayment?.items || [])" :key="item.id">
                                <div class="flex items-center justify-between text-sm">
                                    <div>
                                        <span class="font-medium text-slate-700" x-text="item.product_name || item.name || 'Produk'"></span>
                                        <span class="text-slate-500">×</span>
                                        <span class="text-slate-500" x-text="(item.qty || 0) + ' ' + (item.unit || 'pcs')"></span>
                                    </div>
                                    <span class="font-semibold text-slate-900" x-text="'Rp ' + formatNumber(item.subtotal || 0)"></span>
                                </div>
                            </template>
                            <template x-if="!selectedPayment?.items?.length">
                                <p class="text-sm text-slate-400">Tidak ada detail item.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-medium text-slate-700" x-text="'Rp ' + formatNumber(selectedPayment?.subtotal || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Ongkir</span>
                            <span class="font-medium text-slate-700" x-text="'Rp ' + formatNumber(selectedPayment?.shipping_cost || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tip</span>
                            <span class="font-medium text-slate-700" x-text="'Rp ' + formatNumber(selectedPayment?.tip || 0)"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2">
                            <span class="font-semibold text-slate-900">Grand Total</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + formatNumber(selectedPayment?.grand_total || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Dibayar</span>
                            <span class="font-medium text-emerald-600" x-text="'Rp ' + formatNumber(selectedPayment?.payment_received || 0)"></span>
                        </div>
                        <template x-if="getBalance(selectedPayment) > 0">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Sisa</span>
                                <span class="font-semibold text-amber-600" x-text="'Rp ' + formatNumber(getBalance(selectedPayment))"></span>
                            </div>
                        </template>
                        <template x-if="getChange(selectedPayment) > 0">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Kembalian</span>
                                <span class="font-semibold text-emerald-600" x-text="'Rp ' + formatNumber(getChange(selectedPayment))"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Note -->
                    <template x-if="selectedPayment?.note">
                        <div class="rounded-xl bg-amber-50 border border-amber-100 p-3">
                            <p class="text-xs font-semibold text-amber-700 mb-1">Catatan</p>
                            <p class="text-sm text-amber-800" x-text="selectedPayment.note"></p>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 border-t border-slate-100 bg-slate-50 px-5 py-4">
                    <template x-if="!isPaymentPaid(selectedPayment)">
                        <button type="button" @click="showDetailModal = false; openPayModal(selectedPayment)"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                            <i class="fas fa-check"></i>
                            Tandai Dibayar
                        </button>
                    </template>
                    <template x-if="isPaymentPaid(selectedPayment)">
                        <button type="button" @click="showDetailModal = false"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Tutup
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-show="showPayModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl"
                 @click.away="showPayModal = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Konfirmasi Pembayaran</h3>
                        <p class="text-xs text-slate-500" x-text="paymentTarget?.customer_name || 'Guest'"></p>
                    </div>
                    <button type="button" @click="showPayModal = false"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content -->
                <form :action="paymentTarget ? '{{ url('pos/status') }}/' + paymentTarget.id + '/pay' : '#'" method="POST" class="p-5 space-y-4">
                    @csrf
                    <input type="hidden" name="transaction_id" :value="paymentTarget?.id">

                    <!-- Amount Summary -->
                    <div class="rounded-xl bg-slate-50 p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Grand Total</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + formatNumber(paymentTarget?.grand_total || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sudah Dibayar</span>
                            <span class="font-medium text-slate-700" x-text="'Rp ' + formatNumber(paymentTarget?.payment_received || 0)"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2">
                            <span class="font-semibold text-amber-700">Sisa Pembayaran</span>
                            <span class="font-bold text-amber-700" x-text="'Rp ' + formatNumber(getBalance(paymentTarget))"></span>
                        </div>
                    </div>

                    <!-- Payment Input -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Pembayaran</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-semibold text-slate-400">Rp</span>
                            <input type="text" name="payment_amount" x-model="paymentAmount"
                                   inputmode="numeric"
                                   @input="formatPaymentInput"
                                   class="w-full h-12 rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-lg font-bold text-slate-900 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Masukkan jumlah yang dibayarkan customer</p>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="setPaymentAmount(getBalance(paymentTarget))"
                                class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-100">
                            Bayar Lunas
                        </button>
                        <button type="button" @click="setPaymentAmount(Math.ceil(getBalance(paymentTarget) / 1000) * 1000)"
                                class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-200">
                            Bulatkan
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="showPayModal = false"
                                class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                            <i class="fas fa-check"></i>
                            Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    @php
        $paymentDataForJs = $payments->map(function($p) {
            return [
                'id' => $p->id,
                'customer_name' => $p->customer_name,
                'reference' => $p->reference,
                'order_id' => $p->order_id ?? null,
                'status' => $p->status ?? 'pending',
                'date' => $p->created_at ? $p->created_at->format('Y-m-d') : 'unknown'
            ];
        })->values();
    @endphp
    <script>
        function paymentPage() {
            return {
                // Modal states
                showDetailModal: false,
                showPayModal: false,
                selectedPayment: null,
                paymentTarget: null,
                paymentAmount: '',

                // Filter states
                searchQuery: '',
                statusFilter: 'all',
                datePreset: '{{ $selectedDate ? "custom" : "all" }}',
                isLoading: false,
                filteredCount: {{ $payments->count() }},

                // All payments data for filtering
                allPayments: @json($paymentDataForJs),

                initPage() {
                    this.filterData();
                },

                // Filter methods
                setStatusFilter(status) {
                    this.statusFilter = status;
                    this.filterData();
                },

                setDatePreset(preset) {
                    this.datePreset = preset;
                    
                    let targetDate = '';
                    const today = new Date();
                    
                    if (preset === 'today') {
                        targetDate = today.toISOString().split('T')[0];
                    } else if (preset === 'yesterday') {
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);
                        targetDate = yesterday.toISOString().split('T')[0];
                    } else if (preset === 'week') {
                        // For week, we'll redirect with no date (show all) but client-side filter
                        window.location.href = '{{ route("pos.payments") }}';
                        return;
                    } else if (preset === 'all') {
                        window.location.href = '{{ route("pos.payments") }}';
                        return;
                    }

                    if (targetDate) {
                        window.location.href = '{{ route("pos.payments") }}?date=' + targetDate;
                    }
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.statusFilter = 'all';
                    this.filterData();
                },

                refreshPage() {
                    this.isLoading = true;
                    window.location.reload();
                },

                filterData() {
                    let count = 0;
                    const query = this.searchQuery.toLowerCase().trim();
                    
                    this.allPayments.forEach(payment => {
                        if (this.isPaymentVisible(payment)) {
                            count++;
                        }
                    });
                    
                    this.filteredCount = count;
                },

                isPaymentVisible(payment) {
                    if (!payment) return false;

                    // Status filter
                    if (this.statusFilter !== 'all') {
                        const isPaid = ['paid', 'dibayar'].includes(payment.status);
                        if (this.statusFilter === 'paid' && !isPaid) return false;
                        if (this.statusFilter === 'pending' && isPaid) return false;
                    }

                    // Search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase().trim();
                        const customerName = (payment.customer_name || '').toLowerCase();
                        const reference = (payment.reference || '').toLowerCase();
                        const orderId = (payment.order_id || '').toString().toLowerCase();
                        
                        if (!customerName.includes(query) && 
                            !reference.includes(query) && 
                            !orderId.includes(query)) {
                            return false;
                        }
                    }

                    return true;
                },

                hasVisibleItemsInGroup(dateKey) {
                    return this.allPayments.some(p => p.date === dateKey && this.isPaymentVisible(p));
                },

                getVisibleCountInGroup(dateKey) {
                    return this.allPayments.filter(p => p.date === dateKey && this.isPaymentVisible(p)).length;
                },

                // Modal methods
                showDetail(payment) {
                    this.selectedPayment = payment;
                    this.showDetailModal = true;
                },

                openPayModal(payment) {
                    this.paymentTarget = payment;
                    const balance = this.getBalance(payment);
                    this.paymentAmount = this.formatNumber(balance > 0 ? balance : payment.grand_total);
                    this.showPayModal = true;
                },

                isPaymentPaid(payment) {
                    if (!payment) return false;
                    return ['paid', 'dibayar'].includes(payment.status);
                },

                getBalance(payment) {
                    if (!payment) return 0;
                    const balance = payment.balance_due ?? Math.max((payment.grand_total || 0) - (payment.payment_received || 0), 0);
                    return balance;
                },

                getChange(payment) {
                    if (!payment) return 0;
                    const change = payment.change_due ?? Math.max((payment.payment_received || 0) - (payment.grand_total || 0), 0);
                    return change;
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num || 0);
                },

                formatPaymentInput(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    this.paymentAmount = value ? this.formatNumber(parseInt(value)) : '';
                },

                setPaymentAmount(amount) {
                    this.paymentAmount = this.formatNumber(amount);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
