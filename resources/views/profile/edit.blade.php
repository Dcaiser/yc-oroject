<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 bg-emerald-100 text-emerald-700 rounded-lg sm:rounded-xl">
                    <i class="fas fa-user-cog text-sm sm:text-base"></i>
                </span>
                <div>
                    <h1 class="text-base sm:text-xl font-bold text-slate-800">Pengaturan Profil</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Kelola informasi dan keamanan akun Anda</p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg sm:rounded-xl hover:bg-emerald-100 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        <div class="flex flex-col gap-2 sm:gap-3 p-3 sm:p-4 text-xs sm:text-sm border rounded-xl sm:rounded-2xl bg-emerald-50/60 border-emerald-100 text-emerald-800 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 sm:gap-3">
                <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-emerald-600 bg-white border border-emerald-100 rounded-lg sm:rounded-xl shrink-0">
                    <i class="fas fa-id-card-alt text-sm"></i>
                </span>
                <div>
                    <p class="text-xs sm:text-sm font-semibold">Kelola identitas dan keamanan akun Anda</p>
                    <p class="text-[10px] sm:text-xs text-emerald-700 hidden sm:block">Perbarui informasi personal, ubah password, dan atur keamanan akun dari satu halaman.</p>
                </div>
            </div>
            <div class="hidden lg:block px-3 py-2 text-xs font-medium text-emerald-700 bg-white border border-emerald-100 rounded-lg">
                <i class="fas fa-lightbulb mr-1 text-amber-500"></i> Gunakan password unik
            </div>
        </div>

        @php
            $profileAvatarPath = $user->avatar ?? null;
            if (!empty($profileAvatarPath) && !\Illuminate\Support\Str::startsWith($profileAvatarPath, ['http://', 'https://'])) {
                $profileAvatarUrl = \Illuminate\Support\Facades\Storage::url($profileAvatarPath);
            } else {
                $profileAvatarUrl = !empty($profileAvatarPath) ? $profileAvatarPath : null;
            }
            $profileInitials = collect(explode(' ', $user->name))
                ->filter()
                ->map(fn ($segment) => mb_substr($segment, 0, 1))
                ->join('');
            if (empty($profileInitials)) {
                $profileInitials = 'A';
            }
        @endphp

        <div class="grid gap-4 sm:gap-6 lg:grid-cols-[1fr,1.5fr]">
            <div class="space-y-4 sm:space-y-6">
                <section class="p-4 sm:p-6 border border-emerald-100 rounded-xl sm:rounded-2xl bg-white/90 shadow-sm">
                    <div class="flex flex-col items-center gap-3 sm:gap-4 text-center">
                        <button type="button"
                                class="relative w-20 h-20 sm:w-28 sm:h-28 overflow-hidden rounded-full bg-linear-to-br from-emerald-400 to-emerald-600 ring-4 ring-emerald-100 shadow-lg cursor-zoom-in focus:outline-none focus:ring-4 focus:ring-emerald-300/60"
                                x-data="{}"
                                x-on:click.prevent="$dispatch('open-modal', 'profile-avatar-preview')">
                            @if ($profileAvatarUrl)
                                <img src="{{ $profileAvatarUrl }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
                            @else
                                <div class="flex items-center justify-center w-full h-full text-2xl sm:text-3xl font-semibold text-white">
                                    {{ $profileInitials }}
                                </div>
                            @endif
                            <span class="absolute bottom-2 right-2 sm:bottom-3 sm:right-3 inline-flex items-center justify-center w-6 h-6 sm:w-7 sm:h-7 text-emerald-600 bg-white rounded-full shadow">
                                  <i class="fas fa-user-circle text-xs sm:text-sm"></i>
                            </span>
                        </button>
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold text-emerald-900">{{ $user->name }}</h2>
                            <p class="text-xs sm:text-sm text-emerald-600">{{ $user->email }}</p>
                        </div>
                        <div class="grid w-full gap-2 sm:gap-3 text-xs sm:text-sm text-slate-600">
                            <div class="flex items-center justify-between px-3 sm:px-4 py-2 rounded-lg sm:rounded-xl bg-emerald-50">
                                <span class="font-medium text-emerald-800">Role</span>
                                <span class="px-2 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-semibold text-emerald-700 uppercase bg-white border border-emerald-100 rounded-md sm:rounded-lg">{{ $user->role }}</span>
                            </div>
                            <div class="flex items-center justify-between px-3 sm:px-4 py-2 rounded-lg sm:rounded-xl bg-slate-50">
                                <span class="text-slate-500">Bergabung</span>
                                <span class="font-semibold text-slate-700">{{ $user->created_at?->format('d M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between px-3 sm:px-4 py-2 rounded-lg sm:rounded-xl bg-slate-50">
                                <span class="text-slate-500">Diperbarui</span>
                                <span class="font-semibold text-slate-700">{{ $user->updated_at?->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="hidden sm:block p-4 sm:p-6 border border-emerald-100 rounded-xl sm:rounded-2xl bg-emerald-50/60 shadow-sm space-y-3 sm:space-y-4">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-emerald-700 bg-white rounded-lg sm:rounded-xl">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <div>
                            <h3 class="text-xs sm:text-sm font-semibold text-emerald-900 uppercase tracking-wide">Langkah Keamanan</h3>
                            <p class="text-[10px] sm:text-xs text-emerald-700">Tips menjaga akun tetap aman.</p>
                        </div>
                    </div>
                    <ul class="space-y-3 text-xs sm:text-sm leading-relaxed text-emerald-800">
                        <li class="flex items-start gap-2 sm:gap-3">
                            <span class="inline-flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 text-white bg-emerald-500 rounded-full shrink-0 mt-0.5">
                                <i class="fas fa-check text-[8px] sm:text-[10px]"></i>
                            </span>
                            <p>Password minimal 8 karakter dengan huruf, angka, dan simbol.</p>
                        </li>
                        <li class="flex items-start gap-2 sm:gap-3">
                            <span class="inline-flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 text-white bg-emerald-500 rounded-full shrink-0 mt-0.5">
                                <i class="fas fa-check text-[8px] sm:text-[10px]"></i>
                            </span>
                            <p>Periksa email secara berkala untuk notifikasi penting.</p>
                        </li>
                        <li class="flex items-start gap-2 sm:gap-3">
                            <span class="inline-flex items-center justify-center w-5 h-5 sm:w-6 sm:h-6 text-white bg-emerald-500 rounded-full shrink-0 mt-0.5">
                                <i class="fas fa-check text-[8px] sm:text-[10px]"></i>
                            </span>
                            <p>Perbarui data profil saat ada perubahan.</p>
                        </li>
                    </ul>
                </section>
            </div>

            <div class="space-y-4 sm:space-y-6">
                @include('profile.partials.update-profile-information-form')
                @include('profile.partials.update-password-form')
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

    <x-modal name="profile-avatar-preview" focusable maxWidth="sm">
        <div class="relative overflow-hidden">
            <div class="px-5 sm:px-6 py-4 bg-white/85 backdrop-blur rounded-t-3xl border-b border-emerald-100 flex items-center justify-between">
                <h2 class="text-base sm:text-lg font-semibold text-emerald-900">Foto Profil</h2>
                <button type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-emerald-500 hover:text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-full transition"
                        x-on:click="$dispatch('close')">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="px-5 sm:px-6 py-5 bg-white/95">
                @if ($profileAvatarUrl)
                    <div class="flex justify-center">
                        <div class="relative flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 rounded-full border border-emerald-100 shadow bg-white overflow-hidden">
                            <img src="{{ $profileAvatarUrl }}" alt="{{ $user->name }}" class="object-cover w-full h-full">
                        </div>
                    </div>
                @else
                    <div class="p-5 text-center border border-dashed border-emerald-200 rounded-2xl bg-emerald-50/70">
                        <p class="text-sm font-semibold text-emerald-700">Belum ada foto profil.</p>
                        <p class="mt-1 text-xs text-emerald-500">Unggah foto melalui formulir profil terlebih dahulu.</p>
                    </div>
                @endif
            </div>

            <div class="px-5 sm:px-6 py-3 bg-emerald-50/80 border-t border-emerald-100 rounded-b-3xl flex justify-end">
                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white rounded-xl shadow transition bg-linear-to-r from-emerald-500 to-teal-500 hover:scale-[1.02]"
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
                        icon.classList.toggle('fa-undo', isRemoving);
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
