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
                    <div class="absolute inset-0 bg-gradient-to-br from-green-900/85 via-green-800/80 to-green-900/85"></div>
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
                            Melayani<br>
                            Dengan<br>
                            Amanah
                        </h1>
                        <p class="text-sm opacity-90 max-w-xs leading-relaxed">
                            Sistem manajemen inventori yang membantu kami melayani masyarakat dengan lebih baik dan terorganisir.
                        </p>
                        
                        <!-- Features List -->
                        <div class="space-y-2 text-sm mt-4">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-white rounded-full mr-3 opacity-90"></div>
                                <span class="opacity-90">Manajemen Inventori Terintegrasi</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-white rounded-full mr-3 opacity-90"></div>
                                <span class="opacity-90">Laporan Real-time</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-white rounded-full mr-3 opacity-90"></div>
                                <span class="opacity-90">Keamanan Data Terjamin</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Quote -->
                    <div>
                        <blockquote class="text-xs italic opacity-80 border-l-2 border-white/30 pl-3">
                            "Dan barangsiapa yang menghidupkan satu jiwa, maka seakan-akan dia telah menghidupkan seluruh manusia."
                        </blockquote>
                    </div>
                </div>
            </div>
            
            <!-- Panel Kanan - Form Login -->
            <div class="w-full lg:w-1/2 p-8">
                <!-- Mobile Branding dengan Logo -->
                <div class="lg:hidden mb-6 text-center">
                    <div class="flex items-center justify-center space-x-3 mb-2">
                        <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png" 
                             alt="Al-Ruhamaa Logo" 
                             class="w-8 h-8 object-contain">
                        <h3 class="text-lg font-bold text-green-700">Al-Ruhamaa' Inventory</h3>
                    </div>
                    <p class="text-sm text-gray-600">Yatim Center Management System</p>
                </div>
                
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Selamat Datang</h2>
                    <p class="text-gray-600 text-sm">
                        Masukkan kredensial Anda untuk mengakses sistem inventori
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if (session('message'))
                    <div class="mb-4 p-3 bg-blue-100 border border-blue-300 text-blue-700 rounded-lg text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('message') }}
                        </div>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="nama@email.com">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                </svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Password dengan Show/Hide -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="w-full pl-10 pr-12 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <!-- Toggle Password Visibility -->
                            <button type="button" onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center focus:outline-none">
                                <svg id="eye-open" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-closed" class="h-5 w-5 text-gray-400 hover:text-gray-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox" 
                                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <label for="remember_me" class="ml-2 text-sm text-gray-700">Ingat saya</label>
                        </div>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700 hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Masuk
                        </button>
                    </div>
                </form>

                <!-- Footer info -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} Al-Ruhamaa' Yatim Center. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript untuk Toggle Password dan Security -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Security: Clear password field on page unload
        window.addEventListener('beforeunload', function() {
            const passwordField = document.getElementById('password');
            if (passwordField) {
                passwordField.value = '';
            }
        });

        // Security: Auto logout after inactivity (30 minutes)
        let inactivityTimer;
        const INACTIVITY_TIMEOUT = 30 * 60 * 1000; // 30 minutes

        function resetInactivityTimer() {
            clearTimeout(inactivityTimer);
            inactivityTimer = setTimeout(() => {
                if (document.querySelector('form[action*="login"]')) {
                    // Only show warning on login page
                    console.log('Session will expire due to inactivity');
                }
            }, INACTIVITY_TIMEOUT);
        }

        // Track user activity
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
            document.addEventListener(event, resetInactivityTimer);
        });

        // Initialize timer
        resetInactivityTimer();

        // Auto-focus email field
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            if (emailField && !emailField.value) {
                emailField.focus();
            }
        });
    </script>
</x-guest-layout>