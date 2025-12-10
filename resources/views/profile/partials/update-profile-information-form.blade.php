<section class="p-6 border border-emerald-100 rounded-2xl bg-white shadow-sm space-y-6">
    @php
        $currentAvatarPath = $user->avatar ?? null;
        if ($currentAvatarPath && !\Illuminate\Support\Str::startsWith($currentAvatarPath, ['http://', 'https://'])) {
            $originalAvatarUrl = \Illuminate\Support\Facades\Storage::url($currentAvatarPath);
        } else {
            $originalAvatarUrl = $currentAvatarPath;
        }

        $currentInitials = collect(explode(' ', $user->name))
            ->filter()
            ->map(fn ($segment) => mb_substr($segment, 0, 1))
            ->join('') ?: 'A';

        $currentAvatarUrl = $originalAvatarUrl;
        $shouldRemoveAvatar = old('remove_avatar') === '1';
        if ($shouldRemoveAvatar) {
            $currentAvatarUrl = null;
        }
    @endphp
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-sm font-semibold tracking-wide text-slate-900 uppercase">Informasi Profil</h2>
            <p class="mt-1 text-sm text-slate-500">Perbarui nama dan alamat email yang digunakan untuk masuk ke sistem.</p>
        </div>
        <span class="px-3 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-lg">Wajib</span>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Foto Profil</label>
                <div class="flex flex-col gap-4 mt-3 sm:flex-row sm:items-center">
                    <div class="relative flex items-center justify-center w-24 h-24 shrink-0" data-profile-avatar-wrapper>
                        <img data-profile-avatar-preview
                             data-current-src="{{ $currentAvatarUrl ?? '' }}"
                             data-original-src="{{ $originalAvatarUrl ?? '' }}"
                             src="{{ $currentAvatarUrl ?? '' }}"
                             alt="Preview avatar"
                             class="object-cover w-24 h-24 rounded-full border-2 border-emerald-100 shadow-lg {{ $currentAvatarUrl ? '' : 'hidden' }}">
                        <div data-profile-avatar-placeholder
                             data-initials="{{ $currentInitials }}"
                             class="flex items-center justify-center w-24 h-24 text-lg font-semibold text-emerald-600 bg-emerald-50 border-2 border-dashed border-emerald-200 rounded-full {{ $currentAvatarUrl ? 'hidden' : '' }}">
                            {{ $currentInitials }}
                        </div>
                        <span class="absolute bottom-1 right-1 inline-flex items-center justify-center w-7 h-7 text-emerald-600 bg-white rounded-full shadow-lg">
                            <i class="fas fa-camera"></i>
                        </span>
                    </div>
                    <div class="flex-1 space-y-3">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                            <label for="avatar"
                                   class="flex-1 inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl cursor-pointer hover:bg-emerald-100 transition">
                                <i class="fas fa-upload"></i>
                                <span>Pilih Foto Baru</span>
                                <input id="avatar" name="avatar" type="file" accept="image/*" class="hidden" data-profile-avatar-input>
                            </label>
                            <button type="button"
                                    data-profile-avatar-remove
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-600 border border-emerald-200 rounded-xl transition hover:bg-emerald-50 {{ $originalAvatarUrl || $currentAvatarUrl ? '' : 'opacity-50 cursor-not-allowed pointer-events-none' }}"
                                    data-has-original="{{ $originalAvatarUrl ? 'true' : 'false' }}">
                                <i class="fas {{ $shouldRemoveAvatar ? 'fa-rotate-left' : 'fa-trash' }}"></i>
                                <span data-profile-avatar-remove-label>{{ $shouldRemoveAvatar ? 'Batalkan Penghapusan' : 'Hapus Foto' }}</span>
                            </button>
                        </div>
                        <input type="hidden" name="remove_avatar" value="{{ old('remove_avatar', $shouldRemoveAvatar ? '1' : '0') }}" data-profile-avatar-remove-field>
                        <p class="text-xs leading-relaxed text-slate-500">Unggah gambar berformat JPG, PNG, atau WEBP dengan ukuran maksimal 2MB. Disarankan menggunakan foto dengan rasio persegi.</p>
                        @error('avatar')
                            <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label for="name" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Nama Lengkap <span class="text-red-500">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       class="w-full mt-2 px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400"
                       placeholder="Masukkan nama lengkap">
                @error('name')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="email" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Email <span class="text-red-500">*</span></label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                       class="w-full mt-2 px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400"
                       placeholder="nama@perusahaan.com">
                @error('email')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="p-4 mt-4 text-sm text-amber-700 border border-amber-200 rounded-xl bg-amber-50/80">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-100 rounded-lg">
                                <i class="fas fa-envelope-open"></i>
                            </span>
                            <div class="space-y-2">
                                <p class="font-semibold">Email Anda belum terverifikasi.</p>
                                <button form="send-verification"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-amber-700 transition bg-white border border-amber-200 rounded-lg hover:bg-amber-100">
                                    <i class="fas fa-paper-plane"></i>
                                    Kirim ulang tautan verifikasi
                                </button>
                                @if (session('status') === 'verification-link-sent')
                                    <p class="text-xs font-semibold text-emerald-600">Tautan verifikasi baru telah dikirim ke email Anda.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-1.5">
                    Perubahan profil tersimpan.
                </p>
            @else
                <span class="text-xs text-slate-500">Periksa kembali sebelum menyimpan.</span>
            @endif

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-linear-to-r from-emerald-500 to-emerald-600">
                <i class="fas fa-save"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</section>
