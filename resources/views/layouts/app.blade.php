<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
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
        .sidebar-green {
            background: #047857;
        }

        .sidebar-green-dark {
            background: #065f46;
        }

        .nav-item:hover {
            background: #065f46;
        }

        .active-nav-link {
            background: #065f46;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed z-30 hidden w-64 h-screen shadow-xl sidebar-green lg:block">
            <!-- Logo & Brand -->
            <div class="p-6 border-b border-green-600">
                <div class="flex items-center gap-3">
                    <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                        alt="Al-Ruhamaa Logo"
                        class="object-contain w-10 h-10">
                    <div>
                        <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                        <p class="text-xs text-green-200">Inventory System</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="text-white text-sm font-medium pt-4 h-[calc(100vh-180px)] overflow-y-auto">
                <!-- Dashboard - Semua user yang login -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center text-white py-3 px-6 nav-item {{ request()->routeIs('dashboard') ? 'active-nav-link' : '' }}">
                    <i class="w-4 mr-3 fas fa-tachometer-alt"></i>
                    Dashboard
                </a>

                <!-- User Management - Hanya Admin -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                    class="flex items-center text-white py-3 px-6 nav-item {{ request()->routeIs('users.*') ? 'active-nav-link' : '' }}">
                    <i class="w-4 mr-3 fas fa-users"></i>
                    Manajemen User
                </a>
                @endif

                <!-- Product Management - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('products.index') }}"
                    class="flex items-center text-white py-3 px-6 nav-item {{ request()->routeIs('products.*') ? 'active-nav-link' : '' }}">
                    <i class="w-4 mr-3 fas fa-box"></i>
                    Produk
                </a>
                @endif

                <!-- Menu lainnya untuk semua role -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{route('category')}}" class="flex items-center px-6 py-3 text-white nav-item">
                    <i class="w-4 mr-3 fas fa-tags"></i>
                    Kategori Produk
                </a>
                @endif


                <a href="{{route('invent')}}" class="flex items-center px-6 py-3 text-white nav-item">
                    <i class="w-4 mr-3 fas fa-warehouse"></i>
                    Inventory
                </a>

                <a href="{{ route('suppliers.index') }}" class="flex items-center px-6 py-3 text-white nav-item {{ request()->routeIs('suppliers.*') ? 'active-nav-link' : '' }}">
                    <i class="w-4 mr-3 fas fa-truck"></i>
                    Supplier
                </a>

                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="#" class="flex items-center px-6 py-3 text-white nav-item">
                    <i class="w-4 mr-3 fas fa-chart-line"></i>
                    Laporan
                </a>
                @endif
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('activities.index') }}" class="flex items-center px-6 py-3 text-white nav-item">
                    <i class="fa-solid fa-note-sticky"></i>&nbsp
                    aktivitas
                </a>
                @endif
                <a href="{{ route('pos') }}" class="flex items-center px-6 py-3 text-white nav-item">
                    <i class="fa-solid fa-cart-shopping"></i>&nbsp
                    POS
                </a>

                @if(in_array(Auth::user()->role, ['staff']))
            <div class="text-sm font-medium text-center pt-7">
                <a href="" class="p-3 text-[#047857] bg-white rounded"><i class="fas fa-plus"></i>buat laporan</a>

            </div>
            @endif
            </nav>

            <!-- User Profile Section -->
            <div class="absolute bottom-0 w-64">
                <div class="p-4 border-t border-green-600 sidebar-green-dark">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-green-200 rounded-full">
                                <i class="text-sm text-green-800 fas fa-user"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-green-200 capitalize">{{ Auth::user()->role }}</p>
                            </div>
                        </div>

                        <!-- Dropdown Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-green-200 hover:text-white">
                                <i class="text-xs fas fa-chevron-up"></i>
                            </button>

                            <div x-show="open" @click.away="open = false"
                                class="absolute right-0 z-50 w-48 py-1 mb-2 bg-white rounded-md shadow-lg bottom-full">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="mr-2 fas fa-user-cog"></i>Profile
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                        <i class="mr-2 fas fa-sign-out-alt"></i>Logout
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
            <div class="fixed top-0 left-0 right-0 z-40 bg-white shadow-sm lg:hidden">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                            alt="Al-Ruhamaa Logo"
                            class="object-contain w-8 h-8">
                        <h1 class="font-bold text-gray-900">Al-Ruhamaa'</h1>
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600">
                        <i class="text-xl fas fa-bars"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" @click.away="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden">
                <aside class="w-64 h-full shadow-xl sidebar-green">
                    <div class="p-6 border-b border-green-600">
                        <div class="flex items-center gap-3">
                            <img src="https://yatimcenter-alruhamaa.org/assets/images/logo/icon-white.png"
                                alt="Al-Ruhamaa Logo"
                                class="object-contain w-10 h-10">
                            <div>
                                <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                                <p class="text-xs text-green-200">Inventory System</p>
                            </div>
                        </div>
                    </div>

                    <nav class="pt-4 text-sm font-medium text-white">
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="flex items-center px-6 py-3 text-white nav-item">
                            <i class="w-4 mr-3 fas fa-tachometer-alt"></i>Dashboard
                        </a>

                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-6 py-3 text-white nav-item">
                            <i class="w-4 mr-3 fas fa-users"></i>Manajemen User
                        </a>
                        @endif

                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('products.index') }}" @click="sidebarOpen = false"
                            class="flex items-center px-6 py-3 text-white nav-item">
                            <i class="w-4 mr-3 fas fa-box"></i>Produk
                        </a>
                        @endif
                    </nav>
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
