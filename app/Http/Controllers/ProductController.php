<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Price;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Activity;
use App\Models\Supplier;
use App\Models\Units;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $products = Produk::with('category')// ambil data kategori juga
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
        })
        ->paginate(10);

        $units = Units::all();
        $supplier = Supplier::all();
        $category = Kategori::all();
        $customertypes = ['agent', 'reseller', 'pelanggan'];
        return view('products.index', compact(['products','category','customertypes','supplier','units']))->with('i', (request()->input('page', 1) - 1) * 10);
    }

    public function create()
    {
        $units = Units::all();
        $supplier = Supplier::all();
        $category = Kategori::paginate(5);
        $customertypes = ['agent', 'reseller', 'pelanggan'];
        return view('products.create', compact(['category','customertypes','supplier','units']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'         => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'supplier_id'  => 'required|int',
            'kategori_id'  => 'required|int',
            'stok_user'    => 'required|numeric|min:0.0001',
            'satuan'       => 'required|exists:units,id',
            'gambar'       => 'nullable|image|max:2048',
            'prices'       => 'required|array',
            'prices.*'     => 'required|numeric|min:0',
        ]);

        $category = Kategori::findOrFail($validated['kategori_id']);
        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $unit     = Units::findOrFail($validated['satuan']);
        $stokDisimpan = $validated['stok_user'];

        // Cek apakah produk ini sudah ada berdasarkan kombinasi nama, kategori, dan supplier
        $produk = Produk::whereRaw('LOWER(name) = ?', [Str::lower($validated['nama'])])
            ->where('category_id', $category->id)
            ->where('supplier_id', $supplier->id)
            ->first();

        $path = null;
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('foto_produk', 'public');
        }

        if ($produk) {
            // Jika produk sudah ada, pastikan stok disimpan pada satuan dasar produk
            $existingUnit = $produk->units;
            $existingConversion = $existingUnit?->conversion_to_base ?: 1;
            $incomingConversion = $unit->conversion_to_base ?: 1;

            $stokDalamSatuanProduk = $incomingConversion > 0
                ? ($stokDisimpan * $incomingConversion) / ($existingConversion ?: 1)
                : $stokDisimpan;

            $produk->stock_quantity += $stokDalamSatuanProduk;

            if ($path) {
                if ($produk->image_path && Storage::disk('public')->exists($produk->image_path)) {
                    Storage::disk('public')->delete($produk->image_path);
                }
                $produk->image_path = $path;
            }

            if (!$produk->satuan) {
                $produk->satuan = $unit->id;
            }

            $produk->save();
            $pro = $produk;
        } else {
            $pro = Produk::create([
                'name'            => $validated['nama'],
                'description'     => $validated['deskripsi'],
                'sku'             => $this->generateComplexSku($category, $supplier),
                'supplier_id'     => $validated['supplier_id'],
                'category_id'     => $validated['kategori_id'],
                'stock_quantity'  => $stokDisimpan,
                'satuan'          => $unit->id,
                'image_path'      => $path,
            ]);
        }

        foreach ($validated['prices'] as $type => $price) {
            Price::updateOrCreate(
                [
                    'product_id'    => $pro->id,
                    'customer_type' => $type,
                ],
                [
                    'price' => $price,
                ]
            );
        }
// Simpan aktivitas
Activity::create([
    'user'       => Auth::check() ? Auth::user()->name : 'Guest',
    'action'     => 'Menambah produk',
    'model'      => 'Produk', // konsisten, karena modelmu bernama Produk
    'record_id'  => $pro->id,
]);

        // Validation dan store logic akan ditambahkan nanti
        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function show($id)
    {
        // Show product detail
        return view('products.show');
    }

    public function edit($id)
    {
        // Edit product
        return view('products.edit');
    }

 public function update(request $request, $id)
    {
  // validasi input
        $validated = $request->validate([
            'title1' => 'required|string|max:255',
            'price1' => 'nullable|numeric',
            'stock1'  => 'required|numeric|min:0',
            'satuan1' => 'required|exists:units,id',
            'description1' => 'required|string',
            'sku1' => 'required|string',
            'description1' => 'required|string',
            'kategori_id1' => 'required|string',
            'supplier_id1' => 'required|int',
            'prices' => 'sometimes|array',
            'prices.*' => 'nullable|numeric|min:0',
            'gambar_edit' => 'nullable|image|max:2048',


        ]);

        // cari produk berdasarkan id
        $product = Produk::findOrFail($id);

        // update data
        $oldImagePath = $product->image_path;
        $newImagePath = null;
        if ($request->hasFile('gambar_edit')) {
            $newImagePath = $request->file('gambar_edit')->store('foto_produk', 'public');
        }

        $updateData = [
            'name'           => $request->title1,
            'category_id'    => $request->kategori_id1,
            'satuan'         => $request->satuan1,
            'sku'            => $request->sku1,
            'stock_quantity' => $request->stock1,
            'description'    => $request->description1,
            'supplier_id'    => $request->supplier_id1,
        ];

        if ($newImagePath) {
            $updateData['image_path'] = $newImagePath;
        }

        $product->update($updateData);

        if ($newImagePath && $oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }
        Activity::create([
            'user'       => Auth::check() ? Auth::user()->name : 'Guest',
            'action'     => 'Mengedit produk',
            'model'      => 'Produk', // konsisten, karena modelmu bernama Produk
            'record_id'  => $id,
]);

        // Update/insert harga per tipe customer jika dikirim
        if ($request->filled('prices')) {
            foreach ($request->input('prices') as $customerType => $price) {
                if ($price === null || $price === '') { continue; }
                Price::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'customer_type' => $customerType,
                    ],
                    [
                        'price' => $price,
                    ]
                );
            }
        }


        // redirect balik dengan pesan sukses
        return redirect()->route('products.index')
                         ->with('success', 'Produk berhasil diperbarui!');
        }

    protected function generateComplexSku(Kategori $category, Supplier $supplier): string
    {
        $categoryCode = $this->buildCodeFragment($category->name ?? 'CAT');
        $supplierCode = $this->buildCodeFragment($supplier->name ?? 'SUP');
        $dateCode     = now()->format('ymd');

        $baseSequence = Produk::whereDate('created_at', now()->toDateString())->count() + 1;
        $attempt      = 0;

        do {
            $sequence = str_pad($baseSequence + $attempt, 3, '0', STR_PAD_LEFT);
            $random   = strtoupper(Str::random(2));
            $sku      = sprintf('%s%s-%s-%s%s', $categoryCode, $supplierCode, $dateCode, $sequence, $random);
            $attempt++;
        } while (Produk::where('sku', $sku)->exists());

        return $sku;
    }

    protected function buildCodeFragment(string $source): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9]/', '', $source);
        $code      = strtoupper(Str::limit($sanitized, 3, ''));

        if (strlen($code) < 3) {
            $code = strtoupper(str_pad($code, 3, 'X'));
        }

        return $code;
    }

    public function destroy($id)
    {
        // Delete product logic
        $data = Produk::findOrFail($id);
        $data-> delete();

        Activity::create([
            'user'       => Auth::check() ? Auth::user()->name : 'Guest',
            'action'     => 'Menghapus produk',
            'model'      => 'Produk', // konsisten, karena modelmu bernama Produk
            'record_id'  => $id,
]);


        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }
    public function save(request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'kategori_id' => 'required|int|max:255',
        ]);

                  $price = preg_replace('/[^0-9]/', '', $request->harga); // hapus semua selain angka

        // Kalau pakai DECIMAL dan mau simpan dalam format 1.200.000,00
                  $price = number_format($price, 2, '.', '');
                 // Ambil kode kategori (3 huruf pertama)
                $kodeKategori = strtoupper(Str::limit(preg_replace('/\s+/', '', $request->kategori_id), 3, ''));

                 // Ambil kode nama (3 huruf pertama)
                $kodeNama = strtoupper(Str::limit(preg_replace('/\s+/', '', $request->nama), 3, ''));

                // Ambil 4 digit harga terakhir
                $kodeHarga = str_pad(substr((int)$request->harga, -4), 4, '0', STR_PAD_LEFT);

                // SKU Final
                $sku = $kodeKategori . '-' . $kodeNama . '-' . $kodeHarga;

                // Cek apakah SKU ini sudah ada
                $produk = Produk::where('sku', $sku)->first();

                 if ($produk) {
                    // Kalau sudah ada → tambahkan stok
                   $produk->stok += $request->stok;
                    $produk->save();
                 } else {
                // Simpan gambar jika ada
                   $path = null;
                   //
                //  if ($request->hasFile('gambar')) {
                //    $path = $request->file('gambar')->store('foto_produk', 'public');

                 //}
                 //
                }

     // Kalau belum ada → buat produk baru
     Produk::create([
         'name'      => $request->nama,
         'price'     => $price,
         'category_id'  => $request->kategori_id,
         'satuan'  => $request->satuan,
         'sku'       => $sku,
         'stock_quantity' => $request->stok,
         'description' => $request->deskripsi,
        // 'gambar'    => $path,
     ]);



     return redirect()->back()->with('success', 'berhasil menambahkan produk');

    }
    public function deleteall()
    {

        Produk::query()->delete();
        return redirect()->back()->with('success', 'Semua data berhasil dihapus!');
    }

}
