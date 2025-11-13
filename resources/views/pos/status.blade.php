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

        <form action="{{ route('pos.payments') }}" method="GET" class="flex flex-wrap items-end gap-3 mb-6">
            <div>
                <label for="filter-date" class="block text-sm font-semibold text-green-700">Tampilkan per tanggal</label>
                <input
                    id="filter-date"
                    type="date"
                    name="date"
                    value="{{ $selectedDate }}"
                    class="px-3 py-2 text-sm border border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                >
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-[1.02]">
                    Filter
                </button>
                <a href="{{ route('pos.payments') }}" class="px-4 py-2 text-sm font-semibold text-green-700 transition border border-green-300 rounded-lg hover:bg-green-50">
                    Reset
                </a>
            </div>
        </form>

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
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3">Grand Total</th>
                            <th class="px-4 py-3">Dibayar</th>
                            <th class="px-4 py-3">Sisa/Kembali</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-green-50">
                        @php $rowNumber = 1; @endphp
                        @foreach($groupedPayments as $dateKey => $transactions)
                            @php
                                $dateLabel = $dateKey !== 'unknown'
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $dateKey)->translatedFormat('d M Y')
                                    : 'Tanggal tidak diketahui';
                            @endphp
                            @if(!$loop->first)
                                <tr>
                                    <td colspan="7" class="py-2">
                                        <div class="h-px bg-green-200"></div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="bg-green-100/60">
                                <td colspan="7" class="px-4 py-2 text-xs font-semibold text-green-700 uppercase">Tanggal: {{ $dateLabel }}</td>
                            </tr>
                            @foreach($transactions as $payment)
                            @php
                                $createdAt = $payment->created_at;
                                $grandTotal = $payment->grand_total ?? 0;
                                $paid = $payment->payment_received ?? 0;
                                $balance = $payment->balance_due ?? max($grandTotal - $paid, 0);
                                $change = $payment->change_due ?? max($paid - $grandTotal, 0);
                                $status = $payment->status ?? 'pending';
                                $isPaid = in_array($status, ['paid', 'dibayar'], true);
                                $badgeClass = $isPaid ? 'bg-green-500' : 'bg-yellow-500';
                                $statusLabel = $isPaid ? 'Dibayar' : 'Belum Dibayar';
                                $prefillPayment = (string) ($balance > 0 ? $balance : $grandTotal);

                                if (old('transaction_id') == $payment->id) {
                                    $prefillPayment = preg_replace('/[^0-9]/', '', (string) old('payment_amount', $prefillPayment));
                                }
                            @endphp
                            <tr class="align-top hover:bg-green-50">
                                <td class="px-4 py-3 font-semibold">{{ $rowNumber++ }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $createdAt?->translatedFormat('d M Y H:i') ?? '-' }}</div>
                                    <div class="text-xs text-green-600">Ref: {{ $payment->reference ?? '-' }}</div>
                                    <div class="text-xs text-green-600">Order: {{ $payment->order_id ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold">{{ $payment->customer_name ?? '-' }}</div>
                                    <div class="text-xs text-green-600 capitalize">{{ $payment->customer_type ?? '-' }}</div>
                                    @if(!empty($payment->note))
                                        <div class="px-2 py-1 mt-2 text-xs text-green-700 rounded-lg bg-green-50">Catatan: {{ $payment->note }}</div>
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
                                    @if(!$isPaid)
                                        <form
                                            action="{{ route('pos.payments.pay', $payment) }}"
                                            method="POST"
                                            class="mt-3 space-y-2 sm:flex sm:items-center sm:space-y-0 sm:space-x-2"
                                            data-payment-form
                                        >
                                            @csrf
                                            <input type="hidden" name="transaction_id" value="{{ $payment->id }}">
                                            <div class="relative sm:w-40">
                                                <span class="absolute text-xs font-semibold text-green-600 transform -translate-y-1/2 left-3 top-1/2">Rp</span>
                                                <input
                                                    type="text"
                                                    name="payment_amount"
                                                    value="{{ $prefillPayment }}"
                                                    placeholder="Nominal pembayaran"
                                                    inputmode="numeric"
                                                    data-payment-input
                                                    class="w-full py-2 pl-8 pr-3 text-sm border border-green-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                                                >
                                            </div>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white transition rounded-lg shadow bg-gradient-to-r from-green-500 to-green-700 hover:scale-[1.02]">
                                                Tandai Dibayar
                                            </button>
                                        </form>
                                        @if($errors->has('payment_amount') && (int) old('transaction_id') === $payment->id)
                                            <p class="mt-2 text-xs font-semibold text-red-600">{{ $errors->first('payment_amount') }}</p>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr class="text-xs text-green-700 bg-green-50/60">
                                <td colspan="7" class="px-4 py-3">
                                    <div class="flex flex-wrap justify-between gap-6">
                                        <div>
                                            <p class="font-semibold text-green-600 uppercase">Rincian Item</p>
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
                                            <p class="font-semibold text-green-600 uppercase">Ringkasan Biaya</p>
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-payment-form]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!confirm('Tandai transaksi ini sebagai sudah dibayar?')) {
                        event.preventDefault();
                        return;
                    }

                    var input = form.querySelector('[data-payment-input]');
                    if (input) {
                        input.value = input.value.replace(/[^0-9]/g, '');
                    }
                });
            });
        });
    </script>
@endpush
