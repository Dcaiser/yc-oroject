<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Customer;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Activity;

class Poscontroller extends Controller
{
    public function index()
    {
        $product = Produk::with(['prices', 'category:id,name', 'units'])
            ->orderBy('name')
            ->get();

            
        $categories = Kategori::select('id', 'name')
            ->orderBy('name')
            ->get();

        $regularCustomers = Customer::select('id', 'customer_name', 'address', 'shipping_cost')
            ->orderBy('customer_name')
            ->get();

        $customertypes = ['agent', 'reseller', 'pelanggan'];

        return view('pos.index', compact('product', 'customertypes', 'categories', 'regularCustomers'));
    }

    /**
     * Process checkout and create transaction
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_type' => 'required|string|in:agent,reseller,pelanggan',
            'customer_name' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'cart' => 'required|array',
            'cart.id' => 'required|array|min:1',
            'cart.id.*' => 'required|exists:products,id',
            'cart.qty' => 'required|array|min:1',
            'cart.qty.*' => 'required|integer|min:1',
            'cart.price' => 'required|array',
            'cart.price.*' => 'required|numeric|min:0',
            'cart.subtotal' => 'required|array',
            'cart.subtotal.*' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'payment_received' => 'required|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'tip' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        // Get validated values (now all numeric, no parsing needed)
        $shippingCost = (int) ($request->shipping_cost ?? 0);
        $tip = (int) ($request->tip ?? 0);
        $paymentReceived = (int) $request->payment_received;
        $grandTotal = (int) $request->grand_total;

        // Get customer name
        $customerName = $request->customer_name;
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if ($customer) {
                $customerName = $customer->customer_name;
            }
        }

        // Calculate totals
        $subtotal = 0;
        $cartIds = $request->input('cart.id', []);
        $cartQtys = $request->input('cart.qty', []);
        $cartPrices = $request->input('cart.price', []);
        $cartSubtotals = $request->input('cart.subtotal', []);
        $cartNames = $request->input('cart.name', []);
        $cartSatuans = $request->input('cart.satuan', []);

        foreach ($cartSubtotals as $itemSubtotal) {
            $subtotal += (int) $itemSubtotal;
        }

        // Calculate balance and change
        $balanceDue = max(0, $grandTotal - $paymentReceived);
        $changeDue = max(0, $paymentReceived - $grandTotal);
        $status = $paymentReceived >= $grandTotal ? 'paid' : 'pending';

        // Generate order ID
        $orderId = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = PosTransaction::create([
                'order_id' => $orderId,
                'reference' => (string) Str::uuid(),
                'customer_name' => $customerName,
                'customer_type' => $request->customer_type,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tip' => $tip,
                'grand_total' => $grandTotal,
                'payment_received' => $paymentReceived,
                'balance_due' => $balanceDue,
                'change_due' => $changeDue,
                'status' => $status,
                'note' => $request->note,
            ]);

            // Create transaction items and reduce stock
            foreach ($cartIds as $index => $productId) {
                $qty = (int) ($cartQtys[$index] ?? 1);
                $price = (int) ($cartPrices[$index] ?? 0);
                $itemSubtotal = (int) ($cartSubtotals[$index] ?? 0);
                $productName = $cartNames[$index] ?? '';
                $unit = $cartSatuans[$index] ?? 'pcs';

                PosTransactionItem::create([
                    'pos_transaction_id' => $transaction->id,
                    'product_id' => $productId,
                    'product_name' => $productName,
                    'qty' => $qty,
                    'unit' => $unit,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                ]);

                // Reduce stock
                $product = Produk::find($productId);
                if ($product) {
                    $product->decrement('stock_quantity', $qty);
                }
            }

            // Log activity
            Activity::create([
                'action' => 'Transaksi POS',
                'description' => "Transaksi {$orderId} berhasil dibuat. Total: Rp " . number_format($grandTotal, 0, ',', '.'),
            ]);

            DB::commit();

            return redirect()->route('pos')
                ->with('success', "Transaksi {$orderId} berhasil disimpan!")
                ->with('transaction_id', $transaction->id)
                ->with('order_id', $orderId);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display transaction status / payment history
     */
    public function status(Request $request)
    {
        $selectedDate = $request->get('date', now()->toDateString());
        
        $query = PosTransaction::with('items')->orderBy('created_at', 'desc');

        // Filter by date if specified
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        $payments = $query->get();

        // Group payments by date
        $groupedPayments = $payments->groupBy(function ($payment) {
            return $payment->created_at ? $payment->created_at->format('Y-m-d') : 'unknown';
        });

        return view('pos.status', compact('payments', 'groupedPayments', 'selectedDate'));
    }

    /**
     * Apply additional payment to a pending transaction
     */
    public function applyPayment(Request $request, PosTransaction $transaction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $this->parseCurrency($request->amount);
        
        if ($transaction->status === 'paid') {
            return back()->withErrors(['error' => 'Transaksi sudah lunas.']);
        }

        $newPaymentReceived = $transaction->payment_received + $amount;
        $newBalanceDue = max(0, $transaction->grand_total - $newPaymentReceived);
        $newChangeDue = max(0, $newPaymentReceived - $transaction->grand_total);
        $newStatus = $newPaymentReceived >= $transaction->grand_total ? 'paid' : 'pending';

        $transaction->update([
            'payment_received' => $newPaymentReceived,
            'balance_due' => $newBalanceDue,
            'change_due' => $newChangeDue,
            'status' => $newStatus,
        ]);

        // Log activity
        Activity::create([
            'action' => 'Pembayaran POS',
            'description' => "Pembayaran Rp " . number_format($amount, 0, ',', '.') . " untuk transaksi {$transaction->order_id}",
        ]);

        $message = $newStatus === 'paid' 
            ? "Pembayaran berhasil! Transaksi {$transaction->order_id} sudah lunas."
            : "Pembayaran Rp " . number_format($amount, 0, ',', '.') . " berhasil ditambahkan. Sisa: Rp " . number_format($newBalanceDue, 0, ',', '.');

        return back()->with('success', $message);
    }

    /**
     * Parse currency string to integer
     */
    private function parseCurrency($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        // Remove 'Rp', spaces, dots, and other non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $value);
        return (int) $cleaned;
    }
}
