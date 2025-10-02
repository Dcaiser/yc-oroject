<section x-data="{ showPasswords: false }">
    <form method="post" action="{{ route('password.update') }}" class="space-y-8">
        @csrf
        @method('put')

        <div class="space-y-6">
            <!-- Current Password -->
            <div class="space-y-2">
                <label for="update_password_current_password" class="flex items-center text-sm font-semibold text-gray-700">
                    <div class="w-6 h-6 bg-amber-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-key text-amber-600 text-xs"></i>
                    </div>
                    Kata Sandi Saat Ini
                </label>
                <div class="relative group">
                    <input id="update_password_current_password" 
                           name="current_password" 
                           :type="showPasswords ? 'text' : 'password'"
                           autocomplete="current-password"
                           class="w-full px-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition-all duration-300 pr-12" 
                           placeholder="Masukkan kata sandi saat ini">
                    <button type="button" @click="showPasswords = !showPasswords"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i :class="showPasswords ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
                @error('current_password', 'updatePassword')
                    <div class="flex items-center mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- New Password -->
            <div class="space-y-2">
                <label for="update_password_password" class="flex items-center text-sm font-semibold text-gray-700">
                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-lock text-green-600 text-xs"></i>
                    </div>
                    Kata Sandi Baru
                </label>
                <div class="relative group">
                    <input id="update_password_password" 
                           name="password" 
                           :type="showPasswords ? 'text' : 'password'"
                           autocomplete="new-password"
                           class="w-full px-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition-all duration-300 pr-12" 
                           placeholder="Masukkan kata sandi baru">
                    <button type="button" @click="showPasswords = !showPasswords"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i :class="showPasswords ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
                @error('password', 'updatePassword')
                    <div class="flex items-center mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label for="update_password_password_confirmation" class="flex items-center text-sm font-semibold text-gray-700">
                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-2">
                        <i class="fas fa-shield-check text-blue-600 text-xs"></i>
                    </div>
                    Konfirmasi Kata Sandi Baru
                </label>
                <div class="relative group">
                    <input id="update_password_password_confirmation" 
                           name="password_confirmation" 
                           :type="showPasswords ? 'text' : 'password'"
                           autocomplete="new-password"
                           class="w-full px-4 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 focus:bg-white transition-all duration-300 pr-12" 
                           placeholder="Konfirmasi kata sandi baru">
                    <button type="button" @click="showPasswords = !showPasswords"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <i :class="showPasswords ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                    </button>
                </div>
                @error('password_confirmation', 'updatePassword')
                    <div class="flex items-center mt-2 text-sm text-red-600">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>

        <!-- Password Requirements -->
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl p-6">
            <div class="flex items-start">
                <div class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                    <i class="fas fa-info-circle text-amber-600"></i>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-amber-800 mb-3">Persyaratan Kata Sandi</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-center text-sm text-amber-700">
                            <div class="w-5 h-5 bg-amber-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-check text-amber-700 text-xs"></i>
                            </div>
                            Minimal 8 karakter
                        </div>
                        <div class="flex items-center text-sm text-amber-700">
                            <div class="w-5 h-5 bg-amber-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-check text-amber-700 text-xs"></i>
                            </div>
                            Satu huruf besar
                        </div>
                        <div class="flex items-center text-sm text-amber-700">
                            <div class="w-5 h-5 bg-amber-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-check text-amber-700 text-xs"></i>
                            </div>
                            Satu huruf kecil
                        </div>
                        <div class="flex items-center text-sm text-amber-700">
                            <div class="w-5 h-5 bg-amber-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-check text-amber-700 text-xs"></i>
                            </div>
                            Satu karakter khusus
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-8 border-t border-gray-100">
            <div class="flex items-center space-x-4">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white font-semibold rounded-xl hover:from-amber-700 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Perbarui Kata Sandi
                </button>

                @if (session('status') === 'password-updated')
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
                        <span class="font-medium">Kata sandi berhasil diperbarui!</span>
                    </div>
                @endif
            </div>
        </div>
    </form>
</section>
