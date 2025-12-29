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

class PosController extends Controller
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
        // Determine if request wants JSON (AJAX)
        $wantsJson = $request->wantsJson() || $request->ajax();

        // Log incoming request
        \Log::info('POS Checkout Request', [
            'type' => $wantsJson ? 'AJAX' : 'FORM',
            'customer_type' => $request->customer_type,
            'payment_method' => $request->payment_method,
            'cart_count' => count($request->input('cart.id', [])),
            'session_id' => session()->getId()
        ]);

        try {
            // Validation rules
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
                'cart.name' => 'required|array',
                'cart.name.*' => 'required|string|max:255',
                'cart.satuan' => 'required|array',
                'cart.satuan.*' => 'required|string|max:50',
                'grand_total' => 'required|numeric|min:0',
                'payment_received' => 'required|numeric|min:0',
                'shipping_cost' => 'nullable|numeric|min:0',
                'tip' => 'nullable|numeric|min:0',
                'expense_amount' => 'nullable|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'discount_percent' => 'nullable|numeric|min:0|max:100',
                'shipping_address' => 'nullable|string|max:500',
                'payment_method' => 'required|string|in:cash,transfer',
                'bank_name' => 'required_if:payment_method,transfer|nullable|string|max:100',
                'account_number' => 'required_if:payment_method,transfer|nullable|string|max:50',
                'note' => 'nullable|string|max:1000',
            ]);

            // Parse all inputs to integers
            $shippingCost = $this->parseCurrency($request->shipping_cost ?? 0);
            $tip = $this->parseCurrency($request->tip ?? 0);
            $expenseAmount = $this->parseCurrency($request->expense_amount ?? 0);
            $discount = $this->parseCurrency($request->discount ?? 0);
            $paymentReceived = $this->parseCurrency($request->payment_received);
            $grandTotalFromForm = $this->parseCurrency($request->grand_total);

            // Get cart data
            $cartIds = $request->input('cart.id', []);
            $cartQtys = $request->input('cart.qty', []);
            $cartPrices = $request->input('cart.price', []);
            $cartSubtotals = $request->input('cart.subtotal', []);
            $cartNames = $request->input('cart.name', []);
            $cartSatuans = $request->input('cart.satuan', []);

            // Validate array lengths
            $arrayLengths = [
                'ids' => count($cartIds),
                'qtys' => count($cartQtys),
                'prices' => count($cartPrices),
                'subtotals' => count($cartSubtotals)
            ];

            if (count(array_unique($arrayLengths)) > 1) {
                throw new \Exception('Data cart tidak konsisten. Silakan refresh halaman dan coba lagi.');
            }

            // Calculate subtotal from cart
            $subtotal = 0;
            foreach ($cartSubtotals as $index => $itemSubtotal) {
                $subtotal += $this->parseCurrency($itemSubtotal);
            }

            // Calculate grand total from components
            $calculatedGrandTotal = $subtotal + $shippingCost + $tip + $expenseAmount - $discount;

            // Validate grand total
            $grandTotal = $grandTotalFromForm;
            if (abs($grandTotalFromForm - $calculatedGrandTotal) > 1000) {
                \Log::warning('Grand total mismatch', [
                    'form_total' => $grandTotalFromForm,
                    'calculated_total' => $calculatedGrandTotal,
                    'difference' => abs($grandTotalFromForm - $calculatedGrandTotal)
                ]);
                // Use calculated total for safety
                $grandTotal = $calculatedGrandTotal;
            }

            // Calculate balance and change
            $balanceDue = max(0, $grandTotal - $paymentReceived);
            $changeDue = max(0, $paymentReceived - $grandTotal);
            $status = $paymentReceived >= $grandTotal ? 'paid' : 'pending';

            // Get customer name
            $customerName = $request->customer_name;
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customerName = $customer->customer_name;
                    // Auto-fill shipping address if empty
                    if (empty($request->shipping_address) && !empty($customer->address)) {
                        $request->merge(['shipping_address' => $customer->address]);
                    }
                }
            }

            // Generate order ID
            $orderId = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            DB::beginTransaction();

            try {
                // Sanitize input
                $sanitized = $this->sanitizeInput([
                    'customer_name' => $customerName,
                    'note' => $request->note,
                    'shipping_address' => $request->shipping_address,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                ]);

                // Create transaction
                $transaction = PosTransaction::create([
                    'order_id' => $orderId,
                    'reference' => (string) Str::uuid(),
                    'customer_name' => $sanitized['customer_name'],
                    'customer_type' => $request->customer_type,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'tip' => $tip,
                    'expense_amount' => $expenseAmount,
                    'discount' => $discount,
                    'discount_percent' => $request->discount_percent ?? 0,
                    'shipping_address' => $sanitized['shipping_address'],
                    'grand_total' => $grandTotal,
                    'payment_received' => $paymentReceived,
                    'payment_method' => $request->payment_method,
                    'bank_name' => $sanitized['bank_name'],
                    'account_number' => $sanitized['account_number'],
                    'balance_due' => $balanceDue,
                    'change_due' => $changeDue,
                    'status' => $status,
                    'note' => $sanitized['note'],
                    'created_by' => auth()->id(),
                ]);

                // Fetch all products in cart at once
                $products = Produk::whereIn('id', $cartIds)->get()->keyBy('id');

                // Create transaction items and reduce stock
                foreach ($cartIds as $index => $productId) {
                    $qty = (int) ($cartQtys[$index] ?? 1);
                    $price = $this->parseCurrency($cartPrices[$index] ?? 0);
                    $itemSubtotal = $this->parseCurrency($cartSubtotals[$index] ?? 0);
                    $productName = $cartNames[$index] ?? '';
                    $unit = $cartSatuans[$index] ?? 'pcs';

                    // Validate product exists and has enough stock
                    $product = $products->get($productId);
                    if (!$product) {
                        throw new \Exception("Produk dengan ID {$productId} tidak ditemukan.");
                    }

                    if ($product->stock_quantity < $qty) {
                        throw new \Exception("Stok '{$product->name}' tidak cukup. Stok tersedia: {$product->stock_quantity}, dibutuhkan: {$qty}");
                    }

                    // Create transaction item
                    PosTransactionItem::create([
                        'pos_transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'product_name' => $productName,
                        'qty' => $qty,
                        'unit' => $unit,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                    ]);

                    // Reduce stock with optimistic locking
                    $affectedRows = DB::table('products')
                        ->where('id', $productId)
                        ->where('stock_quantity', '>=', $qty)
                        ->decrement('stock_quantity', $qty);

                    if ($affectedRows === 0) {
                        throw new \Exception("Gagal mengurangi stok produk '{$product->name}'. Stok mungkin berubah.");
                    }
                }

                // Log activity
                Activity::create([
                    'action' => 'Transaksi POS',
                    'description' => "Transaksi {$orderId} berhasil dibuat. Customer: {$customerName}, Total: Rp " . number_format($grandTotal, 0, ',', '.') . ", Status: {$status}",
                    'user_id' => auth()->id(),
                ]);

                DB::commit();

                \Log::info('POS Transaction Success', [
                    'order_id' => $orderId,
                    'transaction_id' => $transaction->id,
                    'customer' => $customerName,
                    'total' => $grandTotal,
                    'status' => $status
                ]);

                // Return appropriate response
                if ($wantsJson) {
                    return response()->json([
                        'success' => true,
                        'message' => "Transaksi {$orderId} berhasil disimpan!",
                        'order_id' => $orderId,
                        'transaction_id' => $transaction->id,
                        'status' => $status,
                        'redirect' => route('pos')
                    ]);
                } else {
                    return redirect()->route('pos')
                        ->with('success', "Transaksi {$orderId} berhasil disimpan!")
                        ->with('transaction_id', $transaction->id)
                        ->with('order_id', $orderId)
                        ->with('status', $status);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e; // Re-throw for outer catch block
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('POS Validation Error', [
                'errors' => $e->errors(),
                'input' => $request->except(['cart', '_token'])
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $e->errors()
                ], 422);
            } else {
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('POS Checkout Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['cart', '_token'])
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()
                ], 500);
            } else {
                return back()
                    ->withErrors(['error' => 'Gagal menyimpan transaksi: ' . $e->getMessage()])
                    ->withInput();
            }
        }
    }

    /**
     * Display transaction status / payment history
     */
    public function status(Request $request)
    {
        $selectedDate = $request->get('date', now()->toDateString());

        $query = PosTransaction::with(['items', 'creator'])
            ->whereDate('created_at', $selectedDate)
            ->orderBy('created_at', 'desc');

        $payments = $query->get();

        // Group payments by date for display
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
            'payment_amount' => 'required|numeric|min:1',
        ]);

        $amount = $this->parseCurrency($request->payment_amount);

        if ($transaction->status === 'paid') {
            return back()->withErrors(['error' => 'Transaksi sudah lunas.']);
        }

        if ($transaction->status === 'cancelled') {
            return back()->withErrors(['error' => 'Transaksi yang dibatalkan tidak dapat dibayar.']);
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
            'description' => "Pembayaran Rp " . number_format($amount, 0, ',', '.') .
                           " untuk transaksi {$transaction->order_id}. Status: {$newStatus}",
            'user_id' => auth()->id(),
        ]);

        $message = $newStatus === 'paid'
            ? "Pembayaran berhasil! Transaksi {$transaction->order_id} sudah lunas."
            : "Pembayaran Rp " . number_format($amount, 0, ',', '.') .
              " berhasil ditambahkan. Sisa: Rp " . number_format($newBalanceDue, 0, ',', '.');

        return back()->with('success', $message);
    }

    /**
     * Update transaction status
     */
    public function updateStatus(Request $request, PosTransaction $transaction)
    {
        $request->validate([
            'status' => 'required|string|in:pending,cancelled',
        ]);

        $status = strtolower($request->status);
        $updates = ['status' => $status];

        if ($status === 'pending') {
            $updates['balance_due'] = max(0, $transaction->grand_total - $transaction->payment_received);
            $updates['change_due'] = max(0, $transaction->payment_received - $transaction->grand_total);
        } else {
            // If cancelled, reset balance and change
            $updates['balance_due'] = 0;
            $updates['change_due'] = 0;
        }

        $transaction->update($updates);

        $statusLabel = $status === 'pending' ? 'Belum Dibayar' : 'Dibatalkan';

        Activity::create([
            'action' => 'Status Transaksi POS',
            'description' => "Status transaksi {$transaction->order_id} diubah menjadi {$statusLabel}",
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', "Status transaksi {$transaction->order_id} diubah menjadi {$statusLabel}.");
    }

    /**
     * Parse currency string to integer
     */
    private function parseCurrency($value): int
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        // If already integer, return directly
        if (is_int($value) || (is_numeric($value) && intval($value) == $value)) {
            return (int) $value;
        }

        // If string with currency format
        if (is_string($value)) {
            // Remove all non-digit characters except dot and comma
            $cleaned = preg_replace('/[^0-9,\.]/', '', $value);

            // Handle Indonesian format (1.234.567,89)
            if (strpos($cleaned, ',') !== false && strpos($cleaned, '.') !== false) {
                // Format: 1.234.567,89
                $cleaned = str_replace('.', '', $cleaned); // Remove dots
                $cleaned = str_replace(',', '.', $cleaned); // Replace comma with dot
            } elseif (strpos($cleaned, ',') !== false) {
                // Format: 1234567,89
                $cleaned = str_replace(',', '.', $cleaned);
            }

            // Convert to float then to int (to handle decimals)
            $floatValue = (float) $cleaned;
            return (int) round($floatValue);
        }

        return (int) $value;
    }

    /**
     * Sanitize input data
     */
    private function sanitizeInput(array $data): array
    {
        return [
            'customer_name' => htmlspecialchars(strip_tags($data['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'note' => htmlspecialchars(strip_tags($data['note'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'shipping_address' => htmlspecialchars(strip_tags($data['shipping_address'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'bank_name' => htmlspecialchars(strip_tags($data['bank_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'account_number' => preg_replace('/[^0-9]/', '', $data['account_number'] ?? ''),
        ];
    }
}
