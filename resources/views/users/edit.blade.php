<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-user-edit"></i>{{ __('Edit User: ') . $user->name }}
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
            ['title' => 'Edit: ' . $user->name]
        ]" />

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

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
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
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
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block mb-1 text-sm font-medium text-gray-700">
                                    Password Baru (Kosongkan jika tidak ingin mengubah)
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           placeholder="Masukkan password baru">
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
                                    Konfirmasi Password Baru
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                           placeholder="Konfirmasi password baru">
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
                            <!-- Current Avatar -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Avatar Saat Ini</label>
                                <div class="flex items-center space-x-4">
                                    @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                         alt="{{ $user->name }}"
                                         class="object-cover w-20 h-20 border-2 border-gray-200 rounded-full">
                                    @else
                                    <div class="flex items-center justify-center w-20 h-20 bg-gray-200 border-2 border-gray-200 rounded-full">
                                        <i class="text-2xl text-gray-400 fas fa-user"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="text-sm text-gray-600">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($user->role) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- New Avatar Upload -->
                            <div>
                                <label class="block mb-1 text-sm font-medium text-gray-700">Avatar Baru (Opsional)</label>
                                <div x-data="imageUploader()" class="space-y-4">
                                    <div class="p-6 text-center border-2 border-gray-300 border-dashed rounded-lg">
                                        <template x-if="!preview">
                                            <div>
                                                <i class="mb-4 text-4xl text-gray-400 fas fa-cloud-upload-alt"></i>
                                                <p class="mb-2 text-gray-600">Pilih gambar baru jika ingin mengubah avatar</p>
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
                                </div>
                                @error('avatar')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- User Stats -->
                            <div class="p-4 rounded-lg bg-gray-50">
                                <h4 class="mb-2 font-medium text-gray-900">Informasi User:</h4>
                                <div class="space-y-2 text-sm text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Bergabung:</span>
                                        <span>{{ $user->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Terakhir update:</span>
                                        <span>{{ $user->updated_at->format('d M Y H:i') }}</span>
                                    </div>
                                    @if($user->id === auth()->id())
                                    <div class="font-medium text-green-600">
                                        <i class="mr-1 fas fa-info-circle"></i>
                                        Ini adalah akun Anda saat ini
                                    </div>
                                    @endif
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
                            <i class="mr-2 fas fa-save"></i>Update User
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
