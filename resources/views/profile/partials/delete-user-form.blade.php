<section class="p-6 border border-red-100 rounded-2xl bg-red-50/70 shadow-sm space-y-6">
    <header class="flex items-start gap-3">
        <span class="inline-flex items-center justify-center w-10 h-10 text-red-600 bg-white border border-red-100 rounded-xl">
            <i class="fas fa-triangle-exclamation"></i>
        </span>
        <div>
            <h2 class="text-sm font-semibold tracking-wide text-red-700 uppercase">Hapus Akun</h2>
            <p class="mt-1 text-sm text-red-600/90">
                Setelah akun dihapus, seluruh data dan histori akan hilang permanen. Pastikan Anda telah mengekspor atau menyimpan data penting terlebih dahulu.
            </p>
        </div>
    </header>

    <button type="button"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl shadow transition hover:scale-[1.02] bg-gradient-to-r from-red-500 to-rose-600"
            x-data="{}"
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        <i class="fas fa-trash"></i>
        Hapus Akun Secara Permanen
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-5">
            @csrf
            @method('delete')

            <div class="flex items-start gap-3">
                <span class="inline-flex items-center justify-center w-10 h-10 text-red-600 bg-red-50 border border-red-100 rounded-xl">
                    <i class="fas fa-circle-exclamation"></i>
                </span>
                <div>
                    <h2 class="text-lg font-semibold text-red-700">Yakin ingin menghapus akun?</h2>
                    <p class="text-sm text-red-500/90 mt-1">Tindakan ini tidak dapat dibatalkan. Masukkan password Anda untuk konfirmasi.</p>
                </div>
            </div>

            <div>
                <label for="password" class="text-xs font-semibold tracking-wide text-red-600 uppercase">Password</label>
                <input id="password" name="password" type="password" placeholder="Masukkan password Anda"
                       class="w-full mt-2 px-4 py-2.5 text-sm text-red-900 bg-white border-2 border-red-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 placeholder:text-red-300">
                @error('password', 'userDeletion')
                    <p class="mt-2 text-xs font-semibold text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <button type="button" x-on:click="$dispatch('close')"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition">
                    <i class="fas fa-times"></i>
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
