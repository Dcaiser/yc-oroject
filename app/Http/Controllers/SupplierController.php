<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Tampilkan semua supplier + cari
    public function index(Request $request)
    {
        $query = Supplier::query();

    $searchTerm = $request->string('search')->trim();
    $searchValue = $searchTerm->value();
        $activeFilter = $request->get('filter', 'all');
        $allowedFilters = ['all', 'recent', 'missing-contact', 'with-po'];
        if (! in_array($activeFilter, $allowedFilters, true)) {
            $activeFilter = 'all';
        }

        if ($searchValue !== '') {
            $query->where(function ($subQuery) use ($searchValue) {
                $subQuery->where('name', 'like', "%{$searchValue}%")
                         ->orWhere('supplier_code', 'like', "%{$searchValue}%")
                         ->orWhere('contact_person', 'like', "%{$searchValue}%");
            });
        }

        switch ($activeFilter) {
            case 'recent':
                $query->where('created_at', '>=', now()->subDays(30));
                break;
            case 'missing-contact':
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('phone')
                             ->orWhere('phone', '')
                             ->orWhereNull('email')
                             ->orWhere('email', '');
                });
                break;
            case 'with-po':
                $query->whereHas('purchaseOrders');
                break;
        }

        $suppliers = $query
            ->withCount('purchaseOrders')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => Supplier::count(),
            'newThisMonth' => Supplier::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'activeVendors' => Supplier::whereHas('purchaseOrders')->count(),
            'missingContacts' => Supplier::where(function ($subQuery) {
                $subQuery->whereNull('phone')
                         ->orWhere('phone', '')
                         ->orWhereNull('email')
                         ->orWhere('email', '');
            })->count(),
        ];

        return view('supplier.index', [
            'suppliers' => $suppliers,
            'stats' => $stats,
            'activeFilter' => $activeFilter,
            'search' => $searchValue,
        ]);
    }

    // Form tambah supplier
    public function create()
    {
        return view('supplier.create');
    }

    // Simpan supplier baru
    public function store(SupplierRequest $request)
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    // Tampilkan detail supplier
    public function show(Supplier $supplier)
    {
        return view('supplier.show', compact('supplier'));
    }

    // Form edit supplier
    public function edit(Supplier $supplier)
    {
        return view('supplier.edit', compact('supplier'));
    }

    // Update supplier
    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    // Hapus supplier
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
