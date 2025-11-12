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




<div class="p-6 bg-white shadow-lg rounded-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="flex items-center gap-2 text-xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full">
                    <i class="fas fa-warehouse"></i>
                </span>
                Riwayat Stok Masuk
            </h2>
            <p class="mt-1 text-sm text-green-600">Pantau setiap penambahan stok yang tercatat di sistem.</p>
        </div>
        @if($stock->isNotEmpty())
            <form action="" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua data stok masuk?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition rounded-lg shadow bg-gradient-to-r from-red-500 to-red-700 hover:scale-[1.02]">
                    <i class="mr-2 fa-solid fa-trash"></i>Hapus Semua
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="px-4 py-3 mb-6 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50">
            {{ session('success') }}
        </div>
    @endif

    @if ($stock->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center text-green-700">
            <div class="flex items-center justify-center w-16 h-16 mb-4 bg-green-100 rounded-full">
                <i class="text-3xl fas fa-box-open"></i>
            </div>
            <h3 class="mb-2 text-xl font-semibold">Belum ada aktivitas stok</h3>
            <p class="text-sm text-green-600">Tambahkan stok baru untuk melihat riwayatnya di sini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-green-900 border border-green-100 rounded-xl">
                <thead class="text-xs font-semibold text-green-700 uppercase bg-green-50">
                    <tr>
                        <th class="px-4 py-3">No.</th>
                        <th class="px-4 py-3">Produk</th>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Stok Masuk</th>
                        <th class="px-4 py-3">Satuan</th>
                        <th class="px-4 py-3">Harga Satuan</th>
                        <th class="px-4 py-3">Harga Modal</th>
                        <th class="px-4 py-3">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-green-50">
                    @php $rowNumber = 1; @endphp
                    @foreach($groupedStock as $dateKey => $entries)
                        @php
                            $dateLabel = $dateKey !== 'unknown'
                                ? \Carbon\Carbon::createFromFormat('Y-m-d', $dateKey)->translatedFormat('d M Y')
                                : 'Tanggal tidak diketahui';
                        @endphp
                        @if(!$loop->first)
                            <tr>
                                <td colspan="8" class="py-2">
                                    <div class="h-px bg-green-200"></div>
                                </td>
                            </tr>
                        @endif
                        <tr class="bg-green-100/60">
                            <td colspan="8" class="px-4 py-2 text-xs font-semibold uppercase text-green-700">Tanggal: {{ $dateLabel }}</td>
                        </tr>
                        @foreach($entries as $entry)
                            <tr class="align-top transition hover:bg-green-50">
                                <td class="px-4 py-3 font-semibold">{{ $rowNumber++ }}</td>
                                <td class="px-4 py-3 font-semibold text-green-900">{{ $entry->product_name ?? '-' }}</td>
                                <td class="px-4 py-3 text-green-700">{{ $entry->supplier_name ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold text-green-900">{{ number_format((int) $entry->stock_qty, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 capitalize">{{ $entry->satuan ?? '-' }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format((int) $entry->prices, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format((int) $entry->total_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ optional($entry->created_at)->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</x-app-layout>
