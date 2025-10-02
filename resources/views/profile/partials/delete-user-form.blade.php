<section class="space-y-8" x-data="{ showDeleteModal: false }">
    
    <!-- Warning Card -->
    <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-2xl p-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-red-100 rounded-2xl flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-red-900 mb-2">Penghapusan Akun Permanen</h3>
                <p class="text-red-700 text-sm leading-relaxed mb-4">
                    Tindakan ini tidak dapat dibatalkan. Setelah akun Anda dihapus, semua data, pengaturan, dan informasi akan dihapus secara permanen dari server kami.
                </p>
                
                <!-- Data Loss Warning -->
                <div class="bg-red-100/50 border border-red-200 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold text-red-900 text-sm mb-3 flex items-center">
                        <i class="fas fa-database mr-2"></i>
                        Data yang akan hilang permanen:
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-center text-sm text-red-800">
                            <div class="w-5 h-5 bg-red-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-times text-red-700 text-xs"></i>
                            </div>
                            Informasi profil & pengaturan
                        </div>
                        <div class="flex items-center text-sm text-red-800">
                            <div class="w-5 h-5 bg-red-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-times text-red-700 text-xs"></i>
                            </div>
                            Riwayat aktivitas & log
                        </div>
                        <div class="flex items-center text-sm text-red-800">
                            <div class="w-5 h-5 bg-red-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-times text-red-700 text-xs"></i>
                            </div>
                            Data keamanan & autentikasi
                        </div>
                        <div class="flex items-center text-sm text-red-800">
                            <div class="w-5 h-5 bg-red-200 rounded-lg flex items-center justify-center mr-2">
                                <i class="fas fa-times text-red-700 text-xs"></i>
                            </div>
                            Semua data terkait
                        </div>
                    </div>
                </div>

                <button @click="showDeleteModal = true"
                        class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-lg"
                    <i class="fas fa-trash-alt mr-2"></i>
                    Hapus Akun Saya
                </button>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div x-show="showDeleteModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm" 
         style="display: none;">
        
        <div class="flex items-center justify-center min-h-screen p-4">
            <!-- Modal Content -->
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 @click.away="showDeleteModal = false"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden"
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-5">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Konfirmasi Penghapusan Akun</h3>
                            <p class="text-red-100 text-sm">Tindakan ini tidak dapat dibalik</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Body -->
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')
                    
                    <div class="mb-6">
                        <p class="text-gray-600 text-sm leading-relaxed mb-4">
                            Apakah Anda benar-benar yakin ingin menghapus akun Anda? Tindakan ini akan menghapus semua data secara permanen dan tidak dapat dibatalkan.
                        </p>
                        
                        <!-- Password Confirmation -->
                        <div class="space-y-2">
                            <label for="password" class="flex items-center text-sm font-semibold text-gray-700">
                                <div class="w-6 h-6 bg-red-100 rounded-lg flex items-center justify-center mr-2">
                                    <i class="fas fa-key text-red-600 text-xs"></i>
                                </div>
                                Konfirmasi dengan kata sandi Anda
                            </label>
                            <div class="relative">
                                <input id="password" 
                                       name="password" 
                                       type="password" 
                                       required
                                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" 
                                       placeholder="Masukkan kata sandi untuk konfirmasi">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <i class="fas fa-shield-alt text-gray-400"></i>
                                </div>
                            </div>
                            @error('password', 'userDeletion')
                                <div class="flex items-center mt-2 text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                        <button type="button" 
                                @click="showDeleteModal = false"
                                class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200"
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>
                        
                        <button type="submit"
                                class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-xl hover:from-red-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 shadow-lg"
                            <i class="fas fa-trash-alt mr-2"></i>
                            Ya, Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
