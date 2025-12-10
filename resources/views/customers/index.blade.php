<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                <i class="fas fa-users"></i>
            </span>
            <h2 class="text-xl font-semibold leading-tight text-slate-700">Manajemen Customer</h2>
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
                    <h1 class="text-2xl font-semibold tracking-tight text-slate-800 lg:text-[1.8rem]">Manajemen Customer</h1>
                    <p class="text-sm text-slate-500">
                        Kelola data pelanggan, lihat riwayat transaksi, dan tentukan ongkir default untuk setiap customer.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-3.5 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600"
                        @click="customerModalOpen = true; customerMode = 'create'; resetCustomerForm();">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Customer
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($statCards as $card)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/50">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl {{ $card['accent'] }}">
                            <i class="fas {{ $card['icon'] }} text-lg"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">{{ $card['sub'] }}</p>
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
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm shadow-slate-200/50">
            <!-- Toolbar -->
            <div class="flex flex-col gap-4 border-b border-slate-100 p-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- Search & Filter -->
                <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:items-center">
                    <form action="{{ route('customers.index') }}" method="GET" class="relative flex-1 sm:max-w-xs">
                        @if($activeFilter !== 'all')
                            <input type="hidden" name="filter" value="{{ $activeFilter }}">
                        @endif
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="search" name="search" value="{{ $search ?? '' }}" placeholder="Cari customer..."
                            class="w-full rounded-xl border-2 border-emerald-100 bg-emerald-50/60 py-2.5 pl-12 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    </form>

                    <!-- Filter Dropdown -->
                    @php
                        $filterOptions = [
                            'all' => [
                                'label' => 'Semua Customer',
                                'icon' => 'fas fa-users',
                                'iconClasses' => 'bg-emerald-100 text-emerald-600'
                            ],
                            'recent' => [
                                'label' => 'Baru ditambahkan',
                                'icon' => 'fas fa-clock',
                                'iconClasses' => 'bg-sky-100 text-sky-600'
                            ],
                            'with-transactions' => [
                                'label' => 'Pernah transaksi',
                                'icon' => 'fas fa-receipt',
                                'iconClasses' => 'bg-indigo-100 text-indigo-600'
                            ],
                            'no-transactions' => [
                                'label' => 'Belum transaksi',
                                'icon' => 'fas fa-user-clock',
                                'iconClasses' => 'bg-amber-100 text-amber-600'
                            ],
                        ];
                        $selectedFilterLabel = $filterOptions[$activeFilter]['label'] ?? 'Semua Customer';
                        $selectedFilterIcon = $filterOptions[$activeFilter]['icon'] ?? 'fas fa-users';
                        $selectedFilterIconClasses = $filterOptions[$activeFilter]['iconClasses'] ?? 'bg-emerald-100 text-emerald-600';
                    @endphp
                    <div class="sm:w-52" x-data="{ open: false }" @click.away="open = false">
                        <div class="relative">
                            <button type="button"
                                    @click="open = !open"
                                    class="w-full flex items-center justify-between gap-2 py-2 pl-2.5 pr-2.5 text-sm font-medium text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $selectedFilterIconClasses }}">
                                        <i class="{{ $selectedFilterIcon }}"></i>
                                    </span>
                                    <span class="text-sm font-medium text-slate-700">{{ $selectedFilterLabel }}</span>
                                </div>
                                <span class="text-emerald-400" :class="{ 'rotate-180': open }">
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                                </span>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute left-0 right-0 z-30 mt-1.5 origin-top overflow-hidden bg-white border border-emerald-100 rounded-xl shadow-lg">
                                <div class="py-1 max-h-52 overflow-y-auto">
                                    @foreach($filterOptions as $key => $option)
                                        @php
                                            $isActive = $activeFilter === $key;
                                            $queryParams = array_filter([
                                                'search' => ($search ?? null) ?: null,
                                                'filter' => $key === 'all' ? null : $key,
                                            ]);
                                        @endphp
                                        <a href="{{ route('customers.index', $queryParams) }}"
                                           class="w-full px-3 py-2 flex items-center justify-between gap-2 text-sm text-left transition {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg text-xs {{ $option['iconClasses'] }}">
                                                    <i class="{{ $option['icon'] }}"></i>
                                                </span>
                                                <span>{{ $option['label'] }}</span>
                                            </div>
                                            @if($isActive)
                                                <i class="fas fa-check text-xs text-emerald-500"></i>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-500">
                        <span class="font-medium text-slate-700">{{ $customers->total() }}</span> customer ditemukan
                    </span>
                </div>
            </div>

            <!-- Content Area -->
            <div class="p-4">
                @if($customers->isEmpty())
                    <!-- Empty State -->
                    <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-16 text-center">
                        <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ $hasActiveFilter ? 'Tidak ada hasil' : 'Belum ada customer' }}</h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $hasActiveFilter ? 'Tidak ditemukan customer dengan filter yang dipilih' : 'Mulai dengan menambahkan customer pertama' }}
                        </p>
                        @if($hasActiveFilter)
                            <a href="{{ route('customers.index') }}" class="mt-4 text-sm font-medium text-emerald-600 hover:text-emerald-700">
                                <i class="fas fa-rotate-left mr-1"></i>Reset filter
                            </a>
                        @else
                            <button type="button"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600"
                                @click="customerModalOpen = true; customerMode = 'create'; resetCustomerForm();">
                                <i class="fas fa-plus"></i>Tambah Customer
                            </button>
                        @endif
                    </div>
                @else
                    <!-- Desktop Table View -->
                    <div class="hidden lg:block">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Customer</th>
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Telepon</th>
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Alamat</th>
                                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">Ongkir Default</th>
                                        <th class="px-3 py-2.5 text-right text-[11px] font-semibold uppercase tracking-wide text-slate-500">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($customers as $customer)
                                    @php
                                        $hasTransaction = in_array($customer->customer_name, $customersWithTransactions ?? []);
                                        $missingPhone = empty($customer->phone);
                                    @endphp
                                    <tr class="group transition hover:bg-slate-50/50">
                                        <!-- Customer -->
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <span class="h-10 w-10 shrink-0 inline-flex items-center justify-center rounded-lg bg-emerald-100 text-emerald-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($customer->customer_name, 0, 2)) }}
                                                </span>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-semibold text-slate-900 truncate max-w-[160px]">{{ $customer->customer_name }}</p>
                                                    <div class="flex flex-wrap gap-1 mt-0.5">
                                                        @if($hasTransaction)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                                                                <i class="fas fa-receipt text-[8px]"></i> Aktif
                                                            </span>
                                                        @endif
                                                        @if($missingPhone)
                                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-medium text-amber-700">
                                                                <i class="fas fa-phone-slash text-[8px]"></i> No HP
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <!-- Phone -->
                                        <td class="px-3 py-3">
                                            <span class="text-sm text-slate-600">{{ $customer->phone ?: '-' }}</span>
                                        </td>
                                        <!-- Address -->
                                        <td class="px-3 py-3">
                                            <span class="text-sm text-slate-600 truncate max-w-[180px] block">{{ $customer->address ?: '-' }}</span>
                                        </td>
                                        <!-- Shipping Cost -->
                                        <td class="px-3 py-3">
                                            <span class="text-sm font-semibold text-slate-800">Rp {{ number_format($customer->shipping_cost ?? 0, 0, ',', '.') }}</span>
                                        </td>
                                        <!-- Actions -->
                                        <td class="px-3 py-3">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" @click="openForEdit({{ $customer->toJson() }})"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200"
                                                    title="Edit">
                                                    <i class="fas fa-pen-to-square text-xs"></i>
                                                </button>
                                                <button type="button" @click="confirmDelete('{{ route('customers.destroy', $customer) }}')"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-slate-200 text-slate-400 transition hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-can text-xs"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Mobile Card View -->
                    <div class="grid gap-4 sm:grid-cols-2 lg:hidden">
                        @foreach($customers as $customer)
                        @php
                            $hasTransaction = in_array($customer->customer_name, $customersWithTransactions ?? []);
                            $missingPhone = empty($customer->phone);
                        @endphp
                        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
                            <div class="flex gap-3">
                                <!-- Avatar -->
                                <span class="h-12 w-12 shrink-0 inline-flex items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 font-semibold">
                                    {{ strtoupper(substr($customer->customer_name, 0, 2)) }}
                                </span>
                                <!-- Info -->
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-semibold text-slate-900 truncate">{{ $customer->customer_name }}</h4>
                                    <p class="text-xs text-slate-500">{{ $customer->phone ?: 'Belum ada telepon' }}</p>
                                    <div class="mt-2 flex flex-wrap gap-1.5">
                                        @if($hasTransaction)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/10 px-2 py-0.5 text-[10px] font-medium text-emerald-700">
                                                Aktif
                                            </span>
                                        @endif
                                        @if($missingPhone)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-medium text-amber-700">
                                                No HP
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($customer->address)
                            <p class="mt-3 text-xs text-slate-500 line-clamp-2">{{ $customer->address }}</p>
                            @endif
                            <!-- Price & Actions -->
                            <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3">
                                <span class="font-semibold text-slate-900">Rp {{ number_format($customer->shipping_cost ?? 0, 0, ',', '.') }}</span>
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="openForEdit({{ $customer->toJson() }})"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-emerald-100 hover:text-emerald-600">
                                        <i class="fas fa-pen-to-square text-sm"></i>
                                    </button>
                                    <button type="button" @click="confirmDelete('{{ route('customers.destroy', $customer) }}')"
                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-rose-100 hover:text-rose-600">
                                        <i class="fas fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                <!-- Pagination -->
                @if($customers->hasPages())
                    <div class="flex flex-col items-center justify-between gap-3 pt-6 mt-6 border-t border-slate-100 lg:flex-row">
                        <div class="text-sm text-slate-600">
                            Menampilkan <span class="font-medium text-slate-800">{{ $customers->firstItem() ?? 0 }}</span> - <span class="font-medium text-slate-800">{{ $customers->lastItem() ?? 0 }}</span> dari <span class="font-medium text-slate-800">{{ $customers->total() }}</span> customer
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            {{-- Previous --}}
                            @if ($customers->onFirstPage())
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                    Sebelumnya
                                </span>
                            @else
                                <a href="{{ $customers->previousPageUrl() }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                    Sebelumnya
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            @php
                                $currentPage = $customers->currentPage();
                                $lastPage = $customers->lastPage();
                                $start = max(1, $currentPage - 2);
                                $end = min($lastPage, $currentPage + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $customers->url(1) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-slate-600 transition bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800">1</a>
                                @if($start > 2)
                                    <span class="px-1 text-slate-400">...</span>
                                @endif
                            @endif

                            @for($page = $start; $page <= $end; $page++)
                                @if ($page == $currentPage)
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-emerald-500 rounded-lg shadow-sm">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $customers->url($page) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-slate-600 transition bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endfor

                            @if($end < $lastPage)
                                @if($end < $lastPage - 1)
                                    <span class="px-1 text-slate-400">...</span>
                                @endif
                                <a href="{{ $customers->url($lastPage) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm font-medium text-slate-600 transition bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800">{{ $lastPage }}</a>
                            @endif

                            {{-- Next --}}
                            @if ($customers->hasMorePages())
                                <a href="{{ $customers->nextPageUrl() }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                    Selanjutnya
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-slate-400 bg-slate-50 rounded-lg cursor-not-allowed">
                                    Selanjutnya
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Modal Customer -->
        <div x-show="customerModalOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            x-cloak
            @keydown.tab.prevent="handleTab($event)">
            
            <div 
                x-show="customerModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white shadow-2xl"
                @click.away="closeModal()"
                x-ref="modalPanel">
                
                <!-- Header -->
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4 rounded-t-3xl">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl" :class="customerMode === 'create' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600'">
                            <i class="fas" :class="customerMode === 'create' ? 'fa-user-plus' : 'fa-pen-to-square'"></i>
                        </span>
                        <h3 class="text-lg font-bold text-slate-900" x-text="customerMode === 'create' ? 'Tambah Customer' : 'Edit Customer'"></h3>
                    </div>
                    <button type="button" @click="closeModal()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                        <i class="fas fa-xmark"></i>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" :action="formAction()" @submit.once="isSubmitting = true" class="p-6">
                    @csrf
                    <template x-if="customerMode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                            <p class="font-semibold">Periksa kembali data berikut:</p>
                            <ul class="mt-2 space-y-1 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Customer <span class="text-rose-500">*</span></label>
                            <input x-ref="customerName" type="text" name="customer_name" x-model="customerForm.customer_name" required
                                   class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="Masukkan nama customer">
                            @error('customer_name')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Telepon</label>
                            <input type="text" name="phone" x-model="customerForm.phone"
                                   class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500"
                                   placeholder="Contoh: 08123456789">
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Ongkir Default</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">Rp</span>
                                <input type="number" min="0" name="shipping_cost" x-model.number="customerForm.shipping_cost"
                                       class="w-full rounded-xl border-slate-200 pl-10 pr-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="0">
                            </div>
                            @error('shipping_cost')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Alamat</label>
                            <textarea name="address" x-model="customerForm.address" rows="3"
                                      class="w-full rounded-xl border-slate-200 px-4 py-2.5 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-emerald-500 resize-none"
                                      placeholder="Alamat lengkap customer"></textarea>
                            @error('address')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                        <button type="button" @click="closeModal()"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-400/30 transition hover:bg-emerald-600 disabled:opacity-60 disabled:cursor-not-allowed">
                            <template x-if="!isSubmitting">
                                <i class="fas fa-check"></i>
                            </template>
                            <template x-if="isSubmitting">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </template>
                            <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirm Delete Modal -->
        <div x-show="confirmOpen" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            @keydown.tab.prevent="handleConfirmTab($event)">
            
            <div class="relative w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl text-center"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                @click.away="confirmOpen = false"
                x-ref="confirmPanel">
                
                <div class="mx-auto mb-4 inline-flex h-14 w-14 items-center justify-center rounded-full bg-rose-100 text-rose-600">
                    <i class="fas fa-triangle-exclamation text-2xl"></i>
                </div>

                <h3 class="text-lg font-bold text-slate-900">Hapus Customer?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Apakah Anda yakin ingin menghapus customer ini? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" @click="confirmOpen = false"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:border-slate-300">
                        Batal
                    </button>
                    <form method="POST" :action="confirmActionUrl" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 rounded-xl bg-rose-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-rose-400/30 transition hover:bg-rose-600">
                            <i class="fas fa-trash-can"></i>Ya, Hapus
                        </button>
                    </form>
                </div>
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
                        last.focus();
                    } else if (!event.shiftKey && document.activeElement === last) {
                        first.focus();
                    }
                },

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
