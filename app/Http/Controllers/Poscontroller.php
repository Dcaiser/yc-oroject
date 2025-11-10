<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Produk;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use Illuminate\Support\Str;

class Poscontroller extends Controller
{
    public function index()
    {
        $product = Produk::with('prices')->get();
        $customertypes = ['agent', 'reseller', 'pelanggan'];

        return view('pos.index', compact('product', 'customertypes'));
    }

    public function status()
    {
        $payments = PosTransaction::with('items')->latest()->get();

        $summary = [
            'total_transactions' => $payments->count(),
            'paid_transactions' => $payments->where('status', 'paid')->count(),
            'pending_transactions' => $payments->where('status', 'pending')->count(),
            'total_grand' => $payments->sum('grand_total'),
            'total_revenue' => $payments->sum('payment_received'),
        ];

        return view('pos.status', [
            'payments' => $payments,
            'summary' => $summary,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'string', 'max:191', Rule::unique('pos_transactions', 'order_id')],
            'customer_type' => ['required', 'string'],
            'customer_name' => ['nullable', 'string'],
            'cart.id' => ['required', 'array', 'min:1'],
            'cart.name' => ['required', 'array', 'min:1'],
            'cart.qty' => ['required', 'array', 'min:1'],
            'cart.satuan' => ['required', 'array', 'min:1'],
            'cart.price' => ['required', 'array', 'min:1'],
            'cart.subtotal' => ['required', 'array', 'min:1'],
            'cart.name.*' => ['string'],
            'cart.qty.*' => ['numeric', 'min:1'],
            'cart.satuan.*' => ['string'],
            'grand_total' => ['required'],
            'shippingCost' => ['nullable'],
            'tip' => ['nullable'],
            'payment_received' => ['nullable'],
            'note' => ['nullable', 'string'],
        ], [
            'cart.id.min' => 'Keranjang tidak boleh kosong.',
            'order_id.unique' => 'ID pesanan sudah digunakan. Silakan gunakan ID lain.',
        ]);

        $items = $this->extractCartItems($request);

        if (empty($items)) {
            return back()->withErrors(['cart' => 'Keranjang tidak boleh kosong.'])->withInput();
        }

        $subtotal = array_sum(array_column($items, 'subtotal'));
        $shipping = $this->normalizeCurrency($request->input('shippingCost'));
        $tip = $this->normalizeCurrency($request->input('tip'));
        $grandTotal = $this->normalizeCurrency($request->input('grand_total', $subtotal + $shipping + $tip));
        $paymentReceived = $this->normalizeCurrency($request->input('payment_received'));
        $status = $paymentReceived >= $grandTotal && $grandTotal > 0 ? 'paid' : 'pending';

        $orderId = $request->input('order_id');
        $customerType = $request->input('customer_type');
        $customerName = $request->filled('customer_name') ? $request->input('customer_name') : 'Tanpa Nama';
        $note = $request->input('note');

        try {
            DB::transaction(function () use ($items, $orderId, $customerType, $customerName, $note, $subtotal, $shipping, $tip, $grandTotal, $paymentReceived, $status) {
                $productIds = collect($items)->pluck('id');

                $products = Produk::whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                if ($products->count() !== $productIds->unique()->count()) {
                    throw ValidationException::withMessages([
                        'cart' => 'Beberapa produk tidak ditemukan. Silakan muat ulang halaman POS.',
                    ]);
                }

                foreach ($items as $item) {
                    $product = $products->get($item['id']);
                    $currentStock = $product->stock_quantity ?? 0;

                    if ($currentStock < $item['qty']) {
                        throw ValidationException::withMessages([
                            'cart' => "Stok {$product->name} tidak mencukupi (tersedia {$currentStock}).",
                        ]);
                    }
                }

                $transaction = PosTransaction::create([
                    'order_id' => $orderId,
                    'reference' => (string) Str::uuid(),
                    'customer_name' => $customerName,
                    'customer_type' => $customerType,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shipping,
                    'tip' => $tip,
                    'grand_total' => $grandTotal,
                    'payment_received' => $paymentReceived,
                    'balance_due' => max($grandTotal - $paymentReceived, 0),
                    'change_due' => max($paymentReceived - $grandTotal, 0),
                    'status' => $status,
                    'note' => $note,
                ]);

                foreach ($items as $item) {
                    $product = $products->get($item['id']);

                    $product->decrement('stock_quantity', $item['qty']);

                    PosTransactionItem::create([
                        'pos_transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'product_name' => $item['name'],
                        'qty' => $item['qty'],
                        'unit' => $item['unit'],
                        'price' => $item['price'],
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }, 3);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors())->withInput();
        }

        return redirect()->route('pos.payments')->with('success', 'Transaksi POS berhasil diproses.');
    }

    public function updatePaymentStatus(Request $request)
    {
        $data = $request->validate([
            'reference' => ['required', 'uuid', 'exists:pos_transactions,reference'],
            'status' => ['required', Rule::in(['paid', 'pending'])],
        ]);

        $transaction = PosTransaction::where('reference', $data['reference'])->firstOrFail();

        if ($transaction->status === $data['status']) {
            return redirect()->route('pos.payments')->with('success', 'Status transaksi tidak berubah.');
        }

        if ($data['status'] === 'paid') {
            $transaction->status = 'paid';
            $transaction->balance_due = 0;
            $transaction->change_due = max($transaction->payment_received - $transaction->grand_total, 0);
        } else {
            $transaction->status = 'pending';
            $transaction->balance_due = max($transaction->grand_total - $transaction->payment_received, 0);
            $transaction->change_due = 0;
        }

        $transaction->save();

        $message = $transaction->status === 'paid'
            ? 'Transaksi berhasil ditandai sudah dibayar.'
            : 'Transaksi berhasil ditandai belum dibayar.';

        return redirect()->route('pos.payments')->with('success', $message);
    }

    protected function normalizeCurrency($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        return $digits === '' ? 0 : (int) $digits;
    }

    protected function extractCartItems(Request $request): array
    {
        $ids = $request->input('cart.id', []);
        $names = $request->input('cart.name', []);
        $qtys = $request->input('cart.qty', []);
        $units = $request->input('cart.satuan', []);
        $prices = $request->input('cart.price', []);
        $subtotals = $request->input('cart.subtotal', []);

        $items = [];

        foreach ($ids as $index => $id) {
            if ($id === null || $id === '') {
                continue;
            }

            $price = $this->normalizeCurrency($prices[$index] ?? 0);
            $qty = max((int) ($qtys[$index] ?? 1), 1);
            $subtotal = $this->normalizeCurrency($subtotals[$index] ?? ($price * $qty));

            $items[] = [
                'id' => (int) $id,
                'name' => $names[$index] ?? '',
                'qty' => $qty,
                'unit' => $units[$index] ?? 'pcs',
                'price' => $price,
                'subtotal' => $subtotal,
            ];
        }

        return $items;
    }
}
