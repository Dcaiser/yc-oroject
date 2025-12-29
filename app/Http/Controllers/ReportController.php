<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Expense;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Produk;
use App\Models\Stockin;
use App\Models\Stockout;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\SalesTransactionExport;
use App\Exports\StockMovementExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default filters
        $mode = $request->input('mode', 'range');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());
        $year = $request->input('year', Carbon::now()->year);
        $week = $request->input('week');
        $weekMonth = $request->input('week_month', Carbon::now()->month);
        $weekYear = $request->input('week_year', Carbon::now()->year);

        // Determine date range based on mode
        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate = Carbon::parse($dateTo)->endOfDay();
        $periodDescription = '';

        if ($mode === 'week') {
            if ($week) {
                // Parse week string "2023-W01"
                $startDate = Carbon::parse($week)->startOfWeek();
                $endDate = Carbon::parse($week)->endOfWeek();
                $periodDescription = 'Minggu ' . Carbon::parse($week)->weekOfYear . ' (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
            } else {
                // Default to current week
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $periodDescription = 'Minggu Ini';
            }
        } elseif ($mode === 'year') {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
            $periodDescription = 'Tahun ' . $year;
        } else {
            // Range mode
            $periodDescription = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
        }

        // Summary Stats
        $totalActivities = Activity::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $stockIn = Stockin::whereBetween('created_at', [$startDate, $endDate])->sum('stock_qty');
        $stockOut = Stockout::whereBetween('created_at', [$startDate, $endDate])->sum('stock_qty');
        
        $endingStock = Produk::sum('stock_quantity'); 

        $uniqueUsers = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user')
            ->count('user');

        // Table Data (Inventory Summary by Date)
        $tableData = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        
        // Optimization: Fetch aggregated data directly from database
        $activitiesByDate = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');
            
        $stockInByDate = Stockin::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(stock_qty) as total_qty')
            ->groupBy('date')
            ->pluck('total_qty', 'date');
            
        $stockOutByDate = Stockout::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(stock_qty) as total_qty')
            ->groupBy('date')
            ->pluck('total_qty', 'date');

        $salesItemsByDate = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.pos_transaction_id', '=', 'pos_transactions.id')
            ->whereBetween('pos_transactions.created_at', [$startDate, $endDate])
            ->selectRaw('DATE(pos_transactions.created_at) as date, SUM(pos_transaction_items.qty) as total_qty')
            ->groupBy('date')
            ->pluck('total_qty', 'date');

        $chartLabels = [];
        $chartTotal = [];
        $chartStockIn = [];
        $chartStockOut = [];
        $chartEndingStock = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            $dayActivitiesCount = $activitiesByDate->get($dateStr, 0);
            $dayStockIn = $stockInByDate->get($dateStr, 0);
            
            $manualStockOut = $stockOutByDate->get($dateStr, 0);
            $salesStockOut = $salesItemsByDate->get($dateStr, 0);
            $dayStockOut = $manualStockOut + $salesStockOut;
            
            // Chart Data (All days)
            $chartLabels[] = $date->format('d M');
            $chartTotal[] = $dayActivitiesCount;
            $chartStockIn[] = $dayStockIn;
            $chartStockOut[] = $dayStockOut;
            $chartEndingStock[] = $endingStock;

            // Table Data (Only active days)
            if ($dayActivitiesCount > 0 || $dayStockIn > 0 || $dayStockOut > 0) {
                $tableData[] = [
                    'label' => $date->translatedFormat('d M Y'),
                    'total' => $dayActivitiesCount,
                    'stock_in' => $dayStockIn,
                    'stock_out' => $dayStockOut,
                    'ending_stock' => $endingStock, 
                ];
            }
        }

        $chartData = [
            'labels' => $chartLabels,
            'datasets' => [
                'total' => $chartTotal,
                'stock_in' => $chartStockIn,
                'stock_out' => $chartStockOut,
                'ending_stock' => $chartEndingStock,
            ]
        ];

        // Sales Transactions
        $sales = PosTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->with(['creator']) 
            ->withCount('items')
            ->latest()
            ->get()
            ->map(function ($transaction) {
                return [
                    'entry_type' => 'sale',
                    'customer_type' => $transaction->customer_type,
                    'date' => $transaction->created_at->translatedFormat('d M Y'),
                    'timestamp' => $transaction->created_at,
                    'time_input' => $transaction->created_at->format('H:i'),
                    'customer_name' => $transaction->customer_name,
                    'salesperson' => $transaction->creator ? $transaction->creator->name : 'System',
                    'grand_total' => $transaction->grand_total,
                    'subtotal' => $transaction->subtotal,
                    'shipping_cost' => $transaction->shipping_cost,
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                    'items_count' => $transaction->items_count,
                    'payment_method' => $transaction->payment_method,
                    'expense_label' => null, // Ensure key exists
                ];
            });

        $expenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->latest()
            ->get()
            ->map(function ($expense) {
                $time = $expense->created_at ? $expense->created_at->format('H:i:s') : '00:00:00';
                // Ensure expense_date is treated as a date string Y-m-d before appending time
                $dateStr = $expense->expense_date instanceof \Carbon\Carbon 
                    ? $expense->expense_date->format('Y-m-d') 
                    : \Carbon\Carbon::parse($expense->expense_date)->format('Y-m-d');
                
                return [
                    'entry_type' => 'expense',
                    'customer_type' => 'expense',
                    'date' => Carbon::parse($dateStr)->translatedFormat('d M Y'),
                    'timestamp' => Carbon::parse($dateStr . ' ' . $time),
                    'time_input' => $expense->created_at ? $expense->created_at->format('H:i') : '',
                    'expense_label' => $expense->description,
                    'customer_name' => null, // Ensure key exists
                    'notes' => $expense->notes,
                    'grand_total' => $expense->amount,
                    'salesperson' => 'System',
                ];
            });

        $salesTransactions = $sales->concat($expenses)->sortByDesc('timestamp')->values()->all();


        // Chart Data
        // $chartData = $this->getChartData($startDate, $endDate, $mode); // Removed as we calculate it above

        // Options for filters
        $availableYears = range(Carbon::now()->year, Carbon::now()->subYears(5)->year);
        $weekOptions = []; 
        // Generate week options for current year or selected year
        $targetYear = $mode === 'year' ? $year : Carbon::now()->year;
        $weeksInYear = (new Carbon($targetYear . '-01-01'))->weeksInYear;
        for ($i = 1; $i <= $weeksInYear; $i++) {
             $wStart = Carbon::now()->setISODate($targetYear, $i)->startOfWeek();
             $wEnd = Carbon::now()->setISODate($targetYear, $i)->endOfWeek();
             $weekOptions[] = [
                 'value' => $targetYear . '-W' . str_pad($i, 2, '0', STR_PAD_LEFT),
                 'label' => 'Minggu ' . $i . ' (' . $wStart->format('d M') . ' - ' . $wEnd->format('d M') . ')'
             ];
        }

        $weekMonthOptions = [];
        for ($m = 1; $m <= 12; $m++) {
            $weekMonthOptions[] = [
                'value' => $m,
                'label' => Carbon::create(null, $m)->translatedFormat('F')
            ];
        }

        $filters = [
            'mode' => $mode,
            'date_from' => $startDate->toDateString(),
            'date_to' => $endDate->toDateString(),
            'year' => $year,
            'week' => $week,
            'week_month' => $weekMonth,
            'week_year' => $weekYear,
        ];
        
        $filterQuery = $request->query();

        $summary = [
            'totalActivities' => $totalActivities,
            'stockIn' => $stockIn,
            'stockOut' => $stockOut,
            'endingStock' => $endingStock,
            'uniqueUsers' => $uniqueUsers,
        ];

        $recentActivities = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        return view('reports.index', compact(
            'periodDescription',
            'summary',
            'chartData',
            'availableYears',
            'filters',
            'weekOptions',
            'weekMonthOptions',
            'tableData',
            'salesTransactions',
            'filterQuery',
            'recentActivities'
        ));
    }

    private function getChartData($startDate, $endDate, $mode)
    {
        $labels = [];
        $incomeData = [];
        $expenseData = [];

        // Group by day
        $period = CarbonPeriod::create($startDate, $endDate);
        
        // Optimization: Fetch all transactions and expenses once
        $transactions = PosTransaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'paid')
            ->get()
            ->groupBy(function($item) { return $item->created_at->format('Y-m-d'); });

        $expenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get()
            ->groupBy('expense_date');

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            
            $dayIncome = $transactions->get($dateStr, collect())->sum('grand_total');
            $dayExpense = $expenses->get($dateStr, collect())->sum('amount');
            
            $incomeData[] = $dayIncome;
            $expenseData[] = $dayExpense;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pemasukan',
                    'data' => $incomeData,
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $expenseData,
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.4
                ]
            ]
        ];
    }

    public function editSale($id)
    {
        $transaction = PosTransaction::with('items')->findOrFail($id);
        $transactionDate = $transaction->created_at->format('Y-m-d');
        $transactionTime = $transaction->created_at->format('H:i');
        $customerTypes = ['pelanggan', 'agent', 'reseller'];

        return view('reports.sales.edit', compact('transaction', 'transactionDate', 'transactionTime', 'customerTypes'));
    }

    public function updateSale(Request $request, $id)
    {
        $transaction = PosTransaction::with('items')->findOrFail($id);
        
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'transaction_time' => 'nullable',
            'customer_name' => 'required|string',
            'customer_type' => 'required|string',
            'shipping_cost' => 'nullable|numeric',
            'note' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $dateTime = $validated['transaction_date'] . ' ' . ($validated['transaction_time'] ?? '00:00:00');
        
        $transaction->created_at = Carbon::parse($dateTime);
        $transaction->customer_name = $validated['customer_name'];
        $transaction->customer_type = $validated['customer_type'];
        $transaction->shipping_cost = $validated['shipping_cost'] ?? 0;
        $transaction->note = $validated['note'];
        
        $subtotal = 0;
        
        foreach ($request->items as $itemId => $itemData) {
            $item = $transaction->items()->find($itemId);
            if ($item) {
                $item->product_name = $itemData['product_name'];
                $item->qty = $itemData['qty'];
                $item->unit = $itemData['unit'];
                $item->price = $itemData['price'];
                $item->subtotal = $item->qty * $item->price;
                $item->save();
                
                $subtotal += $item->subtotal;
            }
        }
        
        $transaction->subtotal = $subtotal;
        $transaction->grand_total = $subtotal + $transaction->shipping_cost;
        $transaction->save();

        return redirect()->route('reports.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    public function exportSales(Request $request)
    {
        $mode = $request->input('mode', 'range');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());
        $year = $request->input('year', Carbon::now()->year);
        $week = $request->input('week');

        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate = Carbon::parse($dateTo)->endOfDay();

        if ($mode === 'week') {
            if ($week) {
                $startDate = Carbon::parse($week)->startOfWeek();
                $endDate = Carbon::parse($week)->endOfWeek();
            } else {
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
            }
        } elseif ($mode === 'year') {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
        }

        $transactions = PosTransaction::with(['items', 'creator'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->get();

        $data = [];

        foreach ($transactions as $transaction) {
            $firstItem = true;
            foreach ($transaction->items as $item) {
                $shipping = $firstItem ? $transaction->shipping_cost : 0;
                $data[] = [
                    'entry_type' => 'sale',
                    'date' => $transaction->created_at->format('d M Y'),
                    'customer_name' => $transaction->customer_name,
                    'customer_type' => $transaction->customer_type,
                    'product_name' => $item->product_name,
                    'qty' => $item->qty,
                    'satuan' => $item->unit,
                    'price_per_unit' => $item->price,
                    'total_price' => $item->subtotal,
                    'shipping_cost' => $shipping,
                ];
                $firstItem = false;
            }
        }

        foreach ($expenses as $expense) {
             $data[] = [
                'entry_type' => 'expense',
                'date' => Carbon::parse($expense->expense_date)->format('d M Y'),
                'expense_label' => $expense->description,
                'expense_amount' => $expense->amount,
             ];
        }

        // Sort by date descending
        usort($data, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.sales', [
                'data' => $data,
                'start' => $startDate->translatedFormat('d M Y'),
                'end' => $endDate->translatedFormat('d M Y'),
            ]);
            return $pdf->download('laporan-penjualan.pdf');
        }

        return Excel::download(new SalesTransactionExport($data, $startDate, $endDate), 'laporan-penjualan.xlsx');
    }

    public function exportStock(Request $request)
    {
        $mode = $request->input('mode', 'range');
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->toDateString());
        $year = $request->input('year', Carbon::now()->year);
        $week = $request->input('week');

        $startDate = Carbon::parse($dateFrom)->startOfDay();
        $endDate = Carbon::parse($dateTo)->endOfDay();

        if ($mode === 'week') {
            if ($week) {
                $startDate = Carbon::parse($week)->startOfWeek();
                $endDate = Carbon::parse($week)->endOfWeek();
            } else {
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
            }
        } elseif ($mode === 'year') {
            $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
            $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();
        }

        $activitiesByDate = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');
            
        $stockInByDate = Stockin::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(stock_qty) as total_qty')
            ->groupBy('date')
            ->pluck('total_qty', 'date');
            
        $stockOutByDate = Stockout::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(stock_qty) as total_qty')
            ->groupBy('date')
            ->pluck('total_qty', 'date');

        $salesItemsByDate = PosTransactionItem::join('pos_transactions', 'pos_transaction_items.pos_transaction_id', '=', 'pos_transactions.id')
            ->whereBetween('pos_transactions.created_at', [$startDate, $endDate])
            ->selectRaw('DATE(pos_transactions.created_at) as date, SUM(pos_transaction_items.qty) as total_qty')
            ->groupBy('date')
            ->pluck('total_qty', 'date');

        $endingStock = Produk::sum('stock_quantity'); 
        $period = CarbonPeriod::create($startDate, $endDate);
        $tableData = [];

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            $dayActivitiesCount = $activitiesByDate->get($dateStr, 0);
            $dayStockIn = $stockInByDate->get($dateStr, 0);
            
            $manualStockOut = $stockOutByDate->get($dateStr, 0);
            $salesStockOut = $salesItemsByDate->get($dateStr, 0);
            $dayStockOut = $manualStockOut + $salesStockOut;

            if ($dayActivitiesCount > 0 || $dayStockIn > 0 || $dayStockOut > 0) {
                $tableData[] = [
                    'label' => $date->translatedFormat('d M Y'),
                    'total' => $dayActivitiesCount,
                    'stock_in' => $dayStockIn,
                    'stock_out' => $dayStockOut,
                    'ending_stock' => $endingStock, 
                ];
            }
        }

        // Sort by date descending (same as index view)
        $tableData = array_reverse($tableData);

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('reports.pdf.stock', [
                'data' => $tableData,
                'start' => $startDate->translatedFormat('d M Y'),
                'end' => $endDate->translatedFormat('d M Y'),
            ]);
            return $pdf->download('laporan-stok.pdf');
        }

        return Excel::download(new StockMovementExport($tableData, $startDate, $endDate), 'laporan-stok.xlsx');
    }
}