<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>YC - Wakaf Produktif Bisto</title>

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
    <meta property="og:title" content="YC - Wakaf Produktif Bisto">
    <meta property="og:description" content="Sistem Inventory Management Al-Ruhamaa'">
    <meta property="og:image" content="{{ asset('assets/logo/icon-green.png') }}">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:title" content="YC - Wakaf Produktif Bisto">
    <meta property="twitter:description" content="Sistem Inventory Management Al-Ruhamaa'">
    <meta property="twitter:image" content="{{ asset('assets/logo/icon-green.png') }}">

    <!-- Fonts and Icons are bundled locally via Vite -->

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- AlpineJS Component Definition -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('currencyField', ({ initial = 0, name }) => ({
                name,
                raw: Number(initial) || 0,
                display: '',
                init() {
                    this.display = this.format(this.raw);
                },
                selectAll(event) {
                    event.target.select();
                },
                handleInput(event) {
                    const digits = event.target.value.replace(/[^0-9]/g, '');
                    this.raw = digits ? parseInt(digits, 10) : 0;
                    this.display = this.format(this.raw);
                },
                format(value) {
                    return new Intl.NumberFormat('id-ID').format(value || 0);
                }
            }));

            Alpine.data('searchAssist', ({ initial = '', dataset = [] }) => ({
                term: initial || '',
                dataset: Array.isArray(dataset) ? dataset : [],
                get matchCount() {
                    if (!this.term) {
                        return this.dataset.length;
                    }
                    const keyword = this.term.toLowerCase();
                    return this.dataset.filter((item) => item && item.includes(keyword)).length;
                }
            }));
        });
    </script>


</head>

<body class="font-sans antialiased bg-gray-100 overflow-x-hidden">
    <div x-data="{ sidebarCollapsed: false }" class="flex min-h-screen">
        <!-- Sidebar -->
     <aside class="fixed z-30 hidden h-screen pt-6 shadow-2xl bg-linear-to-br from-emerald-700 to-emerald-950 lg:flex lg:flex-col"
         :class="sidebarCollapsed ? 'sidebar-collapsed w-24' : 'w-64'">
            @php
                $authUser = Auth::user();
                $rawAvatarPath = $authUser->avatar ?? null;
                if (!empty($rawAvatarPath) && !\Illuminate\Support\Str::startsWith($rawAvatarPath, ['http://', 'https://'])) {
                    $sidebarAvatarUrl = \Illuminate\Support\Facades\Storage::url($rawAvatarPath);
                } else {
                    $sidebarAvatarUrl = !empty($rawAvatarPath) ? $rawAvatarPath : null;
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
                            <img src="{{ asset('assets/logo/icon-white.png') }}"
                                alt="Al-Ruhamaa Logo"
                                class="object-contain w-7 h-7">
                        </div>
                        <div class="sidebar-label">
                            <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                            <p class="text-xs text-emerald-100/80">Wakaf Produktif Bisto</p>
                        </div>
                    </div>
                    <button type="button"
                            class="hidden lg:flex items-center justify-center w-9 h-9 text-white/80 hover:text-white transition-all duration-300"
                            @click="sidebarCollapsed = !sidebarCollapsed"
                            :title="sidebarCollapsed ? 'Perluas sidebar' : 'Perkecil sidebar'">
                                <i class="text-base fas transition-transform duration-300"
                                   :class="sidebarCollapsed ? 'fa-chevron-right' : 'fa-chevron-left'"></i>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
          <nav class="py-4 space-y-1 overflow-y-auto flex-1 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
              :class="sidebarCollapsed ? 'px-2 pb-6' : 'px-4 pb-8'">
                <!-- Dashboard - Semua user yang login -->
                <a href="{{ route('dashboard') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08] hover:ring-1 hover:ring-inset hover:ring-white/[0.12]' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Dashboard' : null">
                    <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300 group-hover:scale-105"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-house text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Dashboard</span>
                </a>

                <!-- Point of Sale Dropdown -->
                <div x-data="{ open: {{ request()->routeIs('pos') || request()->routeIs('pos.payments*') ? 'true' : 'false' }} }"
                     class="relative mb-3">
                    <button type="button"
                        class="flex items-center w-full text-white text-sm font-medium rounded-xl {{ request()->routeIs('pos') || request()->routeIs('pos.payments*') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                        :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                        :title="sidebarCollapsed ? 'Point of Sale' : null"
                        @click="open = !open">
                        <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300"
                             :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </div>
                        <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Point of Sale</span>
                        <span x-show="!sidebarCollapsed" x-cloak class="ml-auto transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </span>
                    </button>

                    <div x-show="open" x-transition.opacity.duration.200ms x-cloak
                        class="mt-1.5 p-2 rounded-xl bg-white/8 border border-white/12 backdrop-blur-xl"
                        :class="sidebarCollapsed ? 'absolute left-full top-0 ml-4 w-56' : 'ml-3'">
                        <a href="{{ route('pos') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('pos') ? 'bg-emerald-500/20 text-white' : 'text-white/80 hover:bg-white/8 hover:text-white' }}"
                            :title="sidebarCollapsed ? 'Kasir' : null">
                            <i class="fa-solid fa-cash-register text-xs"></i>
                            <span>Kasir</span>
                        </a>
                        <a href="{{ route('pos.payments') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('pos.payments*') ? 'bg-emerald-500/20 text-white' : 'text-white/80 hover:bg-white/8 hover:text-white' }}"
                            :title="sidebarCollapsed ? 'Status Pembayaran' : null">
                            <i class="fas fa-receipt text-xs"></i>
                            <span>Status Pembayaran</span>
                        </a>
                    </div>
                </div>

                <!-- Inventory -->
                <a href="{{ route('invent') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('invent') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Inventory' : null">
                    <div class="flex items-center justify-center w-8 h-8"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-warehouse text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Inventory</span>
                </a>

                <!-- Product Management - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('products.index') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('products.*') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Produk' : null">
                    <div class="flex items-center justify-center w-8 h-8"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-box text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Produk</span>
                </a>
                @endif

                <!-- Kategori & Satuan - Manager dan Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('category') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('category') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Kategori & Satuan' : null">
                    <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-tags text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Kategori & Satuan</span>
                </a>
                @endif

                <!-- Customer -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('customers.index') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('customers.*') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Customer' : null">
                    <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-address-book text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Customer</span>
                </a>
                @endif

                <!-- Aktivitas -->
                <a href="{{ route('activities.index') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('activities.*') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Aktivitas' : null">
                    <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-clipboard-list text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Aktivitas</span>
                </a>

                <!-- Reports - Manager & Admin -->
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('reports.index') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('reports.index') ? 'bg-white/14 ring-1 ring-inset ring-white/18' : 'hover:bg-white/8 hover:ring-1 hover:ring-inset hover:ring-white/12' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Laporan' : null">
                    <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-chart-line text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Laporan</span>
                </a>
                @endif

                <!-- User Management - Hanya Admin -->
                @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                    class="flex items-center text-white text-sm font-medium rounded-xl {{ request()->routeIs('users.*') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08] hover:ring-1 hover:ring-inset hover:ring-white/[0.12]' }}"
                    :class="sidebarCollapsed ? 'w-14 h-14 justify-center mx-auto rounded-[1.25rem]' : 'py-2.5 px-3'"
                    :title="sidebarCollapsed ? 'Manajemen User' : null">
                    <div class="flex items-center justify-center w-8 h-8 transition-transform duration-300"
                         :class="sidebarCollapsed ? '' : 'mr-3'">
                        <i class="fas fa-user-gear text-sm"></i>
                    </div>
                    <span x-show="!sidebarCollapsed" x-cloak class="transition-opacity duration-300">Manajemen User</span>
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
            <div class="mt-auto w-full">
                <div class="mb-4 rounded-xl"
                     :class="sidebarCollapsed ? 'mx-2' : 'mx-3'">
                    <div x-data="{ userMenuOpen: false }" class="relative" @click.away="userMenuOpen = false">
                        <button @click="userMenuOpen = !userMenuOpen"
                            class="flex items-center w-full transition-all duration-200 bg-white/10 hover:bg-white/15 rounded-xl border border-white/10"
                            :class="sidebarCollapsed ? 'justify-center p-2' : 'justify-between py-2 px-3'">
                            <div class="flex items-center flex-1 min-w-0"
                                 :class="sidebarCollapsed ? '' : 'gap-2.5'">
                                <!-- Avatar -->
                                <div class="relative shrink-0"
                                     :class="sidebarCollapsed ? 'w-9 h-9' : 'w-8 h-8'">
                                    <div class="flex items-center justify-center w-full h-full overflow-hidden bg-emerald-500 rounded-lg">
                                        @if ($sidebarAvatarUrl)
                                            <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                        @else
                                            <span class="text-xs font-bold text-white">{{ $userInitials }}</span>
                                        @endif
                                    </div>
                                    <span class="absolute -bottom-0.5 -right-0.5 block w-2.5 h-2.5 bg-green-400 border-2 border-emerald-800 rounded-full"></span>
                                </div>
                                <!-- User Info -->
                                <div x-show="!sidebarCollapsed" x-cloak class="flex-1 min-w-0 text-left">
                                    <p class="text-sm font-semibold text-white truncate leading-tight">{{ $authUser->name }}</p>
                                    <span class="text-[10px] font-medium text-emerald-300/90 uppercase">{{ $authUser->role }}</span>
                                </div>
                            </div>
                            <div x-show="!sidebarCollapsed" x-cloak class="flex items-center justify-center w-6 h-6 text-white/50 hover:text-white transition-colors">
                                <i class="text-xs fas fa-chevron-up transition-transform duration-200"
                                   x-bind:class="userMenuOpen && 'rotate-180'"></i>
                            </div>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div x-cloak x-show="userMenuOpen"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute bottom-full mb-2 w-56 bg-white rounded-xl shadow-xl border border-slate-200 overflow-hidden"
                             :class="sidebarCollapsed ? 'left-full ml-3 bottom-0 mb-0' : 'left-0 right-0'">
                            <!-- User Info Header -->
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-emerald-500 flex items-center justify-center">
                                        @if ($sidebarAvatarUrl)
                                            <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                        @else
                                            <span class="text-sm font-bold text-white">{{ $userInitials }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate">{{ $authUser->name }}</p>
                                        <p class="text-xs text-slate-500 truncate">{{ $authUser->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <!-- Menu Items -->
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors">
                                    <i class="fas fa-user-edit w-4 text-slate-400"></i>
                                    <span class="font-medium">Edit Profil</span>
                                </a>
                                <div class="my-1 border-t border-slate-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                            class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt w-4"></i>
                                        <span class="font-medium">Keluar</span>
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
            <div class="fixed top-0 left-0 right-0 z-40 bg-white shadow-md lg:hidden">
                <div class="flex items-center justify-between px-3 py-2">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-linear-to-r from-emerald-600 to-emerald-700 rounded-lg flex items-center justify-center shadow">
                            <img src="{{ asset('assets/logo/icon-white.png') }}"
                                alt="Al-Ruhamaa Logo"
                                class="object-contain w-5 h-5">
                        </div>
                        <div>
                            <h1 class="text-sm font-bold text-gray-900">Al-Ruhamaa'</h1>
                            <p class="text-[10px] text-gray-500">Wakaf Produktif Bisto</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = !sidebarOpen" 
                            class="p-1.5 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200">
                        <i class="text-lg fas fa-bars"></i>
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
                class="fixed inset-0 z-40 bg-transparent">
                
                <aside x-show="sidebarOpen"
                    x-transition:enter="transition ease-in-out duration-300 transform"
                    x-transition:enter-start="-translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300 transform"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="-translate-x-full"
                    class="w-72 max-w-[85vw] h-full shadow-2xl bg-linear-to-br from-emerald-700 to-emerald-950">
                    
                    <!-- Mobile Logo & Brand -->
                    <div class="p-4 border-b border-emerald-400/20">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/10 rounded-lg backdrop-blur-sm shadow-lg">
                                    <img src="{{ asset('assets/logo/icon-white.png') }}"
                                        alt="Al-Ruhamaa Logo"
                                        class="object-contain w-7 h-7">
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-white">Al-Ruhamaa'</h1>
                                <p class="text-xs text-emerald-100/80">Wakaf Produktif Bisto</p>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Navigation -->
                    <nav x-data="{ posOpen: {{ request()->routeIs('pos') || request()->routeIs('pos.payments*') ? 'true' : 'false' }} }" class="px-4 py-4 space-y-1 overflow-y-auto [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden h-[calc(100vh-170px)] pb-6">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('dashboard') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-house text-sm"></i>
                            </div>
                            <span>Dashboard</span>
                        </a>

                        <!-- Point of Sale Dropdown (mobile) -->
                        <div class="relative">
                            <button type="button"
                                class="flex items-center w-full py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('pos') || request()->routeIs('pos.payments*') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}"
                                @click="posOpen = !posOpen">
                                <div class="flex items-center justify-center w-8 h-8 mr-3">
                                    <i class="fa-solid fa-cart-shopping text-sm"></i>
                                </div>
                                <span>Point of Sale</span>
                                <span class="ml-auto transition-transform duration-200" :class="posOpen ? 'rotate-180' : ''">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </span>
                            </button>
                            <div x-show="posOpen" x-transition.opacity.duration.200ms x-cloak class="mt-1 space-y-1 pl-12 pr-2">
                                <a href="{{ route('pos') }}" @click="sidebarOpen = false"
                                    class="flex items-center gap-3 py-2 px-3 text-white text-base font-medium rounded-lg {{ request()->routeIs('pos') ? 'bg-white/[0.12] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                                    <i class="fa-solid fa-cash-register text-sm"></i>
                                    <span>Kasir</span>
                                </a>
                                <a href="{{ route('pos.payments') }}" @click="sidebarOpen = false"
                                    class="flex items-center gap-3 py-2 px-3 text-white text-base font-medium rounded-lg {{ request()->routeIs('pos.payments*') ? 'bg-white/[0.12] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                                    <i class="fas fa-receipt text-sm"></i>
                                    <span>Status Pembayaran</span>
                                </a>
                            </div>
                        </div>

                        <!-- Inventory -->
                        <a href="{{ route('invent') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('invent') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-warehouse text-sm"></i>
                            </div>
                            <span>Inventory</span>
                        </a>

                        <!-- Product Management -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('products.index') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('products.*') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-box text-sm"></i>
                            </div>
                            <span>Produk</span>
                        </a>
                        @endif

                        <!-- Kategori & Satuan -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('category') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('category') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-tags text-sm"></i>
                            </div>
                            <span>Kategori & Satuan</span>
                        </a>
                        @endif

                        <!-- Customer -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('customers.index') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('customers.*') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-address-book text-sm"></i>
                            </div>
                            <span>Customer</span>
                        </a>
                        @endif

                        <!-- Aktivitas -->
                        <a href="{{ route('activities.index') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('activities.*') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-clipboard-list text-sm"></i>
                            </div>
                            <span>Aktivitas</span>
                        </a>

                        <!-- Reports - Manager & Admin -->
                        @if(in_array(Auth::user()->role, ['manager', 'admin']))
                        <a href="{{ route('reports.index') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('reports.index') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-chart-line text-sm"></i>
                            </div>
                            <span>Laporan</span>
                        </a>
                        @endif

                        <!-- User Management - Admin only -->
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('users.index') }}" @click="sidebarOpen = false"
                            class="flex items-center py-2.5 px-3 text-white text-base font-medium rounded-xl {{ request()->routeIs('users.*') ? 'bg-white/[0.14] ring-1 ring-inset ring-white/[0.18]' : 'hover:bg-white/[0.08]' }}">
                            <div class="flex items-center justify-center w-8 h-8 mr-3">
                                <i class="fas fa-user-gear text-sm"></i>
                            </div>
                            <span>Manajemen User</span>
                        </a>
                        @endif

                        <!-- Mobile User Profile Trigger -->
                        <div class="pt-6 mt-6 border-t border-white/10">
                            <div x-data="{ mobileUserMenuOpen: false }" class="relative">
                                <button @click="mobileUserMenuOpen = !mobileUserMenuOpen"
                                        class="w-full flex items-center justify-between transition-all duration-200 bg-black/20 backdrop-blur-xl hover:bg-white/[0.08] rounded-xl px-3 py-2.5 border border-white/10 group">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <div class="relative w-9 h-9 shrink-0">
                                            <div class="flex items-center justify-center w-full h-full overflow-hidden bg-white/15 rounded-full ring-1 ring-white/10">
                                                @if ($sidebarAvatarUrl)
                                                    <img src="{{ $sidebarAvatarUrl }}" alt="{{ $authUser->name }}" class="object-cover w-full h-full">
                                                @else
                                                    <span class="text-xs font-semibold text-white">{{ $userInitials }}</span>
                                                @endif
                                            </div>
                                            <span class="absolute -bottom-0.5 -right-0.5 block w-3 h-3 bg-emerald-400 border-2 border-emerald-800 rounded-full"></span>
                                        </div>
                                        <div class="flex-1 min-w-0 text-left">
                                            <p class="text-sm font-semibold text-white truncate">{{ $authUser->name }}</p>
                                            <span class="block text-[10px] font-semibold text-emerald-200/80 uppercase tracking-[0.1em]">{{ strtoupper($authUser->role) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-center w-5 h-5 text-white/60 group-hover:text-white transition-colors">
                                        <i class="text-[10px] fas fa-chevron-up transition-transform duration-200"
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
                                    <div class="px-4 pt-4 pb-3 border-b border-emerald-100/60 bg-linear-to-br from-emerald-50/80 via-white to-white/80">
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
    <main class="flex-1 min-w-0 overflow-x-hidden" :class="sidebarCollapsed ? 'lg:ml-24' : 'lg:ml-64'">
            <div class="h-12 lg:hidden"></div>

            @isset($header)
            <header class="bg-white border-b border-gray-200 shadow-sm">
                <div class="px-4 py-4 sm:px-6">
                    {{ $header }}
                </div>
            </header>
            @endisset

            <!-- Page Content -->
            <div class="p-6 min-w-0 overflow-x-hidden">
                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('modals')
    @stack('scripts')
</body>

</html>

