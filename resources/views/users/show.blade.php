<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-user"></i>{{ __('Detail User: ') . $user->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('users.edit', $user) }}"
                   class="px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                    <i class="mr-2 fas fa-edit"></i>Edit
                </a>
                <a href="{{ route('users.index') }}"
                   class="px-4 py-2 font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                    <i class="mr-2 fas fa-arrow-left"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <x-breadcrumb :items="[
            ['title' => 'Manajemen User', 'url' => route('users.index')],
            ['title' => 'Detail: ' . $user->name]
        ]" />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- User Profile Card -->
            <div class="lg:col-span-1">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="mb-4">
                            @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                 alt="{{ $user->name }}"
                                 class="object-cover w-32 h-32 mx-auto border-4 border-gray-200 rounded-full">
                            @else
                            <div class="flex items-center justify-center w-32 h-32 mx-auto bg-gray-200 border-4 border-gray-200 rounded-full">
                                <i class="text-4xl text-gray-400 fas fa-user"></i>
                            </div>
                            @endif
                        </div>

                        <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="mb-3 text-gray-600">{{ $user->email }}</p>

                        @php
                        $roleClasses = [
                            'admin' => 'bg-red-100 text-red-800',
                            'manager' => 'bg-yellow-100 text-yellow-800',
                        ];
                        $roleClass = $roleClasses[$user->role] ?? 'bg-green-100 text-green-800';
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $roleClass }}">
                            @if($user->role === 'admin')
                                <i class="mr-1 fas fa-crown"></i>
                            @elseif($user->role === 'manager')
                                <i class="mr-1 fas fa-user-tie"></i>
                            @else
                                <i class="mr-1 fas fa-user"></i>
                            @endif
                            {{ ucfirst($user->role) }}
                        </span>

                        @if($user->id === auth()->id())
                        <div class="mt-3">
                            <span class="px-2 py-1 text-xs font-semibold text-green-600 bg-green-100 rounded">
                                <i class="mr-1 fas fa-info-circle"></i>Akun Anda
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- User Details -->
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Informasi Detail</h4>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Nama Lengkap</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Role</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role) }}</p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Tanggal Bergabung</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d F Y, H:i') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Terakhir Diupdate</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d F Y, H:i') }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Status</label>
                                    <span class="inline-flex px-2 py-1 mt-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                        <i class="mr-1 fas fa-check-circle"></i>Aktif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Aksi</h4>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('users.edit', $user) }}"
                               class="flex items-center px-4 py-2 text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                                <i class="mr-2 fas fa-edit"></i>Edit User
                            </a>

                            @if($user->id !== auth()->id())
                            <form method="POST"
                                  action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan!')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="flex items-center px-4 py-2 text-white transition-colors bg-red-600 rounded-lg hover:bg-red-700">
                                    <i class="mr-2 fas fa-trash"></i>Hapus User
                                </button>
                            </form>
                            @else
                            <span class="flex items-center px-4 py-2 text-gray-500 bg-gray-300 rounded-lg cursor-not-allowed">
                                <i class="mr-2 fas fa-lock"></i>Tidak dapat menghapus akun sendiri
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Activity Log Card (Optional) -->
                <div class="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="mb-4 text-lg font-semibold text-gray-900">Log Aktivitas Terbaru</h4>

                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="mr-3 text-green-500 fas fa-sign-in-alt"></i>
                                <span>Login terakhir: {{ $user->updated_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="mr-3 text-blue-500 fas fa-user-edit"></i>
                                <span>Profil diupdate: {{ $user->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="mr-3 text-green-500 fas fa-user-plus"></i>
                                <span>Akun dibuat: {{ $user->created_at->format('d M Y, H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
