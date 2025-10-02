<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Al-Ruhamaa'</h1>
                        <p class="text-base text-emerald-100/80">Inventory System</p>
                    </div>
                </div>
            </div>-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Al-Ruhamaa Inventory') }}</title>

    <link rel="icon" href="https://yatimcenter-alruhamaa.org/assets/images/logo/logo-green.png" class="bg-[#047857]">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Sidebar Styling */
        .sidebar-gradient {
            background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
        }

        .sidebar-item {
            transition: all 0.3s ease;
            border-radius: 0.5rem;
            margin: 0.25rem 0.75rem;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #10b981;
        }

        .sidebar-dropdown {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0.5rem;
            margin: 0.25rem 0.75rem;
        }

        .sidebar-dropdown-item {
            transition: all 0.2s ease;
            border-radius: 0.375rem;
            margin: 0.125rem;
        }

        .sidebar-dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-dropdown-item.active {
            background: rgba(16, 185, 129, 0.3);
            border-left: 3px solid #10b981;
        }

        .icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.375rem;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-text {
            font-weight: 500;
            font-size: 0.875rem;
        }

        .dropdown-arrow {
            transition: transform 0.3s ease;
        }

        .dropdown-arrow.rotated {
            transform: rotate(180deg);
        }

        /* Hide scrollbars */
        .sidebar-gradient {
            overflow: hidden;
        }
        
        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        /* Hide scrollbar for mobile */
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide all scrollbars globally */
        body {
            overflow-x: hidden;
        }
        
        /* Prevent horizontal scroll on main content */
        main {
            overflow-x: hidden;
        }

        /* Ensure sidebar content fits without scrolling */
        nav {
            overflow: hidden;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed z-30 hidden w-96 h-screen shadow-2xl sidebar-gradient lg:block">
            <!-- Logo & Brand -->
            <div class="p-8 border-b border-emerald-400/20">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-16 h-16 bg-white/10 rounded-xl backdrop-blur-sm">
                        <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                            alt="Al-Ruhamaa Logo"
                            class="object-contain w-12 h-12">
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Al-Ruhamaa'</h1>
                        <p class="text-base text-emerald-100/80">Inventory System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-8 py-6 space-y-2 h-[calc(100vh-220px)]">
                <!-- Dashboard - Semua user yang login -->
                <a href="{{ route('dashboard') }}"
                    class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fas fa-tachometer-alt text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Dashboard</span>
                </a>

                <!-- User Management - Hanya Admin -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                    class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fas fa-users text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Manajemen User</span>
                </a>
                @endif

                <!-- Product Management - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('products.index') }}"
                    class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fas fa-box text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Produk</span>
                </a>
                @endif

                <!-- Category - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{route('category')}}" 
                   class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('category') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fas fa-tags text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Kategori Produk</span>
                </a>
                @endif

                <!-- Inventory -->
                <a href="{{route('invent')}}" 
                   class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('invent') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fas fa-warehouse text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Inventory</span>
                </a>

                <!-- Suppliers -->
                <a href="{{ route('suppliers.index') }}" 
                   class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fas fa-truck text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Supplier</span>
                </a>

                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <!-- Reports Menu with Dropdown -->
                <div x-data="{ reportsOpen: {{ request()->routeIs('reports.*') ? 'true' : 'false' }} }">
                    <button @click="reportsOpen = !reportsOpen" 
                            class="sidebar-item flex items-center w-full py-3 px-6 text-white text-base {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <div class="icon-wrapper mr-4">
                            <i class="fas fa-chart-line text-base"></i>
                        </div>
                        <span class="sidebar-text font-medium flex-1 text-left">Laporan</span>
                        <i class="dropdown-arrow fas fa-chevron-down text-sm ml-auto transition-transform duration-300" 
                           :class="{ 'rotate-180': reportsOpen }"></i>
                    </button>
                    
                    <!-- Dropdown Submenu -->
                    <div x-show="reportsOpen" 
                         x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 transform -translate-y-2" 
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="sidebar-dropdown mt-3 py-3">
                        
                        <a href="{{ route('reports.index') }}" 
                           class="sidebar-dropdown-item flex items-center py-3 px-6 text-white/90 {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-4">
                                <i class="fas fa-chart-pie text-sm"></i>
                            </div>
                            <span class="text-base font-medium">Dashboard Laporan</span>
                        </a>
                        
                        <a href="{{ route('reports.supplier-performance') }}" 
                           class="sidebar-dropdown-item flex items-center py-3 px-6 text-white/90 {{ request()->routeIs('reports.supplier-performance') ? 'active' : '' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-4">
                                <i class="fas fa-truck text-sm"></i>
                            </div>
                            <span class="text-base font-medium">Performa Supplier</span>
                        </a>
                        
                        <a href="{{ route('reports.stock-value') }}" 
                           class="sidebar-dropdown-item flex items-center py-3 px-6 text-white/90 {{ request()->routeIs('reports.stock-value') ? 'active' : '' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-4">
                                <i class="fas fa-dollar-sign text-sm"></i>
                            </div>
                            <span class="text-base font-medium">Nilai Stok</span>
                        </a>
                        
                        <a href="{{ route('reports.movement') }}" 
                           class="sidebar-dropdown-item flex items-center py-3 px-6 text-white/90 {{ request()->routeIs('reports.movement') ? 'active' : '' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-4">
                                <i class="fas fa-arrows-alt text-sm"></i>
                            </div>
                            <span class="text-base font-medium">Pergerakan Stok</span>
                        </a>
                        
                        <a href="{{ route('reports.weekly') }}" 
                           class="sidebar-dropdown-item flex items-center py-3 px-6 text-white/90 {{ request()->routeIs('reports.weekly') ? 'active' : '' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-week text-sm"></i>
                            </div>
                            <span class="text-base font-medium">Laporan Mingguan</span>
                        </a>
                        
                        <a href="{{ route('reports.monthly') }}" 
                           class="sidebar-dropdown-item flex items-center py-3 px-6 text-white/90 {{ request()->routeIs('reports.monthly') ? 'active' : '' }}">
                            <div class="w-8 h-8 flex items-center justify-center mr-4">
                                <i class="fas fa-calendar-alt text-sm"></i>
                            </div>
                            <span class="text-base font-medium">Laporan Bulanan</span>
                        </a>
                    </div>
                </div>
                @endif
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('activities.index') }}" 
                   class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('activities.*') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fa-solid fa-note-sticky text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Aktivitas</span>
                </a>
                @endif

                <!-- POS -->
                <a href="{{ route('pos') }}" 
                   class="sidebar-item flex items-center py-3 px-6 text-white text-base {{ request()->routeIs('pos') ? 'active' : '' }}">
                    <div class="icon-wrapper mr-4">
                        <i class="fa-solid fa-cart-shopping text-base"></i>
                    </div>
                    <span class="sidebar-text font-medium">Point of Sale</span>
                </a>

                @if(in_array(Auth::user()->role, ['staff']))
                <div class="mt-6 pt-6 border-t border-white/20">
                    <a href="" class="flex items-center justify-center gap-3 py-4 px-6 text-emerald-600 bg-white hover:bg-emerald-50 rounded-xl font-semibold text-base transition-all duration-200 shadow-lg">
                        <i class="fas fa-plus"></i>
                        <span>Buat Laporan</span>
                    </a>
                </div>
                @endif
            </nav>

            <!-- User Profile Section -->
            <div class="absolute bottom-0 w-96">
                <div class="mx-8 mb-6 p-6 bg-black/20 backdrop-blur-sm rounded-xl border border-white/10">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 flex-1 min-w-0">
                            <div class="flex items-center justify-center w-14 h-14 bg-emerald-400 rounded-full shadow-lg">
                                <i class="text-white fas fa-user text-lg"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 bg-emerald-400 rounded-full"></div>
                                    <p class="text-sm text-emerald-200 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="p-3 text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-200">
                                <i class="fas fa-ellipsis-v text-sm"></i>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 bottom-full mb-3 w-56 py-3 bg-white rounded-xl shadow-xl border border-gray-100 z-50">
                                
                                <a href="{{ route('profile.edit') }}"
                                    class="flex items-center px-5 py-3 text-base text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="w-10 h-10 flex items-center justify-center bg-gray-100 rounded-lg mr-4">
                                        <i class="fas fa-user-cog text-sm text-gray-600"></i>
                                    </div>
                                    <span>Edit Profile</span>
                                </a>
                                
                                <div class="border-t border-gray-100 my-2"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full px-5 py-3 text-base text-red-600 hover:bg-red-50 transition-colors">
                                        <div class="w-10 h-10 flex items-center justify-center bg-red-100 rounded-lg mr-4">
                                            <i class="fas fa-sign-out-alt text-sm text-red-600"></i>
                                        </div>
                                        <span>Logout</span>
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
            <!-- Mobile menu button -->
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

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click.away="sidebarOpen = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden">
                <aside class="w-80 h-full shadow-2xl sidebar-gradient overflow-hidden"
                       x-transition:enter="transition ease-out duration-300"
                       x-transition:enter-start="transform -translate-x-full"
                       x-transition:enter-end="transform translate-x-0"
                       x-transition:leave="transition ease-in duration-200"
                       x-transition:leave-start="transform translate-x-0"
                       x-transition:leave-end="transform -translate-x-full">
                    
                    <div class="p-6 border-b border-emerald-400/20">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-12 h-12 bg-white/10 rounded-xl backdrop-blur-sm">
                                <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                                    alt="Al-Ruhamaa Logo"
                                    class="object-contain w-8 h-8">
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-white">Al-Ruhamaa'</h1>
                                <p class="text-sm text-emerald-100/80">Inventory System</p>
                            </div>
                        </div>
                    </div>

                    <nav class="flex-1 px-6 py-4 space-y-1 h-[calc(100vh-180px)]">
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <span class="font-medium">Dashboard</span>
                        </a>

                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="font-medium">Manajemen User</span>
                        </a>
                        @endif

                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('products.index') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('products.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fas fa-box"></i>
                            </div>
                            <span class="font-medium">Produk</span>
                        </a>
                        @endif

                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('category') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('category') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fas fa-tags"></i>
                            </div>
                            <span class="font-medium">Kategori Produk</span>
                        </a>
                        @endif

                        <a href="{{ route('invent') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('invent') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fas fa-warehouse"></i>
                            </div>
                            <span class="font-medium">Inventory</span>
                        </a>

                        <a href="{{ route('suppliers.index') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('suppliers.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fas fa-truck"></i>
                            </div>
                            <span class="font-medium">Supplier</span>
                        </a>

                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <!-- Mobile Reports Menu -->
                        <div x-data="{ mobileReportsOpen: false }" class="relative">
                            <button @click="mobileReportsOpen = !mobileReportsOpen" 
                                    class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group w-full {{ request()->routeIs('reports.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                                <div class="icon-wrapper">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <span class="font-medium flex-1 text-left">Laporan</span>
                                <div class="flex items-center justify-center w-6 h-6">
                                    <i class="text-xs transition-transform duration-300 fas fa-chevron-down" 
                                       :class="{ 'rotate-180': mobileReportsOpen }"></i>
                                </div>
                            </button>
                            
                            <div x-show="mobileReportsOpen" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="transform scale-95 opacity-0"
                                 x-transition:enter-end="transform scale-100 opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="transform scale-100 opacity-100"
                                 x-transition:leave-end="transform scale-95 opacity-0"
                                 class="mt-2 ml-4 space-y-1 bg-black/20 backdrop-blur-sm rounded-xl border border-white/10 p-2">
                                
                                <a href="{{ route('reports.index') }}" @click="sidebarOpen = false"
                                   class="sidebar-dropdown-item flex items-center py-2 px-4 text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 rounded-lg group {{ request()->routeIs('reports.index') ? 'bg-white/20 text-white' : '' }}">
                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                        <i class="fas fa-chart-pie text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">Dashboard Laporan</span>
                                </a>
                                
                                <a href="{{ route('reports.supplier-performance') }}" @click="sidebarOpen = false"
                                   class="sidebar-dropdown-item flex items-center py-2 px-4 text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 rounded-lg group {{ request()->routeIs('reports.supplier-performance') ? 'bg-white/20 text-white' : '' }}">
                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                        <i class="fas fa-truck text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">Performa Supplier</span>
                                </a>
                                
                                <a href="{{ route('reports.stock-value') }}" @click="sidebarOpen = false"
                                   class="sidebar-dropdown-item flex items-center py-2 px-4 text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 rounded-lg group {{ request()->routeIs('reports.stock-value') ? 'bg-white/20 text-white' : '' }}">
                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                        <i class="fas fa-dollar-sign text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">Nilai Stok</span>
                                </a>
                                
                                <a href="{{ route('reports.movement') }}" @click="sidebarOpen = false"
                                   class="sidebar-dropdown-item flex items-center py-2 px-4 text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 rounded-lg group {{ request()->routeIs('reports.movement') ? 'bg-white/20 text-white' : '' }}">
                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                        <i class="fas fa-arrows-alt text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">Pergerakan Stok</span>
                                </a>
                                
                                <a href="{{ route('reports.weekly') }}" @click="sidebarOpen = false"
                                   class="sidebar-dropdown-item flex items-center py-2 px-4 text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 rounded-lg group {{ request()->routeIs('reports.weekly') ? 'bg-white/20 text-white' : '' }}">
                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-week text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">Laporan Mingguan</span>
                                </a>
                                
                                <a href="{{ route('reports.monthly') }}" @click="sidebarOpen = false"
                                   class="sidebar-dropdown-item flex items-center py-2 px-4 text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 rounded-lg group {{ request()->routeIs('reports.monthly') ? 'bg-white/20 text-white' : '' }}">
                                    <div class="w-6 h-6 flex items-center justify-center mr-3">
                                        <i class="fas fa-calendar-alt text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium">Laporan Bulanan</span>
                                </a>
                            </div>
                        </div>
                        @endif

                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('activities.index') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('activities.*') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fa-solid fa-note-sticky"></i>
                            </div>
                            <span class="font-medium">Aktivitas</span>
                        </a>
                        @endif

                        <a href="{{ route('pos') }}" @click="sidebarOpen = false"
                            class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl text-emerald-100/90 hover:text-white hover:bg-white/15 transition-all duration-200 group {{ request()->routeIs('pos') ? 'bg-white/20 text-white shadow-lg' : '' }}">
                            <div class="icon-wrapper">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <span class="font-medium">Point of Sale</span>
                        </a>

                        @if(in_array(Auth::user()->role, ['staff']))
                        <div class="mt-6 pt-6 border-t border-white/20">
                            <div class="px-4">
                                <a href="" @click="sidebarOpen = false"
                                   class="flex items-center justify-center gap-2 px-4 py-3 bg-white text-emerald-600 hover:bg-emerald-50 rounded-xl font-semibold transition-all duration-200 shadow-lg">
                                    <i class="fas fa-plus"></i>
                                    <span>Buat Laporan</span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </nav>

                    <!-- Mobile User Profile Section -->
                    <div class="absolute bottom-0 w-full">
                        <div class="mx-6 mb-6 p-4 bg-black/20 backdrop-blur-sm rounded-xl border border-white/10">
                            <div x-data="{ mobileUserMenuOpen: false }" class="relative">
                                <button @click="mobileUserMenuOpen = !mobileUserMenuOpen"
                                        class="flex items-center justify-between w-full space-x-3">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex items-center justify-center w-10 h-10 bg-emerald-400 rounded-full shadow-lg">
                                            <i class="text-white fas fa-user text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                                            <p class="text-xs text-emerald-200 capitalize">{{ Auth::user()->role }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center w-8 h-8 text-white/70 hover:text-white transition-colors">
                                        <i class="text-xs fas fa-chevron-up transition-transform duration-200" 
                                           :class="{ 'rotate-180': mobileUserMenuOpen }"></i>
                                    </div>
                                </button>
                                
                                <!-- Mobile User Dropdown -->
                                <div x-show="mobileUserMenuOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform scale-95 opacity-0"
                                     x-transition:enter-end="transform scale-100 opacity-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="transform scale-100 opacity-100"
                                     x-transition:leave-end="transform scale-95 opacity-0"
                                    class="absolute left-0 right-0 bottom-full mb-2 py-2 bg-white rounded-lg shadow-xl border border-gray-100 z-50">
                                    
                                    <a href="{{ route('profile.edit') }}" @click="sidebarOpen = false"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        <div class="w-8 h-8 flex items-center justify-center bg-gray-100 rounded-lg mr-3">
                                            <i class="fas fa-user-cog text-xs text-gray-600"></i>
                                        </div>
                                        <span>Edit Profile</span>
                                    </a>
                                    
                                    <div class="border-t border-gray-100 my-1"></div>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                            <div class="w-8 h-8 flex items-center justify-center bg-red-100 rounded-lg mr-3">
                                                <i class="fas fa-sign-out-alt text-xs text-red-600"></i>
                                            </div>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 lg:ml-96">
            <div class="h-16 lg:hidden"></div>

            @isset($header)
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <div class="p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- AlpineJS -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Image uploader function -->
    <script>
        function imageUploader() {
            return {
                preview: null,
                dragging: false,

                init() {
                    this.preview = null;
                },

                updatePreview(file) {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = e => this.preview = e.target.result;
                        reader.readAsDataURL(file);
                    } else {
                        this.preview = null;
                    }
                },

                handleDrop(e) {
                    this.dragging = false;
                    const file = e.dataTransfer.files[0];
                    if (file) {
                        this.$refs.fileInput.files = e.dataTransfer.files;
                        this.updatePreview(file);
                    }
                }
            };
        }
    </script>

    @stack('scripts')
</body>

</html>
