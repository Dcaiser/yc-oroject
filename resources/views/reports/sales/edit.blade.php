<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-500">Transaksi Penjualan</p>
                <h1 class="text-2xl font-extrabold text-slate-900">Edit Transaksi</h1>
            </div>
            <a href="{{ route('reports.index', $filters) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-emerald-700 bg-emerald-50 border border-emerald-100 rounded-xl hover:bg-emerald-100">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Laporan
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <x-breadcrumb :items="[['title' => 'Laporan Inventori', 'url' => route('reports.index', $filters)], ['title' => 'Edit Transaksi']]" />

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                <p class="font-semibold">Terjadi kesalahan pada data yang dikirim.</p>
                <ul class="mt-2 list-disc list-inside space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-2xl bg-white shadow-md ring-1 ring-emerald-100">
            <form method="POST" action="{{ route('reports.sales.update', $transaction->id) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                @foreach($filters as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="transaction_date">Tanggal</label>
                        <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transactionDate) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="transaction_time">Waktu (opsional)</label>
                        <input type="time" id="transaction_time" name="transaction_time" value="{{ old('transaction_time', $transactionTime) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-emerald-50/60 px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="customer_name">Nama Customer</label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name', $transaction->customer_name) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="customer_type">Tipe Customer</label>
                        <select id="customer_type" name="customer_type" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                            @foreach($customerTypes as $type)
                                <option value="{{ $type }}" @selected(old('customer_type', $transaction->customer_type) === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-slate-900">Item Transaksi</h3>
                    @foreach($transaction->items as $item)
                    <div class="p-4 border border-emerald-100 rounded-xl bg-emerald-50/30">
                        <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Produk</label>
                                <input type="text" name="items[{{ $item->id }}][product_name]" value="{{ old("items.{$item->id}.product_name", $item->product_name) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Jumlah</label>
                                    <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][qty]" value="{{ old("items.{$item->id}.qty", $item->qty) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Satuan</label>
                                    <input type="text" name="items[{{ $item->id }}][unit]" value="{{ old("items.{$item->id}.unit", $item->unit) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                             <label class="text-sm font-semibold text-slate-700">Harga Satuan</label>
                             <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][price]" value="{{ old("items.{$item->id}.price", $item->price) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="shipping_cost">Ongkir</label>
                        <input type="number" step="0.01" min="0" id="shipping_cost" name="shipping_cost" value="{{ old('shipping_cost', $transaction->shipping_cost) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-700" for="note">Catatan</label>
                        <input type="text" id="note" name="note" value="{{ old('note', $transaction->note) }}" class="mt-1 w-full rounded-xl border border-emerald-100 bg-white px-4 py-2.5 text-sm font-semibold text-slate-800 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('reports.index', $filters) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                        <i class="fas fa-save"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>
    </div>
</x-app-layout>
