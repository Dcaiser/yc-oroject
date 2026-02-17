<x-app-layout>
    @php
        $todayLabel = \Illuminate\Support\Carbon::now()->locale('id')->translatedFormat('l, d F Y');
        $userName = Auth::user()->name ?? 'Kasir';
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-700 rounded-2xl shrink-0">
                    <i class="fas fa-receipt text-lg"></i>
                </span>
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900">Status Pembayaran</h1>
                    <p class="text-sm text-slate-600 mt-0.5">Kasir: <span class="font-semibold text-emerald-700">{{ $userName }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <i class="fas fa-calendar-day"></i>
                    <span>{{ $todayLabel }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="paymentPage()" x-init="initPage()">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['title' => 'POS Kasir', 'url' => route('pos')],
            ['title' => 'Status Pembayaran']
        ]" />

        <!-- Header Stats Cards -->
        @php
            $paymentStats = $paymentStats ?? [
                'total_transactions' => 0,
                'paid_count' => 0,
                'unpaid_count' => 0,
                'paid_percentage' => 0,
                'cash_transactions' => 0,
                'transfer_transactions' => 0,
                'cash_amount' => 0,
                'transfer_amount' => 0,
            ];
        @endphp

        <div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
            <!-- Total Transaksi -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide truncate">Total Transaksi</p>
                        <p class="text-xl font-bold text-slate-900 mt-1 truncate">{{ number_format($paymentStats['total_transactions']) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $selectedDate ? 'Pada ' . \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') : 'Semua waktu' }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl shrink-0 ml-2">
                        <i class="fas fa-receipt"></i>
                    </span>
                </div>
            </div>

            <!-- Sudah Dibayar -->
            <button type="button"
                    @click="setStatusFilter('paid')"
                    class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-left transition hover:border-emerald-300 hover:shadow-md"
                    :class="{ 'ring-2 ring-emerald-500 border-emerald-300': statusFilter === 'paid' }">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide truncate">Sudah Dibayar</p>
                        <p class="text-xl font-bold text-emerald-600 mt-1 truncate">{{ number_format($paymentStats['paid_count']) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ ($paymentStats['paid_percentage'] ?? 0) . '% dari total' }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl shrink-0 ml-2">
                        <i class="fas fa-circle-check"></i>
                    </span>
                </div>
            </button>

            <!-- Belum Dibayar -->
            <button type="button"
                    @click="setStatusFilter('pending')"
                    class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm text-left transition hover:border-amber-300 hover:shadow-md"
                    :class="{ 'ring-2 ring-amber-500 border-amber-300': statusFilter === 'pending' }">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide truncate">Belum Dibayar</p>
                        <p class="text-xl font-bold text-amber-600 mt-1 truncate">{{ number_format($paymentStats['unpaid_count']) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">{{ ($paymentStats['unpaid_count'] ?? 0) > 0 ? 'Perlu tindakan' : 'Semua lunas' }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-amber-100 text-amber-600 rounded-xl shrink-0 ml-2">
                        <i class="fas fa-clock"></i>
                    </span>
                </div>
            </button>

            <!-- Cash -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide truncate">Cash</p>
                        <p class="text-xl font-bold text-emerald-600 mt-1 truncate">{{ number_format($paymentStats['cash_transactions']) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">Rp {{ number_format($paymentStats['cash_amount'], 0, ',', '.') }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl shrink-0 ml-2">
                        <i class="fas fa-money-bill-wave"></i>
                    </span>
                </div>
            </div>

            <!-- Transfer -->
            <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide truncate">Transfer</p>
                        <p class="text-xl font-bold text-blue-600 mt-1 truncate">{{ number_format($paymentStats['transfer_transactions']) }}</p>
                        <p class="text-xs text-slate-500 mt-0.5 truncate">Rp {{ number_format($paymentStats['transfer_amount'], 0, ',', '.') }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 text-blue-600 rounded-xl shrink-0 ml-2">
                        <i class="fas fa-university"></i>
                    </span>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="toastVisible" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform translate-y-0"
             x-transition:leave-end="opacity-0 transform translate-y-2"
             class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50">
            <div class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl"
                 :class="toastType === 'error' ? 'bg-red-600 text-white' : 'bg-slate-900 text-white'">
                <i class="fas" :class="toastType === 'error' ? 'fa-exclamation-circle text-red-200' : 'fa-check-circle text-emerald-400'"></i>
                <span class="font-medium" x-text="toastMessage"></span>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
            <!-- Toolbar -->
            <div class="flex flex-col gap-4 px-4 sm:px-6 py-5 border-b border-emerald-100 bg-emerald-50/40">
                <!-- Search & Actions -->
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text"
                               x-model="searchQuery"
                               @input="queueServerFilterApply()"
                               placeholder="Cari customer, referensi, metode..."
                               class="w-full py-2.5 pl-10 pr-4 text-sm font-semibold text-slate-700 bg-white border-2 border-emerald-100 rounded-xl focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:font-normal placeholder:text-slate-400">
                        <button type="button"
                                x-show="searchQuery"
                                @click="searchQuery = ''; applyServerFilters()"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-red-500 transition">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Right: Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Status Filter Buttons -->
                        <div class="flex gap-2">
                            <button type="button"
                                    @click="setStatusFilter('all')"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold transition border rounded-xl"
                                    :class="statusFilter === 'all'
                                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg'
                                        : 'bg-white border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-600'">
                                <i class="fas fa-layer-group"></i>
                                <span class="hidden sm:inline">Semua</span>
                            </button>
                            <button type="button"
                                    @click="setStatusFilter('paid')"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold transition border rounded-xl"
                                    :class="statusFilter === 'paid'
                                        ? 'bg-emerald-600 border-emerald-600 text-white shadow-lg'
                                        : 'bg-white border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-600'">
                                <i class="fas fa-circle-check"></i>
                                <span class="hidden sm:inline">Sudah Bayar</span>
                            </button>
                            <button type="button"
                                    @click="setStatusFilter('pending')"
                                    class="inline-flex items-center gap-2 px-3 py-2 text-xs font-semibold transition border rounded-xl"
                                    :class="statusFilter === 'pending'
                                        ? 'bg-amber-500 border-amber-500 text-white shadow-lg'
                                        : 'bg-white border-slate-200 text-slate-600 hover:border-amber-300 hover:text-amber-600'">
                                <i class="fas fa-clock"></i>
                                <span class="hidden sm:inline">Belum Bayar</span>
                            </button>
                        </div>

                        <!-- Refresh -->
                        <button type="button" @click="refreshPage()"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-50 hover:border-slate-300"
                                :class="{ 'animate-spin': isLoading }"
                                title="Refresh">
                            <i class="fas fa-sync"></i>
                        </button>

                        <!-- Back to POS -->
                        <a href="{{ route('pos') }}"
                           class="inline-flex items-center gap-2 px-3 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition">
                            <i class="fas fa-arrow-left"></i>
                            <span class="hidden sm:inline">Kembali ke POS</span>
                        </a>
                    </div>
                </div>

                <!-- Filters -->
                <div class="flex flex-col gap-3">
                    <!-- Date & Payment Method Filters -->
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Date Presets -->
                        <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-1">
                            <button type="button" @click="setDatePreset('today')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="datePreset === 'today' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                Hari Ini
                            </button>
                            <button type="button" @click="setDatePreset('yesterday')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="datePreset === 'yesterday' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                Kemarin
                            </button>
                            <button type="button" @click="setDatePreset('week')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="datePreset === 'week' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                7 Hari
                            </button>
                            <button type="button" @click="setDatePreset('all')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="datePreset === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                Semua
                            </button>
                        </div>

                        <!-- Payment Method Filter -->
                        <div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 p-1">
                            <button type="button" @click="setPaymentMethodFilter('all')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="paymentMethodFilter === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                Semua
                            </button>
                            <button type="button" @click="setPaymentMethodFilter('cash')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="paymentMethodFilter === 'cash' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                <i class="fas fa-money-bill-wave mr-1"></i> Cash
                            </button>
                            <button type="button" @click="setPaymentMethodFilter('transfer')"
                                    class="rounded-lg px-2 py-1 text-xs font-semibold transition whitespace-nowrap"
                                    :class="paymentMethodFilter === 'transfer' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'">
                                <i class="fas fa-university mr-1"></i> Transfer
                            </button>
                        </div>
                    </div>

                    <!-- Custom Date Filter -->
                    <form action="{{ route('pos.payments') }}" method="GET" class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-emerald-400">
                                <i class="fas fa-calendar"></i>
                            </span>
                            <input type="date" name="date" value="{{ $selectedDate }}"
                                   class="w-full h-10 rounded-xl border-2 border-emerald-100 bg-white pl-10 pr-4 text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                        </div>
                        <input type="hidden" name="search" :value="searchQuery">
                        <input type="hidden" name="status" :value="statusFilter">
                        <input type="hidden" name="payment_method" :value="paymentMethodFilter">
                        <input type="hidden" name="per_page" value="{{ (int)($perPage ?? 80) }}">
                        <button type="submit"
                                class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-500 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600 whitespace-nowrap">
                            <i class="fas fa-filter"></i>
                            Filter Tanggal
                        </button>
                        @if($selectedDate)
                            <a href="{{ route('pos.payments') }}"
                               class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 whitespace-nowrap">
                                <i class="fas fa-times"></i>
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                <!-- Active Filters Indicator -->
                <div x-show="searchQuery || statusFilter !== 'all' || paymentMethodFilter !== 'all'" x-cloak
                     class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200">
                    <span class="text-xs text-slate-500">Filter aktif:</span>
                    <template x-if="searchQuery">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-700">
                            <i class="fas fa-search text-slate-400 text-xs"></i>
                            "<span class="truncate max-w-[100px]" x-text="searchQuery"></span>"
                            <button type="button" @click="searchQuery = ''; applyServerFilters()" class="ml-1 text-slate-400 hover:text-red-500">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                    </template>
                    <template x-if="statusFilter !== 'all'">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-semibold whitespace-nowrap"
                              :class="statusFilter === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                            <i class="fas text-xs" :class="statusFilter === 'paid' ? 'fa-circle-check' : 'fa-clock'"></i>
                            <span x-text="statusFilter === 'paid' ? 'Sudah Dibayar' : 'Belum Bayar'"></span>
                            <button type="button" @click="setStatusFilter('all')" class="ml-1 opacity-70 hover:opacity-100">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                    </template>
                    <template x-if="paymentMethodFilter !== 'all'">
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-1 text-xs font-semibold whitespace-nowrap"
                              :class="paymentMethodFilter === 'cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'">
                            <i class="fas text-xs" :class="paymentMethodFilter === 'cash' ? 'fa-money-bill-wave' : 'fa-university'"></i>
                            <span x-text="paymentMethodFilter === 'cash' ? 'Cash' : 'Transfer'"></span>
                            <button type="button" @click="setPaymentMethodFilter('all')" class="ml-1 opacity-70 hover:opacity-100">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </span>
                    </template>
                    <button type="button" @click="resetFilters()" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 hover:underline whitespace-nowrap">
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
                     class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 px-6 py-16 text-center">
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Tidak ditemukan</h3>
                    <p class="mt-1 text-sm text-slate-500">
                        Tidak ada transaksi yang sesuai dengan filter Anda.
                    </p>
                    <button type="button" @click="resetFilters()" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600">
                        <i class="fas fa-rotate-left"></i>
                        Reset filter
                    </button>
                </div>

                @if($payments->isEmpty())
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 px-6 py-16 text-center">
                        <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <i class="fas fa-file-invoice text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">
                            {{ $selectedDate ? 'Tidak ada transaksi' : 'Belum ada transaksi' }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $selectedDate ? 'Tidak ditemukan transaksi pada tanggal ' . \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M Y') : 'Transaksi yang diproses melalui POS akan muncul di sini.' }}
                        </p>
                        @if($selectedDate)
                            <a href="{{ route('pos.payments') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600">
                                <i class="fas fa-rotate-left"></i>
                                Tampilkan semua transaksi
                            </a>
                        @else
                            <a href="{{ route('pos') }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-600">
                                <i class="fas fa-cash-register"></i>
                                Buka POS
                            </a>
                        @endif
                    </div>
                @else
                    <!-- Desktop Table View - FIXED NO SCROLL -->
                    <div class="hidden lg:block" x-show="!isLoading && filteredCount > 0">
                        @foreach($groupedPayments as $dateKey => $transactions)
                            @php
                                $dateLabel = $dateKey !== 'unknown'
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $dateKey)->translatedFormat('l, d M Y')
                                    : 'Tanggal tidak diketahui';
                            @endphp

                            <!-- Date Header -->
                            <div class="mb-3 {{ !$loop->first ? 'mt-8' : '' }}"
                                 x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar-day text-emerald-500 text-sm"></i>
                                    <span class="text-sm font-bold text-slate-800">{{ $dateLabel }}</span>
                                    <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700"
                                          x-text="getVisibleCountInGroup('{{ $dateKey }}') + ' transaksi'">
                                        {{ count($transactions) }} transaksi
                                    </span>
                                </div>
                            </div>

                            <!-- Compact Table Container -->
                            <div class="mb-6" x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                <div class="overflow-x-auto rounded-lg border border-slate-200">
                                    <table class="w-full divide-y divide-slate-200 min-w-full">
                                        <thead class="bg-slate-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Waktu & Ref</th>
                                                <th class="px-3 py-2 text-left text-xs font-semibold uppercase tracking-wider text-slate-700">Pembeli</th>
                                                <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Total</th>
                                                <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Dibayar</th>
                                                <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Sisa/Kembali</th>
                                                <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-slate-700">Metode</th>
                                                <th class="px-3 py-2 text-center text-xs font-semibold uppercase tracking-wider text-slate-700">Status</th>
                                                <th class="px-3 py-2 text-right text-xs font-semibold uppercase tracking-wider text-slate-700">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-slate-100">
                                            @foreach($transactions as $payment)
                                                @php
                                                    $createdAt = $payment->created_at;
                                                    $grandTotal = $payment->grand_total ?? 0;
                                                    $paid = $payment->payment_received ?? 0;
                                                    $balance = $payment->balance_due ?? max($grandTotal - $paid, 0);
                                                    $change = $payment->change_due ?? max($paid - $grandTotal, 0);
                                                    $status = strtolower($payment->status ?? 'pending');
                                                    $paymentMethod = strtolower($payment->payment_method ?? 'cash');
                                                    $isPaid = in_array($status, ['paid', 'dibayar'], true);
                                                    $isCancelled = $status === 'cancelled';
                                                @endphp
                                                <tr class="hover:bg-slate-50/50 transition-colors duration-150"
                                                    x-show="isPaymentIdVisible({{ $payment->id }})"
                                                    data-payment-id="{{ $payment->id }}"
                                                    data-date-group="{{ $dateKey }}">
                                                    <!-- Time & Ref -->
                                                    <td class="px-3 py-2">
                                                        <div class="text-xs font-semibold text-slate-900">{{ $createdAt?->format('H:i') ?? '-' }}</div>
                                                        <div class="text-[10px] text-slate-500 font-medium mt-0.5 truncate max-w-20">{{ $payment->reference ?? '-' }}</div>
                                                    </td>
                                                    <!-- Customer -->
                                                    <td class="px-3 py-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="shrink-0">
                                                                <div class="h-7 w-7 inline-flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 font-semibold text-xs">
                                                                    {{ strtoupper(substr($payment->customer_name ?? 'G', 0, 2)) }}
                                                                </div>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-xs font-semibold text-slate-900 truncate max-w-[100px]" title="{{ $payment->customer_name ?? 'Guest' }}">
                                                                    {{ $payment->customer_name ?? 'Guest' }}
                                                                </p>
                                                                <p class="text-[10px] text-slate-500 mt-0.5 capitalize truncate max-w-[100px]">
                                                                    {{ $payment->customer_type ?? '-' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <!-- Grand Total -->
                                                    <td class="px-3 py-2 text-right">
                                                        <span class="text-xs font-semibold text-slate-900 whitespace-nowrap">
                                                            Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                    <!-- Paid -->
                                                    <td class="px-3 py-2 text-right">
                                                        <span class="text-xs font-semibold text-emerald-600 whitespace-nowrap">
                                                            Rp {{ number_format($paid, 0, ',', '.') }}
                                                        </span>
                                                    </td>
                                                    <!-- Balance/Change -->
                                                    <td class="px-3 py-2 text-right">
                                                        @if($change > 0)
                                                            <span class="text-xs font-semibold text-emerald-600 whitespace-nowrap">
                                                                +{{ number_format($change, 0, ',', '.') }}
                                                            </span>
                                                        @elseif($balance > 0)
                                                            <span class="text-xs font-semibold text-amber-600 whitespace-nowrap">
                                                                -{{ number_format($balance, 0, ',', '.') }}
                                                            </span>
                                                        @else
                                                            <span class="text-xs text-slate-400 whitespace-nowrap">-</span>
                                                        @endif
                                                    </td>
                                                    <!-- Payment Method -->
                                                    <td class="px-3 py-2 text-center">
                                                        @if($paymentMethod === 'transfer')
                                                            <span class="inline-flex items-center justify-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 border border-blue-100">
                                                                <i class="fas fa-university text-[8px]"></i>
                                                                <span>Transfer</span>
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center justify-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-100">
                                                                <i class="fas fa-money-bill-wave text-[8px]"></i>
                                                                <span>Cash</span>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <!-- Status -->
                                                    <td class="px-3 py-2 text-center">
                                                        @if($isCancelled)
                                                            <span class="inline-flex items-center justify-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700 border border-rose-100">
                                                                <i class="fas fa-ban text-[8px]"></i>
                                                                <span>Batal</span>
                                                            </span>
                                                        @elseif($isPaid)
                                                            <span class="inline-flex items-center justify-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-100">
                                                                <i class="fas fa-circle-check text-[8px]"></i>
                                                                <span>Dibayar</span>
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center justify-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-100">
                                                                <i class="fas fa-clock text-[8px]"></i>
                                                                <span>Belum</span>
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <!-- Actions -->
                                                    <td class="px-3 py-2 text-right relative">
                                                        <div class="flex items-center justify-end gap-1">
                                                            <button type="button" @click="showDetailById({{ $payment->id }})"
                                                                class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300"
                                                                title="Lihat Detail">
                                                                <i class="fas fa-eye text-xs"></i>
                                                            </button>
                                                            @if(!$isPaid && !$isCancelled)
                                                                <button type="button" @click="openPayModalById({{ $payment->id }})"
                                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-emerald-200 bg-white text-emerald-500 transition hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300"
                                                                    title="Tandai Dibayar">
                                                                    <i class="fas fa-check text-xs"></i>
                                                                </button>
                                                            @endif
                                                            <div x-data="{ open: false }" class="relative">
                                                                <button type="button"
                                                                        @click="open = !open; $event.stopPropagation()"
                                                                        class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-50 hover:text-slate-700"
                                                                        title="Ubah Status">
                                                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                                                </button>
                                                                <div x-show="open" x-cloak
                                                                     x-transition:enter="transition ease-out duration-100"
                                                                     x-transition:enter-start="opacity-0 scale-95"
                                                                     x-transition:enter-end="opacity-100 scale-100"
                                                                     x-transition:leave="transition ease-in duration-75"
                                                                     x-transition:leave-start="opacity-100 scale-100"
                                                                     x-transition:leave-end="opacity-0 scale-95"
                                                                     class="absolute right-0 mt-1 z-50 w-40 origin-top-right rounded-lg border border-slate-200 bg-white py-1 shadow-xl"
                                                                     @click.away="open = false">
                                                                    <form method="POST" action="{{ route('pos.payments.status', $payment) }}">
                                                                        @csrf
                                                                        @method('patch')
                                                                        <input type="hidden" name="status" value="pending">
                                                                        <button type="submit"
                                                                                class="flex w-full items-center justify-between gap-2 px-3 py-2 text-xs font-semibold transition {{ $status === 'pending' ? 'bg-emerald-50 text-emerald-700 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-50' }}"
                                                                                {{ $status === 'pending' ? 'disabled' : '' }}
                                                                                @click="open = false">
                                                                            <span class="inline-flex items-center gap-2">
                                                                                <i class="fas fa-rotate-left text-xs"></i>
                                                                                Belum Dibayar
                                                                            </span>
                                                                            @if($status === 'pending')
                                                                                <i class="fas fa-check text-xs text-emerald-500"></i>
                                                                            @endif
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST" action="{{ route('pos.payments.status', $payment) }}">
                                                                        @csrf
                                                                        @method('patch')
                                                                        <input type="hidden" name="status" value="cancelled">
                                                                        <button type="submit"
                                                                                class="flex w-full items-center justify-between gap-2 px-3 py-2 text-xs font-semibold transition {{ $status === 'cancelled' ? 'bg-rose-50 text-rose-700 cursor-not-allowed' : 'text-rose-600 hover:bg-rose-50' }}"
                                                                                {{ $status === 'cancelled' ? 'disabled' : '' }}
                                                                                @click="open = false">
                                                                            <span class="inline-flex items-center gap-2">
                                                                                <i class="fas fa-ban text-xs"></i>
                                                                                Batalkan
                                                                            </span>
                                                                            @if($status === 'cancelled')
                                                                                <i class="fas fa-check text-xs text-rose-500"></i>
                                                                            @endif
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Mobile Card View -->
                    <div class="space-y-4 lg:hidden" x-show="!isLoading && filteredCount > 0">
                        @foreach($groupedPayments as $dateKey => $transactions)
                            @php
                                $dateLabel = $dateKey !== 'unknown'
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $dateKey)->translatedFormat('l, d M Y')
                                    : 'Tanggal tidak diketahui';
                            @endphp

                            <!-- Date Header -->
                            <div class="flex items-center gap-3"
                                 x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                <i class="fas fa-calendar-day text-emerald-500 text-sm"></i>
                                <span class="text-sm font-bold text-slate-800">{{ $dateLabel }}</span>
                                <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-700"
                                      x-text="getVisibleCountInGroup('{{ $dateKey }}')">
                                    {{ count($transactions) }}
                                </span>
                            </div>

                            <div class="grid gap-3" x-show="hasVisibleItemsInGroup('{{ $dateKey }}')">
                                @foreach($transactions as $payment)
                                    @php
                                        $createdAt = $payment->created_at;
                                        $grandTotal = $payment->grand_total ?? 0;
                                        $paid = $payment->payment_received ?? 0;
                                        $balance = $payment->balance_due ?? max($grandTotal - $paid, 0);
                                        $change = $payment->change_due ?? max($paid - $grandTotal, 0);
                                        $status = strtolower($payment->status ?? 'pending');
                                        $paymentMethod = strtolower($payment->payment_method ?? 'cash');
                                        $isPaid = in_array($status, ['paid', 'dibayar'], true);
                                        $isCancelled = $status === 'cancelled';
                                    @endphp
                                     <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"
                                         x-show="isPaymentIdVisible({{ $payment->id }})"
                                         data-payment-id="{{ $payment->id }}"
                                         data-date-group="{{ $dateKey }}">
                                        <!-- Header -->
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                                <div class="h-8 w-8 shrink-0 inline-flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 font-semibold text-xs">
                                                    {{ strtoupper(substr($payment->customer_name ?? 'G', 0, 2)) }}
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="font-semibold text-slate-900 truncate text-sm" title="{{ $payment->customer_name ?? 'Guest' }}">
                                                        {{ $payment->customer_name ?? 'Guest' }}
                                                    </p>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span class="text-xs text-slate-500">{{ $createdAt?->format('H:i') }}</span>
                                                        <span class="text-xs">
                                                            @if($paymentMethod === 'transfer')
                                                                <span class="inline-flex items-center gap-1 text-blue-600 font-medium">
                                                                    <i class="fas fa-university text-[10px]"></i>
                                                                    <span>Transfer</span>
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1 text-emerald-600 font-medium">
                                                                    <i class="fas fa-money-bill-wave text-[10px]"></i>
                                                                    <span>Cash</span>
                                                                </span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Status Badge -->
                                            @if($isCancelled)
                                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold text-rose-700 border border-rose-100">
                                                    <i class="fas fa-ban text-[8px]"></i>
                                                    <span class="truncate">Batal</span>
                                                </span>
                                            @elseif($isPaid)
                                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 border border-emerald-100">
                                                    <i class="fas fa-circle-check text-[8px]"></i>
                                                    <span class="truncate">Dibayar</span>
                                                </span>
                                            @else
                                                <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-semibold text-amber-700 border border-amber-100">
                                                    <i class="fas fa-clock text-[8px]"></i>
                                                    <span class="truncate">Belum</span>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Reference -->
                                        <div class="mt-2">
                                            <span class="text-xs text-slate-500 font-medium truncate block">{{ $payment->reference ?? '-' }}</span>
                                        </div>

                                        <!-- Amount Info -->
                                        <div class="mt-3 grid grid-cols-3 gap-2 rounded-lg bg-slate-50 p-2 text-center">
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-semibold uppercase text-slate-700 mb-0.5 truncate">Total</p>
                                                <p class="text-xs font-semibold text-slate-900 truncate">{{ number_format($grandTotal, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-semibold uppercase text-slate-700 mb-0.5 truncate">Dibayar</p>
                                                <p class="text-xs font-semibold text-emerald-600 truncate">{{ number_format($paid, 0, ',', '.') }}</p>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-[10px] font-semibold uppercase text-slate-700 mb-0.5 truncate">
                                                    {{ $change > 0 ? 'Kembali' : ($balance > 0 ? 'Sisa' : 'Sisa') }}
                                                </p>
                                                <p class="text-xs font-semibold {{ $change > 0 ? 'text-emerald-600' : ($balance > 0 ? 'text-amber-600' : 'text-slate-400') }} truncate">
                                                    {{ $change > 0 ? number_format($change, 0, ',', '.') : ($balance > 0 ? number_format($balance, 0, ',', '.') : '-') }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="mt-3 flex items-center gap-2 border-t border-slate-100 pt-3">
                                            <button type="button" @click="showDetailById({{ $payment->id }})"
                                                class="flex-1 inline-flex items-center justify-center gap-1 rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-emerald-50 hover:border-emerald-300 hover:text-emerald-600">
                                                <i class="fas fa-eye text-xs"></i>
                                                Detail
                                            </button>
                                            @if(!$isPaid && !$isCancelled)
                                                <button type="button" @click="openPayModalById({{ $payment->id }})"
                                                    class="flex-1 inline-flex items-center justify-center gap-1 rounded-lg bg-emerald-500 px-2 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-600">
                                                    <i class="fas fa-check text-xs"></i>
                                                    Bayar
                                                </button>
                                            @endif
                                            <div x-data="{ open: false }" class="relative">
                                                <button type="button" @click="open = !open; $event.stopPropagation()"
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-50 hover:text-slate-700"
                                                        title="Ubah Status">
                                                    <i class="fas fa-ellipsis-v text-xs"></i>
                                                </button>
                                                <div x-show="open" x-cloak
                                                     x-transition:enter="transition ease-out duration-100"
                                                     x-transition:enter-start="opacity-0 scale-95"
                                                     x-transition:enter-end="opacity-100 scale-100"
                                                     x-transition:leave="transition ease-in duration-75"
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute right-0 bottom-full mb-2 z-50 w-36 origin-bottom-right rounded-lg border border-slate-200 bg-white py-1 shadow-xl"
                                                     @click.away="open = false">
                                                    <form method="POST" action="{{ route('pos.payments.status', $payment) }}">
                                                        @csrf
                                                        @method('patch')
                                                        <input type="hidden" name="status" value="pending">
                                                        <button type="submit"
                                                                class="flex w-full items-center justify-between gap-2 px-2 py-1.5 text-xs font-semibold transition {{ $status === 'pending' ? 'bg-emerald-50 text-emerald-700 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-50' }}"
                                                                {{ $status === 'pending' ? 'disabled' : '' }}
                                                                @click="open = false">
                                                            <span class="inline-flex items-center gap-1">
                                                                <i class="fas fa-rotate-left text-xs"></i>
                                                                Belum
                                                            </span>
                                                            @if($status === 'pending')
                                                                <i class="fas fa-check text-xs text-emerald-500"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('pos.payments.status', $payment) }}">
                                                        @csrf
                                                        @method('patch')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit"
                                                                class="flex w-full items-center justify-between gap-2 px-2 py-1.5 text-xs font-semibold transition {{ $status === 'cancelled' ? 'bg-rose-50 text-rose-700 cursor-not-allowed' : 'text-rose-600 hover:bg-rose-50' }}"
                                                                {{ $status === 'cancelled' ? 'disabled' : '' }}
                                                                @click="open = false">
                                                            <span class="inline-flex items-center gap-1">
                                                                <i class="fas fa-ban text-xs"></i>
                                                                Batalkan
                                                            </span>
                                                            @if($status === 'cancelled')
                                                                <i class="fas fa-check text-xs text-rose-500"></i>
                                                            @endif
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if(isset($paymentsPaginator) && $paymentsPaginator->total() > 0)
            <div class="flex flex-col gap-3 rounded-2xl bg-white px-4 py-4 shadow-sm ring-1 ring-slate-200 sm:flex-row sm:items-center sm:justify-between">
                <div class="text-sm text-slate-500">
                    @php
                        $from = $paymentsPaginator->firstItem() ?? 0;
                        $to = $paymentsPaginator->lastItem() ?? 0;
                    @endphp
                    Menampilkan <span class="font-semibold text-slate-700">{{ $from }}-{{ $to }}</span> dari <span class="font-semibold text-slate-700">{{ $paymentsPaginator->total() }}</span> transaksi
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <form action="{{ route('pos.payments') }}" method="GET" class="flex items-center gap-2 text-xs sm:text-sm">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <input type="hidden" name="search" :value="searchQuery">
                        <input type="hidden" name="status" :value="statusFilter">
                        <input type="hidden" name="payment_method" :value="paymentMethodFilter">
                        <label for="per_page" class="font-semibold text-slate-600 whitespace-nowrap">Per halaman</label>
                        <select id="per_page" name="per_page" onchange="this.form.submit()"
                                class="h-9 rounded-lg border border-slate-200 bg-white px-2 text-slate-700 focus:border-emerald-400 focus:ring-emerald-400">
                            @foreach([20, 40, 80, 120, 200] as $option)
                                <option value="{{ $option }}" {{ (int)($perPage ?? 80) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    </form>
                    <div>
                        {{ $paymentsPaginator->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Detail Modal -->
        <div x-show="showDetailModal" x-cloak
             class="fixed inset-0 z-60 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
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
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Detail Transaksi</h3>
                        <p class="text-xs text-slate-500" x-text="selectedPayment?.reference || '-'"></p>
                    </div>
                    <button type="button" @click="showDetailModal = false"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Customer & Status -->
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="h-12 w-12 inline-flex items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 font-bold">
                                <span x-text="(selectedPayment?.customer_name || 'G').substring(0, 2).toUpperCase()"></span>
                            </span>
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 truncate" x-text="selectedPayment?.customer_name || 'Guest'"></p>
                                <p class="text-sm text-slate-500 capitalize truncate" x-text="selectedPayment?.customer_type || '-'"></p>
                            </div>
                        </div>
                        <template x-if="isPaymentCancelled(selectedPayment)">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-500/10 px-3 py-1.5 text-xs font-bold text-rose-700">
                                <i class="fas fa-ban"></i>
                                Dibatalkan
                            </span>
                        </template>
                        <template x-if="!isPaymentCancelled(selectedPayment) && isPaymentPaid(selectedPayment)">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-3 py-1.5 text-xs font-bold text-emerald-700">
                                <i class="fas fa-circle-check"></i>
                                Dibayar
                            </span>
                        </template>
                        <template x-if="!isPaymentCancelled(selectedPayment) && !isPaymentPaid(selectedPayment)">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 px-3 py-1.5 text-xs font-bold text-amber-700">
                                <i class="fas fa-clock"></i>
                                Belum Bayar
                            </span>
                        </template>
                    </div>

                    <!-- Meta Info -->
                    <div class="flex justify-between text-xs text-slate-500 px-1">
                        <span>Kasir: <span class="font-semibold text-slate-700" x-text="selectedPayment?.creator?.name || '-'"></span></span>
                        <span x-text="selectedPayment?.created_at ? new Date(selectedPayment.created_at).toLocaleString('id-ID') : '-'"></span>
                    </div>

                    <!-- Shipping Address -->
                    <template x-if="selectedPayment?.shipping_address">
                        <div class="rounded-xl bg-slate-50 border border-slate-100 p-3 text-sm">
                            <p class="text-xs font-bold text-slate-500 uppercase mb-1">Alamat Pengiriman</p>
                            <p class="text-slate-700" x-text="selectedPayment.shipping_address"></p>
                        </div>
                    </template>

                    <!-- Payment Method Badge -->
                    <div>
                        <template x-if="selectedPayment?.payment_method === 'transfer'">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-blue-50 border border-blue-100 px-4 py-2.5 text-sm font-bold text-blue-700">
                                <i class="fas fa-university"></i>
                                Transfer Bank
                            </span>
                        </template>
                        <template x-if="selectedPayment?.payment_method === 'cash' || !selectedPayment?.payment_method">
                            <span class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-100 px-4 py-2.5 text-sm font-bold text-emerald-700">
                                <i class="fas fa-money-bill-wave"></i>
                                Cash
                            </span>
                        </template>
                    </div>

                    <!-- Items -->
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-700 mb-3">Rincian Item</p>
                        <template x-if="detailLoading">
                            <p class="text-xs text-slate-500 mb-2">Memuat detail transaksi...</p>
                        </template>
                        <div class="space-y-3">
                            <template x-for="item in (selectedPayment?.items || [])" :key="item.id">
                                <div class="flex items-center justify-between text-sm">
                                    <div class="min-w-0 flex-1">
                                        <span class="font-bold text-slate-900 truncate block" x-text="item.product_name || item.name || 'Produk'"></span>
                                        <div class="text-slate-500 mt-0.5">
                                            <span x-text="item.qty || 0"></span>
                                            <span x-text="' ' + (item.unit || 'pcs')"></span>
                                        </div>
                                    </div>
                                    <span class="font-bold text-slate-900 whitespace-nowrap ml-4" x-text="'Rp ' + formatNumber(item.subtotal || 0)"></span>
                                </div>
                            </template>
                            <template x-if="!selectedPayment?.items?.length">
                                <p class="text-sm text-slate-400 text-center py-2">Tidak ada detail item.</p>
                            </template>
                        </div>
                    </div>

                    <!-- Summary -->
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-bold text-slate-700 whitespace-nowrap" x-text="'Rp ' + formatNumber(selectedPayment?.subtotal || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Ongkir</span>
                            <span class="font-bold text-slate-700 whitespace-nowrap" x-text="'Rp ' + formatNumber(selectedPayment?.shipping_cost || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Tip</span>
                            <span class="font-bold text-slate-700 whitespace-nowrap" x-text="'Rp ' + formatNumber(selectedPayment?.tip || 0)"></span>
                        </div>
                        <template x-if="selectedPayment?.expense_amount > 0">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Pengeluaran</span>
                                <span class="font-bold text-slate-700 whitespace-nowrap" x-text="'Rp ' + formatNumber(selectedPayment?.expense_amount || 0)"></span>
                            </div>
                        </template>
                        <template x-if="selectedPayment?.discount > 0">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Diskon <span x-show="selectedPayment?.discount_percent > 0" x-text="'(' + selectedPayment.discount_percent + '%)'"></span></span>
                                <span class="font-bold text-orange-600 whitespace-nowrap" x-text="'-Rp ' + formatNumber(selectedPayment?.discount || 0)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between border-t border-slate-200 pt-3">
                            <span class="font-bold text-slate-900 text-base">Grand Total</span>
                            <span class="font-bold text-slate-900 text-base whitespace-nowrap" x-text="'Rp ' + formatNumber(selectedPayment?.grand_total || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Dibayar</span>
                            <span class="font-bold text-emerald-600 whitespace-nowrap" x-text="'Rp ' + formatNumber(selectedPayment?.payment_received || 0)"></span>
                        </div>
                        <template x-if="getBalance(selectedPayment) > 0">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Sisa</span>
                                <span class="font-bold text-amber-600 whitespace-nowrap" x-text="'Rp ' + formatNumber(getBalance(selectedPayment))"></span>
                            </div>
                        </template>
                        <template x-if="getChange(selectedPayment) > 0">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Kembalian</span>
                                <span class="font-bold text-emerald-600 whitespace-nowrap" x-text="'Rp ' + formatNumber(getChange(selectedPayment))"></span>
                            </div>
                        </template>
                    </div>

                    <!-- Transfer Info (if applicable) -->
                    <template x-if="selectedPayment?.payment_method === 'transfer'">
                        <div class="rounded-xl bg-blue-50 border border-blue-100 p-4">
                            <p class="text-xs font-bold text-blue-700 mb-2">Informasi Transfer</p>
                            <div class="space-y-2 text-sm">
                                <template x-if="selectedPayment?.bank_name">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">Bank</span>
                                        <span class="font-bold text-slate-700 truncate" x-text="selectedPayment.bank_name"></span>
                                    </div>
                                </template>
                                <template x-if="selectedPayment?.account_number">
                                    <div class="flex justify-between">
                                        <span class="text-slate-500">No. Rekening</span>
                                        <span class="font-bold text-slate-700 truncate" x-text="selectedPayment.account_number"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Note -->
                    <template x-if="selectedPayment?.note">
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-3">
                            <p class="text-xs font-bold text-amber-700 mb-1">Catatan</p>
                            <p class="text-sm text-amber-800 wrap-break-word" x-text="selectedPayment.note"></p>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 border-t border-slate-100 bg-slate-50 px-6 py-4">
                    <template x-if="!isPaymentCancelled(selectedPayment) && !isPaymentPaid(selectedPayment)">
                        <button type="button" @click="showDetailModal = false; openPayModal(selectedPayment)"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-600">
                            <i class="fas fa-check"></i>
                            Tandai Dibayar
                        </button>
                    </template>
                    <template x-if="isPaymentCancelled(selectedPayment) || isPaymentPaid(selectedPayment)">
                        <button type="button" @click="showDetailModal = false"
                                class="w-full inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Tutup
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div x-show="showPayModal" x-cloak
             class="fixed inset-0 z-60 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
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
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Konfirmasi Pembayaran</h3>
                        <p class="text-xs text-slate-500" x-text="paymentTarget?.customer_name || 'Guest'"></p>
                    </div>
                    <button type="button" @click="showPayModal = false"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Content -->
                <form :action="paymentTarget ? '{{ route('pos.payments.pay', ':id') }}'.replace(':id', paymentTarget.id) : '#'" method="POST" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="transaction_id" :value="paymentTarget?.id">

                    <!-- Amount Summary -->
                    <div class="rounded-xl bg-emerald-50 p-4 space-y-2 text-sm border border-emerald-100">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Grand Total</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + formatNumber(paymentTarget?.grand_total || 0)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sudah Dibayar</span>
                            <span class="font-bold text-slate-700" x-text="'Rp ' + formatNumber(paymentTarget?.payment_received || 0)"></span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 pt-2">
                            <span class="font-bold text-amber-700">Sisa Pembayaran</span>
                            <span class="font-bold text-amber-700" x-text="'Rp ' + formatNumber(getBalance(paymentTarget))"></span>
                        </div>
                    </div>

                    <!-- Payment Input -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Jumlah Pembayaran</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-sm font-bold text-emerald-400">Rp</span>
                            <input type="text" name="payment_amount" x-model="paymentAmount"
                                   inputmode="numeric"
                                   @input="formatPaymentInput"
                                   class="w-full h-12 rounded-xl border-2 border-emerald-200 bg-white pl-10 pr-4 text-lg font-bold text-slate-900 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Masukkan jumlah yang dibayarkan customer</p>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="setPaymentAmount(getBalance(paymentTarget))"
                                class="rounded-xl bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                            Bayar Lunas
                        </button>
                        <button type="button" @click="setPaymentAmount(Math.ceil(getBalance(paymentTarget) / 1000) * 1000)"
                                class="rounded-xl bg-slate-100 px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-slate-200">
                            Bulatkan
                        </button>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showPayModal = false"
                                class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-600">
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
        $paymentSummariesById = $payments->mapWithKeys(function ($p) {
            return [
                (string) $p->id => [
                    'id' => $p->id,
                    'order_id' => $p->order_id,
                    'reference' => $p->reference,
                    'customer_name' => $p->customer_name,
                    'customer_type' => $p->customer_type,
                    'grand_total' => (int) ($p->grand_total ?? 0),
                    'payment_received' => (int) ($p->payment_received ?? 0),
                    'balance_due' => (int) ($p->balance_due ?? 0),
                    'change_due' => (int) ($p->change_due ?? 0),
                    'payment_method' => strtolower((string) ($p->payment_method ?? 'cash')),
                    'status' => strtolower((string) ($p->status ?? 'pending')),
                    'date' => $p->created_at ? $p->created_at->format('Y-m-d') : 'unknown',
                    'created_at' => optional($p->created_at)?->toIso8601String(),
                ],
            ];
        });
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
                searchQuery: @json($filterState['search'] ?? ''),
                statusFilter: @json($filterState['status'] ?? 'all'),
                paymentMethodFilter: @json($filterState['payment_method'] ?? 'all'),
                datePreset: '{{ $selectedDate ? "custom" : "all" }}',
                isLoading: false,
                filteredCount: {{ $payments->count() }},
                filterDebounceTimer: null,

                // Toast
                toastMessage: '',
                toastVisible: false,
                toastType: 'success',
                detailLoading: false,

                // All payments data for filtering
                paymentDetailsById: @json($paymentSummariesById),
                paymentDetailCache: {},
                paymentDetailEndpointTemplate: '{{ route("pos.payments.detail", ":id") }}',
                allPayments: [],
                visiblePaymentIds: {},
                visibleCountByDate: {},

                initPage() {
                    this.allPayments = Object.values(this.paymentDetailsById).map(payment => ({
                        id: payment.id,
                        customer_name: payment.customer_name,
                        reference: payment.reference,
                        order_id: payment.order_id || null,
                        status: (payment.status || 'pending').toLowerCase(),
                        payment_method: (payment.payment_method || 'cash').toLowerCase(),
                        date: payment.date || 'unknown'
                    }));
                    this.filterData();
                },

                // Filter methods
                setStatusFilter(status) {
                    this.statusFilter = status;
                    this.applyServerFilters();
                },

                setPaymentMethodFilter(method) {
                    this.paymentMethodFilter = method;
                    this.applyServerFilters();
                },

                queueServerFilterApply() {
                    if (this.filterDebounceTimer) {
                        clearTimeout(this.filterDebounceTimer);
                    }

                    this.filterDebounceTimer = setTimeout(() => {
                        this.applyServerFilters();
                    }, 350);
                },

                applyServerFilters() {
                    window.location.href = this.buildFilterUrl({
                        date: '{{ $selectedDate }}',
                        per_page: '{{ (int)($perPage ?? 80) }}'
                    });
                },

                buildFilterUrl(extra = {}) {
                    const params = new URLSearchParams();

                    const dateValue = (extra.date ?? '').toString().trim();
                    if (dateValue) {
                        params.set('date', dateValue);
                    }

                    const perPageValue = parseInt(extra.per_page || '{{ (int)($perPage ?? 80) }}', 10);
                    if (!Number.isNaN(perPageValue) && perPageValue > 0) {
                        params.set('per_page', String(perPageValue));
                    }

                    const query = (this.searchQuery || '').trim();
                    if (query) {
                        params.set('search', query);
                    }

                    if (this.statusFilter && this.statusFilter !== 'all') {
                        params.set('status', this.statusFilter);
                    }

                    if (this.paymentMethodFilter && this.paymentMethodFilter !== 'all') {
                        params.set('payment_method', this.paymentMethodFilter);
                    }

                    const queryString = params.toString();
                    return queryString
                        ? `{{ route("pos.payments") }}?${queryString}`
                        : '{{ route("pos.payments") }}';
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
                        window.location.href = this.buildFilterUrl({ per_page: '{{ (int)($perPage ?? 80) }}' });
                        return;
                    } else if (preset === 'all') {
                        window.location.href = this.buildFilterUrl({ per_page: '{{ (int)($perPage ?? 80) }}' });
                        return;
                    }

                    if (targetDate) {
                        window.location.href = this.buildFilterUrl({ date: targetDate, per_page: '{{ (int)($perPage ?? 80) }}' });
                    }
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.statusFilter = 'all';
                    this.paymentMethodFilter = 'all';
                    this.applyServerFilters();
                },

                refreshPage() {
                    this.isLoading = true;
                    window.location.reload();
                },

                filterData() {
                    let count = 0;
                    const visiblePaymentIds = {};
                    const visibleCountByDate = {};

                    this.allPayments.forEach(payment => {
                        if (this.matchesFilters(payment)) {
                            visiblePaymentIds[payment.id] = true;
                            visibleCountByDate[payment.date] = (visibleCountByDate[payment.date] || 0) + 1;
                            count++;
                        }
                    });

                    this.visiblePaymentIds = visiblePaymentIds;
                    this.visibleCountByDate = visibleCountByDate;
                    this.filteredCount = count;
                },

                matchesFilters(payment) {
                    if (!payment) return false;

                    const statusValue = (payment.status || '').toLowerCase();
                    const paymentMethodValue = (payment.payment_method || 'cash').toLowerCase();

                    // Status filter
                    if (this.statusFilter === 'paid' && !['paid', 'dibayar'].includes(statusValue)) {
                        return false;
                    }
                    if (this.statusFilter === 'pending' && statusValue !== 'pending') {
                        return false;
                    }

                    // Payment method filter
                    if (this.paymentMethodFilter === 'cash' && paymentMethodValue !== 'cash') {
                        return false;
                    }
                    if (this.paymentMethodFilter === 'transfer' && paymentMethodValue !== 'transfer') {
                        return false;
                    }

                    // Search filter
                    if (this.searchQuery) {
                        const query = this.searchQuery.toLowerCase().trim();
                        const customerName = (payment.customer_name || '').toLowerCase();
                        const reference = (payment.reference || '').toLowerCase();
                        const orderId = (payment.order_id || '').toString().toLowerCase();
                        const paymentMethod = paymentMethodValue;

                        // Cari juga di metode pembayaran
                        const paymentMethodText = paymentMethodValue === 'cash' ? 'cash tunai' : 'transfer bank';

                        if (!customerName.includes(query) &&
                            !reference.includes(query) &&
                            !orderId.includes(query) &&
                            !paymentMethod.includes(query) &&
                            !paymentMethodText.includes(query)) {
                            return false;
                        }
                    }

                    return true;
                },

                isPaymentIdVisible(paymentId) {
                    return !!this.visiblePaymentIds[paymentId];
                },

                hasVisibleItemsInGroup(dateKey) {
                    return (this.visibleCountByDate[dateKey] || 0) > 0;
                },

                getVisibleCountInGroup(dateKey) {
                    return this.visibleCountByDate[dateKey] || 0;
                },

                // Modal methods
                showDetail(payment) {
                    this.selectedPayment = payment;
                    this.detailLoading = false;
                    this.showDetailModal = true;
                },

                async showDetailById(paymentId) {
                    const key = String(paymentId);
                    const summary = this.paymentDetailsById[key] || this.paymentDetailsById[paymentId] || null;

                    if (!summary) {
                        this.showToast('Detail transaksi tidak ditemukan', 'error');
                        return;
                    }

                    if (this.paymentDetailCache[key]) {
                        this.showDetail(this.paymentDetailCache[key]);
                        return;
                    }

                    this.selectedPayment = summary;
                    this.showDetailModal = true;
                    this.detailLoading = true;

                    try {
                        const endpoint = this.paymentDetailEndpointTemplate.replace(':id', encodeURIComponent(paymentId));
                        const response = await fetch(endpoint, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        const result = await response.json();

                        if (!response.ok || !result?.success || !result?.data) {
                            throw new Error(result?.message || 'Gagal memuat detail transaksi');
                        }

                        this.paymentDetailCache[key] = result.data;
                        this.selectedPayment = result.data;
                    } catch (error) {
                        this.showToast(error?.message || 'Gagal memuat detail transaksi', 'error');
                    } finally {
                        this.detailLoading = false;
                    }
                },

                openPayModalById(paymentId) {
                    const payment = this.paymentDetailsById[String(paymentId)] || this.paymentDetailsById[paymentId] || null;
                    if (!payment) return;
                    this.openPayModal(payment);
                },

                openPayModal(payment) {
                    if (this.isPaymentCancelled(payment)) {
                        this.showToast('Transaksi yang dibatalkan tidak dapat dibayar', 'error');
                        return;
                    }
                    this.paymentTarget = payment;
                    const balance = this.getBalance(payment);
                    this.paymentAmount = this.formatNumber(balance > 0 ? balance : payment.grand_total);
                    this.showPayModal = true;
                },

                isPaymentPaid(payment) {
                    if (!payment) return false;
                    const statusValue = (payment.status || '').toLowerCase();
                    return statusValue === 'paid' || statusValue === 'dibayar';
                },

                isPaymentCancelled(payment) {
                    if (!payment) return false;
                    return (payment.status || '').toLowerCase() === 'cancelled';
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
                },

                showToast(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.toastVisible = true;
                    setTimeout(() => {
                        this.toastVisible = false;
                    }, 3000);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
