<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Illuminate\Validation\ValidationException;
use App\Models\Produk;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use Illuminate\Support\Str;

class Poscontroller extends Controller
{
    public function index()
    {
        $product = Produk::with(['prices', 'units'])->get();
        $customertypes = ['agent', 'reseller', 'pelanggan'];

        return view('pos.index', compact('product', 'customertypes'));
    }

    public function status(Request $request)
    {
        $query = PosTransaction::with('items')->orderByDesc('created_at');

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        $payments = $query->get();
        $groupedPayments = $payments->groupBy(function ($payment) {
            return optional($payment->created_at)->format('Y-m-d') ?: 'unknown';
        });

        return view('pos.status', [
            'payments' => $payments,
            'groupedPayments' => $groupedPayments,
            'selectedDate' => $request->input('date'),
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
        $customerName = $request->filled('customer_name') ? $request->input('customer_name') : 'Tanpa Nama';
        $customerType = $request->input('customer_type');
        $note = $request->input('note');

        try {
            DB::transaction(function () use ($items, $customerName, $customerType, $note, $subtotal, $shipping, $tip, $grandTotal, $paymentReceived, $status) {
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
                    'order_id' => $this->generateOrderId(),
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
        } catch (Throwable $exception) {
            Log::error('POS checkout failed', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return back()->withErrors([
                'checkout' => 'Terjadi kesalahan saat memproses transaksi. Silakan coba lagi atau hubungi admin.',
            ])->withInput();
        }

        return redirect()->route('pos.payments')->with('success', 'Transaksi POS berhasil dicatat.');
    }

    public function applyPayment(Request $request, PosTransaction $transaction)
    {
        $request->validate([
            'payment_amount' => ['required', 'string'],
            'transaction_id' => ['required', 'integer'],
        ], [
            'payment_amount.required' => 'Nominal pembayaran wajib diisi.',
        ]);

        if ((int) $request->input('transaction_id') !== $transaction->id) {
            return back()
                ->withErrors(['payment_amount' => 'Transaksi tidak valid.'])
                ->withInput($request->all());
        }

        $amount = $this->normalizeCurrency($request->input('payment_amount'));

        if ($amount <= 0) {
            return back()
                ->withErrors(['payment_amount' => 'Nominal pembayaran harus lebih dari 0.'])
                ->withInput($request->all());
        }

        $totalPaid = $transaction->payment_received + $amount;
        $balanceDue = max($transaction->grand_total - $totalPaid, 0);
        $changeDue = max($totalPaid - $transaction->grand_total, 0);
        $status = $balanceDue === 0 ? 'paid' : 'pending';

        $transaction->update([
            'payment_received' => $totalPaid,
            'balance_due' => $balanceDue,
            'change_due' => $changeDue,
            'status' => $status,
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    protected function generateOrderId(): string
    {
        do {
            $orderId = 'POS-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4));
        } while (PosTransaction::where('order_id', $orderId)->exists());

        return $orderId;
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
