<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
        <div class="w-full max-w-5xl flex bg-white shadow-lg rounded-lg overflow-hidden">
            
            <!-- Panel Kiri - Dengan Background Image -->
            <div class="hidden lg:flex lg:w-1/2 relative">
                <!-- Background Image -->
                <div class="absolute inset-0">
                    <img src="https://yatimcenter-alruhamaa.org/assets/images/slider/83652c817a8e2e9347e81004f7442e7b.jpg" 
                         alt="Al-Ruhamaa Background" 
                         class="w-full h-full object-cover">
                    <!-- Dark Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/85 via-blue-800/80 to-blue-900/85"></div>
                </div>

                <!-- Content -->
                <div class="relative z-20 flex flex-col justify-between p-8 text-white w-full">
                    <!-- Branding dengan Logo -->
                    <div class="flex items-center space-x-3">
                        <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png" 
                             alt="Al-Ruhamaa Logo" 
                             class="w-10 h-10 object-contain">
                        <div>
                            <h3 class="font-bold text-sm tracking-wider opacity-90">AL-RUHAMAA'</h3>
                            <p class="text-xs opacity-80">YATIM CENTER</p>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="space-y-4">
                        <h1 class="text-3xl font-bold leading-tight">
                            Pulihkan<br>
                            Akses<br>
                            Anda
                        </h1>
                        <p class="text-sm opacity-90 max-w-xs leading-relaxed">
                            Sistem akan mengirimkan link reset password ke email Anda untuk memulihkan akses ke sistem inventori.
                        </p>
                        
                        <!-- Security Features -->
                        <div class="space-y-2 text-sm mt-4">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-white rounded-full mr-3 opacity-90"></div>
                                <span class="opacity-90">Link Aman & Terenkripsi</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-white rounded-full mr-3 opacity-90"></div>
                                <span class="opacity-90">Berlaku 60 Menit</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-white rounded-full mr-3 opacity-90"></div>
                                <span class="opacity-90">Verifikasi Email Required</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Quote -->
                    <div>
                        <blockquote class="text-xs italic opacity-80 border-l-2 border-white/30 pl-3">
                            "Keamanan data adalah prioritas utama dalam melayani umat."
                        </blockquote>
                    </div>
                </div>
            </div>
            
            <!-- Panel Kanan - Form Reset Password -->
            <div class="w-full lg:w-1/2 p-8">
                <!-- Mobile Branding dengan Logo -->
                <div class="lg:hidden mb-6 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-2">
                        <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png" 
                             alt="Al-Ruhamaa Logo" 
                             class="w-8 h-8 object-contain">
                        <h3 class="text-lg font-bold text-blue-700">Al-Ruhamaa' Reset Password</h3>
                    </div>
                    <p class="text-sm text-gray-600">Pulihkan Akses Anda</p>
                </div>
                
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                    <p class="text-gray-600 text-sm">
                        Tidak masalah! Masukkan email Anda dan kami akan mengirimkan link untuk mereset password Anda.
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm flex items-start">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Form Reset Password -->
                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="nama@email.com">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Kirim Link Reset Password
                        </button>
                    </div>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-700 hover:underline flex items-center justify-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali ke halaman login
                    </a>
                </div>

                <!-- Footer info -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} Al-Ruhamaa' Yatim Center. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
