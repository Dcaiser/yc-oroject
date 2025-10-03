<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Stockin;
use App\Models\Units;

use App\Models\Supplier;
use App\Models\Price;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ActivityController;



class InventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Query data dengan kondisi search
        $products = Produk::with('category') // ambil data kategori juga
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        })
        ->paginate(10);
        $units = Units::all();
        $supplier = Supplier::all();
        $category = Kategori::all();
        return view('inventory.index', compact(['products','category','units','supplier']));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stock = Stockin::all();
        return view('inventory.invent_activity',compact(['stock']));

    }
    public function createStock()
    {
        $units = Units::all();
        $produk = Produk::all();
        $category = Kategori::all();
        return view('inventory.create_stock',compact(['produk', 'category', 'units']));

    }
    public function updateStock(request $request)
    {
           $validated = $request->validate([
        'name_p'   => 'required|exists:products,id',
        'harga_p'  => 'required|numeric|min:0',
        'stok'     => 'required|integer|min:1',
        'satuan'   => 'required|exists:units,id',
        'harga_t'  => 'required|numeric|min:0',
        'bukti'    => 'nullable|image|max:2048',
    ]);

    // Ambil produk dan satuan
    $produk = \App\Models\Produk::findOrFail($validated['name_p']);
    $unit   = \App\Models\Units::findOrFail($validated['satuan']);

    // Simpan bukti jika ada
    $buktiPath = null;
    if ($request->hasFile('bukti')) {
        $buktiPath = $request->file('bukti')->store('bukti', 'public');
    }

    // Catat stok masuk
    Stockin::create([
        'product_name'  => $produk->name,
        'supplier_name' => optional($produk->supplier)->name,
        'stock_qty'     => $validated['stok'],
        'satuan'        => $unit->name,
        'prices'        => $validated['harga_p'],
        'total_price'   => $validated['harga_t'],
        'bukti'         => $buktiPath,
    ]);

    // Update stok produk
    $produk->stock_quantity += $validated['stok'];
    $produk->satuan = $unit->id;
    $produk->save();

    // Catat aktivitas
    Activity::create([
        'user'       => Auth::check() ? Auth::user()->name : 'Guest',
        'action'     => 'Menambah stok',
        'model'      => 'inventori',
        'record_id'  => $produk->id,
    ]);

    return redirect()->route('invent')->with('success', 'Berhasil menambahkan stok');
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
    public function update(Request $request)
    {
 $produkData = $request->input('produk', []);

    foreach ($produkData as $id => $data) {
        $product = Produk::find($id);

        if ($product) {
            // update data produk utama
            $product->update([
                'name'           => $data['name'] ?? $product->name,
                'sku'            => $data['sku'] ?? $product->sku,
                'stock_quantity' => $data['stock_quantity'] ?? $product->stock_quantity,
                'satuan'         => $data['satuan'] ?? $product->satuan,
                'category_id'    => $data['category_id'] ?? $product->category_id,
                'description'    => $data['description'] ?? $product->description,
            ]);



            // update harga (agen, reseller, pelanggan)
            if (isset($data['prices'])) {
                foreach ($data['prices'] as $customertype => $price) {
                    $product->prices()->updateOrCreate(
                        ['customer_type' => $customertype], // kondisi cari dulu
                        ['price' => $price] // update/insert harga
                    );
                }
            }
        }
    }

    return redirect()->back()->with('success', 'Semua produk berhasil diperbarui!');    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Produk::findOrFail($id);
        $data->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil dihapus'
        ]);
    }
    public function deleteall() {
        Produk::query()->delete();
        return redirect()->back()->with('success', 'Semua data berhasil dihapus!');

    }
}
