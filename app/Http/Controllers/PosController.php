<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Customer;
use App\Models\Price;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Activity;

class PosController extends Controller
{
    private const POS_INITIAL_LIMIT = 24;
    private const POS_CHUNK_LIMIT = 200;
    private ?bool $productsHasBarcodeColumn = null;

    public function index()
    {
        $productsVersion = (int) Cache::get('pos:products:cache_version', 1);
        $categoriesVersion = (int) Cache::get('pos:categories:cache_version', 1);
        $customersVersion = (int) Cache::get('pos:customers:cache_version', 1);

        $defaultCustomerType = 'pelanggan';
        $productQuery = $this->baseProductQuery($defaultCustomerType);
        $totalProducts = (clone $productQuery)->count();

        $initialProducts = $productQuery
            ->limit(self::POS_INITIAL_LIMIT)
            ->get();

        $posProducts = $this->transformProductsForPos($initialProducts);

        $categories = Cache::remember("pos:categories:list:v{$categoriesVersion}", now()->addMinutes(10), function () {
            return Kategori::select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        $regularCustomers = Cache::remember("pos:customers:list:v{$customersVersion}", now()->addMinutes(3), function () {
            return Customer::select('id', 'customer_name', 'phone', 'address', 'shipping_cost')
                ->orderBy('customer_name')
                ->get();
        });

        $customertypes = ['agent', 'reseller', 'pelanggan'];

        $posProductsTotal = $totalProducts;
        $posProductsChunkLimit = self::POS_CHUNK_LIMIT;
        $posProductsEndpoint = route('pos.products.data');

        return view('pos.index', compact(
            'posProducts',
            'customertypes',
            'categories',
            'regularCustomers',
            'posProductsTotal',
            'posProductsChunkLimit',
            'posProductsEndpoint'
        ));
    }

    public function productsData(Request $request)
    {
        $productsVersion = (int) Cache::get('pos:products:cache_version', 1);

        $page = max(1, (int) $request->query('page', 1));
        $perPage = (int) $request->query('per_page', $request->query('limit', self::POS_INITIAL_LIMIT));
        $perPage = max(1, min(120, $perPage));

        $search = trim((string) $request->query('search', ''));
        $selectedCategory = (string) $request->query('category', 'all');
        $inStockOnly = filter_var($request->query('in_stock', false), FILTER_VALIDATE_BOOLEAN);
        $sortBy = (string) $request->query('sort', 'name_asc');
        $customerType = (string) $request->query('customer_type', 'pelanggan');

        $cacheFingerprint = md5(json_encode([
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search,
            'category' => $selectedCategory,
            'in_stock' => $inStockOnly,
            'sort' => $sortBy,
            'customer_type' => $customerType,
        ]));

        $payload = Cache::remember("pos:products:data:v{$productsVersion}:{$cacheFingerprint}", now()->addSeconds(45), function () use (
            $search,
            $selectedCategory,
            $inStockOnly,
            $sortBy,
            $customerType,
            $page,
            $perPage
        ) {
            $query = $this->baseProductQuery($customerType);

            if ($search !== '') {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");

                    if ($this->productsHasBarcode()) {
                        $builder->orWhere('barcode', 'like', "%{$search}%");
                    }
                });
            }

            if ($selectedCategory !== 'all' && is_numeric($selectedCategory)) {
                $query->where('category_id', (int) $selectedCategory);
            }

            if ($inStockOnly) {
                $query->where('stock_quantity', '>', 0);
            }

            $this->applyProductSort($query, $sortBy, $customerType);

            $total = (clone $query)->count();

            $products = $query
                ->forPage($page, $perPage)
                ->get();

            $data = $this->transformProductsForPos($products);
            $lastPage = max(1, (int) ceil($total / $perPage));
            $from = $total > 0 ? (($page - 1) * $perPage) + 1 : 0;
            $to = $total > 0 ? min($from + $data->count() - 1, $total) : 0;

            return [
                'data' => $data,
                'meta' => [
                    'total' => $total,
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'last_page' => $lastPage,
                    'from' => $from,
                    'to' => $to,
                ],
            ];
        });

        return response()->json($payload);
    }

    private function baseProductQuery(string $customerType = 'pelanggan')
    {
        $columns = [
            'id',
            'name',
            'sku',
            'stock_quantity',
            'description',
            'image_path',
            'category_id',
            'satuan',
        ];

        if ($this->productsHasBarcode()) {
            $columns[] = 'barcode';
        }

        return Produk::query()
            ->select($columns)
            ->with([
                'prices' => function ($priceQuery) use ($customerType) {
                    $priceQuery
                        ->select('id', 'product_id', 'customer_type', 'price')
                        ->where('customer_type', $customerType);
                },
                'category:id,name',
                'units:id,name',
            ])
            ->orderBy('name');
    }

    private function productsHasBarcode(): bool
    {
        if ($this->productsHasBarcodeColumn === null) {
            $this->productsHasBarcodeColumn = Schema::hasColumn('products', 'barcode');
        }

        return $this->productsHasBarcodeColumn;
    }

    private function applyProductSort($query, string $sortBy, string $customerType): void
    {
        if ($sortBy === 'price_asc' || $sortBy === 'price_desc') {
            $direction = $sortBy === 'price_desc' ? 'desc' : 'asc';

            $query->orderBy(
                Price::select('price')
                    ->whereColumn('prices.product_id', 'products.id')
                    ->where('customer_type', $customerType)
                    ->limit(1),
                $direction
            )->orderBy('name');

            return;
        }

        $query->orderBy('name', 'asc');
    }

    private function transformProductsForPos($products)
    {
        return $products->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'barcode' => $this->productsHasBarcode() ? ($item->barcode ?? null) : null,
                'stock_quantity' => (int) ($item->stock_quantity ?? 0),
                'description' => $item->description,
                'image_url' => $item->image_url,
                'category_id' => $item->category_id,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                ] : null,
                'satuan' => $item->satuan,
                'units' => $item->units ? [
                    'id' => $item->units->id,
                    'name' => $item->units->name,
                ] : null,
                'prices' => $item->prices->map(function ($price) {
                    return [
                        'customer_type' => $price->customer_type,
                        'price' => (int) ($price->price ?? 0),
                    ];
                })->values(),
            ];
        })->values();
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

            $normalizedCartItems = [];
            $subtotal = 0;
            foreach ($cartIds as $index => $productId) {
                $normalizedItem = [
                    'product_id' => (int) $productId,
                    'qty' => (int) ($cartQtys[$index] ?? 1),
                    'price' => $this->parseCurrency($cartPrices[$index] ?? 0),
                    'subtotal' => $this->parseCurrency($cartSubtotals[$index] ?? 0),
                    'product_name' => $cartNames[$index] ?? '',
                    'unit' => $cartSatuans[$index] ?? 'pcs',
                ];

                $subtotal += $normalizedItem['subtotal'];
                $normalizedCartItems[] = $normalizedItem;
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
            $shippingAddress = $request->shipping_address;
            if ($request->customer_id) {
                $customer = Customer::query()
                    ->select('id', 'customer_name', 'address')
                    ->find($request->customer_id);

                if ($customer) {
                    $customerName = $customer->customer_name;
                    // Auto-fill shipping address if empty
                    if (empty($shippingAddress) && !empty($customer->address)) {
                        $shippingAddress = $customer->address;
                    }
                }
            }

            // Generate order ID
            $orderId = 'POS-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $transaction = DB::transaction(function () use (
                $orderId,
                $customerName,
                $request,
                $subtotal,
                $shippingCost,
                $tip,
                $expenseAmount,
                $discount,
                $grandTotal,
                $paymentReceived,
                $balanceDue,
                $changeDue,
                $status,
                $shippingAddress,
                $normalizedCartItems
            ) {
                $sanitized = $this->sanitizeInput([
                    'customer_name' => $customerName,
                    'note' => $request->note,
                    'shipping_address' => $shippingAddress,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                ]);

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

                $products = Produk::query()
                    ->select('id', 'name', 'stock_quantity')
                    ->whereIn('id', array_column($normalizedCartItems, 'product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $transactionItemsPayload = [];
                $stockAdjustments = [];
                $timestamp = now();

                foreach ($normalizedCartItems as $normalizedItem) {
                    $productId = $normalizedItem['product_id'];
                    $qty = $normalizedItem['qty'];
                    $price = $normalizedItem['price'];
                    $itemSubtotal = $normalizedItem['subtotal'];
                    $productName = $normalizedItem['product_name'];
                    $unit = $normalizedItem['unit'];

                    $product = $products->get($productId);
                    if (!$product) {
                        throw new \Exception("Produk dengan ID {$productId} tidak ditemukan.");
                    }

                    if ($product->stock_quantity < $qty) {
                        throw new \Exception("Stok '{$product->name}' tidak cukup. Stok tersedia: {$product->stock_quantity}, dibutuhkan: {$qty}");
                    }

                    $transactionItemsPayload[] = [
                        'pos_transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'product_name' => $productName,
                        'qty' => $qty,
                        'unit' => $unit,
                        'price' => $price,
                        'subtotal' => $itemSubtotal,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];

                    $stockAdjustments[$productId] = ($stockAdjustments[$productId] ?? 0) + $qty;
                }

                if (!empty($transactionItemsPayload)) {
                    PosTransactionItem::insert($transactionItemsPayload);
                }

                foreach ($stockAdjustments as $productId => $totalQty) {
                    $affectedRows = DB::table('products')
                        ->where('id', $productId)
                        ->where('stock_quantity', '>=', $totalQty)
                        ->decrement('stock_quantity', $totalQty);

                    if ($affectedRows === 0) {
                        $failedProduct = $products->get($productId);
                        $productLabel = $failedProduct?->name ?? $productId;
                        throw new \Exception("Gagal mengurangi stok produk '{$productLabel}'. Stok mungkin berubah.");
                    }
                }

                Activity::create([
                    'action' => 'Transaksi POS',
                    'description' => "Transaksi {$orderId} berhasil dibuat. Customer: {$customerName}, Total: Rp " . number_format($grandTotal, 0, ',', '.') . ", Status: {$status}",
                    'user_id' => auth()->id(),
                ]);

                return $transaction;
            }, 3);

            Cache::forever('pos:products:cache_version', ((int) Cache::get('pos:products:cache_version', 1)) + 1);
            $this->bumpPosStatusCacheVersion();

            \Log::info('POS Transaction Success', [
                'order_id' => $orderId,
                'transaction_id' => $transaction->id,
                'customer' => $customerName,
                'total' => $grandTotal,
                'status' => $status
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => "Transaksi {$orderId} berhasil disimpan!",
                    'order_id' => $orderId,
                    'transaction_id' => $transaction->id,
                    'status' => $status,
                    'redirect' => route('pos')
                ]);
            }

            return redirect()->route('pos')
                ->with('success', "Transaksi {$orderId} berhasil disimpan!")
                ->with('transaction_id', $transaction->id)
                ->with('order_id', $orderId)
                ->with('status', $status);

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
                'code' => $e->getCode(),
                'line' => $e->getLine(),
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
        $selectedDateStart = \Carbon\Carbon::parse($selectedDate)->startOfDay();
        $selectedDateEnd = \Carbon\Carbon::parse($selectedDate)->endOfDay();
        $search = trim((string) $request->query('search', ''));
        $statusFilter = strtolower((string) $request->query('status', 'all'));
        $paymentMethodFilter = strtolower((string) $request->query('payment_method', 'all'));

        if (!in_array($statusFilter, ['all', 'paid', 'pending'], true)) {
            $statusFilter = 'all';
        }

        if (!in_array($paymentMethodFilter, ['all', 'cash', 'transfer'], true)) {
            $paymentMethodFilter = 'all';
        }

        $page = max(1, (int) $request->query('page', 1));
        $perPage = (int) $request->query('per_page', 80);
        $perPage = max(20, min(200, $perPage));
        $statusVersion = (int) Cache::get('pos:status:cache_version', 1);
        $filterFingerprint = md5(json_encode([
            'search' => $search,
            'status' => $statusFilter,
            'payment_method' => $paymentMethodFilter,
        ]));

        $payments = Cache::remember(
            "pos:status:list:v{$statusVersion}:{$selectedDate}:f{$filterFingerprint}:p{$page}:pp{$perPage}",
            now()->addSeconds(45),
            function () use ($selectedDateStart, $selectedDateEnd, $search, $statusFilter, $paymentMethodFilter, $page, $perPage) {
                $query = PosTransaction::query()
                    ->select([
                        'id',
                        'order_id',
                        'reference',
                        'customer_name',
                        'customer_type',
                        'grand_total',
                        'payment_received',
                        'balance_due',
                        'change_due',
                        'payment_method',
                        'status',
                        'created_at',
                    ])
                    ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);

                $this->applyStatusListFilters($query, $search, $statusFilter, $paymentMethodFilter);

                return $query->orderBy('created_at', 'desc')
                    ->forPage($page, $perPage)
                    ->get();
            }
        );

        $totalTransactions = (int) Cache::remember(
            "pos:status:count:v{$statusVersion}:{$selectedDate}:f{$filterFingerprint}",
            now()->addSeconds(45),
            function () use ($selectedDateStart, $selectedDateEnd, $search, $statusFilter, $paymentMethodFilter) {
                $query = PosTransaction::query()
                    ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);

                $this->applyStatusListFilters($query, $search, $statusFilter, $paymentMethodFilter);

                return $query->count();
            }
        );

        $paymentsPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $payments,
            $totalTransactions,
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        // Group payments by date for display
        $groupedPayments = $payments->groupBy(function ($payment) {
            return $payment->created_at ? $payment->created_at->format('Y-m-d') : 'unknown';
        });

        $stats = Cache::remember(
            "pos:status:stats:v{$statusVersion}:{$selectedDate}:f{$filterFingerprint}",
            now()->addSeconds(45),
            function () use ($selectedDateStart, $selectedDateEnd, $search, $statusFilter, $paymentMethodFilter) {
                $query = PosTransaction::query()
                    ->whereBetween('created_at', [$selectedDateStart, $selectedDateEnd]);

                $this->applyStatusListFilters($query, $search, $statusFilter, $paymentMethodFilter);

                return $query
                    ->selectRaw('COUNT(*) as total_transactions')
                    ->selectRaw("SUM(CASE WHEN LOWER(status) IN ('paid','dibayar') THEN 1 ELSE 0 END) as paid_count")
                    ->selectRaw("SUM(CASE WHEN LOWER(status) = 'pending' THEN 1 ELSE 0 END) as unpaid_count")
                    ->selectRaw("SUM(CASE WHEN LOWER(payment_method) = 'cash' THEN 1 ELSE 0 END) as cash_transactions")
                    ->selectRaw("SUM(CASE WHEN LOWER(payment_method) = 'transfer' THEN 1 ELSE 0 END) as transfer_transactions")
                    ->selectRaw("COALESCE(SUM(CASE WHEN LOWER(payment_method) = 'cash' THEN grand_total ELSE 0 END), 0) as cash_amount")
                    ->selectRaw("COALESCE(SUM(CASE WHEN LOWER(payment_method) = 'transfer' THEN grand_total ELSE 0 END), 0) as transfer_amount")
                    ->first();
            }
        );

        $paidCount = (int) ($stats->paid_count ?? 0);
        $statsTotalTransactions = (int) ($stats->total_transactions ?? 0);

        $paymentStats = [
            'total_transactions' => $statsTotalTransactions,
            'paid_count' => $paidCount,
            'unpaid_count' => (int) ($stats->unpaid_count ?? 0),
            'paid_percentage' => $statsTotalTransactions > 0 ? round(($paidCount / $statsTotalTransactions) * 100) : 0,
            'cash_transactions' => (int) ($stats->cash_transactions ?? 0),
            'transfer_transactions' => (int) ($stats->transfer_transactions ?? 0),
            'cash_amount' => (int) ($stats->cash_amount ?? 0),
            'transfer_amount' => (int) ($stats->transfer_amount ?? 0),
        ];

        $filterState = [
            'search' => $search,
            'status' => $statusFilter,
            'payment_method' => $paymentMethodFilter,
        ];

        return view('pos.status', compact('payments', 'groupedPayments', 'selectedDate', 'paymentStats', 'paymentsPaginator', 'perPage', 'filterState'));
    }

    public function statusDetail(int $transaction)
    {
        $statusVersion = (int) Cache::get('pos:status:cache_version', 1);
        $cacheKey = "pos:status:detail:v{$statusVersion}:{$transaction}";

        $detail = Cache::remember($cacheKey, now()->addSeconds(90), function () use ($transaction) {
            $payment = PosTransaction::query()
                ->select([
                    'id',
                    'order_id',
                    'reference',
                    'customer_name',
                    'customer_type',
                    'subtotal',
                    'grand_total',
                    'payment_received',
                    'shipping_cost',
                    'tip',
                    'expense_amount',
                    'discount',
                    'discount_percent',
                    'balance_due',
                    'change_due',
                    'payment_method',
                    'bank_name',
                    'account_number',
                    'shipping_address',
                    'note',
                    'status',
                    'created_by',
                    'created_at',
                ])
                ->with([
                    'creator:id,name',
                    'items:id,pos_transaction_id,product_name,qty,unit,subtotal',
                ])
                ->find($transaction);

            if (!$payment) {
                return null;
            }

            return [
                'id' => $payment->id,
                'order_id' => $payment->order_id,
                'reference' => $payment->reference,
                'customer_name' => $payment->customer_name,
                'customer_type' => $payment->customer_type,
                'subtotal' => (int) ($payment->subtotal ?? 0),
                'grand_total' => (int) ($payment->grand_total ?? 0),
                'payment_received' => (int) ($payment->payment_received ?? 0),
                'shipping_cost' => (int) ($payment->shipping_cost ?? 0),
                'tip' => (int) ($payment->tip ?? 0),
                'expense_amount' => (int) ($payment->expense_amount ?? 0),
                'discount' => (int) ($payment->discount ?? 0),
                'discount_percent' => (float) ($payment->discount_percent ?? 0),
                'balance_due' => (int) ($payment->balance_due ?? 0),
                'change_due' => (int) ($payment->change_due ?? 0),
                'payment_method' => strtolower((string) ($payment->payment_method ?? 'cash')),
                'bank_name' => $payment->bank_name,
                'account_number' => $payment->account_number,
                'shipping_address' => $payment->shipping_address,
                'note' => $payment->note,
                'status' => strtolower((string) ($payment->status ?? 'pending')),
                'created_at' => optional($payment->created_at)?->toIso8601String(),
                'creator' => $payment->creator ? ['name' => $payment->creator->name] : null,
                'items' => $payment->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_name' => $item->product_name,
                        'qty' => (int) $item->qty,
                        'unit' => $item->unit,
                        'subtotal' => (int) ($item->subtotal ?? 0),
                    ];
                })->values(),
            ];
        });

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $detail,
        ]);
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

        $this->bumpPosStatusCacheVersion();

        // Log activity
        Activity::create([
            'action' => 'Pembayaran POS',
            'description' => "Pembayaran Rp " . number_format($amount, 0, ',', '.') .
                           " untuk transaksi {$transaction->order_id}. Status: {$newStatus}",
            'user_id' => auth()->id(),
        ]);

        Cache::forever('pos:products:cache_version', ((int) Cache::get('pos:products:cache_version', 1)) + 1);

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
        $this->bumpPosStatusCacheVersion();

        $statusLabel = $status === 'pending' ? 'Belum Dibayar' : 'Dibatalkan';

        Activity::create([
            'action' => 'Status Transaksi POS',
            'description' => "Status transaksi {$transaction->order_id} diubah menjadi {$statusLabel}",
            'user_id' => auth()->id(),
        ]);

        Cache::forever('pos:products:cache_version', ((int) Cache::get('pos:products:cache_version', 1)) + 1);

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

    private function applyStatusListFilters($query, string $search, string $statusFilter, string $paymentMethodFilter): void
    {
        if ($statusFilter === 'paid') {
            $query->whereIn(DB::raw('LOWER(status)'), ['paid', 'dibayar']);
        } elseif ($statusFilter === 'pending') {
            $query->where(DB::raw('LOWER(status)'), 'pending');
        }

        if ($paymentMethodFilter !== 'all') {
            $query->where(DB::raw('LOWER(payment_method)'), $paymentMethodFilter);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%");
            });
        }
    }

    private function bumpPosStatusCacheVersion(): void
    {
        $current = (int) Cache::get('pos:status:cache_version', 1);
        Cache::forever('pos:status:cache_version', $current + 1);
    }
}
