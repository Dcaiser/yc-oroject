<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Units;
use App\Models\Produk;

class Categorycontroller extends Controller
{
    /**
     * Display a listing of categories and units with stats.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'category');
        $search = $request->get('search', '');
        $perPage = 12;

        // Build category query with search and product count
        $categoryQuery = Kategori::withCount('products')->orderBy('name');
        if ($search && $tab === 'category') {
            $categoryQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        $categories = $categoryQuery->paginate($perPage)->appends(['tab' => 'category', 'search' => $search]);

        // Build units query with search and product count
        $unitsQuery = Units::withCount('produk')->orderBy('name');
        if ($search && $tab === 'unit') {
            $unitsQuery->where('name', 'like', "%{$search}%");
        }
        $units = $unitsQuery->paginate($perPage)->appends(['tab' => 'unit', 'search' => $search]);

        // Stats for dashboard cards
        $stats = [
            'total_categories' => Kategori::count(),
            'categories_with_products' => Kategori::has('products')->count(),
            'empty_categories' => Kategori::doesntHave('products')->count(),
            'total_units' => Units::count(),
            'units_in_use' => Units::has('produk')->count(),
            'most_used_category' => Kategori::withCount('products')
                ->orderByDesc('products_count')
                ->first(),
            'most_used_unit' => Units::withCount('produk')
                ->orderByDesc('produk_count')
                ->first(),
        ];

        return view('category.index', compact('categories', 'units', 'stats', 'tab', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        Kategori::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('category', ['tab' => 'category'])
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:500',
        ]);

        $category = Kategori::findOrFail($id);
        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('category', ['tab' => 'category'])
            ->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Kategori::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('category', ['tab' => 'category'])
                ->with('error', 'Tidak dapat menghapus kategori yang masih memiliki produk');
        }

        $category->delete();

        return redirect()->route('category', ['tab' => 'category'])
            ->with('success', 'Kategori berhasil dihapus');
    }

    /**
     * Bulk delete categories
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:categories,id',
        ]);

        $ids = $validated['ids'];
        
        // Check for categories with products
        $categoriesWithProducts = Kategori::whereIn('id', $ids)
            ->has('products')
            ->count();

        if ($categoriesWithProducts > 0) {
            return redirect()->route('category', ['tab' => 'category'])
                ->with('error', "Tidak dapat menghapus {$categoriesWithProducts} kategori yang masih memiliki produk");
        }

        $deleted = Kategori::whereIn('id', $ids)->delete();

        return redirect()->route('category', ['tab' => 'category'])
            ->with('success', "{$deleted} kategori berhasil dihapus");
    }
}
