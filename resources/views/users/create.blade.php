<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-user-plus"></i>{{ __('Tambah User Baru') }}
            </h2>
            <a href="{{ route('users.index') }}"
               class="px-4 py-2 font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['title' => 'Manajemen User', 'url' => route('users.index')],
            ['title' => 'Tambah User']
        ]" />

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <!-- Left Column - User Info -->
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block mb-1 text-sm font-medium text-gray-700">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block mb-1 text-sm font-medium text-gray-700">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                       placeholder="Masukkan alamat email">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block mb-1 text-sm font-medium text-gray-700">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select id="role"
                                        name="role"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    <option value="">Pilih Role</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                        <i class="fas fa-crown"></i> Admin
                                    </option>
                                    <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>
                                        <i class="fas fa-user-tie"></i> Manager
                                    </option>
                                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>
                                        <i class="fas fa-user"></i> Staff
                                    </option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           placeholder="Masukkan password">
                                    <button type="button"
                                            onclick="togglePassword('password')"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i id="password-icon" class="text-gray-400 fas fa-eye-slash"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block mb-1 text-sm font-medium text-gray-700">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           placeholder="Konfirmasi password">
                                    <button type="button"
                                            onclick="togglePassword('password_confirmation')"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <i id="password_confirmation-icon" class="text-gray-400 fas fa-eye-slash"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column - Avatar Upload -->
                        <div class="space-y-4">
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Avatar (Opsional)</label>
                                <div x-data="imageUploader()" class="space-y-4">
                                    <!-- Image Preview -->
                                    <div class="p-6 text-center border-2 border-gray-300 border-dashed rounded-lg"
                                         :class="{ 'border-green-500 bg-green-50': dragging }"
                                         @dragover.prevent="dragging = true"
                                         @dragleave.prevent="dragging = false"
                                         @drop.prevent="handleDrop($event)">

                                        <template x-if="!preview">
                                            <div>
                                                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-200 rounded-full">
                                                    <i class="text-2xl text-gray-400 fas fa-user"></i>
                                                </div>
                                                <p class="mb-2 text-gray-600">Drag & drop avatar di sini</p>
                                                <p class="mb-4 text-sm text-gray-500">atau</p>
                                                <button type="button"
                                                        @click="$refs.fileInput.click()"
                                                        class="px-4 py-2 text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                                                    Pilih File
                                                </button>
                                            </div>
                                        </template>

                                        <template x-if="preview">
                                            <div class="relative">
                                                <img :src="preview" alt="Preview" class="object-cover w-32 h-32 mx-auto rounded-full">
                                                <button type="button"
                                                        @click="preview = null; $refs.fileInput.value = ''"
                                                        class="absolute top-0 right-0 flex items-center justify-center w-8 h-8 text-white bg-red-500 rounded-full hover:bg-red-600">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    <input type="file"
                                           x-ref="fileInput"
                                           name="avatar"
                                           accept="image/*"
                                           @change="updatePreview($event.target.files[0])"
                                           class="hidden">

                                    <p class="text-xs text-gray-500">
                                        Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.
                                    </p>
                                </div>
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role Information -->
                            <div class="p-4 rounded-lg bg-gray-50">
                                <h4 class="mb-2 font-medium text-gray-900">Informasi Role:</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <i class="mr-2 text-red-500 fas fa-crown"></i>
                                        <span><strong>Admin:</strong> Akses penuh ke seluruh sistem</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="mr-2 text-yellow-500 fas fa-user-tie"></i>
                                        <span><strong>Manager:</strong> Kelola produk, inventory, dan laporan</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="mr-2 text-green-500 fas fa-user"></i>
                                        <span><strong>Staff:</strong> Akses terbatas untuk inventory</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex items-center justify-end pt-6 space-x-4 border-t border-gray-200">
                        <a href="{{ route('users.index') }}"
                           class="px-6 py-2 font-medium text-gray-800 transition-colors bg-gray-300 rounded-lg hover:bg-gray-400">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-6 py-2 font-medium text-white transition-colors bg-green-600 rounded-lg hover:bg-green-700">
                            <i class="mr-2 fas fa-save"></i>Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fas fa-eye text-gray-400';
            } else {
                field.type = 'password';
                icon.className = 'fas fa-eye-slash text-gray-400';
            }
        }
    </script>
    @endpush
</x-app-layout>
