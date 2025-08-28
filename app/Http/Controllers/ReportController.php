<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $totalProducts = Produk::count();
        $totalSuppliers = Supplier::count();
        $totalCategories = Kategori::count();
        $totalStockItems = (int) Produk::sum('stock_quantity');
        // Hitung nilai persediaan. Jika kolom price tidak ada, fallback ke 0.
        $inventoryValue = 0.0;
        if (Schema::hasColumn('products', 'price')) {
            $inventoryValue = (float) Produk::query()
                ->select(DB::raw('COALESCE(SUM(stock_quantity * price), 0) as total'))
                ->value('total');
        }

        // Produk terbaru + harga: gunakan kolom price jika ada, jika tidak ambil MIN(price) dari tabel prices
        $recentProductsQuery = Produk::with('category')
            ->orderByDesc('id')
            ->limit(10);

        if (Schema::hasColumn('products', 'price')) {
            $recentProducts = $recentProductsQuery->get([
                'id','name','sku','category_id','stock_quantity','satuan','price'
            ]);
        } else {
            $recentProducts = $recentProductsQuery
                ->select(['id','name','sku','category_id','stock_quantity','satuan'])
                ->selectSub(
                    DB::table('prices')
                        ->selectRaw('MIN(price)')
                        ->whereColumn('product_id', 'products.id'),
                    'price'
                )
                ->get();
        }

        $recentSuppliers = Supplier::orderByDesc('id')
            ->limit(10)
            ->get(['id','supplier_code','name','contact_person','phone','email']);

        return view('reports.index', compact(
            'totalProducts',
            'totalSuppliers',
            'totalCategories',
            'totalStockItems',
            'inventoryValue',
            'recentProducts',
            'recentSuppliers'
        ));
    }

    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function show($id) { abort(404); }
    public function edit($id) { abort(404); }
    public function update(Request $request, $id) { abort(404); }
    public function destroy($id) { abort(404); }
}


