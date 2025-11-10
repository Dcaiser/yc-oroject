<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
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
        $payments = collect(session('pos_payments', []))->map(function ($payment) {
            if (is_array($payment)) {
                if (isset($payment['items']) && is_array($payment['items'])) {
                    $payment['items'] = collect($payment['items'])->map(function ($item) {
                        return (object) $item;
                    })->all();
                }

                return (object) $payment;
            }

            if (is_object($payment) && isset($payment->items) && is_array($payment->items)) {
                $payment->items = collect($payment->items)->map(function ($item) {
                    return (object) $item;
                })->all();
            }

            return $payment;
        });

        return view('pos.status', [
            'payments' => $payments,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate([
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

        $payment = [
            'reference' => (string) Str::uuid(),
            'customer_name' => $request->filled('customer_name') ? $request->input('customer_name') : 'Tanpa Nama',
            'customer_type' => $request->input('customer_type'),
            'items' => $items,
            'total_items' => count($items),
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'tip' => $tip,
            'grand_total' => $grandTotal,
            'payment_received' => $paymentReceived,
            'balance_due' => max($grandTotal - $paymentReceived, 0),
            'change_due' => max($paymentReceived - $grandTotal, 0),
            'status' => $status,
            'note' => $request->input('note'),
            'created_at' => now()->toIso8601String(),
        ];

        $payments = collect(session('pos_payments', []));
        $payments->prepend($payment);
        session()->put('pos_payments', $payments->values()->all());

        return redirect()->route('pos.payments')->with('success', 'Transaksi POS berhasil diproses.');
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
