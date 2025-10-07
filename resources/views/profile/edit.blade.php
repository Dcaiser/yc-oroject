<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h1 class="flex items-center gap-3 text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 bg-green-100 text-green-700 rounded-full">
                    <i class="fas fa-id-card"></i>
                </span>
                Pengaturan Profil
            </h1>
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-gradient-to-r from-teal-500 to-emerald-600">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="flex flex-col gap-3 p-4 text-sm border rounded-2xl bg-emerald-50/60 border-emerald-100 text-emerald-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 text-emerald-600 bg-white border border-emerald-100 rounded-xl">
                    <i class="fas fa-shield-alt"></i>
                </span>
                <div>
                    <p class="text-sm font-semibold">Kelola identitas dan keamanan akun Anda</p>
                    <p class="text-xs text-emerald-700">Perbarui informasi personal, ubah password, dan atur keamanan akun dari satu halaman.</p>
                </div>
            </div>
            <div class="px-3 py-2 text-xs font-semibold text-emerald-700 bg-white border border-emerald-100 rounded-xl">
                Tip: Gunakan password unik dan perbarui secara berkala.
            </div>
        </div>

        @php
            $profileAvatarPath = $user->avatar ?? null;
            if ($profileAvatarPath && !\Illuminate\Support\Str::startsWith($profileAvatarPath, ['http://', 'https://'])) {
                $profileAvatarUrl = \Illuminate\Support\Facades\Storage::url($profileAvatarPath);
            } else {
                $profileAvatarUrl = $profileAvatarPath;
            }
            $profileInitials = collect(explode(' ', $user->name))
                ->filter()
                ->map(fn ($segment) => mb_substr($segment, 0, 1))
                ->join('');
            if (empty($profileInitials)) {
                $profileInitials = 'A';
            }
        @endphp

        <div class="grid gap-6 xl:grid-cols-[1fr,1.5fr]">
            <div class="space-y-6">
                <section class="p-6 border border-emerald-100 rounded-2xl bg-white/90 shadow-sm">
                    <div class="flex flex-col items-center gap-4 text-center">
                        <button type="button"
                                class="relative w-28 h-28 overflow-hidden rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 ring-4 ring-emerald-100 shadow-lg cursor-zoom-in focus:outline-none focus:ring-4 focus:ring-emerald-300/60"
                                x-data="{}"
                                x-on:click.prevent="$dispatch('open-modal', 'profile-avatar-preview')">
                            @if ($profileAvatarUrl)
                                <img src="{{ $profileAvatarUrl }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full text-3xl font-semibold text-white">
                                    {{ $profileInitials }}
                                </div>
                            @endif
                            <span class="absolute bottom-3 right-3 inline-flex items-center justify-center w-7 h-7 text-emerald-600 bg-white rounded-full shadow">
                                <i class="fas fa-circle-user"></i>
                            </span>
                        </button>
                        <div>
                            <h2 class="text-xl font-semibold text-emerald-900">{{ $user->name }}</h2>
                            <p class="text-sm text-emerald-600">{{ $user->email }}</p>
                        </div>
                        <div class="grid w-full gap-3 text-sm text-slate-600">
                            <div class="flex items-center justify-between px-4 py-2 rounded-xl bg-emerald-50">
                                <span class="font-medium text-emerald-800">Role</span>
                                <span class="px-3 py-1 text-xs font-semibold text-emerald-700 uppercase bg-white border border-emerald-100 rounded-lg">{{ $user->role }}</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2 rounded-xl bg-slate-50">
                                <span class="text-slate-500">Bergabung</span>
                                <span class="font-semibold text-slate-700">{{ $user->created_at?->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-2 rounded-xl bg-slate-50">
                                <span class="text-slate-500">Terakhir diperbarui</span>
                                <span class="font-semibold text-slate-700">{{ $user->updated_at?->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="p-6 border border-emerald-100 rounded-2xl bg-emerald-50/60 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 text-emerald-700 bg-white rounded-xl">
                            <i class="fas fa-lock"></i>
                        </span>
                        <div>
                            <h3 class="text-sm font-semibold text-emerald-900 uppercase tracking-wide">Langkah Keamanan</h3>
                            <p class="text-xs text-emerald-700">Ikuti langkah sederhana ini untuk menjaga akun tetap aman.</p>
                        </div>
                    </div>
                    <ul class="space-y-4 text-sm leading-relaxed text-emerald-800">
                        <li class="flex items-start gap-3">
                            <span class="inline-flex items-center justify-center w-6 h-6 text-white bg-emerald-500 rounded-full">
                                <i class="fas fa-check text-[10px]"></i>
                            </span>
                            <div class="flex-1 text-left">
                                <p>Gunakan password minimal 8 karakter dengan kombinasi huruf <span class="font-semibold italic">capital</span>, angka, dan simbol.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="inline-flex items-center justify-center w-6 h-6 text-white bg-emerald-500 rounded-full">
                                <i class="fas fa-check text-[10px]"></i>
                            </span>
                            <div class="flex-1 text-left">
                                <p>Periksa email Anda secara berkala untuk verifikasi dan notifikasi penting.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="inline-flex items-center justify-center w-6 h-6 text-white bg-emerald-500 rounded-full">
                                <i class="fas fa-check text-[10px]"></i>
                            </span>
                            <div class="flex-1 text-left">
                                <p>Perbarui data profil ketika terjadi perubahan kepemilikan atau kontak.</p>
                            </div>
                        </li>
                    </ul>
                </section>
            </div>

            <div class="space-y-6">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <x-modal name="profile-avatar-preview" focusable>
        <div class="p-6 space-y-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-emerald-900">Pratinjau Foto Profil</h2>
                    <p class="text-sm text-emerald-600/80">Klik tutup untuk kembali ke pengaturan profil.</p>
                </div>
                <button type="button" class="text-emerald-500 hover:text-emerald-700 transition"
                        x-on:click="$dispatch('close')">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            @if ($profileAvatarUrl)
                <div class="overflow-hidden rounded-3xl border-2 border-emerald-100 shadow-xl">
                    <img src="{{ $profileAvatarUrl }}" alt="{{ $user->name }}" class="object-contain w-full max-h-[70vh] bg-white">
                </div>
            @else
                <div class="p-6 text-center border border-dashed border-emerald-200 rounded-2xl bg-emerald-50/60">
                    <p class="text-sm font-medium text-emerald-700">Belum ada foto profil yang diunggah.</p>
                    <p class="text-xs text-emerald-500 mt-1">Unggah foto melalui formulir "Informasi Profil" untuk melihat pratinjau di sini.</p>
                </div>
            @endif

            <div class="flex justify-end">
                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow transition bg-gradient-to-r from-emerald-500 to-teal-500 hover:scale-[1.02]"
                        x-on:click="$dispatch('close')">
                    <i class="fas fa-check"></i>
                    Tutup
                </button>
            </div>
        </div>
    </x-modal>

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

                const avatarInput = document.querySelector('[data-profile-avatar-input]');
                const previewImage = document.querySelector('[data-profile-avatar-preview]');
                const placeholder = document.querySelector('[data-profile-avatar-placeholder]');
                const removeField = document.querySelector('[data-profile-avatar-remove-field]');
                const removeButton = document.querySelector('[data-profile-avatar-remove]');
                const removeLabel = document.querySelector('[data-profile-avatar-remove-label]');
                const originalSrc = previewImage?.dataset.originalSrc || '';
                const DISABLED_CLASSES = ['opacity-50', 'cursor-not-allowed', 'pointer-events-none'];
                let avatarObjectUrl = null;

                const revokeAvatarObjectUrl = () => {
                    if (avatarObjectUrl) {
                        URL.revokeObjectURL(avatarObjectUrl);
                        avatarObjectUrl = null;
                    }
                };

                const showPreviewImage = (src) => {
                    if (!previewImage) {
                        return;
                    }

                    if (src) {
                        previewImage.src = src;
                    }

                    previewImage.classList.remove('hidden');
                    placeholder?.classList.add('hidden');
                };

                const showPlaceholder = () => {
                    placeholder?.classList.remove('hidden');
                    previewImage?.classList.add('hidden');
                };

                const setRemoveMode = (isRemoving) => {
                    if (!removeField) {
                        return;
                    }

                    removeField.value = isRemoving ? '1' : '0';

                    if (removeLabel) {
                        removeLabel.textContent = isRemoving ? 'Batalkan Penghapusan' : 'Hapus Foto';
                    }

                    const icon = removeButton?.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-trash', !isRemoving);
                        icon.classList.toggle('fa-rotate-left', isRemoving);
                    }
                };

                const updateRemoveButtonAvailability = () => {
                    if (!removeButton) {
                        return;
                    }

                    const hasSelectedFile = Boolean(avatarInput?.files?.length);
                    const removalActive = removeField?.value === '1';
                    const canInteract = hasSelectedFile || !!originalSrc || removalActive;

                    if (canInteract) {
                        removeButton.classList.remove(...DISABLED_CLASSES);
                    } else {
                        removeButton.classList.add(...DISABLED_CLASSES);
                    }
                };

                if (removeField && removeField.value === '1' && originalSrc) {
                    setRemoveMode(true);
                    showPlaceholder();
                }

                avatarInput?.addEventListener('change', (event) => {
                    const file = event.target.files?.[0];
                    revokeAvatarObjectUrl();

                    if (file) {
                        avatarObjectUrl = URL.createObjectURL(file);
                        showPreviewImage(avatarObjectUrl);
                        setRemoveMode(false);
                    } else if (removeField?.value === '1' && originalSrc) {
                        showPlaceholder();
                    } else if (originalSrc) {
                        showPreviewImage(originalSrc);
                    } else {
                        showPlaceholder();
                    }

                    updateRemoveButtonAvailability();
                });

                removeButton?.addEventListener('click', () => {
                    if (removeButton.classList.contains('pointer-events-none')) {
                        return;
                    }

                    const hasSelectedFile = Boolean(avatarInput?.files?.length);
                    const removalActive = removeField?.value === '1';

                    if (hasSelectedFile) {
                        if (avatarInput) {
                            avatarInput.value = '';
                        }
                        revokeAvatarObjectUrl();

                        if (originalSrc) {
                            showPreviewImage(originalSrc);
                        } else {
                            showPlaceholder();
                        }

                        setRemoveMode(false);
                        updateRemoveButtonAvailability();
                        return;
                    }

                    if (originalSrc) {
                        if (removalActive) {
                            showPreviewImage(originalSrc);
                            setRemoveMode(false);
                        } else {
                            showPlaceholder();
                            setRemoveMode(true);
                        }
                    } else {
                        showPlaceholder();
                        setRemoveMode(false);
                    }

                    updateRemoveButtonAvailability();
                });

                updateRemoveButtonAvailability();
            });
        </script>
    @endpush
</x-app-layout>
