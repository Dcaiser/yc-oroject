<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="mr-2 fas fa-shopping-cart"></i>{{ __('inventory') }}
            </h2>
            <div class="flex gap-4">
            <a href="{{ route('invent_notes') }}" class="flex items-center p-2 text-green-800 bg-green-400 rounded nav-item">
                <i class="fa-solid fa-boxes-stacked"></i>&nbsp
                            note
            </a>


            @if(isset($products) && count($products) > 0)
            <a href="{{ route('stock.create') }}"
                class="flex items-center px-4 py-2 font-medium text-green-900 transition-colors bg-green-300 rounded-lg hover:bg-green-700">
                <i class="mr-2 fas fa-plus"></i>Tambah stok
            </a>
            @else

            @endif

            @if(in_array(Auth::user()->role, ['manager', 'admin']))
            <a href="{{ route('products.create') }}"
                class="flex items-center px-4 py-2 font-medium text-white transition-colors bg-blue-600 rounded-lg hover:bg-blue-700">
                <i class="mr-2 fas fa-plus"></i>Tambah Produk
            </a>
            @endif
            </div>
        </div>
    </x-slot>
        @if(isset($products) && count($products) > 0)

    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <!-- Search Input -->
                <div class="flex-1 max-w-md">
                    <form method="GET" action="{{ route('invent') }}" class="flex">
                        <div class="relative flex-1">
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Cari produk..."
                                class="w-full py-2 pl-10 pr-4 border border-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="text-gray-400 fas fa-search"></i>
                            </div>
                        </div>
                        <button type="submit"
                            class="px-4 py-2 text-white transition-colors bg-blue-600 rounded-r-lg hover:bg-blue-700">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Filter Options -->
                <div class="flex items-center space-x-2">

                        <form action="{{ route('deleteallinvent') }}" method="post" class=""  onsubmit="return confirm('Yakin hapus semua data?')">
                            @csrf
                            @method('DELETE')
                            <button class="p-2 text-white bg-red-600 rounded active:bg-red-800" type="submit">
                                <i class="fa-solid fa-trash"></i> hapus semua
                            </button>
                        </form>

                </div>
            </div>
        </div>
    </div>
@if (session('success'))
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 3000)"
        x-show="show"
        class="p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50"
    >
        {{ session('success') }}
    </div>
@endif
    <div class="w-full m-3 overflow-x-auto bg-white rounded-lg shadow">

        <form action="{{route('updateAll')}}" method="POST">
            @csrf
            @method('PUT')

            <table class="w-full text-sm border border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Nama</th>
                        <th class="px-4 py-2 border-b-4 border-red-500">Harga Agen (Rp)</th>
                        <th class="px-4 py-2 border-b-4 border-yellow-400">Harga Reseller (Rp)</th>
                        <th class="px-4 py-2 border-b-4 border-green-600">Harga Pelanggan (Rp)</th>
                        <th class="px-4 py-2 border">SKU</th>
                        <th class="px-4 py-2 border">Stok</th>
                        <th class="px-4 py-2 border">satuan</th>
                        <th class="px-4 py-2 border">Kategori</th>
                        <th class="px-4 py-2 border">aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $p)
                    <tr class="text-sm text-center border-b font-[2px] hover:bg-gray-50">
                        <td class="px-4 py-2 border">
                            {{ $p->id }}
                            <input type="hidden" name="produk[{{ $p->id }}][id]" value="{{ $p->id }}">
                        </td>
                        <td class="px-4 py-2 border">
                            <input type="text" name="produk[{{ $p->id }}][name]" value="{{ $p->name }}" class="w-full border-none">
                        </td>
                         {{-- Harga Agen --}}
        <td class="px-3 py-2 border">
            <input type="number" name="produk[{{ $p->id }}][prices][agent]"
                value="{{ $p->prices->where('customer_type','agent')->first()->price ?? '' }}"
                class="w-full border-none">
        </td>

        {{-- Harga Reseller --}}
        <td class="px-3 py-2 border">
            <input type="number" name="produk[{{ $p->id }}][prices][reseller]"
                value="{{ $p->prices->where('customer_type','reseller')->first()->price ?? '' }}"
                class="w-full border-none">
        </td>

        {{-- Harga Pelanggan --}}
        <td class="px-3 py-2 border">
            <input type="number" name="produk[{{ $p->id }}][prices][pelanggan]"
                value="{{ $p->prices->where('customer_type','pelanggan')->first()->price ?? '' }}"
                class="w-full border-none">
        </td>
                        <td class="px-4 py-2 border">
                            <input type="text" name="produk[{{ $p->id }}][sku]" value="{{ $p->sku }}" class="w-full border-none">
                        </td>
                        <td class="px-4 py-1 border">
                            <input type="number" name="produk[{{ $p->id }}][stock_quantity]" value="{{ $p->stock_quantity }}" class="w-full border-none">
                        </td>
                        <td class="px-4 py-2">
                        <input type="text" name="produk[{{ $p->id }}][satuan]" value="{{ $p->satuan}}" class="w-full border-none">
                        </td>
                        <td class="px-4 py-2 border">
                            <select name="produk[{{ $p->id }}][category_id]" class="w-full">
                                @foreach ($category as $c)
                                <option value="{{ $c->id }}" {{ $p->category_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="p-5">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-center p-4 mt-4">
                <button type="submit" onclick="return confirm('simpan perubahan?')" class="px-4 py-2 text-white bg-blue-500 rounded shadow hover:bg-blue-600">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
                    @else
                    <div class="flex justify-center text-gray-400 ">
                        <h1>inventori kosong</h1>
                    </div>
                    @endif

        </form>
    </div>




</x-app-layout>
