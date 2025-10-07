<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-700 rounded-full">
                    <i class="fas fa-users-cog"></i>
                </span>
                Manajemen User
            </h1>
            <a href="{{ route('users.create') }}"
               class="flex items-center px-4 py-2 font-medium text-white rounded-lg shadow transition hover:scale-[1.02] bg-gradient-to-r from-green-500 to-green-700">
                <i class="mr-2 fas fa-plus"></i>Tambah User
            </a>
        </div>
    </x-slot>

    @php
        $currentRole = request('role', 'all');
    @endphp

    <div class="space-y-6">
        <x-breadcrumb :items="[['title' => 'Manajemen User']]" />

        @if (session('success'))
            <div class="flex items-start gap-3 p-4 text-green-900 bg-green-50 border border-green-200 rounded-2xl shadow-sm">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full">
                    <i class="fas fa-check-circle"></i>
                </span>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 p-4 text-red-800 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full">
                    <i class="fas fa-exclamation-circle"></i>
                </span>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="p-5 bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-600 rounded-xl">
                        <i class="text-lg fas fa-users"></i>
                    </span>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wide text-emerald-500 uppercase">Total User</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-slate-100 text-slate-600 rounded-xl">
                        <i class="text-lg fas fa-user-gear"></i>
                    </span>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Admin</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['admin'] }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-sky-100 text-sky-600 rounded-xl">
                        <i class="text-lg fas fa-user-tie"></i>
                    </span>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wide text-sky-500 uppercase">Manager</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['manager'] }}</p>
                    </div>
                </div>
            </div>
            <div class="p-5 bg-white rounded-2xl shadow-sm ring-1 ring-emerald-100">
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center justify-center w-12 h-12 bg-teal-100 text-teal-600 rounded-xl">
                        <i class="text-lg fas fa-user"></i>
                    </span>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold tracking-wide text-teal-500 uppercase">Staff</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $stats['staff'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white rounded-2xl shadow-md ring-1 ring-emerald-100">
            <div class="flex flex-col gap-4 p-6 border-b border-emerald-100 md:flex-row md:items-center md:justify-between bg-emerald-50/40">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daftar User</h2>
                    <p class="text-sm text-slate-600">Kelola seluruh akun yang memiliki akses ke sistem.</p>
                </div>

                <div class="flex items-center gap-3">
                    <button id="bulk-delete-btn"
                            type="button"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg shadow bg-gradient-to-r from-red-500 to-red-600 disabled:cursor-not-allowed disabled:opacity-50"
                            disabled>
                        <i class="fas fa-trash"></i>
                        Hapus Dipilih
                        <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-red-600 bg-white rounded-full">
                            <span id="selected-count">0</span>
                        </span>
                    </button>
                </div>
            </div>

            <div class="px-6 py-4 border-b border-emerald-100 bg-white">
                <form id="user-filter-form" method="GET" action="{{ route('users.index') }}" class="flex flex-col gap-3 md:flex-row md:items-center md:gap-3">
                    <div class="flex-1 w-full">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-emerald-400"><i class="fas fa-search"></i></span>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Cari berdasarkan nama atau email"
                                   class="w-full py-2.5 pl-12 pr-4 text-sm text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                        </div>
                    </div>

                    <div class="w-full md:w-48">
                        @php
                            $roleOptions = [
                                'all' => [
                                    'label' => 'Semua Role',
                                    'icon' => 'fas fa-list',
                                    'iconClasses' => 'bg-emerald-100 text-emerald-600'
                                ],
                                'admin' => [
                                    'label' => 'Admin',
                                    'icon' => 'fas fa-user-gear',
                                    'iconClasses' => 'bg-slate-100 text-slate-600'
                                ],
                                'manager' => [
                                    'label' => 'Manager',
                                    'icon' => 'fas fa-user-tie',
                                    'iconClasses' => 'bg-sky-100 text-sky-600'
                                ],
                                'staff' => [
                                    'label' => 'Staff',
                                    'icon' => 'fas fa-user',
                                    'iconClasses' => 'bg-teal-100 text-teal-600'
                                ],
                            ];
                            $selectedRoleLabel = $roleOptions[$currentRole]['label'] ?? $roleOptions['all']['label'];
                        @endphp
                        <div class="relative" data-role-dropdown>
                            <select name="role" class="absolute inset-0 w-full h-full opacity-0 pointer-events-none" tabindex="-1" aria-hidden="true">
                                @foreach($roleOptions as $value => $option)
                                    <option value="{{ $value }}" {{ $currentRole === $value ? 'selected' : '' }}>{{ $option['label'] }}</option>
                                @endforeach
                            </select>

                            <button type="button"
                                    class="w-full flex items-center justify-between gap-2.5 py-2 pl-3 pr-3 text-sm font-medium text-slate-700 bg-emerald-50/60 border-2 border-emerald-100 rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400"
                                    data-dropdown-trigger>
                                <div class="flex items-center gap-2.5">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-sm {{ $roleOptions[$currentRole]['iconClasses'] ?? 'bg-emerald-100 text-emerald-600' }}"
                                          data-dropdown-icon
                                          data-icon-base="inline-flex items-center justify-center w-7 h-7 rounded-lg text-sm">
                                        <i class="{{ $roleOptions[$currentRole]['icon'] ?? 'fas fa-layer-group' }}"></i>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800" data-dropdown-label>{{ $selectedRoleLabel }}</span>
                                </div>
                                <span class="flex items-center justify-center w-7 h-7 text-emerald-400 rounded-lg transition" data-dropdown-arrow>
                                    <i class="fas fa-chevron-down"></i>
                                </span>
                            </button>

                            <div class="absolute left-0 right-0 z-30 hidden mt-2 origin-top transform scale-95 opacity-0 pointer-events-none transition-all duration-150 overflow-hidden bg-white border border-emerald-100 rounded-2xl shadow-lg" data-dropdown-menu>
                                <div class="py-2 max-h-60 overflow-y-auto">
                                    @foreach($roleOptions as $value => $option)
                                        @php
                                            $isActive = $currentRole === $value;
                                        @endphp
                                        <button type="button"
                                                class="w-full px-4 py-2 flex items-center justify-between gap-2.5 text-sm text-left transition {{ $isActive ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}"
                                                data-dropdown-option
                                                data-value="{{ $value }}"
                                                data-label="{{ $option['label'] }}"
                                                data-icon="{{ $option['icon'] }}"
                                                data-icon-classes="{{ $option['iconClasses'] }}"
                                                data-option-base="w-full px-4 py-2 flex items-center justify-between gap-2.5 text-sm text-left transition text-slate-600 hover:bg-emerald-50 hover:text-emerald-700">
                                            <span class="flex items-center gap-2.5">
                                                <span class="inline-flex items-center justify-center w-7 h-7 text-sm rounded-lg {{ $option['iconClasses'] }}">
                                                    <i class="{{ $option['icon'] }}"></i>
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

                    <div class="flex items-center justify-start w-full gap-2 md:w-auto md:justify-end">
                        <a href="{{ route('users.index') }}"
                           class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100">
                            <i class="fas fa-rotate-right"></i>
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="p-6">
                <form id="bulk-delete-form" method="POST" action="{{ route('users.bulk-delete') }}">
                    @csrf
                    @method('DELETE')

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-slate-700">
                            <thead class="text-xs font-semibold tracking-wide uppercase bg-slate-50 text-slate-600 ring-1 ring-slate-100">
                                <tr>
                                    <th class="px-3 py-4">
                                        <input type="checkbox" id="select-all" class="w-4 h-4 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500">
                                    </th>
                                    <th class="px-4 py-4">User</th>
                                    <th class="px-4 py-4">Email</th>
                                    <th class="px-4 py-4">Role</th>
                                    <th class="px-4 py-4">Status</th>
                                    <th class="px-4 py-4">Bergabung</th>
                                    <th class="px-4 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-100">
                                @forelse($users as $user)
                                    <tr class="transition hover:bg-emerald-50/70">
                                        <td class="px-3 py-4 align-top">
                                            @if($user->id !== auth()->id())
                                                <input type="checkbox"
                                                       name="user_ids[]"
                                                       value="{{ $user->id }}"
                                                       class="w-4 h-4 text-green-600 border-green-300 rounded user-checkbox focus:ring-green-500">
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 align-top">
                                            <div class="flex items-center gap-3">
                                                <div class="flex-shrink-0">
                                                    @if($user->avatar)
                                                        <img class="object-cover w-12 h-12 rounded-full ring-2 ring-emerald-100"
                                                             src="{{ asset('storage/' . $user->avatar) }}"
                                                             alt="{{ $user->name }}">
                                                    @else
                                                        <div class="flex items-center justify-center w-12 h-12 text-emerald-600 bg-emerald-50 rounded-full">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="text-sm font-semibold text-slate-800">{{ $user->name }}</p>
                                                    @if($user->id === auth()->id())
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 text-xs font-semibold text-emerald-700 bg-emerald-50 rounded-full">
                                                            <i class="fas fa-user-circle"></i>
                                                            Anda
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 align-top">
                                            <p class="text-sm text-slate-600">{{ $user->email }}</p>
                                        </td>
                                        <td class="px-4 py-4 align-top">
                                            @php
                                                $roleData = [
                                                    'admin' => [
                                                        'bg' => 'from-slate-100 to-slate-200',
                                                        'text' => 'text-slate-700',
                                                        'icon' => 'fas fa-user-gear',
                                                            'border' => 'border-slate-300'
                                                    ],
                                                    'manager' => [
                                                            'bg' => 'from-sky-100 to-sky-200',
                                                            'text' => 'text-sky-700',
                                                        'icon' => 'fas fa-user-tie',
                                                            'border' => 'border-sky-200'
                                                    ],
                                                    'staff' => [
                                                            'bg' => 'from-teal-100 to-teal-200',
                                                            'text' => 'text-teal-700',
                                                        'icon' => 'fas fa-user',
                                                            'border' => 'border-teal-200'
                                                    ],
                                                ];
                                                $role = $roleData[$user->role] ?? $roleData['staff'];
                                            @endphp
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold border rounded-full bg-gradient-to-r {{ $role['bg'] }} {{ $role['text'] }} {{ $role['border'] }}">
                                                <i class="{{ $role['icon'] }}"></i>
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 align-top">
                                            <span class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200 rounded-full bg-emerald-50">
                                                <span class="flex w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                                Aktif
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 align-top">
                                            <div class="space-y-1 text-sm text-slate-600">
                                                <p class="font-medium">{{ $user->created_at->format('d M Y') }}</p>
                                                <p class="text-xs text-slate-500">{{ $user->created_at->diffForHumans() }}</p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 align-top">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('users.show', $user) }}"
                                                   class="inline-flex items-center justify-center w-9 h-9 text-emerald-600 transition bg-emerald-50 rounded-lg hover:bg-emerald-100"
                                                   title="Detail">
                                                    <i class="text-sm fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}"
                                                   class="inline-flex items-center justify-center w-9 h-9 text-blue-700 transition bg-blue-100 rounded-lg hover:bg-blue-200"
                                                   title="Edit">
                                                    <i class="text-sm fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button"
                                                            onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                            class="inline-flex items-center justify-center w-9 h-9 text-red-600 transition bg-red-50 rounded-lg hover:bg-red-100"
                                                            title="Hapus">
                                                        <i class="text-sm fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <span class="inline-flex items-center justify-center w-9 h-9 text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed"
                                                          title="Tidak dapat menghapus akun sendiri">
                                                        <i class="text-sm fas fa-lock"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-12 text-center">
                                            <div class="flex flex-col items-center gap-3 text-emerald-600">
                                                <span class="text-4xl"><i class="fas fa-users-slash"></i></span>
                                                <h3 class="text-lg font-semibold text-slate-800">Belum ada user</h3>
                                                <p class="text-sm text-slate-500">Mulai dengan menambahkan user pertama Anda.</p>
                                                <a href="{{ route('users.create') }}"
                                                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-lg shadow bg-gradient-to-r from-emerald-500 to-emerald-600 hover:scale-[1.02] transition">
                                                    <i class="fas fa-plus"></i>
                                                    Tambah User
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                @if($users->hasPages())
                    <div class="flex flex-col items-center justify-between gap-3 pt-6 mt-6 border-t border-emerald-100 lg:flex-row">
                        <div class="text-sm text-slate-600">
                            Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if ($users->onFirstPage())
                                <span class="px-3 py-2 text-sm text-emerald-400 bg-emerald-50 rounded-lg cursor-not-allowed">
                                    <i class="mr-1 fas fa-chevron-left"></i>
                                    Sebelumnya
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}"
                                   class="px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                    <i class="mr-1 fas fa-chevron-left"></i>
                                    Sebelumnya
                                </a>
                            @endif

                            @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                                @if ($page == $users->currentPage())
                                    <span class="px-3 py-2 text-sm font-semibold text-white rounded-lg shadow bg-gradient-to-r from-emerald-500 to-emerald-600">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                       class="px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}"
                                   class="px-3 py-2 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-lg hover:bg-emerald-100">
                                    Selanjutnya
                                    <i class="ml-1 fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="px-3 py-2 text-sm text-emerald-400 bg-emerald-50 rounded-lg cursor-not-allowed">
                                    Selanjutnya
                                    <i class="ml-1 fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="delete-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
        <div data-modal-card class="w-full max-w-md p-6 mx-4 transition-all duration-300 transform scale-95 bg-white border border-green-100 shadow-2xl rounded-2xl">
            <div class="flex items-start gap-3 mb-5">
                <span class="inline-flex items-center justify-center w-12 h-12 text-red-600 bg-red-100 rounded-full">
                    <i class="text-xl fas fa-exclamation-triangle"></i>
                </span>
                <div>
                    <h3 class="text-lg font-semibold text-green-900">Hapus User?</h3>
                    <p class="text-sm text-green-700">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <p class="mb-6 text-sm text-green-800">Anda yakin ingin menghapus user <strong id="delete-user-name" class="text-red-600"></strong>?</p>
            <div class="flex justify-end gap-3">
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="px-4 py-2 text-sm font-semibold text-green-700 transition bg-green-100 border border-green-200 rounded-xl hover:bg-green-200">
                    Batal
                </button>
                <form id="delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow bg-gradient-to-r from-red-500 to-red-600 hover:scale-[1.02] transition">
                        <i class="fas fa-trash"></i>
                        Hapus User
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const filterForm = document.getElementById('user-filter-form');
                const roleDropdown = document.querySelector('[data-role-dropdown]');

                if (roleDropdown) {
                    const hiddenSelect = roleDropdown.querySelector('select');
                    const trigger = roleDropdown.querySelector('[data-dropdown-trigger]');
                    const menu = roleDropdown.querySelector('[data-dropdown-menu]');
                    const labelEl = roleDropdown.querySelector('[data-dropdown-label]');
                    const iconEl = roleDropdown.querySelector('[data-dropdown-icon]');
                    const arrowEl = roleDropdown.querySelector('[data-dropdown-arrow]');
                    const options = roleDropdown.querySelectorAll('[data-dropdown-option]');
                    const iconBase = iconEl ? iconEl.dataset.iconBase : '';
                    const optionActiveClasses = 'bg-emerald-50 text-emerald-700 font-semibold';
                    const menuVisibleClasses = ['hidden', 'opacity-0', 'scale-95', 'pointer-events-none'];

                    let isMenuOpen = false;

                    const closeMenu = () => {
                        if (!menu) return;
                        isMenuOpen = false;
                        menuVisibleClasses.forEach(cls => menu.classList.add(cls));
                        if (arrowEl) {
                            arrowEl.classList.remove('text-emerald-500');
                            arrowEl.classList.remove('rotate-180');
                        }
                        trigger?.setAttribute('aria-expanded', 'false');
                    };

                    const openMenu = () => {
                        if (!menu) return;
                        isMenuOpen = true;
                        menu.classList.remove('hidden');
                        requestAnimationFrame(() => {
                            menu.classList.remove('opacity-0');
                            menu.classList.remove('scale-95');
                            menu.classList.remove('pointer-events-none');
                        });
                        if (arrowEl) {
                            arrowEl.classList.add('text-emerald-500');
                            arrowEl.classList.add('rotate-180');
                        }
                        trigger?.setAttribute('aria-expanded', 'true');
                    };

                    const submitFilterForm = () => {
                        if (!filterForm) {
                            return;
                        }
                        if (typeof filterForm.requestSubmit === 'function') {
                            filterForm.requestSubmit();
                        } else {
                            filterForm.submit();
                        }
                    };

                    const setSelectedOption = (optionButton, shouldSubmit = true) => {
                        if (!optionButton || !hiddenSelect || !labelEl || !iconEl) {
                            return;
                        }

                        const previousValue = hiddenSelect.value;
                        const value = optionButton.dataset.value;
                        const label = optionButton.dataset.label;
                        const icon = optionButton.dataset.icon;
                        const iconClasses = optionButton.dataset.iconClasses || '';

                        hiddenSelect.value = value;
                        labelEl.textContent = label;
                        iconEl.className = `${iconBase} ${iconClasses}`.trim();

                        const iconElement = iconEl.querySelector('i');
                        if (iconElement) {
                            iconElement.className = icon;
                        }

                        options.forEach((btn) => {
                            const base = btn.dataset.optionBase || '';
                            btn.className = `${base} ${btn === optionButton ? optionActiveClasses : ''}`.trim();
                            const activeIcon = btn.querySelector('[data-active-icon]');
                            if (activeIcon) {
                                activeIcon.classList.toggle('opacity-0', btn !== optionButton);
                            }
                        });

                        if (shouldSubmit && previousValue !== value) {
                            submitFilterForm();
                        }
                    };

                    const toggleMenu = () => {
                        if (isMenuOpen) {
                            closeMenu();
                        } else {
                            openMenu();
                        }
                    };

                    trigger?.addEventListener('click', (event) => {
                        event.preventDefault();
                        toggleMenu();
                    });

                    trigger?.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            toggleMenu();
                        } else if (event.key === 'ArrowDown') {
                            event.preventDefault();
                            if (!isMenuOpen) {
                                openMenu();
                            }
                            options[0]?.focus();
                        }
                    });

                    options.forEach((optionButton) => {
                        optionButton.addEventListener('click', () => {
                            setSelectedOption(optionButton, true);
                            closeMenu();
                            trigger?.focus();
                        });

                        optionButton.addEventListener('keydown', (event) => {
                            if (event.key === 'Enter' || event.key === ' ') {
                                event.preventDefault();
                                setSelectedOption(optionButton, true);
                                closeMenu();
                                trigger?.focus();
                            } else if (event.key === 'Escape') {
                                closeMenu();
                                trigger?.focus();
                            }
                        });
                    });

                    document.addEventListener('click', (event) => {
                        if (!roleDropdown.contains(event.target)) {
                            closeMenu();
                        }
                    });

                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            closeMenu();
                        }
                    });

                    setSelectedOption(roleDropdown.querySelector('[data-dropdown-option][data-value="' + (hiddenSelect?.value || 'all') + '"]'), false);
                }

                const selectAllCheckbox = document.getElementById('select-all');
                const userCheckboxes = document.querySelectorAll('.user-checkbox');
                const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
                const selectedCount = document.getElementById('selected-count');
                const bulkDeleteForm = document.getElementById('bulk-delete-form');

                if (bulkDeleteBtn && selectedCount && bulkDeleteForm) {
                    const updateBulkDeleteButton = () => {
                        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                        const count = checkedBoxes.length;

                        selectedCount.textContent = count;
                        bulkDeleteBtn.disabled = count === 0;

                        bulkDeleteBtn.classList.toggle('cursor-not-allowed', count === 0);
                        bulkDeleteBtn.classList.toggle('opacity-60', count === 0);
                    };

                    if (selectAllCheckbox) {
                        selectAllCheckbox.addEventListener('change', function () {
                            userCheckboxes.forEach((checkbox) => {
                                checkbox.checked = this.checked;
                            });
                            updateBulkDeleteButton();
                        });
                    }

                    userCheckboxes.forEach((checkbox) => {
                        checkbox.addEventListener('change', () => {
                            updateBulkDeleteButton();

                            if (selectAllCheckbox) {
                                const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                                selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
                                selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
                            }
                        });
                    });

                    bulkDeleteBtn.addEventListener('click', () => {
                        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                        if (checkedBoxes.length === 0) {
                            return;
                        }

                        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} user terpilih?`)) {
                            bulkDeleteForm.submit();
                        }
                    });

                    updateBulkDeleteButton();
                }
            });

            function confirmDelete(userId, userName) {
                const deleteUserName = document.getElementById('delete-user-name');
                const deleteForm = document.getElementById('delete-form');
                const modal = document.getElementById('delete-modal');
                const modalCard = modal ? modal.querySelector('[data-modal-card]') : null;

                if (!modal || !modalCard || !deleteUserName || !deleteForm) {
                    return;
                }

                deleteUserName.textContent = userName;
                deleteForm.action = `/users/${userId}`;

                modal.classList.remove('hidden');
                modal.classList.add('flex');

                requestAnimationFrame(() => {
                    modalCard.classList.remove('scale-95');
                    modalCard.classList.add('scale-100');
                });
            }

            function closeDeleteModal() {
                const modal = document.getElementById('delete-modal');
                const modalCard = modal ? modal.querySelector('[data-modal-card]') : null;

                if (!modal || !modalCard) {
                    return;
                }

                modalCard.classList.remove('scale-100');
                modalCard.classList.add('scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 200);
            }

            const deleteModal = document.getElementById('delete-modal');
            if (deleteModal) {
                deleteModal.addEventListener('click', (event) => {
                    if (event.target === deleteModal) {
                        closeDeleteModal();
                    }
                });
            }

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeDeleteModal();
                }
            });
        </script>
    @endpush
</x-app-layout>
