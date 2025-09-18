<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                <i class="fa-solid fa-note-sticky"></i> {{ __('inventory') }}
            </h2>
            <a href="{{ route('invent') }}"
                class="px-4 py-2 font-medium text-white transition-colors bg-gray-500 rounded-lg hover:bg-gray-600">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali
            </a>

        </div>
    </x-slot>




<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="mb-4 text-xl font-bold">Aktivitas</h2>

    @if(session('success'))
        <div class="p-3 mb-4 text-green-800 bg-green-200 rounded">
            {{ session('success') }}
        </div>
    @endif
            @if ($stock->isNotEmpty())

    <form action="" method="POST" class="mb-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-3 py-2 text-white bg-red-600 rounded hover:bg-red-700">
            <i class="fa-solid fa-trash"></i> Hapus Semua
        </button>
    </form>

    <table class="w-full border border-gray-300 rounded-lg">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-3 py-2">nama produk</th>
                <th class="px-3 py-2">supplier</th>
                <th class="px-3 py-2">stok masuk</th>
                <th class="px-3 py-2">satuan</th>
                <th class="px-3 py-2">harga satuan</th>
                <th class="px-3 py-2">harga modal</th>
                <th class="px-3 py-2">tanggal</th>

            </tr>
        </thead>
        <tbody>

            @foreach ($stock as $st )
            <tr class="text-center border-t">
                <td class="px-3 py-2">{{ $st->product_name}}</td>
                <th class="px-3 py-2">{{ $st->supplier_name }}</th>
                <td class="px-3 py-2">{{ $st->stock_qty }}</td>
                <td class="px-3 py-2">{{ $st->satuan }}</td>
                <td class="px-3 py-2">{{ number_format($st->prices,0,',','.') }}</td>
                <td class="px-3 py-2">{{ number_format($st->total_price,0,',','.')}}</td>
                <td class="px-3 py-2">{{ $st->created_at}}</td>

            </tr>
            @endforeach
            @else
            <tr>
                <td colspan="5" class="py-4 text-center">belum ada stok masuk</td>
            </tr>
            @endif

        </tbody>
    </table>

    <div class="mt-4">

    </div>
</div>
</x-app-layout>
