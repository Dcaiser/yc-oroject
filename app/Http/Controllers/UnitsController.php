<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Units;

class UnitsController extends Controller
{
    /**
     * Show the form to create a new unit.
     */
    public function create()
    {
        return view('units.create');
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name',
            'conversion_to_base' => 'required|numeric|min:0.0001',
        ]);

        Units::create([
            'name' => $validated['name'],
            'conversion_to_base' => $validated['conversion_to_base'],
        ]);

        return redirect()->route('category', ['tab' => 'unit'])
            ->with('success', 'Satuan berhasil ditambahkan');
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $id,
            'conversion_to_base' => 'required|numeric|min:0.0001',
        ]);

        $unit = Units::findOrFail($id);
        $unit->update([
            'name' => $validated['name'],
            'conversion_to_base' => $validated['conversion_to_base'],
        ]);

        return redirect()->route('category', ['tab' => 'unit'])
            ->with('success', 'Satuan berhasil diperbarui');
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(string $id)
    {
        $unit = Units::findOrFail($id);
        
        // Check if unit is in use
        if ($unit->produk()->count() > 0) {
            return redirect()->route('category', ['tab' => 'unit'])
                ->with('error', 'Tidak dapat menghapus satuan yang masih digunakan produk');
        }

        $unit->delete();

        return redirect()->route('category', ['tab' => 'unit'])
            ->with('success', 'Satuan berhasil dihapus');
    }

    /**
     * Bulk delete units
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:units,id',
        ]);

        $ids = $validated['ids'];
        
        // Check for units in use
        $unitsInUse = Units::whereIn('id', $ids)
            ->has('produk')
            ->count();

        if ($unitsInUse > 0) {
            return redirect()->route('category', ['tab' => 'unit'])
                ->with('error', "Tidak dapat menghapus {$unitsInUse} satuan yang masih digunakan produk");
        }

        $deleted = Units::whereIn('id', $ids)->delete();

        return redirect()->route('category', ['tab' => 'unit'])
            ->with('success', "{$deleted} satuan berhasil dihapus");
    }
}
