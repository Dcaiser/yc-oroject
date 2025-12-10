<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Customer;
use App\Models\PosTransaction;
use App\Models\Produk;
use App\Models\User;

class DashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $lowStockThreshold = config('inventory.low_stock_threshold', 20);

        $stats = [
            'total_products' => Produk::count(),
            'total_stock' => (int) Produk::sum('stock_quantity'),
            'low_stock' => Produk::where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', $lowStockThreshold)
                ->count(),
            'out_of_stock' => Produk::where('stock_quantity', '<=', 0)->count(),
            'total_customers' => Customer::count(),
        ];

        $inventoryAlerts = Produk::select('id', 'name', 'stock_quantity', 'satuan')
            ->whereNotNull('stock_quantity')
            ->where('stock_quantity', '<=', $lowStockThreshold)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get();

        $todayActivitiesCount = Activity::whereDate('created_at', today())->count();

        $recentActivities = Activity::latest()->limit(7)->get();

        $salesSummary = $this->buildSalesSummary();

        $quickActions = $this->quickActionsForRole($user);

        $opsPulse = $this->buildOpsPulse(
            $todayActivitiesCount,
            $salesSummary['pending'] ?? 0,
            $inventoryAlerts->count()
        );

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'inventoryAlerts' => $inventoryAlerts,
            'recentActivities' => $recentActivities,
            'salesSummary' => $salesSummary,
            'quickActions' => $quickActions,
            'opsPulse' => $opsPulse,
            'lowStockThreshold' => $lowStockThreshold,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    protected function buildSalesSummary(): array
    {
        $totalSalesToday = PosTransaction::whereDate('created_at', today())->sum('grand_total');
        $ordersToday = PosTransaction::whereDate('created_at', today())->count();

        $pendingPayments = PosTransaction::where(function ($query) {
            $query->whereIn('status', ['pending', 'unpaid'])
                ->orWhereNull('status')
                ->orWhere('balance_due', '>', 0);
        })->count();

        $paidRate = $ordersToday > 0
            ? max(0, min(100, round((($ordersToday - $pendingPayments) / $ordersToday) * 100)))
            : 100;

        return [
            'total' => (int) $totalSalesToday,
            'orders' => $ordersToday,
            'pending' => $pendingPayments,
            'paid_rate' => $paidRate,
        ];
    }

    protected function buildOpsPulse(int $todayActivities, int $pendingPayments, int $attentionSku): array
    {
        return [
            [
                'label' => 'Aktivitas Hari Ini',
                'value' => $todayActivities,
                'icon' => 'fa-clipboard-list',
                'accent' => 'amber',
                'hint' => 'Log yang tercatat hari ini',
            ],
            [
                'label' => 'Order Belum Lunas',
                'value' => $pendingPayments,
                'icon' => 'fa-receipt',
                'accent' => 'rose',
                'hint' => 'Transaksi yang masih menunggu pembayaran',
            ],
            [
                'label' => 'Stok Produk Rendah',
                'value' => $attentionSku,
                'icon' => 'fa-triangle-exclamation',
                'accent' => 'indigo',
                'hint' => 'Produk mendekati batas stok aman',
            ],
        ];
    }

    protected function quickActionsForRole(User $user): array
    {
        $actions = [
            'admin' => [
                [
                    'label' => 'Kelola Pengguna',
                    'description' => 'Tambah, ubah, atau hapus Akun Pengguna',
                    'icon' => 'fa-user-gear',
                    'url' => route('users.index'),
                    'style' => 'bg-indigo-500/10 text-indigo-600',
                ],
                [
                    'label' => 'Laporan',
                    'description' => 'Lihat ringkasan penjualan dan stok',
                    'icon' => 'fa-chart-line',
                    'url' => route('reports.index'),
                    'style' => 'bg-emerald-500/10 text-emerald-600',
                ],
                [
                    'label' => 'Kelola Supplier',
                    'description' => 'Atur pemasok barang dan informasi lainnya',
                    'icon' => 'fa-truck-fast',
                    'url' => route('suppliers.index'),
                    'style' => 'bg-amber-500/10 text-amber-600',
                ],
                [
                    'label' => 'Aktivitas',
                    'description' => 'Pantau perubahan apa saja di sistem',
                    'icon' => 'fa-history',
                    'url' => route('activities.index'),
                    'style' => 'bg-rose-500/10 text-rose-600',
                ],
            ],
            'manager' => [
                [
                    'label' => 'Inventori',
                    'description' => 'Cek stok dan nilai barang',
                    'icon' => 'fa-boxes-stacked',
                    'url' => route('invent'),
                    'style' => 'bg-blue-500/10 text-blue-600',
                ],
                [
                    'label' => 'Katalog Produk',
                    'description' => 'Atur info serta harga produk',
                    'icon' => 'fa-tags',
                    'url' => route('products.index'),
                    'style' => 'bg-purple-500/10 text-purple-600',
                ],
                [
                    'label' => 'Laporan Penjualan',
                    'description' => 'Unduh ringkasan penjualan',
                    'icon' => 'fa-chart-line',
                    'url' => route('reports.index'),
                    'style' => 'bg-teal-500/10 text-teal-600',
                ],
                [
                    'label' => 'Tambah Stok',
                    'description' => 'Catat barang masuk hari ini',
                    'icon' => 'fa-arrow-up',
                    'url' => route('stock.create'),
                    'style' => 'bg-amber-500/10 text-amber-600',
                ],
            ],
            'staff' => [
                [
                    'label' => 'Kasir POS',
                    'description' => 'Buka transaksi penjualan',
                    'icon' => 'fa-cash-register',
                    'url' => route('pos'),
                    'style' => 'bg-emerald-500/10 text-emerald-600',
                ],
                [
                    'label' => 'Data Pelanggan',
                    'description' => 'Simpan dan perbarui kontak',
                    'icon' => 'fa-address-book',
                    'url' => route('customers.index'),
                    'style' => 'bg-sky-500/10 text-sky-600',
                ],
                [
                    'label' => 'Catatan Inventori',
                    'description' => 'Laporkan kondisi stok lapangan',
                    'icon' => 'fa-clipboard-list',
                    'url' => route('invent_notes'),
                    'style' => 'bg-orange-500/10 text-orange-600',
                ],
                [
                    'label' => 'Aktivitas Sistem',
                    'description' => 'Lihat aktivitas terbaru tim',
                    'icon' => 'fa-clipboard-check',
                    'url' => route('activities.index'),
                    'style' => 'bg-fuchsia-500/10 text-fuchsia-600',
                ],
            ],
        ];

        return $actions[$user->role] ?? [
            [
                'label' => 'Mulai Penjualan',
                'description' => 'Buka halaman kasir POS',
                'icon' => 'fa-shopping-cart',
                'url' => route('pos'),
                'style' => 'bg-indigo-500/10 text-indigo-600',
            ],
            [
                'label' => 'Inventori',
                'description' => 'Lihat stok terbaru',
                'icon' => 'fa-box',
                'url' => route('invent'),
                'style' => 'bg-blue-500/10 text-blue-600',
            ],
        ];
    }
}
