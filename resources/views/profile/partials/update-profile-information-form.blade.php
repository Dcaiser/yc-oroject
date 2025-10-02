<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Name Field -->
            <div class="space-y-2">
                <label for="name" class="flex items-center text-sm font-semibold text-gray-700">
                    <div class="w-6 h-6 bg-emerald-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-user text-emerald-600 text-xs"></i>
                    </div>
                    Nama Lengkap
                </label>
                <div class="relative group">
                    <input id="name" name="name" type="text" 
                           value="{{ old('name', $user->name) }}" 
                           required autofocus autocomplete="name"
                           class="w-full px-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all duration-300 group-hover:border-gray-300" 
                           placeholder="Masukkan nama lengkap Anda">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <i class="fas fa-check text-emerald-500 opacity-0 group-focus-within:opacity-100 transition-opacity"></i>
                    </div>
                </div>
                @error('name')
                    <div class="flex items-center mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email" class="flex items-center text-sm font-semibold text-gray-700">
                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-envelope text-blue-600 text-xs"></i>
                    </div>
                    Alamat Email
                </label>
                <div class="relative group">
                    <input id="email" name="email" type="email" 
                           value="{{ old('email', $user->email) }}" 
                           required autocomplete="username"
                           class="w-full px-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:bg-white transition-all duration-300 group-hover:border-gray-300" 
                           placeholder="Masukkan alamat email Anda">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                        <i class="fas fa-check text-emerald-500 opacity-0 group-focus-within:opacity-100 transition-opacity"></i>
                    </div>
                </div>
                @error('email')
                    <div class="flex items-center mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-4 p-4 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl">
                        <div class="flex items-start">
                            <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center mr-3 flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-amber-800 mb-1">Verifikasi Email Diperlukan</h4>
                                <p class="text-sm text-amber-700 mb-3">
                                    Alamat email Anda belum diverifikasi. Silakan periksa kotak masuk atau minta tautan verifikasi baru.
                                </p>
                                <button form="send-verification" 
                                        class="inline-flex items-center px-4 py-2 bg-amber-600 text-white text-sm font-medium rounded-xl hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Kirim Ulang Email Verifikasi
                                </button>
                            </div>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-3 p-3 bg-emerald-100 border border-emerald-200 rounded-xl">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle text-emerald-600 mr-2"></i>
                                    <p class="text-sm text-emerald-800 font-medium">
                                        Tautan verifikasi baru telah dikirim ke alamat email Anda.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-8 border-t border-gray-100">
            <div class="flex items-center space-x-4">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Perubahan
                </button>

                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" 
                         x-show="show" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-90"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-90"
                         x-init="setTimeout(() => show = false, 4000)"
                         class="inline-flex items-center px-4 py-3 bg-gradient-to-r from-emerald-100 to-teal-100 border border-emerald-200 text-emerald-800 rounded-2xl">
                        <div class="w-6 h-6 bg-emerald-200 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check text-emerald-700 text-sm"></i>
                        </div>
                        <span class="font-medium">Profil berhasil diperbarui!</span>
                    </div>
                @endif
            </div>
        </div>
    </form>
</section>
