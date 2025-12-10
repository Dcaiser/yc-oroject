<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 p-4">
        <div class="w-full max-w-md">
            <div class="bg-white shadow-xl shadow-emerald-500/10 rounded-2xl ring-1 ring-slate-200/50 p-6 sm:p-8">
                <!-- Header -->
                <div class="text-center mb-6">
                    <div class="flex flex-col items-center justify-center mb-4">
                                        <img src="{{ asset('assets/logo/icon-green.png') }}" 
                                             alt="Al-Ruhamaa Logo" 
                                             class="w-12 h-12 object-contain mb-2">
                        <span class="text-sm font-bold uppercase tracking-wide text-emerald-700 leading-tight">Yatim Center Al - Ruhamaa'</span>
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-500">Sistem Wakaf</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Tidak masalah. Masukkan email Anda dan kami akan mengirimkan tautan reset password.
                    </p>
                </div>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 flex items-start gap-2 rounded-lg border border-green-200 bg-green-50 px-3 py-2.5 text-sm text-green-700">
                        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <p>{{ session('status') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 leading-tight mb-1">Email</label>
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

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full bg-green-600 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Kirim Link Reset Password
                        </button>
                    </div>

                    <!-- Back to Login -->
                    <div class="text-center">
                        <a href="{{ route('login') }}" class="text-sm font-medium text-green-600 hover:text-green-700 transition inline-flex items-center justify-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Kembali ke Login
                        </a>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-6 space-y-0.5 text-center text-[11px] leading-relaxed text-slate-500">
                    <p>© {{ date('Y') }} Yatim Center Al-Ruhamaa'</p>
                    <p>Perlu bantuan? Email support di <a href="mailto:yc.alruhamaa@gmail.com" class="font-semibold text-emerald-600 transition hover:text-emerald-700">yc.alruhamaa@gmail.com</a></p>
                    <p class="mt-1.5 text-slate-500">Made by Dana & Dzikri • PPLG - SMKN 1 Ciomas 2025/2026</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
