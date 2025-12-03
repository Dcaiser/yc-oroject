<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Tampilkan semua aktivitas.
     */
    public function index(Request $request)
    {
        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        
        // Validate sort column
        $allowedSorts = ['created_at', 'user', 'action', 'model'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortDir = $sortDir === 'asc' ? 'asc' : 'desc';
        
        $query = Activity::orderBy($sortBy, $sortDir);

        // Filter berdasarkan tipe aktivitas
        $filter = $request->get('filter', 'all');
        if ($filter === 'add') {
            $query->where(function ($q) {
                $q->where('action', 'like', '%menambah%')
                  ->orWhere('action', 'like', '%membuat%')
                  ->orWhere('action', 'like', '%Transaksi POS%');
            });
        } elseif ($filter === 'edit') {
            $query->where(function ($q) {
                $q->where('action', 'like', '%mengedit%')
                  ->orWhere('action', 'like', '%memperbarui%')
                  ->orWhere('action', 'like', '%mengubah%');
            });
        } elseif ($filter === 'delete') {
            $query->where('action', 'like', '%menghapus%');
        }

        // Filter berdasarkan sumber (model)
        $source = $request->get('source');
        if ($source && $source !== 'all') {
            $query->where('model', $source);
        }

        // Filter berdasarkan pengguna
        $user = $request->get('user');
        if ($user && $user !== 'all') {
            $query->where('user', $user);
        }

        // Filter berdasarkan rentang tanggal
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Search berdasarkan user, action, atau model
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('user', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Per page option
        $perPage = $request->get('per_page', 15);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array((int)$perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        $activities = $query->paginate($perPage)->withQueryString();

        // Stats
        $stats = [
            'total' => Activity::count(),
            'today' => Activity::whereDate('created_at', now()->toDateString())->count(),
            'this_week' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'actors' => Activity::distinct('user')->count('user'),
        ];

        $recentActivity = Activity::latest()->first();

        // Get unique sources (models) for filter dropdown
        $sources = Activity::distinct()->pluck('model')->filter()->sort()->values();
        
        // Get unique users for filter dropdown
        $users = Activity::distinct()->pluck('user')->filter()->sort()->values();

        // Mapping sumber ke route (untuk link ke data terkait)
        $sourceRoutes = [
            'Produk' => 'products.index',
            'Transaksi' => 'pos.payments',
            'StockIn' => 'invent',
            'StockOut' => 'invent',
            'Supplier' => 'suppliers.index',
            'Kategori' => 'category',
            'Customer' => 'customers.index',
            'User' => 'users.index',
            'PurchaseOrder' => 'invent',
        ];

        return view('activities.index', compact(
            'activities', 
            'stats', 
            'recentActivity', 
            'sources', 
            'users',
            'sourceRoutes',
            'sortBy',
            'sortDir'
        ));
    }

    /**
     * Simpan aktivitas baru (bisa dipanggil dari controller lain).
     */
    public function store($user, $action, $model = null, $record_id = null)
    {
        Activity::create([
            'user' => $user,
            'action' => $action,
            'model' => $model,
            'record_id' => $record_id,
        ]);
    }

    /**
     * Hapus semua aktivitas.
     */
    public function clear()
    {
        Activity::truncate();
        return redirect()->route('activities.index')->with('success', 'Semua aktivitas berhasil dihapus!');
    }

    /**
     * Bulk delete aktivitas terpilih.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->route('activities.index')->with('error', 'Tidak ada aktivitas yang dipilih.');
        }

        Activity::whereIn('id', $ids)->delete();
        
        return redirect()->route('activities.index')->with('success', count($ids) . ' aktivitas berhasil dihapus!');
    }
}
