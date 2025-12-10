<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-users"></i>
                </span>
                <h2 class="text-xl font-semibold leading-tight text-slate-700">Manajemen Customer</h2>
            </div>
            <p class="text-sm text-slate-500">Kelola data pelanggan dan ongkir default untuk transaksi POS.</p>
        </div>
    </x-slot>

    @php
        $statCards = [
            [
                'label' => 'Total Customer',
                'value' => number_format($stats['total']),
                'sub' => 'Pelanggan terdaftar',
                'icon' => 'fa-users',
                'accent' => 'bg-emerald-500/10 text-emerald-600',
            ],
            [
                'label' => 'Customer Baru',
                'value' => number_format($stats['newThisMonth']),
                'sub' => '30 hari terakhir',
                'icon' => 'fa-user-plus',
                'accent' => 'bg-sky-500/10 text-sky-600',
            ],
            [
                'label' => 'Pernah Transaksi',
                'value' => number_format($stats['withTransactions']),
                'sub' => 'Customer aktif',
                'icon' => 'fa-shopping-cart',
                'accent' => 'bg-indigo-500/10 text-indigo-600',
            ],
            [
                'label' => 'Tanpa Telepon',
                'value' => number_format($stats['noPhone']),
                'sub' => 'Perlu dilengkapi',
                'icon' => 'fa-phone-slash',
                'accent' => 'bg-amber-500/10 text-amber-600',
            ],
        ];

        $filterOptions = [
            'all' => [
                'label' => 'Semua',
                'description' => 'Seluruh customer',
            ],
            'recent' => [
                'label' => 'Baru ditambahkan',
                'description' => '30 hari terakhir',
            ],
            'with-transactions' => [
                'label' => 'Pernah transaksi',
                'description' => 'Customer aktif',
            ],
            'no-transactions' => [
                'label' => 'Belum transaksi',
                'description' => 'Perlu di-follow up',
            ],
        ];

        $activeFilter = $filter ?? 'all';
    @endphp

        <div class="space-y-6"
            x-data="customerManager()"
            x-init="init()"
            @keydown.escape.window="customerModalOpen = false; confirmOpen = false">
         
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['title' => 'Manajemen Customer']]" />

        <!-- Header Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-2">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                        <i class="fas fa-address-book"></i>
                        Data Master
                    </span>
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Manajemen Customer</h1>
                    <p class="text-xs text-slate-500">
                        Kelola data pelanggan, lihat riwayat transaksi, dan tentukan ongkir default untuk setiap customer.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600"
                        @click="customerModalOpen = true; customerMode = 'create'; resetCustomerForm();">
                        <i class="fas fa-user-plus me-2"></i>
                        Tambah Customer
                    </button>
                    <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800">
                        <i class="fas fa-arrow-rotate-left me-2"></i>
                        Reset Tampilan
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
            @foreach($statCards as $card)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/50">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-xs font-medium text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl {{ $card['accent'] }}">
                            <i class="fas {{ $card['icon'] }} text-base"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-xs text-slate-500">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>

        @if(session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-700 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                        <i class="fas fa-check"></i>
                    </span>
                    <div>
                        <p class="text-sm font-semibold">Berhasil</p>
                        <p class="text-sm text-emerald-700/80">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search & Filter Section -->
        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-200/60">
            <form action="{{ route('customers.index') }}" method="GET" class="space-y-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Cari customer berdasarkan nama, telepon, atau alamat"
                            class="w-full rounded-2xl border-slate-200 bg-white py-2.5 pl-12 pr-3.5 text-sm text-slate-700 shadow-inner shadow-slate-100 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        >
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input type="hidden" name="filter" value="{{ $activeFilter !== 'all' ? $activeFilter : '' }}">
                        <div class="hidden sm:flex gap-2">
                            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600">
                                <i class="fas fa-search me-2"></i>
                                Cari Customer
                            </button>
                            @if($hasActiveFilter)
                                <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                                    <i class="fas fa-rotate-left me-2"></i>
                                    Reset pencarian & filter
                                </a>
                            @endif
                        </div>

                        <!-- Mobile filter trigger -->
                        <button type="button" class="sm:hidden inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-700 hover:border-slate-300 hover:bg-slate-50"
                            @click="mobileFilterOpen = true">
                            <i class="fas fa-sliders"></i> Filter & Cari
                        </button>
                    </div>
                </div>

                <div class="hidden sm:flex flex-wrap items-center gap-2">
                    @foreach($filterOptions as $key => $option)
                        @php
                            $isActive = $activeFilter === $key;
                            $queryParams = array_filter([
                                'search' => ($search ?? null) ?: null,
                                'filter' => $key === 'all' ? null : $key,
                            ]);
                        @endphp
                        <a
                            href="{{ route('customers.index', $queryParams) }}"
                            class="group inline-flex items-center gap-2 rounded-full border px-3.5 py-2 text-sm transition {{ $isActive ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700' }}"
                        >
                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-[11px] font-semibold {{ $isActive ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-600' }}">
                                {{ $loop->iteration }}
                            </span>
                            <span class="flex flex-col">
                                <span class="font-semibold">{{ $option['label'] }}</span>
                                <span class="text-xs text-slate-400">{{ $option['description'] }}</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </form>
        </div>

        <!-- Mobile Filter Sheet -->
        <div x-show="mobileFilterOpen" x-cloak class="fixed inset-0 z-50 flex sm:hidden">
            <div class="flex-1 bg-black/50" @click="mobileFilterOpen = false"></div>
            <div class="w-[85%] max-w-sm bg-white h-full shadow-2xl border-l border-slate-200 flex flex-col"
                 x-transition:enter="transition transform duration-200"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition transform duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                <div class="flex items-center justify-between px-4 py-4 border-b border-slate-200">
                    <h3 class="text-base font-semibold text-slate-800">Filter & Cari</h3>
                    <button class="text-slate-500 hover:text-slate-700" @click="mobileFilterOpen = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Kata kunci</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center text-slate-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" x-model="mobileFilter.search" class="w-full rounded-xl border border-slate-200 bg-white py-2 pl-10 pr-3 text-sm text-slate-700 focus:border-sky-400 focus:ring-2 focus:ring-sky-200" placeholder="Cari nama/telepon/alamat">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-700">Filter</label>
                        <div class="flex flex-col gap-2">
                            @foreach($filterOptions as $key => $option)
                                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700">
                                    <input type="radio" class="text-emerald-600" name="mobile-filter" value="{{ $key }}" :checked="mobileFilter.filter === '{{ $key }}'" @change="mobileFilter.filter = '{{ $key }}'">
                                    <div>
                                        <p class="font-semibold">{{ $option['label'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $option['description'] }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-slate-200 flex gap-2">
                    <button class="flex-1 inline-flex items-center justify-center rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="resetMobileFilter()">
                        Reset
                    </button>
                    <button class="flex-1 inline-flex items-center justify-center rounded-xl bg-emerald-500 px-3 py-2 text-sm font-semibold text-white shadow-md hover:bg-emerald-600" @click="applyMobileFilter()">
                        Terapkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Customer Table -->
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/70">
            @if($customers->isEmpty())
                <div class="flex flex-col items-center justify-center gap-4 px-8 py-16 text-center">
                    <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="fas fa-users text-2xl"></i>
                    </span>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-slate-700">
                            {{ $hasActiveFilter ? 'Tidak ada hasil untuk filter ini' : 'Belum ada customer untuk ditampilkan' }}
                        </h3>
                        <p class="text-sm text-slate-500">
                            {{ $hasActiveFilter ? 'Coba reset pencarian atau ubah filter untuk melihat data lain.' : 'Tambah customer baru untuk mulai membangun database pelanggan.' }}
                        </p>
                    </div>
                    @if($hasActiveFilter)
                        <a href="{{ route('customers.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800">
                            <i class="fas fa-rotate-left me-2"></i>
                            Reset filter
                        </a>
                    @else
                        <button type="button"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600"
                            @click="customerModalOpen = true; customerMode = 'create'; resetCustomerForm();">
                            <i class="fas fa-user-plus me-2"></i>
                            Tambah Customer Pertama
                        </button>
                    @endif
                </div>
            @else
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Nama</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Telepon</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Alamat</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ongkir Default</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($customers as $customer)
                            @php
                                $hasTransaction = in_array($customer->customer_name, $customersWithTransactions ?? []);
                                $missingPhone = empty($customer->phone);
                            @endphp
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 font-semibold text-sm">
                                            {{ strtoupper(substr($customer->customer_name, 0, 2)) }}
                                        </span>
                                        <div class="space-y-1">
                                            <span class="font-semibold text-slate-800">{{ $customer->customer_name }}</span>
                                            <div class="flex flex-wrap gap-1">
                                                @if($hasTransaction)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-100">
                                                        <i class="fas fa-receipt"></i> Pernah transaksi
                                                    </span>
                                                @endif
                                                @if($missingPhone)
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-[11px] font-semibold text-amber-700 border border-amber-100">
                                                        <i class="fas fa-phone-slash"></i> Tanpa telepon
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $customer->phone ?: '-' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">{{ $customer->address ?: '-' }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-800">
                                    Rp {{ number_format($customer->shipping_cost ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button type="button"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 transition"
                                            aria-label="Buka menu aksi"
                                            @click="open = !open">
                                            <i class="fas fa-ellipsis-vertical"></i>
                                        </button>
                                        <div x-show="open" x-transition.opacity x-cloak
                                            class="absolute right-0 mt-2 w-40 rounded-xl border border-slate-200 bg-white shadow-lg z-20">
                                            <button type="button"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-emerald-700 hover:bg-emerald-50"
                                                @click="open = false; openForEdit({{ $customer->toJson() }})">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button"
                                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-700 hover:bg-red-50"
                                                @click="open = false; confirmDelete('{{ route('customers.destroy', $customer) }}')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden divide-y divide-slate-100">
                    @foreach($customers as $customer)
                    @php
                        $hasTransaction = in_array($customer->customer_name, $customersWithTransactions ?? []);
                        $missingPhone = empty($customer->phone);
                    @endphp
                    <div class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-600 font-semibold text-sm shrink-0">
                                    {{ strtoupper(substr($customer->customer_name, 0, 2)) }}
                                </span>
                                <div class="space-y-1">
                                    <p class="font-semibold text-slate-800">{{ $customer->customer_name }}</p>
                                    <div class="flex flex-wrap gap-1">
                                        @if($hasTransaction)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-100">
                                                <i class="fas fa-receipt"></i> Pernah transaksi
                                            </span>
                                        @endif
                                        @if($missingPhone)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-1 text-[11px] font-semibold text-amber-700 border border-amber-100">
                                                <i class="fas fa-phone-slash"></i> Tanpa telepon
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-slate-500">{{ $customer->phone ?: 'Belum ada telepon' }}</p>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-emerald-600">
                                Rp {{ number_format($customer->shipping_cost ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($customer->address)
                        <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ $customer->address }}</p>
                        @endif
                        <div class="relative mt-3" x-data="{ open: false }">
                            <button type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.75 text-xs font-semibold text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 transition"
                                @click="open = !open"
                                aria-label="Buka menu aksi">
                                <i class="fas fa-ellipsis-vertical"></i>
                                Kelola
                            </button>
                            <div x-show="open" x-transition.opacity x-cloak
                                class="absolute z-10 mt-2 w-40 rounded-xl border border-slate-200 bg-white shadow-lg">
                                <button type="button"
                                    class="w-full flex items-center gap-2 px-3 py-1.75 text-sm text-emerald-700 hover:bg-emerald-50"
                                    @click="open = false; openForEdit({{ $customer->toJson() }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button"
                                    class="w-full flex items-center gap-2 px-3 py-1.75 text-sm text-red-700 hover:bg-red-50"
                                    @click="open = false; confirmDelete('{{ route('customers.destroy', $customer) }}')">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Modal Customer -->
        <div
        <div x-show="customerModalOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            style="display: none;"
            x-cloak
            @keydown.tab.prevent="handleTab($event)">
            <div 
                x-show="customerModalOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-[90%] max-w-lg bg-white rounded-2xl shadow-2xl p-6"
                @click.away="closeModal()"
                x-ref="modalPanel">
                <button @click="closeModal()" class="absolute text-xl text-slate-400 top-4 right-4 hover:text-slate-600 transition">
                    <i class="fas fa-times"></i>
                </button>

                <div class="flex items-center gap-3 mb-6">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <i class="fas" :class="customerMode === 'create' ? 'fa-user-plus' : 'fa-user-edit'"></i>
                    </span>
                    <h2 class="text-xl font-semibold text-slate-800" x-text="customerMode === 'create' ? 'Tambah Customer' : 'Edit Customer'"></h2>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        <p class="font-semibold">Periksa kembali data berikut:</p>
                        <ul class="mt-2 space-y-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" :action="formAction()" @submit.once="isSubmitting = true">
                    @csrf
                    <template x-if="customerMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="space-y-4">
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Nama Customer <span class="text-red-500">*</span></label>
                            <input x-ref="customerName" type="text" name="customer_name" x-model="customerForm.customer_name" required
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   placeholder="Masukkan nama customer">
                            @error('customer_name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Telepon</label>
                            <input type="text" name="phone" x-model="customerForm.phone"
                                   class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                   placeholder="Contoh: 08123456789">
                            @error('phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Ongkir Default</label>
                            <div class="relative">
                                <span class="absolute text-sm font-semibold text-slate-500 -translate-y-1/2 left-4 top-1/2">Rp</span>
                                <input type="number" min="0" name="shipping_cost" x-model.number="customerForm.shipping_cost"
                                       class="w-full px-4 py-2.5 pl-12 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition" placeholder="0">
                            </div>
                            @error('shipping_cost')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-slate-700">Alamat</label>
                            <textarea name="address" x-model="customerForm.address" rows="3"
                                      class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition"
                                      placeholder="Alamat lengkap customer"></textarea>
                            @error('address')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center gap-3 mt-6 pt-4 border-t border-slate-100">
                        <button type="submit" :disabled="isSubmitting" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.25 text-sm font-semibold text-white bg-emerald-500 rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-400/30 disabled:opacity-60 disabled:cursor-not-allowed">
                            <template x-if="!isSubmitting">
                                <i class="fas fa-save"></i>
                            </template>
                            <template x-if="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                        <button type="button" @click="closeModal()" class="px-4 py-2.25 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirm Delete Modal -->
        <div
            x-show="confirmOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            style="display: none;"
            x-cloak
            @keydown.tab.prevent="handleConfirmTab($event)">
            <div class="relative w-[90%] max-w-md bg-white rounded-2xl shadow-2xl p-6" @click.away="confirmOpen = false" x-ref="confirmPanel">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800">Hapus customer?</h3>
                        <p class="text-sm text-slate-500">Tindakan ini tidak bisa dibatalkan.</p>
                    </div>
                </div>
                <form method="POST" :action="confirmActionUrl" class="space-y-4">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center gap-3">
                        <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.25 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition">
                            <i class="fas fa-trash"></i>
                            Hapus
                        </button>
                        <button type="button" @click="confirmOpen = false" class="flex-1 px-4 py-2.25 text-sm font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerManager', () => ({
                customerModalOpen: {{ $errors->any() ? 'true' : 'false' }},
                customerMode: 'create',
                isSubmitting: false,
                customerForm: {
                    id: '',
                    customer_name: '',
                    phone: '',
                    address: '',
                    shipping_cost: 0,
                },

                confirmOpen: false,
                confirmActionUrl: '',

                mobileFilterOpen: false,
                mobileFilter: {
                    search: @json($search ?? ''),
                    filter: @json($activeFilter ?? 'all'),
                },

                serverState: {
                    hasErrors: {{ $errors->any() ? 'true' : 'false' }},
                    old: @json(old()),
                },

                focusables() {
                    return Array.from(this.$refs.modalPanel?.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])') || []).filter(el => !el.hasAttribute('disabled'));
                },

                handleTab(event) {
                    const nodes = this.focusables();
                    if (!nodes.length) return;
                    const first = nodes[0];
                    const last = nodes[nodes.length - 1];
                    if (event.shiftKey && document.activeElement === first) {

                    confirmFocusables() {
                        return Array.from(this.$refs.confirmPanel?.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])') || []).filter(el => !el.hasAttribute('disabled'));
                    },

                    handleConfirmTab(event) {
                        const nodes = this.confirmFocusables();
                        if (!nodes.length) return;
                        const first = nodes[0];
                        const last = nodes[nodes.length - 1];
                        if (event.shiftKey && document.activeElement === first) {
                            last.focus();
                        } else if (!event.shiftKey && document.activeElement === last) {
                            first.focus();
                        }
                    },
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        first.focus();
                    }
                },

                init() {
                    if (this.serverState.hasErrors) {
                        this.customerMode = 'create';
                        this.customerForm = {
                            id: '',
                            customer_name: this.serverState.old.customer_name || '',
                            phone: this.serverState.old.phone || '',
                            address: this.serverState.old.address || '',
                            shipping_cost: Number(this.serverState.old.shipping_cost || 0),
                        };
                        this.$nextTick(() => this.$refs.customerName && this.$refs.customerName.focus());
                    }
                },

                resetCustomerForm() {
                    this.customerForm = {
                        id: '',
                        customer_name: '',
                        phone: '',
                        address: '',
                        shipping_cost: 0,
                    };
                },

                openForEdit(customer) {
                    this.customerMode = 'edit';
                    this.customerForm = { 
                        id: customer.id,
                        customer_name: customer.customer_name,
                        phone: customer.phone || '',
                        address: customer.address || '',
                        shipping_cost: customer.shipping_cost || 0,
                    };
                    this.customerModalOpen = true;
                    this.$nextTick(() => this.$refs.customerName && this.$refs.customerName.focus());
                },

                closeModal() {
                    this.customerModalOpen = false;
                    this.customerMode = 'create';
                    this.resetCustomerForm();
                    this.isSubmitting = false;
                },

                resetMobileFilter() {
                    this.mobileFilter.search = '';
                    this.mobileFilter.filter = 'all';
                },

                applyMobileFilter() {
                    const params = new URLSearchParams();
                    if (this.mobileFilter.search) params.set('search', this.mobileFilter.search);
                    if (this.mobileFilter.filter && this.mobileFilter.filter !== 'all') params.set('filter', this.mobileFilter.filter);
                    const url = params.toString() ? `{{ route('customers.index') }}?${params.toString()}` : `{{ route('customers.index') }}`;
                    window.location.href = url;
                },

                confirmDelete(url) {
                    this.confirmActionUrl = url;
                    this.confirmOpen = true;
                },

                formAction() {
                    if (this.customerMode === 'edit') {
                        return '{{ url('/customers') }}/' + this.customerForm.id;
                    }
                    return '{{ route('customers.store') }}';
                },
            }));
        });
    </script>
</x-app-layout>
