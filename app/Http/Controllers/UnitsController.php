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
            'name' => 'required|string|max:255',
            'conversion_to_base' => 'required|numeric|min:0.0001',
        ]);

        Units::create([
            'name' => $validated['name'],
            'conversion_to_base' => $validated['conversion_to_base'],
        ]);

        return redirect()->route('category')->with('success', 'Satuan berhasil ditambahkan');
    }
}
