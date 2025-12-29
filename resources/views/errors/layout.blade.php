<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - Yatim Center Al-Ruhamaa'</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/logo/icon-green.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/logo/icon-green.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800 h-screen flex items-center justify-center overflow-hidden selection:bg-emerald-500 selection:text-white">
    <div class="relative w-full max-w-lg p-6 mx-auto">
        <!-- Decorative Background Elements -->
        <div class="absolute top-0 -left-4 w-72 h-72 bg-emerald-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-teal-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-blob animation-delay-4000"></div>

        <div class="relative bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl shadow-emerald-500/10 ring-1 ring-white/50 p-8 sm:p-12 text-center">
            <div class="mb-6 flex justify-center">
                <div class="w-20 h-20 bg-emerald-50 rounded-2xl flex items-center justify-center shadow-inner ring-1 ring-emerald-100 text-emerald-600 text-3xl">
                    @yield('icon')
                </div>
            </div>
            
            <h1 class="text-5xl font-extrabold text-slate-900 tracking-tight mb-2">@yield('code')</h1>
            <h2 class="text-xl font-bold text-emerald-700 mb-4">@yield('message')</h2>
            
            <p class="text-slate-500 mb-8 leading-relaxed text-sm sm:text-base">
                @yield('description')
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ url()->previous() }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-emerald-700 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40 hover:scale-[1.02] transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                    <i class="fas fa-home mr-2"></i>
                    Dashboard
                </a>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                Yatim Center Al-Ruhamaa'
            </p>
        </div>
    </div>
</body>
</html>