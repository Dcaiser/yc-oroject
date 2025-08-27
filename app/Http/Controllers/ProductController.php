<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\Http\Controllers\ActivityController;
use Illuminate\Support\Facades\Auth;

use App\Models\Price;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Activity;

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

        $category = Kategori::all();
        $customertypes = ['agent', 'reseller', 'pelanggan'];
        return view('products.index', compact(['products','category','customertypes']));
    }

    public function create()
    {
        $category = Kategori::paginate(5);
        $customertypes = ['agent', 'reseller', 'pelanggan'];
        return view('products.create', compact(['category','customertypes']));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|string|max:255',
            'satuan' => 'required|string|max:255',
            'kategori_id' => 'required|int|max:255',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric|min:0',
        ]);

                 // Ambil kode kategori (3 huruf pertama)
                $kodeKategori = strtoupper(Str::limit(preg_replace('/\s+/', '', $request->kategori_id), 3, ''));

                 // Ambil kode nama (3 huruf pertama)
                $kodeNama = strtoupper(Str::limit(preg_replace('/\s+/', '', $request->nama), 3, ''));

                // SKU Final
                $sku = $kodeKategori . '-' . $kodeNama;

                // Cek apakah SKU ini sudah ada
                $produk = Produk::where('sku', $sku)->first();

                 if ($produk) {
                    // Kalau sudah ada → tambahkan stok
                   $produk->stock_quantity += $request->stok;
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
$pro = Produk::create([
    'name'           => $request->nama,        // pastikan field DB kamu pakai "name"
    ##'price'          => $price,
    'category_id'    => $request->kategori_id,
    'satuan'         => $request->satuan,
    'sku'            => $sku,
    'stock_quantity' => $request->stok,
    'description'    => $request->deskripsi,
    // 'gambar'      => $path, // aktifkan kalau ada upload gambar
]);

 foreach ($request->prices as $customerType => $price) {
            Price::create([
                'product_id'    => $pro->id,
                'customer_type' => $customerType,
                'price'         => $price,
            ]);
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
            'price1' => 'required|numeric',
            'stock1'  => 'required|integer',
            'satuan1' => 'required|string',
            'description1' => 'required|string',
            'sku1' => 'required|string',
            'description1' => 'required|string',
            'kategori_id1' => 'required|string',


        ]);

        // cari produk berdasarkan id
        $product = Produk::findOrFail($id);

        // update data
        $product->update(
            [
    'name'           => $request->title1,        // pastikan field DB kamu pakai "name"
    'category_id'    => $request->kategori_id1,
    'satuan'         => $request->satuan1,
    'sku'            => $request->sku1,
    'stock_quantity' => $request->stock1,
    'description'    => $request->description1,
    // 'gambar'      => $path, // aktifkan kalau ada upload gambar
]

        );
        Activity::create([
            'user'       => Auth::check() ? Auth::user()->name : 'Guest',
            'action'     => 'Mengedit produk',
            'model'      => 'Produk', // konsisten, karena modelmu bernama Produk
            'record_id'  => $id,
]);


        // redirect balik dengan pesan sukses
        return redirect()->route('products.index')
                         ->with('success', 'Produk berhasil diperbarui!');
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
