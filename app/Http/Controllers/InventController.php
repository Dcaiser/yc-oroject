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
        $categoryFilter = $request->input('category', 'all');
        $stockFilter = $request->input('stock', 'all');
        $perPage = (int) $request->input('per_page', 12);
        $perPageOptions = [12, 24, 36];

        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = 12;
        }

        $productsQuery = $this->buildInventoryQuery($search, $categoryFilter, $stockFilter);

        $products = $productsQuery->paginate($perPage)->withQueryString();

        $inventoryStats = [
            'total_sku' => Produk::count(),
            'total_stock' => Produk::sum('stock_quantity') ?? 0,
            'low_stock' => Produk::whereBetween('stock_quantity', [1, 10])->count(),
            'out_of_stock' => Produk::where('stock_quantity', '<=', 0)->count(),
        ];

        $units = Units::all();
        $supplier = Supplier::all();
        $category = Kategori::all();

        return view('inventory.index', compact(
            'products',
            'category',
            'units',
            'supplier',
            'inventoryStats',
            'categoryFilter',
            'stockFilter',
            'perPage',
            'perPageOptions',
            'search'
        ));
    }

    protected function buildInventoryQuery(?string $search, string $categoryFilter, string $stockFilter)
    {
        return Produk::with(['category', 'units', 'prices'])
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($categoryFilter !== 'all', function ($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            })
            ->when($stockFilter === 'low', function ($query) {
                $query->whereBetween('stock_quantity', [1, 10]);
            })
            ->when($stockFilter === 'out', function ($query) {
                $query->where('stock_quantity', '<=', 0);
            })
            ->when($stockFilter === 'safe', function ($query) {
                $query->where('stock_quantity', '>', 10);
            })
            ->orderBy('name');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $stock = Stockin::orderByDesc('created_at')->get();

        $groupedStock = $stock
            ->groupBy(function ($item) {
                return optional($item->created_at)->format('Y-m-d') ?: 'unknown';
            })
            ->sortKeysUsing(function ($a, $b) {
                if ($a === 'unknown') {
                    return 1;
                }
                if ($b === 'unknown') {
                    return -1;
                }

                return strcmp($b, $a);
            })
            ->map(function ($entries) {
                return $entries->sortByDesc('created_at')->values();
            });

        return view('inventory.invent_activity', compact(['stock', 'groupedStock']));

    }
    public function createStock()
    {
        $units = Units::all();
        $produk = Produk::with('units')->get();
        $category = Kategori::all();
        return view('inventory.create_stock',compact(['produk', 'category', 'units']));

    }

    public function updateStock(Request $request)
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
        $produk   = Produk::findOrFail($validated['name_p']);
        $unit     = Units::findOrFail($validated['satuan']);
        $supplier = Supplier::find($produk->supplier_id)?->name ?? 'Unknown';

        // Simpan bukti jika ada
        $buktiPath = null;
        if ($request->hasFile('bukti')) {
            $buktiPath = $request->file('bukti')->store('bukti', 'public');
        }

        // Hitung stok tambahan dalam satuan produk saat ini
        $produkUnit = $produk->units;
        $produkConversion = $produkUnit?->conversion_to_base ?: ($unit->conversion_to_base ?: 1);
        $inputConversion = $unit->conversion_to_base ?: 1;

        // Konversi stok masuk ke satuan produk
        $stokDalamSatuanProduk = $produkConversion > 0
            ? ($validated['stok'] * $inputConversion) / $produkConversion
            : $validated['stok'];

        // Catat stok masuk (tetap tampilkan satuan asli yg dimasukkan user)
        Stockin::create([
            'product_name'  => $produk->name,
            'supplier_name' => $supplier,
            'stock_qty'     => $validated['stok'],     // jumlah asli yg diinput user (mis. 5 dus)
            'satuan'        => $unit->name,            // nama satuan (mis. dus)
            'prices'        => $validated['harga_p'],
            'total_price'   => $validated['harga_t'],
            'bukti'         => $buktiPath,
        ]);

        // Update stok produk dalam satuan produk
        $produk->stock_quantity += $stokDalamSatuanProduk;
        if (!$produk->satuan) {
            $produk->satuan = $unit->id;
        }
        $produk->save();

        // Catat aktivitas
        Activity::create([
            'user'      => Auth::check() ? Auth::user()->name : 'Guest',
            'action'    => 'Menambah stok',
            'model'     => 'inventori',
            'record_id' => $produk->id,
        ]);

        return redirect()->route('invent')->with('success', 'Berhasil menambahkan stok');
    }

    public function export(Request $request)
    {
        $search = $request->input('search');
        $categoryFilter = $request->input('category', 'all');
        $stockFilter = $request->input('stock', 'all');

        $products = $this->buildInventoryQuery($search, $categoryFilter, $stockFilter)->get();

        $filename = 'inventori-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = ['ID', 'Nama', 'SKU', 'Kategori', 'Stok', 'Satuan', 'Harga Agen', 'Harga Reseller', 'Harga Pelanggan'];

        $callback = function () use ($products, $columns) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, $columns);

            foreach ($products as $product) {
                $priceAgent = optional($product->prices->firstWhere('customer_type', 'agent'))->price;
                $priceReseller = optional($product->prices->firstWhere('customer_type', 'reseller'))->price;
                $priceCustomer = optional($product->prices->firstWhere('customer_type', 'pelanggan'))->price;

                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    optional($product->category)->name,
                    $product->stock_quantity,
                    optional($product->units)->name,
                    $priceAgent ?? 0,
                    $priceReseller ?? 0,
                    $priceCustomer ?? 0,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
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
        Activity::create([
        'user'      => Auth::check() ? Auth::user()->name : 'Guest',
        'action'    => 'Menyesuaikan stok',
        'model'     => 'inventori',
        'record_id' => $product->id,
    ]);


        return redirect()->back()->with('success', 'Semua produk berhasil diperbarui!');
    }

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
