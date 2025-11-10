<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-2xl font-extrabold text-green-900">
                <span class="inline-flex items-center justify-center w-10 h-10 text-green-700 bg-green-100 rounded-full">
                    <i class="fas fa-receipt"></i>
                </span>
                {{ __('Status Pembayaran') }}
            </h2>
            <a href="{{ route('pos') }}"
               class="flex items-center px-4 py-2 text-sm font-semibold text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-105">
                <i class="mr-2 fas fa-arrow-left"></i>Kembali ke POS
            </a>
        </div>
    </x-slot>

    <div class="p-6 bg-white shadow-lg rounded-2xl">
        @if (session('success'))
            <div class="px-4 py-3 mb-6 text-sm text-green-800 border border-green-200 rounded-xl bg-green-50">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 rounded-xl bg-red-50">
                {{ session('error') }}
            </div>
        @endif

        @if($payments->isNotEmpty())
            <div class="grid gap-4 mb-6 md:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 border border-green-100 rounded-xl bg-green-50/60">
                    <p class="text-sm font-semibold text-green-600 uppercase">Total Transaksi</p>
                    <p class="mt-2 text-2xl font-bold text-green-900">{{ data_get($summary, 'total_transactions', 0) }}</p>
                </div>
                <div class="p-4 border border-green-100 rounded-xl bg-green-50/60">
                    <p class="text-sm font-semibold text-green-600 uppercase">Sudah Dibayar</p>
                    <p class="mt-2 text-2xl font-bold text-green-900">{{ data_get($summary, 'paid_transactions', 0) }}</p>
                </div>
                <div class="p-4 border border-green-100 rounded-xl bg-yellow-50/60">
                    <p class="text-sm font-semibold text-yellow-600 uppercase">Belum Dibayar</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-700">{{ data_get($summary, 'pending_transactions', 0) }}</p>
                </div>
                <div class="p-4 border border-green-100 rounded-xl bg-green-50/60">
                    <p class="text-sm font-semibold text-green-600 uppercase">Total Nilai Transaksi</p>
                    <p class="mt-2 text-lg font-bold text-green-900">Rp {{ number_format(data_get($summary, 'total_grand', 0), 0, ',', '.') }}</p>
                    <p class="text-xs text-green-600">Pendapatan diterima: Rp {{ number_format(data_get($summary, 'total_revenue', 0), 0, ',', '.') }}</p>
                </div>
            </div>
        @endif

        @if($payments->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center text-green-700">
                <div class="flex items-center justify-center w-16 h-16 mb-4 bg-green-100 rounded-full">
                    <i class="text-3xl fas fa-file-invoice"></i>
                </div>
                <h3 class="mb-2 text-xl font-semibold">Belum ada transaksi</h3>
                <p class="text-sm text-green-600">Transaksi yang diproses melalui POS akan muncul di sini secara otomatis.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-green-900 border border-green-100 rounded-xl">
                    <thead class="text-xs font-semibold text-green-700 uppercase bg-green-50">
                        <tr>
                            <th class="px-4 py-3">No.</th>
                            <th class="px-4 py-3">Waktu &amp; Ref</th>
                            <th class="px-4 py-3">ID Pesanan</th>
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3">Grand Total</th>
                            <th class="px-4 py-3">Dibayar</th>
                            <th class="px-4 py-3">Sisa/Kembali</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-green-50">
                        @foreach($payments as $index => $payment)
                            @php
                                $createdAt = $payment->created_at ?? null;
                                $grandTotal = $payment->grand_total ?? 0;
                                $paid = $payment->payment_received ?? 0;
                                $balance = $payment->balance_due ?? max($grandTotal - $paid, 0);
                                $change = $payment->change_due ?? max($paid - $grandTotal, 0);
                                $status = $payment->status ?? 'pending';
                                $isPaid = $status === 'paid';
                                $badgeClass = $isPaid ? 'bg-green-500' : 'bg-yellow-500';
                                $statusLabel = $isPaid ? 'Sudah Dibayar' : 'Belum Dibayar';
                                $orderId = $payment->order_id ?? '-';
                            @endphp
                            <tr class="align-top hover:bg-green-50">
                                <td class="px-4 py-3 font-semibold">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $createdAt?->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                    <div class="text-xs text-green-600">Ref: {{ $payment->reference ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 font-semibold">{{ $orderId }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $payment->customer_name ?? '-' }}</div>
                                    <div class="text-xs capitalize text-green-600">{{ $payment->customer_type ?? '-' }}</div>
                                    @if(!empty($payment->note))
                                        <div class="px-2 py-1 mt-2 text-xs text-green-700 bg-green-50 rounded-lg">Catatan: {{ $payment->note }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 font-semibold">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    @if($change > 0)
                                        <span class="font-semibold text-green-700">Kembalian Rp {{ number_format($change, 0, ',', '.') }}</span>
                                    @elseif($balance > 0)
                                        <span class="font-semibold text-yellow-700">Sisa Rp {{ number_format($balance, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-green-700">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold text-white rounded-full {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <form method="POST" action="{{ route('pos.payments.update') }}">
                                            @csrf
                                            <input type="hidden" name="reference" value="{{ $payment->reference }}">
                                            <input type="hidden" name="status" value="paid">
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-semibold text-white transition rounded-lg shadow {{ $isPaid ? 'bg-green-400 cursor-not-allowed opacity-70' : 'bg-green-600 hover:bg-green-700' }}"
                                                    {{ $isPaid ? 'disabled' : '' }}>
                                                Tandai Dibayar
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('pos.payments.update') }}">
                                            @csrf
                                            <input type="hidden" name="reference" value="{{ $payment->reference }}">
                                            <input type="hidden" name="status" value="pending">
                                            <button type="submit"
                                                    class="px-3 py-1 text-xs font-semibold text-white transition rounded-lg shadow {{ $isPaid ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-yellow-400 cursor-not-allowed opacity-70' }}"
                                                    {{ $isPaid ? '' : 'disabled' }}>
                                                Tandai Belum
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr class="text-xs text-green-700 bg-green-50/60">
                                <td colspan="9" class="px-4 py-3">
                                    <div class="flex flex-wrap justify-between gap-6">
                                        <div>
                                            <p class="font-semibold uppercase text-green-600">Rincian Item</p>
                                            <ul class="mt-1 space-y-1 list-disc list-inside">
                                                @forelse($payment->items ?? [] as $item)
                                                    <li>
                                                        {{ $item->product_name ?? $item->name ?? 'Produk' }} — {{ $item->qty ?? 0 }} {{ $item->unit ?? 'pcs' }}
                                                        <span class="font-semibold">(Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }})</span>
                                                    </li>
                                                @empty
                                                    <li>Tidak ada detail item.</li>
                                                @endforelse
                                            </ul>
                                        </div>
                                        <div>
                                            <p class="font-semibold uppercase text-green-600">Ringkasan Biaya</p>
                                            <div class="space-y-1">
                                                <div>Subtotal: <span class="font-semibold">Rp {{ number_format($payment->subtotal ?? 0, 0, ',', '.') }}</span></div>
                                                <div>Ongkir: <span class="font-semibold">Rp {{ number_format($payment->shipping_cost ?? 0, 0, ',', '.') }}</span></div>
                                                <div>Tip: <span class="font-semibold">Rp {{ number_format($payment->tip ?? 0, 0, ',', '.') }}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
