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

        // Jika ada pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('supplier_code', 'like', '%' . $request->search . '%');
        }

        // Kalau mau pakai pagination (lebih rapi)
        $suppliers = $query->latest()->paginate(10);

        return view('supplier.index', compact('suppliers'));
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
