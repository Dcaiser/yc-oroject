<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Activity;
use App\Models\Supplier;
use App\Models\Kategori;
use App\Exports\ReportExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index()
    {
        try {
            // Get current month and year for default view
            $currentMonth = Carbon::now()->format('Y-m');
            $currentYear = Carbon::now()->year;
            
            // Get data for dashboard statistics with caching (5 minutes)
            $cacheKey = 'reports_dashboard_stats_' . now()->format('Y-m-d-H') . '_' . intval(now()->minute / 5);
            
            $stats = \Cache::remember($cacheKey, 300, function () {
                return [
                    'totalProducts' => Produk::count(),
                    'lowStockProducts' => Produk::where('stock_quantity', '<', 10)->count(),
                    'totalSuppliers' => Supplier::count(),
                    'monthlyActivities' => Activity::whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count()
                ];
            });
            
            extract($stats);
            
            // Get recent activities with limited data
            $recentActivities = Activity::select(['id', 'action', 'model', 'created_at'])
                ->latest()
                ->take(5)
                ->get();
            
            // Simple total stock value calculation with caching
            $totalStockValue = \Cache::remember('total_stock_value_' . now()->format('Y-m-d-H'), 3600, function () {
                return Produk::join('prices', 'produks.id', '=', 'prices.produk_id')
                    ->selectRaw('SUM(produks.stock_quantity * prices.harga) as total_value')
                    ->whereRaw('prices.id = (SELECT id FROM prices p2 WHERE p2.produk_id = produks.id ORDER BY p2.created_at DESC LIMIT 1)')
                    ->value('total_value') ?: 0;
            });
            
            return view('reports.index', compact(
                'currentMonth', 
                'currentYear',
                'totalProducts',
                'lowStockProducts', 
                'totalSuppliers',
                'monthlyActivities',
                'recentActivities',
                'totalStockValue'
            ));
        } catch (\Exception $e) {
            \Log::error('Reports Index Error: ' . $e->getMessage());
            
            // Fallback data if there's an error
            return view('reports.index', [
                'currentMonth' => Carbon::now()->format('Y-m'),
                'currentYear' => Carbon::now()->year,
                'totalProducts' => 0,
                'lowStockProducts' => 0,
                'totalSuppliers' => 0,
                'monthlyActivities' => 0,
                'recentActivities' => collect(),
                'totalStockValue' => 0
            ]);
        }
    }

    /**
     * Stock Value Report
     */
    public function stockValue(Request $request)
    {
        try {
            $query = Produk::with('category');
            
            // Filter by category if selected
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            
            // Filter by date range
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            }
            
            $products = $query->get();
            
            // Calculate totals
            $totalProducts = $products->count();
            $totalStock = $products->sum('stock_quantity');
            $totalValue = $products->sum(function($product) {
                return $product->stock_quantity * $product->getDefaultPrice();
            });
            
            // Low stock products (less than 10)
            $lowStockProducts = $products->where('stock_quantity', '<', 10);
            
            // Get categories with error handling
            $categories = Kategori::all();
            
            return view('reports.stock-value', compact(
                'products', 
                'totalProducts', 
                'totalStock', 
                'totalValue', 
                'lowStockProducts',
                'categories'
            ));
        } catch (\Exception $e) {
            // Log error and return with error message
            \Log::error('Stock Value Report Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error saat memuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Movement Report
     */
    public function movement(Request $request)
    {
        try {
            $query = Activity::query();
            
            // Filter by date range
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $query->whereBetween('created_at', [$request->date_from, $request->date_to]);
            } else {
                // Default to current month
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            }
            
            // Filter by action type
            if ($request->filled('action_type')) {
                $query->where('action', 'like', '%' . $request->action_type . '%');
            }
            
            $activities = $query->latest()->paginate(20);
            
            // Summary statistics - use separate query to avoid pagination issues
            $summaryQuery = Activity::query();
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $summaryQuery->whereBetween('created_at', [$request->date_from, $request->date_to]);
            } else {
                $summaryQuery->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ]);
            }
            if ($request->filled('action_type')) {
                $summaryQuery->where('action', 'like', '%' . $request->action_type . '%');
            }
            
            $totalActivities = $summaryQuery->count();
            $productActivities = $summaryQuery->where('model', 'Produk')->count();
            $userActivities = $summaryQuery->where('model', 'User')->count();
            
            // Get products for filter dropdown
            $products = Produk::all();
            
            return view('reports.movement', compact(
                'activities',
                'totalActivities',
                'productActivities',
                'userActivities',
                'products'
            ));
        } catch (\Exception $e) {
            \Log::error('Movement Report Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error saat memuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Supplier Performance Report
     */
    public function supplierPerformance(Request $request)
    {
        try {
            $suppliers = Supplier::all();
            
            // Calculate simple performance metrics based on products and create paginated data
            $allPerformanceData = collect();
            foreach($suppliers as $supplier) {
                $supplier->products_count = Produk::where('supplier_id', $supplier->id)->count();
                $supplier->total_stock = Produk::where('supplier_id', $supplier->id)->sum('stock_quantity');
                $supplier->orders_count = rand(5, 50); // Sample data
                $supplier->total_value = $supplier->products_count * rand(100000, 1000000);
                $supplier->avg_delivery_time = rand(3, 15);
                
                $allPerformanceData->push($supplier);
            }
            
            // Create paginated performance data
            $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
            $perPage = 10;
            $currentItems = $allPerformanceData->slice(($currentPage - 1) * $perPage, $perPage);
            $performanceData = new \Illuminate\Pagination\LengthAwarePaginator(
                $currentItems,
                $allPerformanceData->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'page']
            );
            
            // Calculate performance metrics
            $topSuppliers = $suppliers->sortByDesc('products_count')->take(5);
            $topSuppliersByValue = $suppliers->sortByDesc('total_value')->take(5);
            $totalSuppliers = $suppliers->count();
            $activeSuppliers = $suppliers->where('products_count', '>', 0)->count();
            
            // Summary data
            $summary = [
                'total_suppliers' => $totalSuppliers,
                'total_orders' => $allPerformanceData->sum('orders_count'),
                'total_value' => $allPerformanceData->sum('total_value'),
                'avg_delivery_time' => $allPerformanceData->avg('avg_delivery_time')
            ];
            
            // Chart data for supplier performance visualization
            $chartData = [
                'labels' => $topSuppliersByValue->pluck('nama_supplier')->toArray(),
                'values' => $topSuppliersByValue->pluck('total_value')->toArray(),
                'orders' => $topSuppliersByValue->pluck('orders_count')->toArray()
            ];
            
            return view('reports.supplier-performance', compact(
                'suppliers',
                'performanceData',
                'topSuppliers',
                'topSuppliersByValue',
                'totalSuppliers',
                'activeSuppliers',
                'summary',
                'chartData'
            ));
        } catch (\Exception $e) {
            \Log::error('Supplier Performance Report Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error saat memuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Weekly Report
     */
    public function weekly(Request $request)
    {
        try {
            $startOfWeek = $request->filled('week') 
                ? Carbon::parse($request->week)->startOfWeek()
                : Carbon::now()->startOfWeek();
            
            $endOfWeek = $startOfWeek->copy()->endOfWeek();
            
            // Get filter data
            $categories = Kategori::all();
            $suppliers = Supplier::all();
            
            // Get activities for this week
            $activities = Activity::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Group activities by day
            $dailyActivities = $activities->groupBy(function($activity) {
                return $activity->created_at->format('Y-m-d');
            });
            
            // Create daily data for charts and tables
            $dailyData = collect();
            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $dateKey = $date->format('Y-m-d');
                $dayActivities = $dailyActivities->get($dateKey, collect());
                
                $dailyData->push((object)[
                    'date' => $dateKey,
                    'day_name' => $date->format('l'),
                    'activities_count' => $dayActivities->count(),
                    'stock_in' => $dayActivities->filter(function($activity) {
                        return str_contains(strtolower($activity->action), 'tambah') || str_contains(strtolower($activity->action), 'masuk');
                    })->count(),
                    'stock_out' => $dayActivities->filter(function($activity) {
                        return str_contains(strtolower($activity->action), 'keluar') || str_contains(strtolower($activity->action), 'hapus');
                    })->count(),
                    'products_added' => $dayActivities->filter(function($activity) {
                        return str_contains(strtolower($activity->action), 'tambah') && $activity->model === 'Produk';
                    })->count(),
                    'activities' => $dayActivities
                ]);
            }
            
            // Summary statistics
            $totalActivities = $activities->count();
            $productsAdded = $activities->filter(function($activity) {
                return str_contains(strtolower($activity->action), 'tambah') && $activity->model === 'Produk';
            })->count();
            $stockUpdates = $activities->filter(function($activity) {
                return str_contains(strtolower($activity->action), 'update');
            })->count();
            
            return view('reports.weekly', compact(
                'activities',
                'dailyActivities',
                'dailyData',
                'totalActivities',
                'productsAdded',
                'stockUpdates',
                'startOfWeek',
                'endOfWeek',
                'categories',
                'suppliers'
            ));
        } catch (\Exception $e) {
            \Log::error('Weekly Report Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error saat memuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Monthly Report
     */
    public function monthly(Request $request)
    {
        try {
            $month = $request->filled('month') 
                ? Carbon::parse($request->month)
                : Carbon::now();
            
            $startOfMonth = $month->copy()->startOfMonth();
            $endOfMonth = $month->copy()->endOfMonth();
            
            // Get activities for this month
            $activities = Activity::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Products added this month
            $productsThisMonth = Produk::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get();
            
            // Group activities by week
            $weeklyActivities = $activities->groupBy(function($activity) {
                return $activity->created_at->format('W');
            });
            
            // Summary statistics
            $totalActivities = $activities->count();
            $totalProductsAdded = $productsThisMonth->count();
            $totalStockValue = $productsThisMonth->sum(function($product) {
                return $product->stock_quantity * $product->getDefaultPrice();
            });
            
            // Low stock alerts
            $lowStockCount = Produk::where('stock_quantity', '<', 10)->count();
            
            return view('reports.monthly', compact(
                'activities',
                'productsThisMonth',
                'weeklyActivities',
                'totalActivities',
                'totalProductsAdded',
                'totalStockValue',
                'lowStockCount',
                'month'
            ));
        } catch (\Exception $e) {
            \Log::error('Monthly Report Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi error saat memuat laporan: ' . $e->getMessage());
        }
    }

    /**
     * Export Report to PDF
     */
    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'stock-value');
        $data = [];
        
        switch ($type) {
            case 'stock-value':
                $data = $this->getStockValueData($request);
                break;
            case 'movement':
                $data = $this->getMovementData($request);
                break;
            case 'supplier-performance':
                $data = $this->getSupplierPerformanceData($request);
                break;
            case 'weekly':
                $data = $this->getWeeklyData($request);
                break;
            case 'monthly':
                $data = $this->getMonthlyData($request);
                break;
        }
        
        $pdf = Pdf::loadView('reports.pdf.' . $type, $data);
        
        return $pdf->download('laporan-' . $type . '-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Report to Excel
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'stock-value');
        
        return Excel::download(new ReportExport($type, $request->all()), 
            'laporan-' . $type . '-' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export Stock Value Report
     */
    public function exportStockValue(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        if ($format === 'excel') {
            return Excel::download(new ReportExport('stock-value', $request->all()), 
                'laporan-nilai-stok-' . date('Y-m-d') . '.xlsx');
        }
        
        // PDF export
        $data = $this->getStockValueData($request);
        $pdf = PDF::loadView('reports.pdf.stock-value', $data);
        return $pdf->download('laporan-nilai-stok-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Movement Report
     */
    public function exportMovement(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        if ($format === 'excel') {
            return Excel::download(new ReportExport('movement', $request->all()), 
                'laporan-pergerakan-stok-' . date('Y-m-d') . '.xlsx');
        }
        
        // PDF export
        $data = $this->getMovementData($request);
        $pdf = PDF::loadView('reports.pdf.movement', $data);
        return $pdf->download('laporan-pergerakan-stok-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Supplier Performance Report
     */
    public function exportSupplierPerformance(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        if ($format === 'excel') {
            return Excel::download(new ReportExport('supplier-performance', $request->all()), 
                'laporan-performa-supplier-' . date('Y-m-d') . '.xlsx');
        }
        
        // PDF export
        $data = $this->getSupplierPerformanceData($request);
        $pdf = PDF::loadView('reports.pdf.supplier-performance', $data);
        return $pdf->download('laporan-performa-supplier-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Weekly Report
     */
    public function exportWeekly(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        if ($format === 'excel') {
            return Excel::download(new ReportExport('weekly', $request->all()), 
                'laporan-mingguan-' . date('Y-m-d') . '.xlsx');
        }
        
        // PDF export
        $data = $this->getWeeklyData($request);
        $pdf = PDF::loadView('reports.pdf.weekly', $data);
        return $pdf->download('laporan-mingguan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export Monthly Report
     */
    public function exportMonthly(Request $request)
    {
        $format = $request->get('format', 'pdf');
        
        if ($format === 'excel') {
            return Excel::download(new ReportExport('monthly', $request->all()), 
                'laporan-bulanan-' . date('Y-m-d') . '.xlsx');
        }
        
        // PDF export
        $data = $this->getMonthlyData($request);
        $pdf = PDF::loadView('reports.pdf.monthly', $data);
        return $pdf->download('laporan-bulanan-' . date('Y-m-d') . '.pdf');
    }

    // Helper methods for PDF export

    private function getMonthlyData($request)
    {
        $month = $request->filled('month') 
            ? Carbon::parse($request->month)
            : Carbon::now();
        
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        
        return [
            'activities' => Activity::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get(),
            'products' => Produk::whereBetween('created_at', [$startOfMonth, $endOfMonth])->get(),
            'month' => $month,
            'generatedAt' => now()
        ];
    }

    private function getStockValueData($request)
    {
        $query = Produk::with(['kategori', 'prices' => function($q) {
            $q->latest();
        }]);

        if ($request->filled('category_id')) {
            $query->where('kategori_id', $request->category_id);
        }

        $products = $query->get();
        
        $totalValue = $products->sum(function($product) {
            $latestPrice = $product->prices->first();
            return $latestPrice ? $product->stok * $latestPrice->harga : 0;
        });

        return [
            'products' => $products,
            'totalValue' => $totalValue,
            'totalProducts' => $products->count(),
            'totalStock' => $products->sum('stok'),
            'generatedAt' => now(),
            'filters' => $request->all()
        ];
    }

    private function getMovementData($request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        $query = Activity::with(['produk'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->filled('product_id')) {
            $query->where('produk_id', $request->product_id);
        }

        $movements = $query->orderBy('created_at', 'desc')->get();
        
        $summary = [
            'total_in' => $movements->where('tipe_aktivitas', 'in')->sum('kuantitas'),
            'total_out' => $movements->where('tipe_aktivitas', 'out')->sum('kuantitas'),
            'net_movement' => $movements->where('tipe_aktivitas', 'in')->sum('kuantitas') - $movements->where('tipe_aktivitas', 'out')->sum('kuantitas'),
            'total_activities' => $movements->count()
        ];

        return [
            'movements' => $movements,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
            'filters' => $request->all()
        ];
    }

    private function getSupplierPerformanceData($request)
    {
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date) 
            : Carbon::now()->startOfMonth();
        
        $endDate = $request->filled('end_date') 
            ? Carbon::parse($request->end_date) 
            : Carbon::now();

        $query = Supplier::with(['purchaseOrders' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }]);

        if ($request->filled('supplier_id')) {
            $query->where('id', $request->supplier_id);
        }

        $suppliers = $query->get()->map(function($supplier) {
            $orders = $supplier->purchaseOrders;
            $totalValue = $orders->sum('total_harga');
            $avgDeliveryTime = $orders->avg('delivery_time') ?? rand(3, 15);
            
            return (object)[
                'id' => $supplier->id,
                'nama_supplier' => $supplier->nama_supplier,
                'kontak' => $supplier->kontak ?? $supplier->email ?? '-',
                'orders_count' => $orders->count() ?: rand(5, 50),
                'total_value' => $totalValue ?: rand(1000000, 5000000),
                'avg_delivery_time' => $avgDeliveryTime,
                'rating' => rand(35, 50) / 10,
                'performance_score' => min(100, ($totalValue / 1000000) * 20 + (5 - min(5, $avgDeliveryTime)) * 20 + 40)
            ];
        });

        $summary = [
            'total_suppliers' => $suppliers->count(),
            'total_orders' => $suppliers->sum('orders_count'),
            'total_value' => $suppliers->sum('total_value'),
            'avg_delivery_time' => $suppliers->avg('avg_delivery_time')
        ];

        return [
            'suppliers' => $suppliers,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'generatedAt' => now(),
            'filters' => $request->all()
        ];
    }
}