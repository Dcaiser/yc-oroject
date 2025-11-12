<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Al-Ruhamaa' | Inventory System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/logo/icon-green.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/logo/icon-green.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/logo/icon-green.png') }}">
    <link rel="shortcut icon" href="{{ asset('assets/logo/icon-green.png') }}">
    
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
    <meta property="og:image" content="{{ asset('assets/logo/icon-green.png') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="Al-Ruhamaa' | Inventory System">
    <meta property="twitter:description" content="Sistem Inventory Management Al-Ruhamaa'">
    <meta property="twitter:image" content="{{ asset('assets/logo/icon-green.png') }}">

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
            transition: width 0.35s cubic-bezier(0.22, 0.61, 0.36, 1),
                        padding 0.35s cubic-bezier(0.22, 0.61, 0.36, 1);
        }

        [x-cloak] {
            display: none !important;
        }

        /* Navigation Item Styles */
        .nav-item {
            border-radius: 12px;
            transition: background 0.35s cubic-bezier(0.22, 0.61, 0.36, 1),
                        box-shadow 0.35s cubic-bezier(0.22, 0.61, 0.36, 1),
                        transform 0.35s cubic-bezier(0.22, 0.61, 0.36, 1);
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
        }

        .nav-item.active-nav-link {
            background: rgba(255, 255, 255, 0.14);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        /* Icon Wrapper */
        .icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            transition: transform 0.35s cubic-bezier(0.22, 0.61, 0.36, 1),
                        color 0.35s cubic-bezier(0.22, 0.61, 0.36, 1);
        }

        .nav-item:hover .icon-wrapper {
            transform: scale(1.04);
        }

        /* Collapsed Sidebar Helpers */
        .sidebar-collapsed .sidebar-label,
        .sidebar-collapsed .collapse-chevron,
        .sidebar-collapsed .user-meta,
        .sidebar-collapsed .user-profile-extra {
            display: none !important;
        }

        .sidebar-collapsed .icon-wrapper,
        .sidebar-collapsed .user-avatar-wrapper {
            margin-right: 0 !important;
        }

        .sidebar-collapsed .nav-item,
        .sidebar-collapsed .dropdown-item,
        .sidebar-collapsed .user-profile-trigger {
            justify-content: center;
        }

        .sidebar-collapsed .nav-item span,
        .sidebar-collapsed .dropdown-item span {
            text-align: center;
        }

        .sidebar-collapsed .nav-item {
            width: 3.5rem;
            height: 3.5rem;
            padding: 0;
            margin-left: auto;
            margin-right: auto;
            border-radius: 1.25rem;
        }

        .sidebar-collapsed .nav-item .icon-wrapper {
            width: 1.75rem;
            height: 1.75rem;
        }

        .sidebar-collapsed .nav-item:hover,
        .sidebar-collapsed .nav-item.active-nav-link {
            transform: none;
        }

        .sidebar-collapsed .user-profile-card {
            width: 3.75rem;
            height: 3.75rem;
            padding: 0.4rem;
            margin-left: auto;
            margin-right: auto;
            border-radius: 1.4rem;
        }

        .sidebar-collapsed .user-profile-trigger {
            padding: 0 !important;
            min-height: 0;
            justify-content: center;
        }

        .sidebar-collapsed .user-avatar-wrapper {
            width: 2.75rem !important;
            height: 2.75rem !important;
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
            transition: background 0.2s ease, box-shadow 0.2s ease;
            border-radius: 10px;
            margin: 2px 4px;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.12);
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

        .sidebar-dropdown {
            position: relative;
            margin-bottom: 0.75rem;
        }

        .sidebar-dropdown-panel {
            margin-top: 0.35rem;
            padding: 0.45rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
        }

        .sidebar-dropdown-link {
            display: flex;
            align-items: center;
            padding: 0.55rem 0.75rem;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.82);
            transition: background 0.2s ease, color 0.2s ease;
        }

        .sidebar-dropdown-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }

        .sidebar-dropdown-link.is-active {
            background: rgba(16, 185, 129, 0.22);
            color: #ffffff;
        }

        .sidebar-collapsed .sidebar-dropdown-panel {
            position: absolute;
            left: calc(100% + 1rem);
            top: 0;
            width: 14rem;
        }

        .collapsed-user-dropdown {
            left: calc(100% + 1rem) !important;
            right: auto !important;
            transform: none !important;
            width: 15rem;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div x-data="{ sidebarCollapsed: false }" class="flex min-h-screen">
        <!-- Sidebar -->
     <aside class="fixed z-30 hidden h-screen pt-6 shadow-2xl sidebar-gradient lg:block transition-all duration-300 ease-[cubic-bezier(0.22,0.61,0.36,1)]"
         :class="sidebarCollapsed ? 'sidebar-collapsed w-24' : 'w-64'">
            @php
                $authUser = Auth::user();
                $rawAvatarPath = $authUser->avatar ?? null;
                if ($rawAvatarPath && !\Illuminate\Support\Str::startsWith($rawAvatarPath, ['http://', 'https://'])) {
                    $sidebarAvatarUrl = \Illuminate\Support\Facades\Storage::url($rawAvatarPath);
                } else {
                    $sidebarAvatarUrl = $rawAvatarPath;
                }
                $userInitials = collect(explode(' ', $authUser->name))
                    ->filter()
                    ->map(fn ($segment) => mb_substr($segment, 0, 1))
                    ->join('');
                if (empty($userInitials)) {
                    $userInitials = 'A';
                }
            @endphp
            <!-- Logo & Brand -->
            <div class="px-4 pb-4 border-b border-emerald-400/20" :class="sidebarCollapsed ? 'px-3' : 'px-4'">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'gap-2 justify-center' : 'gap-3'">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/10 rounded-lg backdrop-blur-sm shadow-lg">
                            <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                                alt="Al-Ruhamaa Logo"
                                class="object-contain w-7 h-7">
                        </div>
                        <div class="sidebar-label">
                            <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                            <p class="text-xs text-emerald-100/80">Inventory System</p>
                        </div>
                    </div>
            <button type="button"
                class="hidden lg:flex items-center justify-center w-9 h-9 text-white/80 hover:text-white transition"
                            @click="sidebarCollapsed = !sidebarCollapsed"
                            :title="sidebarCollapsed ? 'Perluas sidebar' : 'Perkecil sidebar'">
                        <i class="text-base fas" :class="sidebarCollapsed ? 'fa-caret-right' : 'fa-caret-left'"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
          <nav class="py-4 space-y-1 overflow-y-auto hide-scrollbar transition-all duration-300 ease-[cubic-bezier(0.22,0.61,0.36,1)]"
              :class="sidebarCollapsed ? 'px-2 h-[calc(100vh-150px)] pb-16' : 'px-4 h-[calc(100vh-180px)] pb-20'">
                <!-- Dashboard - Semua user yang login -->
                <a href="{{ route('dashboard') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('dashboard') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Dashboard' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fas fa-tachometer-alt text-sm"></i>
                    </div>
                    <span class="sidebar-label">Dashboard</span>
                </a>

                <!-- Point of Sale Dropdown -->
                <div x-data="{ open: {{ request()->routeIs('pos') || request()->routeIs('pos.payments*') ? 'true' : 'false' }} }"
                     class="sidebar-dropdown">
                    <button type="button"
                        class="nav-item flex items-center w-full py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('pos') || request()->routeIs('pos.payments*') ? 'active-nav-link' : '' }}"
                        :title="sidebarCollapsed ? 'Point of Sale' : null"
                        @click="open = !open">
                        <div class="icon-wrapper mr-3">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </div>
                        <span class="sidebar-label">Point of Sale</span>
                        <span class="ml-auto collapse-chevron transition-transform" :class="open ? 'rotate-180' : ''">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </button>

                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                        class="sidebar-dropdown-panel"
                        :class="sidebarCollapsed ? 'ml-0' : 'ml-3'">
                        <a href="{{ route('pos') }}"
                            class="sidebar-dropdown-link {{ request()->routeIs('pos') ? 'is-active' : '' }}"
                            :title="sidebarCollapsed ? 'Kasir' : null">
                            <span class="sidebar-label">Kasir</span>
                        </a>
                        <a href="{{ route('pos.payments') }}"
                            class="sidebar-dropdown-link {{ request()->routeIs('pos.payments*') ? 'is-active' : '' }}"
                            :title="sidebarCollapsed ? 'Status Pembayaran' : null">
                            <span class="sidebar-label">Status Pembayaran</span>
                        </a>
                    </div>
                </div>

                <!-- Inventory -->
                <a href="{{ route('invent') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('invent') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Inventory' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fas fa-warehouse text-sm"></i>
                    </div>
                    <span class="sidebar-label">Inventory</span>
                </a>

                <!-- Product Management - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('products.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('products.*') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Produk' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fas fa-box text-sm"></i>
                    </div>
                    <span class="sidebar-label">Produk</span>
                </a>
                @endif

                <!-- Category - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <!-- Master Data Dropdown -->
                <div x-data="{ open: {{ request()->routeIs('category') || request()->routeIs('customers.*') ? 'true' : 'false' }} }"
                     class="sidebar-dropdown">
                    <button type="button"
                        class="nav-item flex items-center w-full py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('category') || request()->routeIs('customers.*') ? 'active-nav-link' : '' }}"
                        :title="sidebarCollapsed ? 'Data Referensi' : null"
                        @click="open = !open">
                        <div class="icon-wrapper mr-3">
                            <i class="fas fa-tags text-sm"></i>
                        </div>
                        <span class="sidebar-label">Data Referensi</span>
                        <span class="ml-auto collapse-chevron transition-transform" :class="open ? 'rotate-180' : ''">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </button>

                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                        class="sidebar-dropdown-panel"
                        :class="sidebarCollapsed ? 'ml-0' : 'ml-3'">
                        <a href="{{ route('category') }}"
                            class="sidebar-dropdown-link {{ request()->routeIs('category') ? 'is-active' : '' }}"
                            :title="sidebarCollapsed ? 'Kategori & Satuan' : null">
                            <span class="sidebar-label">Kategori &amp; Satuan</span>
                        </a>
                        <a href="{{ route('customers.index') }}"
                            class="sidebar-dropdown-link {{ request()->routeIs('customers.*') ? 'is-active' : '' }}"
                            :title="sidebarCollapsed ? 'Daftar Pelanggan' : null">
                            <span class="sidebar-label">Daftar Pelanggan</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Supplier -->
                <a href="{{ route('suppliers.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Supplier' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fas fa-truck text-sm"></i>
                    </div>
                    <span class="sidebar-label">Supplier</span>
                </a>

                <!-- Reports - Manager & Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('reports.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('reports.index') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Laporan' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                    <span class="sidebar-label">Laporan</span>
                </a>
                @endif

                <!-- Activities - Manager & Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('activities.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('activities.*') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Aktivitas' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fa-solid fa-note-sticky text-sm"></i>
                    </div>
                    <span class="sidebar-label">Aktivitas</span>
                </a>
                @endif

                <!-- User Management - Hanya Admin -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                    class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('users.*') ? 'active-nav-link' : '' }}"
                    :title="sidebarCollapsed ? 'Manajemen User' : null">
                    <div class="icon-wrapper mr-3">
                        <i class="fas fa-users text-sm"></i>
                    </div>
                    <span class="sidebar-label">Manajemen User</span>
                </a>
                @endif

                <!-- Staff Create Report Button -->
                @if(in_array(Auth::user()->role, ['staff']))
                <div class="mt-4 px-4">
                          <a href="#" 
                              class="flex items-center justify-center gap-2 py-2.5 px-3 bg-white text-emerald-600 hover:bg-emerald-50 rounded-lg font-medium transition-all duration-200 shadow text-sm"
                              :title="sidebarCollapsed ? 'Buat Laporan' : null">
                        <i class="fas fa-plus"></i>
                        <span class="sidebar-label">Buat Laporan</span>
                    </a>
                </div>
            @endif
            </nav>

            <!-- User Profile Section -->
            <div class="absolute bottom-0 left-0 right-0">
                <div class="mb-4 user-profile-card px-3 py-2" :class="sidebarCollapsed ? 'mx-2' : 'mx-4'">
                    <div x-data="{ userMenuOpen: false }" class="relative" @click.away="userMenuOpen = false">
            <button @click="userMenuOpen = !userMenuOpen"
                class="flex items-center w-full transition-all duration-200 hover:bg-white/10 rounded-xl px-3 py-2 group user-profile-trigger"
                :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                       <div class="flex items-center flex-1 min-w-0"
                           :class="sidebarCollapsed ? '' : 'space-x-3'">
                                <div class="relative w-11 h-11 user-avatar-wrapper">
                                    <div class="flex items-center justify-center w-full h-full overflow-hidden bg-white/15 rounded-full ring-2 ring-white/20 shadow-inner">
                                        @if ($sidebarAvatarUrl)
                                            <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                        @else
                                            <span class="text-sm font-semibold text-white">{{ $userInitials }}</span>
                                        @endif
                                    </div>
                                    <span class="absolute -bottom-0.5 -right-0.5 block w-3.5 h-3.5 bg-emerald-400 border-2 border-white rounded-full shadow-lg"></span>
                                </div>
                                <div class="flex-1 min-w-0 text-left user-profile-extra">
                                    <p class="text-sm font-semibold text-white truncate sidebar-label">{{ $authUser->name }}</p>
                                    <div class="mt-0.5 space-y-0.5 user-meta">
                                        <span class="block text-[11px] font-semibold text-emerald-200/90 uppercase tracking-[0.18em] sidebar-label">{{ strtoupper($authUser->role) }}</span>
                                        <div class="text-[11px] text-emerald-100/80 tracking-normal normal-case truncate sidebar-label">{{ $authUser->email }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center w-7 h-7 text-white/70 group-hover:text-white transition-colors collapse-chevron">
                                <i class="text-xs fas fa-chevron-up transition-transform duration-200"
                                   :class="{ 'rotate-180': userMenuOpen }"></i>
                            </div>
                        </button>

                        <!-- User Dropdown Menu -->
                    <div x-cloak x-show="userMenuOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform scale-95 opacity-0"
                             x-transition:enter-end="transform scale-100 opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform scale-100 opacity-100"
                             x-transition:leave-end="transform scale-95 opacity-0"
                        class="absolute left-1/2 transform -translate-x-1/2 bottom-full mb-3 w-60 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/40 z-[9999] overflow-hidden"
                        :class="sidebarCollapsed ? 'collapsed-user-dropdown' : ''">
                            <div class="px-4 pt-4 pb-3 border-b border-emerald-100/60 bg-gradient-to-br from-emerald-50/80 via-white to-white/80">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex items-center justify-center w-12 h-12 overflow-hidden rounded-full bg-emerald-100">
                                        @if ($sidebarAvatarUrl)
                                            <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                        @else
                                            <span class="text-base font-semibold text-emerald-700">{{ $userInitials }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-emerald-900">{{ $authUser->name }}</p>
                                        <p class="text-xs text-emerald-600/80">{{ $authUser->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="py-2 bg-white/70">
                                <a href="{{ route('profile.edit') }}"
                                   class="group flex items-center px-4 py-3 text-sm text-emerald-700 hover:bg-emerald-50/80 transition-all">
                                    <div class="w-9 h-9 flex items-center justify-center bg-emerald-100 rounded-xl mr-3 shrink-0 transition-transform group-hover:scale-105">
                                        <i class="fas fa-user-edit text-sm text-emerald-600"></i>
                                    </div>
                                    <div class="flex-1 text-left">
                                        <p class="font-semibold leading-none">Edit Profil</p>
                                        <p class="text-[11px] text-emerald-500 mt-1">Perbarui informasi akun Anda</p>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs text-emerald-400 group-hover:translate-x-1 transition-transform"></i>
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="group flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50/80 transition-all">
                                        <div class="w-9 h-9 flex items-center justify-center bg-red-100 rounded-xl mr-3 shrink-0 transition-transform group-hover:scale-105">
                                            <i class="fas fa-sign-out-alt text-sm text-red-600"></i>
                                        </div>
                                        <div class="flex-1 text-left">
                                            <p class="font-semibold leading-none">Keluar</p>
                                            <p class="text-[11px] text-red-500 mt-1">Akhiri sesi aplikasi</p>
                                        </div>
                                        <i class="fas fa-chevron-right text-xs text-red-400 group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                </form>
                            </div>
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
                        <div class="w-10 h-10 bg-gradient-to-r from-emerald-600 to-emerald-700 rounded-xl flex items-center justify-center shadow-lg">
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
                            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="text-xl fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile External Controls -->
            <button type="button"
                    class="fixed z-50 flex items-center justify-center w-12 h-12 text-white bg-emerald-600 rounded-full shadow-xl right-5 bottom-6 lg:hidden hover:bg-emerald-500 transition"
                    x-show="sidebarOpen"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-75"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-75"
                    @click="sidebarOpen = false">
                <i class="fas fa-arrow-left"></i>
            </button>

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
                            <div class="flex items-center justify-center w-10 h-10 bg-white/10 rounded-lg backdrop-blur-sm shadow-lg">
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
                    <nav class="px-4 py-4 space-y-1 overflow-y-auto hide-scrollbar h-[calc(100vh-170px)] pb-6">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('dashboard') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-tachometer-alt text-sm"></i>
                            </div>
                            <span class="sidebar-label">Dashboard</span>
                        </a>

                        <!-- Point of Sale -->
                        <a href="{{ route('pos') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('pos') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fa-solid fa-cart-shopping text-sm"></i>
                            </div>
                            <span class="sidebar-label">Point of Sale</span>
                        </a>

                        <!-- Inventory -->
                        <a href="{{ route('invent') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('invent') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-warehouse text-sm"></i>
                            </div>
                            <span class="sidebar-label">Inventory</span>
                        </a>

                        <!-- Product Management -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('products.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('products.*') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-box text-sm"></i>
                            </div>
                            <span class="sidebar-label">Produk</span>
                        </a>
                        @endif

                        <!-- Category -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('category') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('category') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-tags text-sm"></i>
                            </div>
                            <span class="sidebar-label">Kategori Produk</span>
                        </a>
                        @endif

                        <!-- Supplier -->
                        <a href="{{ route('suppliers.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-truck text-sm"></i>
                            </div>
                            <span class="sidebar-label">Supplier</span>
                        </a>

                        <!-- Reports - Manager & Admin -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('reports.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('reports.index') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span class="sidebar-label">Laporan</span>
                        </a>
                        @endif

                        <!-- Activities -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('activities.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('activities.*') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fa-solid fa-note-sticky text-sm"></i>
                            </div>
                            <span class="sidebar-label">Aktivitas</span>
                        </a>
                        @endif

                        <!-- User Management - Admin only -->
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                            class="nav-item flex items-center py-2.5 px-3 text-white text-sm font-medium {{ request()->routeIs('users.*') ? 'active-nav-link' : '' }}">
                            <div class="icon-wrapper mr-3">
                                <i class="fas fa-users text-sm"></i>
                            </div>
                            <span class="sidebar-label">Manajemen User</span>
                        </a>
                        @endif

                        <!-- Mobile User Profile Trigger -->
                        <div class="pt-6 mt-6 border-t border-white/10">
                            <div x-data="{ mobileUserMenuOpen: false }" class="relative">
                                <button @click="mobileUserMenuOpen = !mobileUserMenuOpen"
                                        class="user-profile-card w-full flex items-center justify-between transition-all duration-200 hover:bg-white/10 rounded-xl px-3 py-2 group user-profile-trigger">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="relative w-10 h-10 user-avatar-wrapper">
                                            <div class="flex items-center justify-center w-full h-full overflow-hidden bg-white/15 rounded-full ring-2 ring-white/20 shadow-inner">
                                                @if ($sidebarAvatarUrl)
                                                    <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                                @else
                                                    <span class="text-xs font-semibold text-white">{{ $userInitials }}</span>
                                                @endif
                                            </div>
                                            <span class="absolute -bottom-0.5 -right-0.5 block w-3 h-3 bg-emerald-400 border-2 border-white rounded-full shadow-lg"></span>
                                        </div>
                                        <div class="flex-1 min-w-0 text-left user-profile-extra">
                                            <p class="text-xs font-semibold text-white truncate sidebar-label">{{ $authUser->name }}</p>
                                            <div class="mt-0.5 space-y-0.5 user-meta">
                                                <span class="block text-[10px] font-semibold text-emerald-200/90 uppercase tracking-[0.18em] sidebar-label">{{ strtoupper($authUser->role) }}</span>
                                                <div class="text-[10px] text-emerald-100/80 tracking-normal normal-case truncate sidebar-label">{{ $authUser->email }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center w-6 h-6 text-white/70 group-hover:text-white transition-colors collapse-chevron">
                                        <i class="text-xs fas fa-chevron-up transition-transform duration-200"
                                           :class="{ 'rotate-180': mobileUserMenuOpen }"></i>
                                    </div>
                                </button>

                                <!-- Mobile User Dropdown Menu -->
                                <div x-cloak x-show="mobileUserMenuOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform scale-95 opacity-0"
                                     x-transition:enter-end="transform scale-100 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="transform scale-100 opacity-100"
                                     x-transition:leave-end="transform scale-95 opacity-0"
                                    class="absolute left-0 right-0 bottom-full mb-2 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/40 z-50 overflow-hidden">
                                    <div class="px-4 pt-4 pb-3 border-b border-emerald-100/60 bg-gradient-to-br from-emerald-50/80 via-white to-white/80">
                                        <div class="flex items-center gap-3">
                                            <div class="relative flex items-center justify-center w-11 h-11 overflow-hidden rounded-full bg-emerald-100">
                                                @if ($sidebarAvatarUrl)
                                                    <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                                @else
                                                    <span class="text-sm font-semibold text-emerald-700">{{ $userInitials }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-emerald-900">{{ $authUser->name }}</p>
                                                <p class="text-[11px] text-emerald-600/80">{{ $authUser->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="py-2 bg-white/70">
                                        <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false; mobileUserMenuOpen = false"
                                           class="group flex items-center px-4 py-3 text-sm text-emerald-700 hover:bg-emerald-50/80 transition-all">
                                            <div class="w-9 h-9 flex items-center justify-center bg-emerald-100 rounded-xl mr-3 shrink-0 transition-transform group-hover:scale-105">
                                                <i class="fas fa-user-edit text-sm text-emerald-600"></i>
                                            </div>
                                            <div class="flex-1 text-left">
                                                <p class="font-semibold leading-none">Edit Profil</p>
                                                <p class="text-[10px] text-emerald-500 mt-1">Ubah informasi akun</p>
                                            </div>
                                            <i class="fas fa-chevron-right text-xs text-emerald-400 group-hover:translate-x-1 transition-transform"></i>
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="group flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50/80 transition-all">
                                                <div class="w-9 h-9 flex items-center justify-center bg-red-100 rounded-xl mr-3 shrink-0 transition-transform group-hover:scale-105">
                                                    <i class="fas fa-sign-out-alt text-sm text-red-600"></i>
                                                </div>
                                                <div class="flex-1 text-left">
                                                    <p class="font-semibold leading-none">Keluar</p>
                                                    <p class="text-[10px] text-red-500 mt-1">Akhiri sesi aplikasi</p>
                                                </div>
                                                <i class="fas fa-chevron-right text-xs text-red-400 group-hover:translate-x-1 transition-transform"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </nav>
                </aside>
            </div>
        </div>

        <!-- Main Content Area -->
    <main class="flex-1 transition-all duration-300 ease-[cubic-bezier(0.22,0.61,0.36,1)]" :class="sidebarCollapsed ? 'lg:ml-24' : 'lg:ml-64'">
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
