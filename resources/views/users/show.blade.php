<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-3 text-2xl font-bold text-slate-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-emerald-600 bg-emerald-100 rounded-xl">
                    <i class="fas fa-user"></i>
                </span>
                Detail User: {{ $user->name }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('users.edit', $user) }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-linear-to-r from-blue-500 to-blue-600 hover:scale-[1.02] transition">
                    <i class="fas fa-edit"></i>Edit
                </a>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100">
                    <i class="fas fa-arrow-left"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['title' => 'Manajemen User', 'url' => route('users.index')],
            ['title' => 'Detail: ' . $user->name]
        ]" />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- User Profile & Actions Column -->
            <div class="space-y-4 lg:col-span-1">
                <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                    <div class="p-5 text-center">
                        <div class="mb-3">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                     alt="{{ $user->name }}"
                                     class="object-cover w-24 h-24 mx-auto border-4 border-emerald-100 rounded-full">
                            @else
                                <div class="flex items-center justify-center w-24 h-24 mx-auto text-emerald-600 bg-emerald-50 border-4 border-emerald-100 rounded-full">
                                    <i class="text-3xl fas fa-user"></i>
                                </div>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-slate-900">{{ $user->name }}</h3>
                        <p class="mb-2 text-sm text-slate-600">{{ $user->email }}</p>

                        @php
                            $roleClasses = [
                                'admin' => 'from-slate-100 to-slate-200 text-slate-700 border border-slate-300',
                                'manager' => 'from-sky-100 to-sky-200 text-sky-700 border border-sky-200',
                                'staff' => 'from-teal-100 to-teal-200 text-teal-700 border border-teal-200',
                            ];
                            $roleClass = $roleClasses[$user->role] ?? 'from-teal-100 to-teal-200 text-teal-700 border border-teal-200';
                        @endphp
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold border rounded-full bg-linear-to-r {{ $roleClass }}">
                                @if($user->role === 'admin')
                                    <i class="fas fa-user-gear text-[10px]"></i>
                                @elseif($user->role === 'manager')
                                    <i class="fas fa-user-tie text-[10px]"></i>
                                @else
                                    <i class="fas fa-user text-[10px]"></i>
                                @endif
                                {{ ucfirst($user->role) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-full bg-emerald-50">
                                <span class="flex w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                        </div>

                        @if($user->id === auth()->id())
                            <div class="mt-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full">
                                    <i class="fas fa-info-circle text-[10px]"></i>Akun Anda
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                @php
                    $accessSummaries = [
                        'admin' => [
                            'Mengelola seluruh modul dan pengguna',
                            'Menyetujui transaksi sensitif dan laporan keuangan',
                            'Mengatur konfigurasi sistem dan keamanan'
                        ],
                        'manager' => [
                            'Mengelola inventaris dan laporan penjualan',
                            'Memverifikasi pembelian serta pemasok',
                            'Memantau aktivitas tim operasional'
                        ],
                        'staff' => [
                            'Mengelola stok harian dan pesanan masuk',
                            'Mencatat aktivitas pelanggan dan pemasok',
                            'Mengakses laporan sesuai kebutuhan operasional'
                        ],
                    ];
                    $accessItems = $accessSummaries[$user->role] ?? [
                        'Akses dasar ke modul yang relevan',
                        'Hak baca dan tulis sesuai penugasan',
                        'Hubungi admin untuk perluasan akses'
                    ];
                @endphp

                <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                    <div class="p-4">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="inline-flex items-center justify-center w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl">
                                <i class="fas fa-user-shield"></i>
                            </span>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900">Ringkasan Hak Akses</h4>
                                <p class="text-xs text-slate-500">Peran: {{ ucfirst($user->role) }}</p>
                            </div>
                        </div>

                        <ul class="space-y-2">
                            @foreach($accessItems as $item)
                                <li class="flex items-start gap-2 text-sm text-slate-600">
                                    <span class="flex items-center justify-center w-6 h-6 mt-0.5 text-emerald-600 bg-emerald-50 rounded-full">
                                        <i class="fas fa-check text-[11px]"></i>
                                    </span>
                                    <span class="leading-relaxed">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                    <div class="p-4">
                        <h4 class="mb-3 text-base font-semibold text-slate-900">Aksi</h4>

                        <div class="flex flex-col gap-2">
                            <a href="{{ route('users.edit', $user) }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl shadow bg-linear-to-r from-blue-500 to-blue-600 hover:scale-[1.02] transition w-full">
                                <i class="fas fa-edit"></i>Edit User
                            </a>

                            @if($user->id !== auth()->id())
                                <form method="POST"
                                      action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan!')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-white rounded-xl shadow bg-linear-to-r from-red-500 to-red-600 hover:scale-[1.02] transition">
                                        <i class="fas fa-trash"></i>Hapus User
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 text-sm font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                                    <i class="fas fa-lock"></i>Tidak dapat menghapus akun sendiri
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- User Details -->
            <div class="lg:col-span-2 space-y-6">
                <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                    <div class="p-6">
                        <h4 class="mb-4 text-lg font-semibold text-slate-900">Informasi Detail</h4>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-4">
                                <div class="p-4 bg-white border border-emerald-100 rounded-xl">
                                    <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Nama Lengkap</span>
                                    <p class="mt-2 text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                                </div>

                                <div class="p-4 bg-white border border-emerald-100 rounded-xl">
                                    <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Email</span>
                                    <p class="mt-2 text-sm text-slate-700">{{ $user->email }}</p>
                                </div>

                                <div class="p-4 bg-white border border-emerald-100 rounded-xl">
                                    <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Status Akun</span>
                                    <p class="mt-2 text-sm text-slate-700">Aktif</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="p-4 bg-white border border-emerald-100 rounded-xl">
                                    <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Tanggal Bergabung</span>
                                    <p class="mt-2 text-sm text-slate-700">{{ $user->created_at->format('d F Y, H:i') }}</p>
                                </div>

                                <div class="p-4 bg-white border border-emerald-100 rounded-xl">
                                    <span class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Terakhir Diupdate</span>
                                    <p class="mt-2 text-sm text-slate-700">{{ $user->updated_at->format('d F Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Log Card -->
                <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
                    <div class="p-6">
                        <h4 class="mb-4 text-lg font-semibold text-slate-900">Log Aktivitas Terbaru</h4>

                        <div class="space-y-3">
                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-600 bg-emerald-50 rounded-lg">
                                    <i class="fas fa-sign-in-alt"></i>
                                </span>
                                <span>Login terakhir: {{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-50 rounded-lg">
                                    <i class="fas fa-user-edit"></i>
                                </span>
                                <span>Profil diupdate: {{ $user->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-slate-600">
                                <span class="inline-flex items-center justify-center w-8 h-8 text-emerald-600 bg-emerald-50 rounded-lg">
                                    <i class="fas fa-user-plus"></i>
                                </span>
                                <span>Akun dibuat: {{ $user->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
