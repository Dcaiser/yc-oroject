<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-700 rounded-full">
                    <i class="fas fa-user-edit"></i>
                </span>
                Edit User: {{ $user->name }}
            </h1>
            <a href="{{ route('users.index') }}"
               class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-linear-to-r from-teal-500 to-emerald-600">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-breadcrumb :items="[
            ['title' => 'Manajemen User', 'url' => route('users.index')],
            ['title' => 'Edit: ' . $user->name]
        ]" />

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-3xl">
            <div class="p-6 sm:p-8">
                <div class="flex flex-col gap-3 p-4 mb-8 text-sm border rounded-2xl bg-emerald-50/60 border-emerald-100 text-emerald-800 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 text-emerald-600 bg-white border border-emerald-100 rounded-xl">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <div>
                            <p class="text-sm font-semibold">Perubahan ini langsung mempengaruhi akses {{ $user->name }}</p>
                            <p class="text-xs text-emerald-700">Pastikan role dan email sudah sesuai sebelum menyimpan.</p>
                        </div>
                    </div>
                    <div class="px-3 py-2 text-xs font-semibold text-emerald-700 bg-white border border-emerald-100 rounded-xl">
                        Tip: Kosongkan password jika tidak ingin mengubahnya.
                    </div>
                </div>

                <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">
                        <div class="space-y-6">
                            <section class="p-5 space-y-4 border border-emerald-100 rounded-2xl">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold tracking-wide text-slate-900">Data Pengguna</h3>
                                    <span class="text-xs font-semibold text-emerald-600">Wajib diisi</span>
                                </div>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label for="name" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Nama Lengkap<span class="text-red-500"> *</span></label>
                                        <input type="text"
                                               id="name"
                                               name="name"
                                               value="{{ old('name', $user->name) }}"
                                               required
                                               class="w-full mt-2 px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                                        @error('name')
                                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="email" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Email<span class="text-red-500"> *</span></label>
                                        <input type="email"
                                               id="email"
                                               name="email"
                                               value="{{ old('email', $user->email) }}"
                                               required
                                               class="w-full mt-2 px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                                        @error('email')
                                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="role" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Role<span class="text-red-500"> *</span></label>
                                        <div class="relative mt-2">
                                            <select id="role"
                                                    name="role"
                                                    required
                                                    class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl appearance-none focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400">
                                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>Manager</option>
                                                <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                                            </select>
                                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400">
                                                <i class="fas fa-chevron-down text-xs"></i>
                                            </span>
                                        </div>
                                        @error('role')
                                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="p-5 space-y-4 border border-emerald-100 rounded-2xl bg-emerald-50/30">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold tracking-wide text-slate-900">Kredensial Masuk</h3>
                                    <span class="text-xs font-semibold text-emerald-600">Opsional</span>
                                </div>
                                <p class="text-xs text-slate-500">Isi password baru hanya bila diperlukan. Kami menyarankan kombinasi huruf, angka, dan karakter spesial.</p>
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="password" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Password Baru</label>
                                        <div class="relative mt-2">
                                            <input type="password"
                                                   id="password"
                                                   name="password"
                                                   placeholder="Kosongkan jika tidak diubah"
                                                   class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                                            <button type="button"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 hover:text-emerald-600"
                                                    data-password-toggle
                                                    data-target="#password">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Konfirmasi Password Baru</label>
                                        <div class="relative mt-2">
                                            <input type="password"
                                                   id="password_confirmation"
                                                   name="password_confirmation"
                                                   placeholder="Ulangi password baru"
                                                   class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                                            <button type="button"
                                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 hover:text-emerald-600"
                                                    data-password-toggle
                                                    data-target="#password_confirmation">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </section>

                            <section class="p-5 border border-emerald-100 rounded-2xl">
                                <h3 class="text-sm font-semibold tracking-wide text-slate-900">Checklist Sebelum Simpan</h3>
                                <ul class="mt-3 space-y-2 text-xs text-emerald-700">
                                    <li class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-full">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </span>
                                        Email masih aktif dan dapat menerima notifikasi
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-full">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </span>
                                        Role telah disesuaikan dengan tanggung jawab terbaru
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <span class="inline-flex items-center justify-center w-5 h-5 text-emerald-600 bg-emerald-50 border border-emerald-200 rounded-full">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </span>
                                        Password baru (jika diubah) mudah diingat oleh user
                                    </li>
                                </ul>
                            </section>
                        </div>

                        <div class="space-y-6">
                            <section class="p-5 space-y-4 border border-emerald-100 rounded-2xl bg-emerald-50/40">
                                <h3 class="text-sm font-semibold text-emerald-900">Profil Saat Ini</h3>
                                <div class="flex items-start gap-4">
                                    <div>
                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="object-cover w-20 h-20 rounded-full ring-4 ring-emerald-100">
                                        @else
                                            <span class="flex items-center justify-center w-20 h-20 text-emerald-500 bg-white border border-emerald-100 rounded-full">
                                                <i class="text-3xl fas fa-user"></i>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-2 text-sm text-slate-600">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $user->name }}</p>
                                            <p class="text-xs uppercase tracking-wide text-emerald-600">{{ ucfirst($user->role) }}</p>
                                        </div>
                                        <div class="space-y-1 text-xs text-slate-500">
                                            <p><span class="font-semibold text-slate-700">Bergabung:</span> {{ $user->created_at->format('d M Y') }}</p>
                                            <p><span class="font-semibold text-slate-700">Terakhir diperbarui:</span> {{ $user->updated_at->format('d M Y H:i') }}</p>
                                        </div>
                                        @if ($user->id === auth()->id())
                                            <p class="flex items-center gap-2 text-xs font-semibold text-emerald-600">
                                                <i class="fas fa-check-circle"></i>
                                                Ini adalah akun Anda saat ini.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </section>

                            <section class="p-5 border border-emerald-100 rounded-2xl" data-avatar-uploader>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">Avatar Baru (Opsional)</h3>
                                        <p class="text-xs text-slate-500">Unggah gambar JPG, PNG, GIF, atau WEBP maksimal 2MB untuk mengganti avatar.</p>
                                    </div>
                                    <button type="button" data-remove class="hidden text-xs font-semibold text-red-600 hover:text-red-700">
                                        Hapus pilihan
                                    </button>
                                </div>
                                <div data-drop-zone class="flex flex-col items-center justify-center gap-4 p-6 mt-4 text-center transition border-2 border-dashed rounded-2xl border-emerald-200 bg-emerald-50/40">
                                    <div data-placeholder class="space-y-3">
                                        <span class="inline-flex items-center justify-center w-16 h-16 bg-white text-emerald-500 border border-emerald-100 rounded-full">
                                            <i class="text-2xl fas fa-user-circle"></i>
                                        </span>
                                        <div class="space-y-1 text-sm text-slate-600">
                                            <p><strong>Drag &amp; drop</strong> file ke sini</p>
                                            <p class="text-xs text-slate-500">atau pilih dari perangkat</p>
                                        </div>
                                        <button type="button" data-trigger class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-linear-to-r from-teal-500 to-emerald-600">
                                            <i class="fas fa-upload"></i>Pilih Avatar
                                        </button>
                                    </div>
                                    <div class="relative hidden" data-preview-container>
                                        <img data-preview-image src="" alt="Preview avatar" class="object-cover w-28 h-28 rounded-full ring-4 ring-emerald-100">
                                    </div>
                                </div>
                                <input type="file"
                                       name="avatar"
                                       accept="image/*"
                                       data-max-size="2048"
                                       class="hidden"
                                       data-file-input>
                                <p class="mt-3 text-xs text-slate-500" data-helper>Pakai gambar persegi agar komposisi tetap rapi.</p>
                                <p class="mt-2 text-xs font-semibold text-red-600 hidden" data-error></p>
                            </section>

                            <section class="p-5 border border-emerald-100 rounded-2xl">
                                <h3 class="text-sm font-semibold tracking-wide text-slate-900">Hak Akses Role</h3>
                                <div class="mt-3 space-y-3 text-sm text-slate-600">
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-slate-100 text-slate-600 rounded-xl">
                                            <i class="fas fa-user-gear"></i>
                                        </span>
                                        <span><strong>Admin</strong> — Kelola konfigurasi, pengguna, dan keamanan sistem.</span>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-sky-100 text-sky-600 rounded-xl">
                                            <i class="fas fa-user-tie"></i>
                                        </span>
                                        <span><strong>Manager</strong> — Mengawasi inventaris, transaksi, dan laporan operasional.</span>
                                    </div>
                                    <div class="flex items-start gap-3">
                                        <span class="inline-flex items-center justify-center w-8 h-8 bg-teal-100 text-teal-600 rounded-xl">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <span><strong>Staff</strong> — Fokus pada tugas harian, pencatatan stok, dan data lapangan.</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 pt-6 mt-4 border-t border-emerald-100 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-slate-500">Kolom bertanda <span class="text-red-500">*</span> wajib diisi.</p>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('users.index') }}"
                               class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-emerald-700 transition bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100">
                                <i class="fas fa-rotate-left"></i>
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-linear-to-r from-emerald-500 to-emerald-600">
                                <i class="fas fa-save"></i>
                                Update User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-password-toggle]').forEach((button) => {
                    const targetSelector = button.dataset.target;
                    const target = document.querySelector(targetSelector);
                    if (!target) {
                        return;
                    }

                    const icon = button.querySelector('i');
                    button.addEventListener('click', () => {
                        if (target.type === 'password') {
                            target.type = 'text';
                            icon?.classList.replace('fa-eye-slash', 'fa-eye');
                        } else {
                            target.type = 'password';
                            icon?.classList.replace('fa-eye', 'fa-eye-slash');
                        }
                    });
                });

                const avatarSection = document.querySelector('[data-avatar-uploader]');
                if (!avatarSection) {
                    return;
                }

                const dropZone = avatarSection.querySelector('[data-drop-zone]');
                const fileInput = avatarSection.querySelector('[data-file-input]');
                const triggerButton = avatarSection.querySelector('[data-trigger]');
                const placeholder = avatarSection.querySelector('[data-placeholder]');
                const previewContainer = avatarSection.querySelector('[data-preview-container]');
                const previewImage = avatarSection.querySelector('[data-preview-image]');
                const removeButton = avatarSection.querySelector('[data-remove]');
                const errorText = avatarSection.querySelector('[data-error]');
                const helperText = avatarSection.querySelector('[data-helper]');
                const maxSizeKb = parseInt(fileInput?.dataset.maxSize ?? '2048', 10);
                const maxSizeBytes = maxSizeKb * 1024;
                const supportedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                let objectUrl = null;

                const clearError = () => {
                    if (!errorText) {
                        return;
                    }
                    errorText.textContent = '';
                    errorText.classList.add('hidden');
                    helperText?.classList.remove('hidden');
                };

                const resetPreview = () => {
                    if (objectUrl) {
                        URL.revokeObjectURL(objectUrl);
                        objectUrl = null;
                    }
                    if (fileInput) {
                        fileInput.value = '';
                    }
                    previewContainer?.classList.add('hidden');
                    previewImage?.setAttribute('src', '');
                    removeButton?.classList.add('hidden');
                    placeholder?.classList.remove('hidden');
                    helperText?.classList.remove('hidden');
                };

                const showError = (message) => {
                    if (!errorText) {
                        return;
                    }
                    resetPreview();
                    errorText.textContent = message;
                    errorText.classList.remove('hidden');
                    helperText?.classList.add('hidden');
                };

                const showPreview = (file) => {
                    if (!previewImage || !previewContainer) {
                        return;
                    }
                    if (objectUrl) {
                        URL.revokeObjectURL(objectUrl);
                    }
                    objectUrl = URL.createObjectURL(file);
                    previewImage.src = objectUrl;
                    previewContainer.classList.remove('hidden');
                    placeholder?.classList.add('hidden');
                    removeButton?.classList.remove('hidden');
                    helperText?.classList.remove('hidden');
                    clearError();
                };

                const handleFiles = (files) => {
                    if (!files || !files.length) {
                        return false;
                    }
                    const file = files[0];
                    if (!supportedTypes.includes(file.type)) {
                        showError('Format tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.');
                        return false;
                    }
                    if (file.size > maxSizeBytes) {
                        showError(`Ukuran file melebihi ${Math.round(maxSizeKb / 1024)}MB. Kurangi ukuran gambar.`);
                        return false;
                    }
                    clearError();
                    showPreview(file);
                    return true;
                };

                triggerButton?.addEventListener('click', () => fileInput?.click());
                removeButton?.addEventListener('click', () => {
                    resetPreview();
                    clearError();
                });
                fileInput?.addEventListener('change', (event) => {
                    const isValid = handleFiles(event.target.files);
                    if (!isValid) {
                        event.target.value = '';
                    }
                });

                ['dragenter', 'dragover'].forEach((eventName) => {
                    dropZone?.addEventListener(eventName, (event) => {
                        event.preventDefault();
                        dropZone.classList.add('border-emerald-300', 'bg-emerald-100');
                    });
                });

                ['dragleave', 'drop'].forEach((eventName) => {
                    dropZone?.addEventListener(eventName, (event) => {
                        event.preventDefault();
                        dropZone.classList.remove('border-emerald-300', 'bg-emerald-100');
                    });
                });

                dropZone?.addEventListener('drop', (event) => {
                    const files = event.dataTransfer?.files;
                    if (files && files.length) {
                        const isValid = handleFiles(files);
                        if (isValid && fileInput) {
                            if (typeof DataTransfer !== 'undefined') {
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(files[0]);
                                fileInput.files = dataTransfer.files;
                            } else {
                                fileInput.files = files;
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
