<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Al-Ruhamaa' | Inventory System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/logo/icon-white.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/logo/icon-white.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/logo/icon-white.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/logo/icon-white.png') }}">

    <!-- Web App Manifest -->
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#047857">

    <!-- SEO Meta Tags -->
    <meta name="description" content="Sistem Inventory Management Al-Ruhamaa' - Pengelolaan stok, produk, dan laporan yang efisien">
    <meta name="keywords" content="inventory, management, al-ruhamaa, sistem, stok, produk">
    <meta name="author" content="Al-Ruhamaa'">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Al-Ruhamaa' | Inventory System">
    <meta property="og:description" content="Sistem Inventory Management Al-Ruhamaa'">
    <meta property="og:image" content="{{ asset('assets/logo/icon-white.png') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Al-Ruhamaa' | Inventory System">
    <meta property="twitter:description" content="Sistem Inventory Management Al-Ruhamaa'">
    <meta property="twitter:image" content="{{ asset('assets/logo/icon-white.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Modern Sidebar Gradient */
        .sidebar-gradient {
            background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
        }

        /* Navigation Item Styles */
        .nav-item {
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.03) 100%);
            opacity: 0;
            transition: opacity 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 16px;
        }

        .nav-item:hover::before {
            opacity: 1;
        }

        .nav-item:hover {
            transform: translateX(12px) scale(1.02);
            box-shadow:
                0 10px 30px rgba(16, 185, 129, 0.2),
                0 6px 20px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .nav-item.active-nav-link {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0.08) 100%);
            transform: translateX(8px);
            box-shadow:
                0 8px 25px rgba(16, 185, 129, 0.25),
                0 4px 15px rgba(0, 0, 0, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .nav-item.active-nav-link .icon-wrapper {
            transform: scale(1.1);
        }

        /* Icon Wrapper */
        .icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .nav-item:hover .icon-wrapper {
            transform: scale(1.15) rotate(5deg);
        }

        /* Dropdown Styles */
        .dropdown-menu {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 8px;
            margin-left: 16px;
        }

        .dropdown-item {
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 12px;
            margin: 2px 4px;
            position: relative;
            overflow: hidden;
        }

        .dropdown-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.03) 100%);
            opacity: 0;
            transition: opacity 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border-radius: 12px;
        }

        .dropdown-item:hover::before {
            opacity: 1;
        }

        .dropdown-item:hover {
            transform: translateX(6px) scale(1.01);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.1);
        }

        .dropdown-item:hover i {
            transform: scale(1.1);
        }

        /* Hide Scrollbars */
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Prevent horizontal scroll */
        body {
            overflow-x: hidden;
        }

        /* User Profile Styling */
        .user-profile-card {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .user-avatar {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    @php
        $authUser = Auth::user();
        $avatarUrl = $authUser && $authUser->avatar ? asset('storage/' . $authUser->avatar) : null;
        $userInitials = 'U';
        if ($authUser && ! empty($authUser->name)) {
            $words = preg_split('/\s+/', trim($authUser->name), -1, PREG_SPLIT_NO_EMPTY);
            if ($words) {
                $userInitials = '';
                foreach (array_slice($words, 0, 2) as $word) {
                    $userInitials .= strtoupper(mb_substr($word, 0, 1));
                }
            }
        }
    @endphp
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed z-30 hidden w-64 h-screen shadow-2xl sidebar-gradient lg:block">
            <!-- Logo & Brand -->
            <div class="p-4 border-b border-emerald-400/20">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg shadow-lg bg-white/10 backdrop-blur-sm">
                        <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                            alt="Al-Ruhamaa Logo"
                            class="object-contain w-7 h-7">
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                        <p class="text-xs text-emerald-100/80">Inventory System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-4 py-4 space-y-1 overflow-y-auto hide-scrollbar h-[calc(100vh-180px)] pb-20">
                <!-- Dashboard - Semua user yang login -->
                <a href="{{ route('dashboard') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('dashboard') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fas fa-tachometer-alt"></i>
                    </div>
                    <span>Dashboard</span>
                </a>

                <!-- User Management - Hanya Admin -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('users.*') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fas fa-users"></i>
                    </div>
                    <span>Manajemen User</span>
                </a>
                @endif

                <!-- Product Management - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('products.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('products.*') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fas fa-box"></i>
                    </div>
                    <span>Produk</span>
                </a>
                @endif

                <!-- Category - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('category') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('category') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fas fa-tags"></i>
                    </div>
                    <span>Kategori Produk</span>
                </a>
                @endif

                <!-- Inventory -->
                <a href="{{ route('invent') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('invent') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fas fa-warehouse"></i>
                    </div>
                    <span>Inventory</span>
                </a>

                <!-- Supplier -->
                <a href="{{ route('suppliers.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fas fa-truck"></i>
                    </div>
                    <span>Supplier</span>
                </a>

                <!-- Reports - Manager & Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <div x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                    <button @click="reportsOpen = !reportsOpen"
                        class="nav-item flex items-center w-full py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('reports.*') ? 'active-nav-link' : '' }}">
                        <div class="mr-3 icon-wrapper">
                            <i class="text-sm fas fa-chart-line"></i>
                        </div>
                        <span class="flex-1 text-left">Laporan</span>
                        <i class="text-sm transition-transform duration-300 fas fa-chevron-down"
                           :class="{ 'rotate-180': reportsOpen }"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="reportsOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="dropdown-menu p-2 space-y-0.5 ml-3">

                        <a href="{{ route('reports.index') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.index') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-chart-pie"></i>
                            </div>
                            <span>Dashboard Laporan</span>
                        </a>

                        <a href="{{ route('reports.supplier-performance') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.supplier-performance') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-truck"></i>
                            </div>
                            <span>Performa Supplier</span>
                        </a>

                        <a href="{{ route('reports.stock-value') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.stock-value') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-dollar-sign"></i>
                            </div>
                            <span>Nilai Stok</span>
                        </a>

                        <a href="{{ route('reports.movement') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.movement') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-arrows-alt"></i>
                            </div>
                            <span>Pergerakan Stok</span>
                        </a>

                        <a href="{{ route('reports.weekly') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.weekly') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-calendar-week"></i>
                            </div>
                            <span>Laporan Mingguan</span>
                        </a>

                        <a href="{{ route('reports.monthly') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.monthly') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-calendar-alt"></i>
                            </div>
                            <span>Laporan Bulanan</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Activities - Manager & Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('activities.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('activities.*') ? 'active-nav-link' : '' }}">
                    <div class="mr-3 icon-wrapper">
                        <i class="text-sm fa-solid fa-note-sticky"></i>
                    </div>
                    <span>Aktivitas</span>
                </a>
                @endif

                <!-- Point of Sale -->
                <div x-data="{ posOpen: {{ request()->routeIs('pos') || request()->routeIs('pos.payments') ? 'true' : 'false' }} }">
                    <button @click="posOpen = !posOpen"
                        class="nav-item flex items-center w-full py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('pos') || request()->routeIs('pos.payments') ? 'active-nav-link' : '' }}">
                        <div class="mr-3 icon-wrapper">
                            <i class="text-sm fa-solid fa-cart-shopping"></i>
                        </div>
                        <span class="flex-1 text-left">Point of Sale</span>
                        <i class="text-sm transition-transform duration-300 fas fa-chevron-down"
                           :class="{ 'rotate-180': posOpen }"></i>
                    </button>

                    <div x-show="posOpen"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="dropdown-menu p-2 space-y-0.5 ml-3">

                        <a href="{{ route('pos') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('pos') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-cash-register"></i>
                            </div>
                            <span>Kasir POS</span>
                        </a>

                        <a href="{{ route('pos.payments') }}"
                           class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('pos.payments') ? 'bg-white/15' : '' }}">
                            <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                <i class="text-xs fas fa-receipt"></i>
                            </div>
                            <span>Status Pembayaran</span>
                        </a>
                    </div>
                </div>

                <!-- Staff Create Report Button -->
                @if(in_array(Auth::user()->role, ['staff']))
                <div class="px-4 mt-4">
                    <a href="#"
                       class="flex items-center justify-center gap-2 py-2.5 px-3 bg-white text-emerald-600 hover:bg-emerald-50 rounded-lg font-medium transition-all duration-200 shadow text-sm">
                        <i class="fas fa-plus"></i>
                        <span>Buat Laporan</span>
                    </a>
                </div>
            @endif
            </nav>

            <!-- User Profile Section -->
            <div class="absolute bottom-0 left-0 right-0">
                <div class="p-3 mx-4 mb-4 user-profile-card">
                    <div x-data="{ userMenuOpen: false }" class="relative">
                        <button @click="userMenuOpen = !userMenuOpen"
                                class="flex items-center justify-between w-full p-1 -m-1 space-x-3 transition-all duration-200 rounded-lg hover:bg-white/5">
                            <div class="flex items-center flex-1 min-w-0 space-x-3">
                                <div class="flex items-center justify-center w-8 h-8 overflow-hidden rounded-full user-avatar">
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                    @else
                                        <span class="text-xs font-semibold tracking-wide text-white">{{ $userInitials }}</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs capitalize text-emerald-200">{{ Auth::user()->role }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-8 h-8 transition-colors text-white/70 hover:text-white">
                                <i class="text-sm transition-transform duration-300 fas fa-chevron-up"
                                   :class="{ 'rotate-180': userMenuOpen }"></i>
                            </div>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div x-show="userMenuOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform scale-95 opacity-0"
                             x-transition:enter-end="transform scale-100 opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform scale-100 opacity-100"
                             x-transition:leave-end="transform scale-95 opacity-0"
                            class="absolute right-0 z-50 w-56 py-2 mb-4 bg-white border border-gray-100 shadow-2xl bottom-full rounded-2xl">

                            <a href="{{ route('profile.edit') }}"
                                class="flex items-center px-4 py-3 mx-2 text-sm text-gray-700 transition-colors hover:bg-emerald-50 rounded-xl">
                                <div class="flex items-center justify-center w-10 h-10 mr-3 bg-emerald-100 rounded-xl">
                                    <i class="text-sm fas fa-user-cog text-emerald-600"></i>
                                </div>
                                <span class="font-medium">Edit Profile</span>
                            </a>

                            <div class="mx-4 my-2 border-t border-gray-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-3 mx-2 text-sm text-red-600 transition-colors hover:bg-red-50 rounded-xl">
                                    <div class="flex items-center justify-center w-10 h-10 mr-3 bg-red-100 rounded-xl">
                                        <i class="text-sm text-red-600 fas fa-sign-out-alt"></i>
                                    </div>
                                    <span class="font-medium">Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Mobile Sidebar -->
        <div x-data="{ sidebarOpen: false }" class="lg:hidden">
            <!-- Mobile Header -->
            <div class="fixed top-0 left-0 right-0 z-40 bg-white shadow-lg lg:hidden">
                <div class="flex items-center justify-between px-4 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 shadow-lg bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl">
                            <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                                alt="Al-Ruhamaa Logo"
                                class="object-contain w-6 h-6">
                        </div>
                        <div>
                            <h1 class="font-bold text-gray-900">Al-Ruhamaa'</h1>
                            <p class="text-xs text-gray-500">Inventory System</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 text-gray-600 transition-all duration-200 rounded-lg hover:text-gray-900 hover:bg-gray-100">
                        <i class="text-xl fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click.away="sidebarOpen = false"
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-black bg-opacity-50">

                <aside x-show="sidebarOpen"
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="w-64 h-full shadow-2xl sidebar-gradient">

                    <!-- Mobile Logo & Brand -->
                    <div class="p-4 border-b border-emerald-400/20">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 rounded-lg shadow-lg bg-white/10 backdrop-blur-sm">
                                <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                                    alt="Al-Ruhamaa Logo"
                                    class="object-contain w-7 h-7">
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                                <p class="text-xs text-emerald-100/80">Inventory System</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Navigation -->
                    <nav class="px-4 py-4 space-y-1 overflow-y-auto hide-scrollbar h-[calc(100vh-180px)] pb-20">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('dashboard') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fas fa-tachometer-alt"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>

                        <!-- User Management - Admin only -->
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('users.*') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fas fa-users"></i>
                            </div>
                            <span>Manajemen User</span>
                        </a>
                        @endif

                        <!-- Product Management -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('products.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('products.*') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fas fa-box"></i>
                            </div>
                            <span>Produk</span>
                        </a>
                        @endif

                        <!-- Category -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('category') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('category') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fas fa-tags"></i>
                            </div>
                            <span>Kategori & satuan</span>
                        </a>
                        @endif

                        <!-- Inventory -->
                        <a href="{{ route('invent') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('invent') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fas fa-warehouse"></i>
                            </div>
                            <span>Inventory</span>
                        </a>

                        <!-- Supplier -->
                        <a href="{{ route('suppliers.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fas fa-truck"></i>
                            </div>
                            <span>Supplier</span>
                        </a>

                        <!-- Reports - Manager & Admin -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <div x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                            <button @click="reportsOpen = !reportsOpen"
                                class="nav-item flex items-center w-full py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('reports.*') ? 'active-nav-link' : '' }}">
                                <div class="mr-3 icon-wrapper">
                                    <i class="text-sm fas fa-chart-line"></i>
                                </div>
                                <span class="flex-1 text-left">Laporan</span>
                                <i class="text-sm transition-transform duration-300 fas fa-chevron-down"
                                   :class="{ 'rotate-180': reportsOpen }"></i>
                            </button>

                            <div x-show="reportsOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="dropdown-menu p-2 space-y-0.5 ml-3">

                                <a href="{{ route('reports.index') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.index') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-chart-pie"></i>
                                    </div>
                                    <span>Dashboard Laporan</span>
                                </a>

                                <a href="{{ route('reports.supplier-performance') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.supplier-performance') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-truck"></i>
                                    </div>
                                    <span>Performa Supplier</span>
                                </a>

                                <a href="{{ route('reports.stock-value') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.stock-value') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-dollar-sign"></i>
                                    </div>
                                    <span>Nilai Stok</span>
                                </a>

                                <a href="{{ route('reports.movement') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.movement') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-arrows-alt"></i>
                                    </div>
                                    <span>Pergerakan Stok</span>
                                </a>

                                <a href="{{ route('reports.weekly') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.weekly') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-calendar-week"></i>
                                    </div>
                                    <span>Laporan Mingguan</span>
                                </a>

                                <a href="{{ route('reports.monthly') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('reports.monthly') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-calendar-alt"></i>
                                    </div>
                                    <span>Laporan Bulanan</span>
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Activities -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('activities.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('activities.*') ? 'active-nav-link' : '' }}">
                            <div class="mr-3 icon-wrapper">
                                <i class="text-sm fa-solid fa-note-sticky"></i>
                            </div>
                            <span>Aktivitas</span>
                        </a>
                        @endif

                        <!-- Point of Sale -->
                        <div x-data="{ posOpenMobile: {{ request()->routeIs('pos') || request()->routeIs('pos.payments') ? 'true' : 'false' }} }">
                            <button @click="posOpenMobile = !posOpenMobile"
                                class="nav-item flex items-center w-full py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('pos') || request()->routeIs('pos.payments') ? 'active-nav-link' : '' }}">
                                <div class="mr-3 icon-wrapper">
                                    <i class="text-sm fa-solid fa-cart-shopping"></i>
                                </div>
                                <span class="flex-1 text-left">Point of Sale</span>
                                <i class="text-sm transition-transform duration-300 fas fa-chevron-down"
                                   :class="{ 'rotate-180': posOpenMobile }"></i>
                            </button>

                            <div x-show="posOpenMobile"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                                 x-transition:enter-end="opacity-100 transform translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform translate-y-0"
                                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                                 class="dropdown-menu p-2 space-y-0.5 ml-3">

                                <a href="{{ route('pos') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('pos') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-cash-register"></i>
                                    </div>
                                    <span>Kasir POS</span>
                                </a>

                                <a href="{{ route('pos.payments') }}" @click="sidebarOpen = false"
                                   class="dropdown-item flex items-center py-2 px-3 text-emerald-100/90 hover:text-white text-xs {{ request()->routeIs('pos.payments') ? 'bg-white/15' : '' }}">
                                    <div class="flex items-center justify-center w-5 h-5 mr-2 rounded bg-white/10">
                                        <i class="text-xs fas fa-receipt"></i>
                                    </div>
                                    <span>Status Pembayaran</span>
                                </a>
                            </div>
                        </div>
                    </nav>

                    <div class="px-4 pb-6 mt-6">
                        <div class="p-3 user-profile-card">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 overflow-hidden rounded-full user-avatar">
                                    @if($avatarUrl)
                                        <img src="{{ $avatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                    @else
                                        <span class="text-sm font-semibold tracking-wide text-white">{{ $userInitials }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-white">{{ $authUser->name }}</p>
                                    <p class="text-xs capitalize text-emerald-200/90">{{ $authUser->role }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 mt-3">
                                <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false"
                                   class="flex items-center justify-center flex-1 gap-2 py-2 text-xs font-semibold text-white transition bg-white/10 rounded-xl hover:bg-white/20">
                                    <i class="text-xs fas fa-user-cog"></i>
                                    <span>Profile</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center justify-center w-full gap-2 py-2 text-xs font-semibold text-red-100 transition bg-red-500/20 rounded-xl hover:bg-red-500/30">
                                        <i class="text-xs fas fa-sign-out-alt"></i>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-64">
            <div class="h-16 lg:hidden"></div>

            @isset($header)
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Page Content -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- AlpineJS -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @stack('scripts')
</body>

</html>
