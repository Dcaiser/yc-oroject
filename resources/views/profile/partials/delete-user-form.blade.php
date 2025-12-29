<section class="p-4 sm:p-6 border border-red-100 rounded-xl sm:rounded-2xl bg-red-50/70 shadow-sm space-y-4 sm:space-y-6">
    <header class="flex items-start gap-2 sm:gap-3">
        <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 text-red-600 bg-white border border-red-100 rounded-lg sm:rounded-xl shrink-0">
            <i class="fas fa-exclamation-triangle text-sm"></i>
        </span>
        <div>
            <h2 class="text-xs sm:text-sm font-semibold tracking-wide text-red-700 uppercase">Hapus Akun</h2>
            <p class="mt-1 text-xs sm:text-sm text-red-600/90">
                Setelah akun dihapus, seluruh data dan histori akan hilang permanen. Pastikan Anda telah mengekspor atau menyimpan data penting terlebih dahulu.
            </p>
        </div>
    </header>

    <button type="button"
            class="inline-flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold text-white rounded-lg sm:rounded-xl shadow transition hover:scale-[1.02] bg-gradient-to-r from-red-500 to-rose-600"
            x-data="{}"
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        <i class="fas fa-trash"></i>
        <span class="hidden sm:inline">Hapus Akun Secara Permanen</span>
        <span class="sm:hidden">Hapus Akun</span>
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable maxWidth="md" initialFocus="#password">
        <form method="post" action="{{ route('profile.destroy') }}" class="relative overflow-hidden">
            @csrf
            @method('delete')

            <div class="px-5 sm:px-7 pt-6 pb-5 bg-white/80 backdrop-blur rounded-t-3xl border-b border-red-100/70">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 text-red-600 bg-white border border-red-100 rounded-2xl shadow-inner">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <div>
                            <h2 class="text-lg font-semibold text-red-700">Yakin ingin menghapus akun?</h2>
                            <p class="text-sm text-red-500/85 mt-1">Tindakan ini tidak dapat dibatalkan. Konfirmasi dengan memasukkan password Anda.</p>
                        </div>
                    </div>
                    <button type="button"
                            class="inline-flex items-center justify-center w-9 h-9 text-red-400 hover:text-red-600 bg-red-50/80 border border-red-100 rounded-full transition"
                            x-on:click="$dispatch('close')">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
            </div>

            <div class="px-5 sm:px-7 py-6 bg-white/90 space-y-4">
                <div>
                    <label for="password" class="text-xs font-semibold tracking-wide text-red-600 uppercase">Password</label>
                    <div class="relative mt-2" x-data="{ show: false }">
                        <input id="password" name="password" :type="show ? 'text' : 'password'" placeholder="Masukkan password Anda"
                               class="w-full px-4 py-2.5 text-sm text-red-900 bg-white border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-red-300">
                        <button type="button"
                                class="absolute inset-y-0 right-0 flex items-center pr-4 text-red-300 transition hover:text-red-500"
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
                    @error('password', 'userDeletion')
                        <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2 text-xs text-red-500/80 bg-red-50/70 border border-dashed border-red-200 rounded-xl px-3 py-2">
                    <i class="fas fa-user-shield"></i>
                    <span>Data yang sudah terhapus tidak dapat dipulihkan.</span>
                </div>
            </div>

            <div class="px-5 sm:px-7 py-4 bg-red-50/70 border-t border-red-100 rounded-b-3xl flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-white border border-red-100 rounded-xl hover:bg-red-50 transition"
                        x-on:click="$dispatch('close')">
                    <i class="fas fa-arrow-left"></i>
                    Batalkan
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white rounded-xl shadow transition bg-gradient-to-r from-red-500 to-rose-600 hover:scale-[1.02]">
                    <i class="fas fa-trash"></i>
                    Konfirmasi Hapus
                </button>
            </div>
        </form>
    </x-modal>
</section>
