<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full"><i class="fas fa-shopping-cart"></i></span>
                Inventory
            </h2>
            <div class="flex gap-4">
                <a href="{{ route('invent_notes') }}" class="flex items-center px-4 py-2 text-green-900 transition bg-green-200 rounded-lg shadow hover:bg-green-400">
                    <i class="fa-solid fa-boxes-stacked"></i>&nbsp;Note
                </a>
                @if(isset($products) && count($products) > 0)
                <a href="{{ route('stock.create') }}"
                    class="flex items-center px-4 py-2 font-medium text-green-900 transition bg-green-300 rounded-lg shadow hover:bg-green-500">
                    <i class="mr-2 fas fa-plus"></i>Tambah Stok
                </a>
                @endif
                @if(in_array(Auth::user()->role, ['manager', 'admin']))
                <a href="{{ route('products.create') }}"
                    class="flex items-center px-4 py-2 font-medium text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                    <i class="mr-2 fas fa-plus"></i>Tambah Produk
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    @if(isset($products) && count($products) > 0)
    <div class="mb-6 overflow-hidden bg-white shadow-lg rounded-2xl">
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
                                class="w-full py-2 pl-10 pr-4 border-2 border-green-200 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-green-400 bg-green-50">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <i class="text-green-400 fas fa-search"></i>
                            </div>
                        </div>
                        <button type="submit"
                            class="px-4 py-2 text-white transition bg-gradient-to-r from-green-500 to-green-700 rounded-r-xl hover:scale-105">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <!-- Filter Options -->
                <div class="flex items-center space-x-2">
                    <form action="{{ route('deleteallinvent') }}" method="post" onsubmit="return confirm('Yakin hapus semua data?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-4 py-2 text-white transition rounded-lg shadow bg-gradient-to-r from-red-500 to-red-700 hover:scale-105" type="submit">
                            <i class="fa-solid fa-trash"></i> Hapus Semua
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

    <div class="w-full m-3 overflow-x-auto bg-white shadow-lg rounded-2xl">
        <form action="{{route('updateAll')}}" method="POST">
            @csrf
            @method('PUT')
                <table class="w-full text-sm border border-collapse">
                    <thead>
                        <tr class="text-green-900 bg-green-100">
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Nama</th>
                            <th class="px-4 py-2 border-b-4 border-green-500">Harga Agen (Rp)</th>
                            <th class="px-4 py-2 border-b-4 border-green-400">Harga Reseller (Rp)</th>
                            <th class="px-4 py-2 border-b-4 border-green-700">Harga Pelanggan (Rp)</th>
                            <th class="px-4 py-2 border">SKU</th>
                            <th class="px-4 py-2 border">Stok</th>
                            <th class="px-4 py-2 border">Satuan</th>
                            <th class="px-4 py-2 border">Kategori</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $p)
                        <tr class="text-sm text-center border-b hover:bg-green-50">
                            <td class="px-4 py-2 border">
                                {{ $p->id }}
                                <input type="hidden" name="produk[{{ $p->id }}][id]" value="{{ $p->id }}">
                            </td>
                            <td class="px-4 py-2 border">
                                <input type="text" name="produk[{{ $p->id }}][name]" value="{{ $p->name }}" class="w-full border-none rounded bg-green-50">
                            </td>
                            <td class="px-3 py-2 border">
                                <input type="number" name="produk[{{ $p->id }}][prices][agent]"
                                    value="{{ $p->prices->where('customer_type','agent')->first()->price ?? '' }}"
                                    class="w-full border-none rounded bg-green-50">
                            </td>
                            <td class="px-3 py-2 border">
                                <input type="number" name="produk[{{ $p->id }}][prices][reseller]"
                                    value="{{ $p->prices->where('customer_type','reseller')->first()->price ?? '' }}"
                                    class="w-full border-none rounded bg-green-50">
                            </td>
                            <td class="px-3 py-2 border">
                                <input type="number" name="produk[{{ $p->id }}][prices][pelanggan]"
                                    value="{{ $p->prices->where('customer_type','pelanggan')->first()->price ?? '' }}"
                                    class="w-full border-none rounded bg-green-50">
                            </td>
                            <td class="px-2 py-2 border">
                                <input type="text" name="produk[{{ $p->id }}][sku]" value="{{ $p->sku }}" class="w-full border-none rounded bg-green-50">
                            </td>
                            <td class="px-2 py-1 border">
                                @php
                                    $unit = $p->units;
                                    $stok = $p['stock_quantity'] ?? 0;
                                    $conversion = $unit ? $unit->conversion_to_base : 1;
                                    $selectedUnitId = old("produk.{$p->id}.satuan", $p->satuan);
                                    $selectedUnit = $units->firstWhere('id', $selectedUnitId);
                                    $isDus = $selectedUnit && $selectedUnit->name == 'dus';
                                    $isPcs = $selectedUnit && $selectedUnit->name == 'pcs';
                                    if ($isDus) {
                                        $displayStok = $conversion > 0 ? floor($stok / $conversion) : $stok;
                                        $displaySatuan = 'dus';
                                    } elseif ($isPcs) {
                                        $displayStok = $stok;
                                        $displaySatuan = 'pcs';
                                    } else {
                                        $displayStok = $stok;
                                        $displaySatuan = $selectedUnit ? $selectedUnit->name : '';
                                    }
                                @endphp
                                <span class="font-semibold text-green-700">
                                    {{ $displayStok }}
                                </span>
                            </td>
                            <td class="px-4 py-2 border-none">
                                <select name="produk[{{ $p->id }}][satuan]" class="w-full border-none rounded bg-green-50">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $p->satuan == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2 border">
                                <select name="produk[{{ $p->id }}][category_id]" class="w-full rounded bg-green-50">
                                    @foreach ($category as $c)
                                    <option value="{{ $c->id }}" {{ $p->category_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            <div class="flex justify-center p-4 mt-4">
                <button type="submit" onclick="return confirm('Simpan perubahan?')" class="px-4 py-2 text-white transition rounded shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                    <i class="fa-solid fa-arrows-rotate"></i> Simpan
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="flex justify-center text-green-400 ">
        <h1>Inventori kosong</h1>
    </div>
    @endif
</x-app-layout>
