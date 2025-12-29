<section class="p-4 sm:p-6 border border-emerald-100 rounded-xl sm:rounded-2xl bg-white shadow-sm space-y-4 sm:space-y-6">
    <header class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
        <div>
            <h2 class="text-xs sm:text-sm font-semibold tracking-wide text-slate-900 uppercase">Keamanan Password</h2>
            <p class="mt-1 text-xs sm:text-sm text-slate-500">Ganti password secara berkala untuk menjaga keamanan akun.</p>
        </div>
        <span class="px-2 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-md sm:rounded-lg w-fit">Opsional</span>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="update_password_current_password" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Password Saat Ini <span class="text-red-500">*</span></label>
                <div class="relative mt-2" x-data="{ show: false }">
                    <input id="update_password_current_password" name="current_password" :type="show ? 'text' : 'password'" autocomplete="current-password"
                           placeholder="Masukkan password sekarang"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                    <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 transition hover:text-emerald-600"
                            :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                            :aria-pressed="show"
                            @click.prevent="show = !show">
                        <svg x-show="!show" x-cloak aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        </svg>
                        <svg x-show="show" x-cloak aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.18 6.18A18.5 18.5 0 0 0 2 12s3 7 10 7a9.8 9.8 0 0 0 2.12-.22" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 4.12A10 10 0 0 1 12 4c7 0 10 8 10 8a18.7 18.7 0 0 1-1.67 2.68" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a3 3 0 0 0 4.24 4.24" />
                        </svg>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Password Baru <span class="text-red-500">*</span></label>
                <div class="relative mt-2" x-data="{ show: false }">
                    <input id="update_password_password" name="password" :type="show ? 'text' : 'password'" autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                    <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 transition hover:text-emerald-600"
                            :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                            :aria-pressed="show"
                            @click.prevent="show = !show">
                        <svg x-show="!show" x-cloak aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        </svg>
                        <svg x-show="show" x-cloak aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.18 6.18A18.5 18.5 0 0 0 2 12s3 7 10 7a9.8 9.8 0 0 0 2.12-.22" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 4.12A10 10 0 0 1 12 4c7 0 10 8 10 8a18.7 18.7 0 0 1-1.67 2.68" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a3 3 0 0 0 4.24 4.24" />
                        </svg>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password_confirmation" class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Konfirmasi Password <span class="text-red-500">*</span></label>
                <div class="relative mt-2" x-data="{ show: false }">
                    <input id="update_password_password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" autocomplete="new-password"
                           placeholder="Ulangi password baru"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border-2 border-emerald-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 placeholder:text-slate-400">
                    <button type="button"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-emerald-400 transition hover:text-emerald-600"
                            :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                            :aria-pressed="show"
                            @click.prevent="show = !show">
                        <svg x-show="!show" x-cloak aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                        </svg>
                        <svg x-show="show" x-cloak aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.18 6.18A18.5 18.5 0 0 0 2 12s3 7 10 7a9.8 9.8 0 0 0 2.12-.22" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.88 4.12A10 10 0 0 1 12 4c7 0 10 8 10 8a18.7 18.7 0 0 1-1.67 2.68" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.58 10.58a3 3 0 0 0 4.24 4.24" />
                        </svg>
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
