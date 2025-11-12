<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Units;
use App\Models\Customer;

class Categorycontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $category = Kategori::orderBy('name')->get();
    // load units so the view can show 'Satuan' table below categories
    $units = Units::orderBy('name')->get();
    $customers = Customer::orderBy('customer_name')->get();

    return view('category.index', compact('category', 'units', 'customers'));
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
            'nama-kategori' => 'required|string|max:255',
            'deskripsi-kategori' => 'nullable|string',
        ]);

        $cate = Kategori::create([
            'name' => $validated['nama-kategori'],
            'description' => $validated['deskripsi-kategori'],
        ]);

        return redirect()->route('category')->with('success', 'sukses menambahkan kategori baru');
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
    public function destroy($id)
    {
        $data = Kategori::findOrFail($id);
        $data->delete();

        return redirect()->route('category')->with('success', 'berhasil dihapus');
    }
}
