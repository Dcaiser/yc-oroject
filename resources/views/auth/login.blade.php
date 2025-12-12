<x-guest-layout>
    <div class="bg-slate-50 h-screen flex items-center justify-center px-4 overflow-hidden">
        <div class="mx-auto flex w-full max-w-4xl flex-col gap-0 rounded-2xl bg-white shadow-xl shadow-emerald-500/10 ring-1 ring-slate-200/50 lg:flex-row lg:overflow-hidden max-h-[95vh]">
            <!-- Hero / Highlight (hidden on mobile, show on lg+) -->
            <aside class="relative hidden lg:flex w-full flex-col overflow-hidden bg-black/20 py-10 px-6 text-white sm:py-12 sm:px-7 lg:w-5/12 lg:flex-none lg:py-14 lg:px-8">
                <div class="absolute inset-0">
                    <img src="{{ asset('assets/images/login-hero.png') }}" alt="Yatim Center Al-Ruhamaa'" class="h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-emerald-900/60 mix-blend-multiply"></div>
                    <div class="absolute inset-0 bg-linear-to-b from-black/40 via-emerald-900/40 to-emerald-950/80"></div>
                </div>

                <div class="relative flex flex-col gap-5">
                    <div class="flex items-start gap-2.5">
                        <img src="{{ asset('assets/logo/icon-white.png') }}" alt="Al-Ruhamaa Logo" class="h-10 w-10 object-contain" />
                        <div class="flex flex-col gap-0">
                            <span class="text-sm font-bold uppercase tracking-wide text-white leading-tight">Yatim Center Al - Ruhamaa'</span>
                            <span class="text-xs font-medium uppercase tracking-wide text-emerald-100">Wakaf Produktif Bisto</span>
                        </div>
                    </div>
                    <div class="w-12 border-t border-emerald-200/60"></div>

                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-100/70">Platform Wakaf Produktif Bisto</p>

                    <h1 class="text-3xl font-bold leading-tight text-white sm:text-[34px] text-left">
                        Kelola penjualan, laporan keuangan, dan stok lebih mudah.
                    </h1>

                    <div class="mt-auto space-y-2 pt-6">
                        <p class="text-[13px] leading-relaxed tracking-tight text-emerald-50">
                            Pantau Stok Barang <br /> 
                            dan Laporan Transaksi dalam satu sistem.
                        </p>
                    </div>
                </div>
            </aside>

            <!-- Form Panel -->
            <section class="flex w-full flex-col justify-center bg-white p-5 pt-6 sm:p-6 sm:pt-8 lg:w-7/12 lg:px-8 lg:py-10">
                <!-- Mobile Header (show on mobile only) -->
                <div class="flex items-center gap-2.5 mb-5 lg:hidden">
                    <img src="{{ asset('assets/logo/icon-green.png') }}" alt="Al-Ruhamaa Logo" class="h-9 w-9 object-contain" />
                    <div class="flex flex-col gap-0">
                        <span class="text-sm font-bold uppercase tracking-wide text-emerald-700 leading-tight">Yatim Center Al - Ruhamaa'</span>
                        <span class="text-xs font-medium uppercase tracking-wide text-slate-500">Sistem Wakaf</span>
                    </div>
                </div>

                <div class="mb-4 space-y-1">
                    <div>
                        <h2 class="pt-2.5 text-2xl font-bold text-slate-900 sm:text-[28px] leading-tight">Welcome!</h2>
                        <p class="text-sm leading-relaxed text-slate-600 mt-1.5">Gunakan akun dari admin untuk lanjut. Jika ada kendala, silakan hubungi admin.</p>
                    </div>
                </div>

                <x-auth-session-status class="mb-3" :status="session('status')" />

                @if (session('message'))
                    <div class="mb-3 flex items-start gap-2 rounded-lg border border-sky-200 bg-sky-50 px-2.5 py-2 text-sm text-sky-700">
                        <span class="mt-0.5 inline-flex h-3.5 w-3.5 items-center justify-center rounded-full bg-sky-200 text-sky-600">
                            <i class="fas fa-info text-[9px]"></i>
                        </span>
                        <p class="text-sm">{{ session('message') }}</p>
                    </div>
                @endif

                <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-3.5">
                    @csrf

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

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 leading-tight mb-1">Kata sandi</label>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                placeholder="••••••••">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition" aria-label="Tampilkan password">
                                <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg id="eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <div class="flex flex-row items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-1.5 text-sm leading-tight text-slate-600">
                            <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-green-600 transition focus:ring-green-500">
                            <span>Ingat saya</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium leading-tight text-green-600 transition hover:text-green-700">Lupa kata sandi?</a>
                        @endif
                    </div>

                    <div>
                        <button id="login-button" type="submit" class="w-full bg-green-600 text-white py-2.5 px-4 rounded-lg font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors flex items-center justify-center">
                            <svg id="login-spinner" class="hidden w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            <span id="login-button-label">Login</span>
                        </button>
                    </div>
                </form>

                @if (!session('status'))
                <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm leading-relaxed text-gray-600">
                    <p class="font-medium text-gray-700">Belum punya akun?</p>
                    <p class="mt-0.5">Hubungi admin Yatim Center. Mereka siap bantu menyiapkan akses tanpa proses daftar mandiri.</p>
                </div>
                @endif

                <div class="mt-3 space-y-0.5 text-center text-[11px] leading-relaxed text-slate-500">
                    <p>© {{ date('Y') }} Yatim Center Al-Ruhamaa'</p>
                    <p>Perlu bantuan? Email support di <a href="mailto:yc.alruhamaa@gmail.com" class="font-semibold text-emerald-600 transition hover:text-emerald-700">yc.alruhamaa@gmail.com</a></p>
                    <p class="mt-1.5 text-slate-500">Made by Dana & Dzikri • PPLG - SMKN 1 Ciomas 2025/2026</p>
                </div>
            </section>
        </div>
    </div>

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

        window.addEventListener('beforeunload', () => {
            const passwordField = document.getElementById('password');
            if (passwordField) {
                passwordField.value = '';
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const emailField = document.getElementById('email');
            if (emailField && !emailField.value) {
                emailField.focus();
            }

            const form = document.getElementById('login-form');
            const button = document.getElementById('login-button');
            const spinner = document.getElementById('login-spinner');
            const label = document.getElementById('login-button-label');

            form?.addEventListener('submit', () => {
                button.disabled = true;
                spinner.classList.remove('hidden');
                label.textContent = 'Memproses...';
            });
        });
    </script>
</x-guest-layout>