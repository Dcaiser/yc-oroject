<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Supplier;
use App\Models\Price;
use App\Models\User;
use Carbon\Carbon;

class ReportTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test categories
        $categories = [
            ['name' => 'Makanan', 'description' => 'Produk makanan dan minuman'],
            ['name' => 'Pakaian', 'description' => 'Pakaian dan aksesoris'],
            ['name' => 'Elektronik', 'description' => 'Barang elektronik'],
            ['name' => 'Kesehatan', 'description' => 'Produk kesehatan dan obat-obatan'],
            ['name' => 'Kebersihan', 'description' => 'Produk kebersihan dan sanitasi'],
        ];

        foreach ($categories as $category) {
            Kategori::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create test suppliers
        $suppliers = [
            ['name' => 'PT Sumber Rejeki', 'contact_person' => 'Ahmad Surya', 'phone' => '08123456789', 'email' => 'ahmad@sumberrejeki.com'],
            ['name' => 'CV Maju Jaya', 'contact_person' => 'Siti Nurhaliza', 'phone' => '08234567890', 'email' => 'siti@majujaya.com'],
            ['name' => 'UD Berkah Mandiri', 'contact_person' => 'Budi Santoso', 'phone' => '08345678901', 'email' => 'budi@berkahmandiri.com'],
            ['name' => 'PT Harapan Bangsa', 'contact_person' => 'Dewi Kusuma', 'phone' => '08456789012', 'email' => 'dewi@harapanbangsa.com'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }

        // Create test users
        $users = ['Admin System', 'Manager Gudang', 'Staff Inventory', 'Supervisor'];
        
        // Create test products
        $categoryIds = Kategori::pluck('id')->toArray();
        $supplierIds = Supplier::pluck('id')->toArray();

        $products = [
            ['name' => 'Beras Premium 25kg', 'sku' => 'BRS001', 'stock_quantity' => 50, 'price' => 350000],
            ['name' => 'Minyak Goreng 2L', 'sku' => 'MYK001', 'stock_quantity' => 80, 'price' => 28000],
            ['name' => 'Gula Pasir 1kg', 'sku' => 'GUL001', 'stock_quantity' => 120, 'price' => 15000],
            ['name' => 'Sarung Dewasa', 'sku' => 'SAR001', 'stock_quantity' => 25, 'price' => 85000],
            ['name' => 'Mukena Anak', 'sku' => 'MUK001', 'stock_quantity' => 15, 'price' => 125000],
            ['name' => 'Sandal Jepit', 'sku' => 'SAN001', 'stock_quantity' => 40, 'price' => 25000],
            ['name' => 'Sabun Cuci Piring 800ml', 'sku' => 'SAB001', 'stock_quantity' => 60, 'price' => 18000],
            ['name' => 'Deterjen Bubuk 1kg', 'sku' => 'DET001', 'stock_quantity' => 35, 'price' => 22000],
            ['name' => 'Sikat Gigi Dewasa', 'sku' => 'SIK001', 'stock_quantity' => 100, 'price' => 8000],
            ['name' => 'Pasta Gigi 75gr', 'sku' => 'PAS001', 'stock_quantity' => 85, 'price' => 12000],
            ['name' => 'Vitamin C 100 tablet', 'sku' => 'VIT001', 'stock_quantity' => 30, 'price' => 45000],
            ['name' => 'Masker Kain 1box', 'sku' => 'MAS001', 'stock_quantity' => 8, 'price' => 35000], // Low stock
            ['name' => 'Hand Sanitizer 500ml', 'sku' => 'HAN001', 'stock_quantity' => 5, 'price' => 28000], // Low stock
            ['name' => 'Tisu Basah 1pack', 'sku' => 'TIS001', 'stock_quantity' => 3, 'price' => 15000], // Low stock
            ['name' => 'Alat Tulis Set', 'sku' => 'ALT001', 'stock_quantity' => 45, 'price' => 75000],
        ];

        foreach ($products as $productData) {
            $product = Produk::firstOrCreate(
                ['sku' => $productData['sku']], 
                [
                    'name' => $productData['name'],
                    'sku' => $productData['sku'],
                    'stock_quantity' => $productData['stock_quantity'],
                    'category_id' => $categoryIds[array_rand($categoryIds)],
                    'supplier_id' => $supplierIds[array_rand($supplierIds)],
                    'satuan' => 'pcs',
                    'description' => 'Produk berkualitas untuk yatim dhuafa',
                    'created_at' => Carbon::now()->subDays(rand(1, 30))
                ]
            );

            // Create prices for different customer types
            $basePrice = $productData['price'];
            Price::firstOrCreate([
                'product_id' => $product->id,
                'customer_type' => 'pelanggan'
            ], [
                'price' => $basePrice
            ]);

            Price::firstOrCreate([
                'product_id' => $product->id,
                'customer_type' => 'reseller'
            ], [
                'price' => $basePrice * 0.85 // 15% discount for resellers
            ]);

            Price::firstOrCreate([
                'product_id' => $product->id,
                'customer_type' => 'agent'
            ], [
                'price' => $basePrice * 0.75 // 25% discount for agents
            ]);
        }

        // Create test activities for the last 30 days
        $activities = [
            'User login ke sistem',
            'Menambah produk baru',
            'Update stok produk',
            'Menghapus produk expired',
            'Export laporan PDF',
            'Menambah supplier baru',
            'Update informasi supplier',
            'Generate laporan bulanan',
            'Backup data sistem',
            'Update kategori produk',
            'Cek stok rendah',
            'Input purchase order',
            'Verifikasi data inventori',
            'Update profile user',
            'Export data ke Excel',
        ];

        // Generate 200 random activities over the last 30 days
        for ($i = 0; $i < 200; $i++) {
            Activity::create([
                'user' => $users[array_rand($users)],
                'action' => $activities[array_rand($activities)],
                'model' => collect(['Produk', 'Supplier', 'Kategori', 'User', null])->random(),
                'record_id' => rand(1, 100),
                'created_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59))
            ]);
        }

        $this->command->info('Report test data created successfully!');
        $this->command->info('- Categories: ' . Kategori::count());
        $this->command->info('- Suppliers: ' . Supplier::count());
        $this->command->info('- Products: ' . Produk::count());
        $this->command->info('- Activities: ' . Activity::count());
        $this->command->info('- Low stock products: ' . Produk::where('stock_quantity', '<', 10)->count());
    }
}
