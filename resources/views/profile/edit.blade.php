<x-app-layout>
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600">        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-2xl mb-6">
                    <i class="fas fa-user-cog text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-4">Pengaturan Akun</h1>
                <p class="text-emerald-100 max-w-xl mx-auto">
                    Kelola informasi profil, preferensi keamanan, dan pengaturan akun Anda
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                
                <!-- Sidebar Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="text-center">
                            <!-- Avatar -->
                            <div class="relative inline-block mb-6">
                                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <span class="text-white font-bold text-2xl">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-emerald-400 rounded-xl border-3 border-white flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            </div>
                            
                            <!-- User Info -->
                            <h3 class="text-lg font-bold text-gray-900 mb-1">{{ Auth::user()->name }}</h3>
                            <p class="text-gray-600 text-sm mb-4 break-all">{{ Auth::user()->email }}</p>
                            
                            <!-- Role Badge -->
                            <div class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-emerald-100 to-teal-100 rounded-full">
                                <i class="fas fa-crown text-emerald-600 mr-2 text-xs"></i>
                                <span class="text-emerald-800 font-medium text-sm capitalize">{{ Auth::user()->role }}</span>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar text-blue-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-xs">Bergabung sejak</p>
                                        <p class="text-gray-500 text-xs">{{ Auth::user()->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                
                                @if(Auth::user()->last_login_at)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="w-6 h-6 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-clock text-green-600 text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-xs">Terakhir aktif</p>
                                        <p class="text-gray-500 text-xs">{{ Auth::user()->last_login_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                                <!-- Forms Section -->
                <div class="lg:col-span-3 space-y-8">
                    <!-- Profile Information Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-5">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">Informasi Profil</h2>
                                    <p class="text-emerald-100 text-sm mt-2">Perbarui informasi pribadi dan detail kontak Anda</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Password Security Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-5">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-shield-alt text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">Pengaturan Keamanan</h2>
                                    <p class="text-amber-100 text-sm mt-2">Jaga keamanan akun dengan kata sandi yang kuat</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Danger Zone Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-red-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-5">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center mr-3">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-white">Area Berbahaya</h2>
                                    <p class="text-red-100 text-sm mt-2">Tindakan yang tidak dapat dikembalikan dan mempengaruhi akun secara permanen</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
