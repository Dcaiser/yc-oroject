<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-extrabold text-emerald-900 flex items-center gap-2">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-emerald-100 text-emerald-700 rounded-full">
                    <i class="fas fa-users"></i>
                </span>
                {{ __('Manajemen User') }}
            </h2>
            <a href="{{ route('users.create') }}"
                class="flex items-center px-4 py-2 font-medium text-white bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-lg shadow hover:scale-105 transition">
                <i class="mr-2 fas fa-plus"></i>Tambah User
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[['title' => 'Manajemen User']]" />

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Users Card -->
            <div class="p-6 transition-all duration-300 bg-white border border-emerald-200 shadow-lg rounded-2xl hover:shadow-xl hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg shadow-sm bg-gradient-to-r from-emerald-500 to-emerald-600">
                            <i class="text-xl text-white fas fa-users"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-emerald-700">Total User</p>
                        <p class="text-2xl font-bold text-emerald-900">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Admin Card -->
            <div class="p-6 transition-all duration-300 bg-white border border-purple-200 shadow-lg rounded-2xl hover:shadow-xl hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg shadow-sm bg-gradient-to-r from-purple-500 to-purple-600">
                            <i class="text-xl text-white fas fa-crown"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-700">Admin</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $stats['admin'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Manager Card -->
            <div class="p-6 transition-all duration-300 bg-white border border-blue-200 shadow-lg rounded-2xl hover:shadow-xl hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg shadow-sm bg-gradient-to-r from-blue-500 to-blue-600">
                            <i class="text-xl text-white fas fa-user-tie"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-700">Manager</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $stats['manager'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Staff Card -->
            <div class="p-6 transition-all duration-300 bg-white border border-teal-200 shadow-lg rounded-2xl hover:shadow-xl hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-lg shadow-sm bg-gradient-to-r from-teal-500 to-teal-600">
                            <i class="text-xl text-white fas fa-user"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-teal-700">Staff</p>
                        <p class="text-2xl font-bold text-teal-900">{{ $stats['staff'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
            <div class="p-6">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <!-- Search Input -->
                    <div class="flex-1 max-w-md">
                        <form method="GET" action="{{ route('users.index') }}" class="flex">
                            <div class="relative flex-1">
                                <input type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Cari user..."
                                    class="w-full py-2 pl-10 pr-4 border-2 border-emerald-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 bg-emerald-50">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <i class="text-emerald-400 fas fa-search"></i>
                                </div>
                            </div>
                            <button type="submit"
                                class="px-4 py-2 text-white bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-r-xl hover:scale-105 transition">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>

                    <!-- Filter and Action Options -->
                    <div class="flex items-center gap-4">
                        <!-- Role Filter -->
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium text-emerald-700">Filter:</label>
                            <form method="GET" action="{{ route('users.index') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <div class="relative">
                                    <select name="role" onchange="this.form.submit()"
                                            style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: none;"
                                            class="bg-white border-2 border-emerald-200 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 min-w-[140px] text-emerald-800 font-medium cursor-pointer transition-colors">
                                        <option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>Semua Role</option>
                                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                                        <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                    </select>
                                    <!-- Single Custom Arrow -->
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-emerald-500 text-sm"></i>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Action Buttons -->
                        <button id="bulk-delete-btn"
                                class="p-3 text-white bg-gradient-to-r from-red-500 to-red-700 rounded-lg shadow hover:scale-105 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                            <i class="fa-solid fa-trash"></i> Hapus (<span id="selected-count">0</span>)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            class="p-4 mb-4 text-emerald-800 border border-emerald-500 rounded-lg bg-emerald-50">
            {{ session('success') }}
        </div>
        @endif

        <!-- Users Table -->
        <div class="overflow-hidden bg-white shadow-lg rounded-2xl">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-emerald-900">Daftar User</h3>
                        <p class="text-sm text-gray-600">Kelola semua pengguna sistem inventory</p>
                    </div>
                </div>

                <form id="bulk-delete-form" method="POST" action="{{ route('users.bulk-delete') }}">
                    @csrf
                    @method('DELETE')

                    <!-- Table Container with Custom Scroll -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="border-b-2 bg-gradient-to-r from-emerald-100 to-emerald-200 border-emerald-500">
                                    <th class="px-3 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">
                                        <input type="checkbox" id="select-all" class="border-gray-300 rounded text-emerald-600 focus:ring-emerald-500">
                                    </th>
                                    <th class="px-4 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">User</th>
                                    <th class="px-4 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Email</th>
                                    <th class="px-4 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Role</th>
                                    <th class="px-4 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Status</th>
                                    <th class="px-4 py-4 text-xs font-semibold tracking-wider text-left text-gray-700 uppercase">Bergabung</th>
                                    <th class="px-4 py-4 text-xs font-semibold tracking-wider text-center text-gray-700 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr class="transition-colors duration-150 border-b border-gray-100 hover:bg-emerald-50">
                                    <td class="px-3 py-4">
                                        @if($user->id !== auth()->id())
                                        <input type="checkbox"
                                               name="user_ids[]"
                                               value="{{ $user->id }}"
                                               class="border-gray-300 rounded user-checkbox text-emerald-600 focus:ring-emerald-500">
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10">
                                                @if($user->avatar)
                                                <img class="object-cover w-10 h-10 rounded-full ring-2 ring-gray-200"
                                                     src="{{ asset('storage/' . $user->avatar) }}"
                                                     alt="{{ $user->name }}">
                                                @else
                                                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-gray-200 to-gray-300">
                                                    <i class="text-gray-500 fas fa-user"></i>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $user->name }}
                                                    @if($user->id === auth()->id())
                                                    <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-full font-medium ml-2">
                                                        <i class="mr-1 fas fa-user-circle"></i>Anda
                                                    </span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm text-gray-700">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-4 py-4">
                                        @php
                                            $roleData = [
                                                'admin' => [
                                                    'bg' => 'bg-gradient-to-r from-purple-100 to-purple-200',
                                                    'text' => 'text-purple-800',
                                                    'icon' => 'fas fa-crown',
                                                    'border' => 'border-purple-300'
                                                ],
                                                'manager' => [
                                                    'bg' => 'bg-gradient-to-r from-blue-100 to-blue-200',
                                                    'text' => 'text-blue-800',
                                                    'icon' => 'fas fa-user-tie',
                                                    'border' => 'border-blue-300'
                                                ],
                                                'staff' => [
                                                    'bg' => 'bg-gradient-to-r from-teal-100 to-teal-200',
                                                    'text' => 'text-teal-800',
                                                    'icon' => 'fas fa-user',
                                                    'border' => 'border-teal-300'
                                                ],
                                            ];
                                            $role = $roleData[$user->role] ?? $roleData['staff'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $role['bg'] }} {{ $role['text'] }} {{ $role['border'] }}">
                                            <i class="{{ $role['icon'] }} mr-1.5"></i>
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-semibold border rounded-full bg-gradient-to-r from-emerald-100 to-emerald-200 text-emerald-800 border-emerald-300">
                                            <div class="w-2 h-2 mr-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">{{ $user->created_at->format('d M Y') }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('users.show', $user) }}"
                                               class="inline-flex items-center justify-center w-9 h-9 text-emerald-600 bg-emerald-100 transition-all duration-200 rounded-lg hover:bg-emerald-200 hover:scale-105"
                                               title="Detail">
                                                <i class="text-sm fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}"
                                               class="inline-flex items-center justify-center w-9 h-9 text-blue-600 bg-blue-100 transition-all duration-200 rounded-lg hover:bg-blue-200 hover:scale-105"
                                               title="Edit">
                                                <i class="text-sm fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                            <button onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                    type="button"
                                                    class="inline-flex items-center justify-center w-9 h-9 text-red-600 bg-red-100 transition-all duration-200 rounded-lg hover:bg-red-200 hover:scale-105"
                                                    title="Hapus">
                                                <i class="text-sm fas fa-trash"></i>
                                            </button>
                                            @else
                                            <span class="inline-flex items-center justify-center w-9 h-9 text-gray-400 bg-gray-100 cursor-not-allowed rounded-lg"
                                                  title="Tidak dapat menghapus akun sendiri">
                                                <i class="text-sm fas fa-lock"></i>
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="flex items-center justify-center w-16 h-16 mb-4 bg-gray-100 rounded-full">
                                                <i class="text-2xl text-gray-400 fas fa-users"></i>
                                            </div>
                                            <h3 class="mb-2 text-xl font-semibold text-gray-900">Belum ada user</h3>
                                            <p class="mb-6 text-gray-500 max-w-sm">Mulai dengan menambahkan user pertama untuk mengelola sistem inventory Anda.</p>
                                            <a href="{{ route('users.create') }}"
                                               class="inline-flex items-center px-6 py-3 text-white bg-gradient-to-r from-emerald-500 to-emerald-700 rounded-lg shadow hover:scale-105 transition">
                                                <i class="mr-2 fas fa-plus"></i>Tambah User Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Custom Pagination -->
                @if($users->hasPages())
                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} user
                    </div>

                    <div class="flex items-center space-x-1">
                        {{-- Previous Page Link --}}
                        @if ($users->onFirstPage())
                            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                <i class="mr-1 fas fa-chevron-left"></i>Previous
                            </span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}"
                               class="px-3 py-2 text-sm text-gray-600 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300">
                                <i class="mr-1 fas fa-chevron-left"></i>Previous
                            </a>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                            @if ($page == $users->currentPage())
                                <span class="px-3 py-2 text-sm font-medium text-white border rounded-lg bg-emerald-600 border-emerald-600">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="px-3 py-2 text-sm text-gray-600 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}"
                               class="px-3 py-2 text-sm text-gray-600 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300">
                                Next<i class="ml-1 fas fa-chevron-right"></i>
                            </a>
                        @else
                            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 rounded-lg cursor-not-allowed">
                                Next<i class="ml-1 fas fa-chevron-right"></i>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center hidden bg-black bg-opacity-50 backdrop-blur-sm">
        <div class="w-full max-w-md p-6 mx-4 transition-all duration-300 transform scale-95 bg-white shadow-2xl rounded-xl">
            <div class="flex items-center mb-4">
                <div class="flex items-center justify-center w-12 h-12 mr-4 rounded-full bg-gradient-to-br from-red-100 to-red-200">
                    <i class="text-xl text-red-600 fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="mb-6 text-gray-700">Apakah Anda yakin ingin menghapus user <strong id="delete-user-name" class="text-red-600"></strong>?</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                        type="button"
                        class="px-5 py-2 font-medium text-gray-600 transition-all duration-200 rounded-lg hover:text-gray-800 hover:bg-gray-100">
                    Batal
                </button>
                <form id="delete-form" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-5 py-2 font-medium text-white transition-all duration-200 rounded-lg shadow-md bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 hover:shadow-lg">
                        <i class="mr-2 fas fa-trash"></i>Hapus User
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Bulk selection functionality
            const selectAllCheckbox = document.getElementById('select-all');
            const userCheckboxes = document.querySelectorAll('.user-checkbox');
            const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
            const selectedCount = document.getElementById('selected-count');
            const bulkDeleteForm = document.getElementById('bulk-delete-form');

            function updateBulkDeleteButton() {
                const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                const count = checkedBoxes.length;

                selectedCount.textContent = count;
                bulkDeleteBtn.disabled = count === 0;

                // Add visual feedback
                if (count > 0) {
                    bulkDeleteBtn.classList.remove('opacity-50');
                    bulkDeleteBtn.classList.add('animate-pulse');
                } else {
                    bulkDeleteBtn.classList.add('opacity-50');
                    bulkDeleteBtn.classList.remove('animate-pulse');
                }
            }

            // Select all functionality
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    userCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButton();
                });
            }

            // Individual checkbox change
            userCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateBulkDeleteButton();

                    const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = checkedCount === userCheckboxes.length;
                        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < userCheckboxes.length;
                    }
                });
            });

            // Bulk delete button click
            if (bulkDeleteBtn) {
                bulkDeleteBtn.addEventListener('click', function() {
                    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
                    if (checkedBoxes.length === 0) return;

                    if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} user terpilih?`)) {
                        bulkDeleteForm.submit();
                    }
                });
            }

            // Initial update
            updateBulkDeleteButton();
        });

        // Delete confirmation functions
        function confirmDelete(userId, userName) {
            document.getElementById('delete-user-name').textContent = userName;
            document.getElementById('delete-form').action = `/users/${userId}`;
            const modal = document.getElementById('delete-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            // Add smooth entrance animation
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');
            }, 10);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            modal.querySelector('.transform').classList.remove('scale-100');
            modal.querySelector('.transform').classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 300);
        }

        // Close modal when clicking outside
        document.getElementById('delete-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
    @endpush
</x-app-layout>
