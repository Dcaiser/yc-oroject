<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <i class="fas fa-truck-fast"></i>
                </span>
                <h2 class="text-xl font-semibold leading-tight text-slate-700">Supplier & Kemitraan</h2>
            </div>
            <p class="text-sm text-slate-500">Pantau performa vendor dan tindak lanjuti kebutuhan kemitraan dengan cepat.</p>
        </div>
    </x-slot>

    @php
        $statCards = [
            [
                'label' => 'Total Supplier',
                'value' => number_format($stats['total']),
                'sub' => 'Mitra aktif terdaftar',
                'icon' => 'fa-warehouse',
                'accent' => 'bg-emerald-500/10 text-emerald-600',
            ],
            [
                'label' => 'Supplier Baru',
                'value' => number_format($stats['newThisMonth']),
                'sub' => '30 hari terakhir',
                'icon' => 'fa-seedling',
                'accent' => 'bg-sky-500/10 text-sky-600',
            ],
            [
                'label' => 'Aktif Belanja',
                'value' => number_format($stats['activeVendors']),
                'sub' => 'Memiliki pesanan berjalan',
                'icon' => 'fa-file-invoice-dollar',
                'accent' => 'bg-indigo-500/10 text-indigo-600',
            ],
            [
                'label' => 'Butuh Lengkapi Kontak',
                'value' => number_format($stats['missingContacts']),
                'sub' => 'Data kontak belum lengkap',
                'icon' => 'fa-circle-exclamation',
                'accent' => 'bg-amber-500/10 text-amber-600',
            ],
        ];

        $filterOptions = [
            'all' => [
                'label' => 'Semua',
                'description' => 'Seluruh supplier',
            ],
            'recent' => [
                'label' => 'Baru ditambahkan',
                'description' => '30 hari terakhir',
            ],
            'with-po' => [
                'label' => 'Aktif bertransaksi',
                'description' => 'Memiliki pesanan',
            ],
            'missing-contact' => [
                'label' => 'Perlu tindak lanjut',
                'description' => 'Kontak belum lengkap',
            ],
        ];
    @endphp

    <section class="mx-auto max-w-7xl space-y-8 px-4 py-10">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm shadow-slate-200/50">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="max-w-xl space-y-3">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-500/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-emerald-600">
                        <i class="fas fa-layer-group"></i>
                        Operasional
                    </span>
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-800 lg:text-4xl">Manajemen Supplier</h1>
                    <p class="text-sm text-slate-500">
                        Kelola jaringan kemitraan, lihat aktivitas terkini, dan tindak lanjuti supplier yang membutuhkan perhatian.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-400/30 transition hover:bg-emerald-600">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Supplier
                    </a>
                    <a href="{{ route('suppliers.index', array_filter(['search' => $search ?: null, 'filter' => $activeFilter !== 'all' ? $activeFilter : null])) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:border-slate-300 hover:text-slate-800">
                        <i class="fas fa-arrow-rotate-left me-2"></i>
                        Reset Tampilan
                    </a>
                </div>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach($statCards as $card)
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-200/50">
                    <div class="flex items-start justify-between">
                        <div class="space-y-1">
                            <p class="text-sm font-medium text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-3xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl {{ $card['accent'] }}">
                            <i class="fas {{ $card['icon'] }} text-lg"></i>
                        </span>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">{{ $card['sub'] }}</p>
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

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm shadow-slate-200/60">
            <form action="{{ route('suppliers.index') }}" method="GET" class="space-y-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center">
                    <div class="relative flex-1">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex w-12 items-center justify-center text-slate-400">
                            <i class="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari supplier berdasarkan nama, kode, atau contact person"
                            class="w-full rounded-2xl border-slate-200 bg-white py-3 pl-12 pr-4 text-sm text-slate-700 shadow-inner shadow-slate-100 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-200"
                        >
                    </div>
                    <div class="flex flex-col gap-3 sm:flex-row">
                        <input type="hidden" name="filter" value="{{ $activeFilter !== 'all' ? $activeFilter : '' }}">
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-400/30 transition hover:bg-emerald-600">
                            <i class="fas fa-search me-2"></i>
                            Cari Supplier
                        </button>
                        @if($search)
                            <a href="{{ route('suppliers.index', array_filter(['filter' => $activeFilter !== 'all' ? $activeFilter : null])) }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 transition hover:border-slate-300 hover:text-slate-700">
                                Hapus Kata Kunci
                            </a>
                        @endif
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @foreach($filterOptions as $key => $option)
                        @php
                            $isActive = $activeFilter === $key;
                            $queryParams = array_filter([
                                'search' => $search ?: null,
                                'filter' => $key === 'all' ? null : $key,
                            ]);
                        @endphp
                        <a
                            href="{{ route('suppliers.index', $queryParams) }}"
                            class="group inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm transition {{ $isActive ? 'border-emerald-500 bg-emerald-50 text-emerald-700 shadow-sm' : 'border-slate-200 text-slate-500 hover:border-slate-300 hover:bg-slate-50 hover:text-slate-700' }}"
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

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm shadow-slate-200/70">
            @if($suppliers->isEmpty())
                <div class="flex flex-col items-center justify-center gap-4 px-8 py-16 text-center">
                    <span class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <i class="fas fa-box-open text-2xl"></i>
                    </span>
                    <div class="space-y-2">
                        <h3 class="text-lg font-semibold text-slate-700">Belum ada supplier untuk ditampilkan</h3>
                        <p class="text-sm text-slate-500">
                            Coba ubah filter atau tambah supplier baru untuk mulai membangun jaringan kemitraan.
                        </p>
                    </div>
                    <a href="{{ route('suppliers.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-500 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-400/30 transition hover:bg-emerald-600">
                        <i class="fas fa-plus me-2"></i>
                        Tambah Supplier Pertama
                    </a>
                </div>
            @else
                <div class="hidden md:block">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Supplier</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Info Kontak</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Status</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($suppliers as $supplier)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-6 py-5 align-top">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <span class="rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-600">{{ $supplier->supplier_code }}</span>
                                                <span class="text-sm font-semibold text-slate-800">{{ $supplier->name }}</span>
                                            </div>
                                            @if($supplier->address)
                                                <p class="text-xs text-slate-500">{{ $supplier->address }}</p>
                                            @endif
                                            @if($supplier->npwp)
                                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">NPWP: {{ $supplier->npwp }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <div class="space-y-1 text-sm text-slate-600">
                                            <p class="font-semibold text-slate-700">{{ $supplier->contact_person ?? 'Kontak belum diisi' }}</p>
                                            <p class="flex items-center gap-2"><i class="fas fa-phone text-slate-400"></i> {{ $supplier->phone ?? '—' }}</p>
                                            <p class="flex items-center gap-2"><i class="fas fa-envelope text-slate-400"></i> {{ $supplier->email ?? '—' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <div class="space-y-2 text-sm text-slate-600">
                                            <div class="flex items-center gap-2 text-slate-700">
                                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-indigo-500/10 text-indigo-600">
                                                    <i class="fas fa-file-invoice"></i>
                                                </span>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-700">{{ $supplier->purchase_orders_count }} Pesanan</p>
                                                    <p class="text-xs text-slate-400">tercatat dalam sistem</p>
                                                </div>
                                            </div>
                                            <p class="text-xs text-slate-400">Terdaftar {{ optional($supplier->created_at)->diffForHumans() }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 align-top">
                                        <div class="flex flex-wrap gap-2">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">
                                                <i class="fas fa-eye"></i>
                                                Detail
                                            </a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-200">
                                                <i class="fas fa-pen"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Data supplier akan dihapus permanen. Lanjutkan?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-200">
                                                    <i class="fas fa-trash"></i>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-4 p-4 md:hidden">
                    @foreach($suppliers as $supplier)
                        <div class="rounded-2xl border border-slate-200 p-5 shadow-sm shadow-slate-200/60">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="inline-flex items-center rounded-full bg-emerald-500/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-emerald-600">{{ $supplier->supplier_code }}</span>
                                    <h3 class="mt-2 text-base font-semibold text-slate-800">{{ $supplier->name }}</h3>
                                    @if($supplier->address)
                                        <p class="mt-1 text-xs text-slate-500">{{ $supplier->address }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400">{{ optional($supplier->created_at)->diffForHumans() }}</span>
                            </div>
                            <div class="mt-4 space-y-2 text-sm text-slate-600">
                                <p class="font-semibold text-slate-700">Kontak utama: {{ $supplier->contact_person ?? 'Belum diisi' }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-phone text-slate-400"></i> {{ $supplier->phone ?? '—' }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-envelope text-slate-400"></i> {{ $supplier->email ?? '—' }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-file-invoice text-slate-400"></i> {{ $supplier->purchase_orders_count }} Pesanan</p>
                            </div>
                            <div class="mt-5 flex flex-wrap gap-2">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-amber-100 px-4 py-2 text-sm font-semibold text-amber-600 transition hover:bg-amber-200">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline-flex flex-1"
                                                                            onsubmit="return confirm('Data supplier akan dihapus permanen. Lanjutkan?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-200">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="flex flex-col gap-3 rounded-3xl bg-transparent pb-4 text-sm text-slate-500 md:flex-row md:items-center md:justify-between">
            <div>
                @php
                    $from = $suppliers->firstItem() ?? 0;
                    $to = $suppliers->lastItem() ?? 0;
                @endphp
                <p>Menampilkan <span class="font-semibold text-slate-700">{{ $from }}-{{ $to }}</span> dari <span class="font-semibold text-slate-700">{{ $suppliers->total() }}</span> supplier</p>
            </div>
            <div>
                {{ $suppliers->links() }}
            </div>
        </div>
    </section>
</x-app-layout>
