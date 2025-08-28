<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-plus mr-2"></i>Tambah Supplier Baru
        </h2>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex items-center mb-6">
                <a href="{{ route('suppliers.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Supplier Baru</h1>
            </div>

            <div class="bg-white shadow-md rounded-lg p-6">
                <form action="{{ route('suppliers.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        

                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Supplier <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Person
                            </label>
                            <input type="text" name="contact_person" id="contact_person" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_person') border-red-500 @enderror"
                                   value="{{ old('contact_person') }}">
                            @error('contact_person')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <input type="text" name="phone" id="phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                   value="{{ old('phone') }}">
                            @error('phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" name="email" id="email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                                   value="{{ old('email') }}">
                            @error('email')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="npwp" class="block text-sm font-medium text-gray-700 mb-2">
                                NPWP
                            </label>
                            <input type="text" name="npwp" id="npwp" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('npwp') border-red-500 @enderror"
                                   value="{{ old('npwp') }}">
                            @error('npwp')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat
                            </label>
                            <textarea name="address" id="address" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('suppliers.index') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Batal
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Simpan Supplier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
