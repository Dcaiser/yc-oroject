<section class="p-6 border border-emerald-100 rounded-2xl bg-white shadow-sm space-y-6">
    <header class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-sm font-semibold tracking-wide text-slate-900 uppercase">Keamanan Password</h2>
            <p class="mt-1 text-sm text-slate-500">Ganti password secara berkala untuk menjaga keamanan akun.</p>
        </div>
        <span class="px-3 py-1 text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-lg">Opsional</span>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="update_password_current_password" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Password Saat Ini <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                           placeholder="Masukkan password sekarang"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                    <button type="button" data-password-toggle data-target="#update_password_current_password"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 hover:text-emerald-600">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Password Baru <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                    <button type="button" data-password-toggle data-target="#update_password_password"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 hover:text-emerald-600">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password_confirmation" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Konfirmasi Password <span class="text-red-500">*</span></label>
                <div class="relative mt-2">
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                           placeholder="Ulangi password baru"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                    <button type="button" data-password-toggle data-target="#update_password_password_confirmation"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 hover:text-emerald-600">
                        <i class="fas fa-eye-slash"></i>
                    </button>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-1.5">
                    Password berhasil diperbarui.
                </p>
            @else
                <span class="text-xs text-slate-500">Gunakan kombinasi huruf, angka, dan simbol.</span>
            @endif

            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-gradient-to-r from-emerald-500 to-emerald-600">
                <i class="fas fa-key"></i>
                Simpan Password Baru
            </button>
        </div>
    </form>
</section>
