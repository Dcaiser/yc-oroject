<?php

namespace Database\Seeders;

use App\Models\Activity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = ['Admin', 'Kasir', 'Manager', 'Supervisor', 'Staff'];
        
        $activities = [
            // Produk activities
            ['action' => 'Menambah produk baru: Beras Premium 5kg', 'model' => 'Produk', 'record_id' => 1],
            ['action' => 'Mengedit produk: Minyak Goreng 2L - Harga diubah', 'model' => 'Produk', 'record_id' => 2],
            ['action' => 'Menghapus produk: Gula Pasir 500g (Discontinued)', 'model' => 'Produk', 'record_id' => 3],
            ['action' => 'Menambah produk baru: Kopi Robusta 250g', 'model' => 'Produk', 'record_id' => 4],
            ['action' => 'Memperbarui stok produk: Teh Celup Box', 'model' => 'Produk', 'record_id' => 5],
            
            // Transaksi POS
            ['action' => 'Transaksi POS berhasil - Total: Rp 150.000', 'model' => 'Transaksi', 'record_id' => 101],
            ['action' => 'Transaksi POS berhasil - Total: Rp 87.500', 'model' => 'Transaksi', 'record_id' => 102],
            ['action' => 'Transaksi POS berhasil - Total: Rp 225.000', 'model' => 'Transaksi', 'record_id' => 103],
            ['action' => 'Transaksi POS berhasil - Total: Rp 45.000', 'model' => 'Transaksi', 'record_id' => 104],
            ['action' => 'Transaksi POS berhasil - Total: Rp 320.000', 'model' => 'Transaksi', 'record_id' => 105],
            
            // Stok Masuk
            ['action' => 'Menambah stok masuk: Beras Premium 5kg (+100 unit)', 'model' => 'StockIn', 'record_id' => 201],
            ['action' => 'Menambah stok masuk: Minyak Goreng 2L (+50 unit)', 'model' => 'StockIn', 'record_id' => 202],
            ['action' => 'Menambah stok masuk: Gula Pasir 1kg (+75 unit)', 'model' => 'StockIn', 'record_id' => 203],
            ['action' => 'Mengedit stok masuk: Koreksi jumlah Kopi Robusta', 'model' => 'StockIn', 'record_id' => 204],
            
            // Stok Keluar
            ['action' => 'Menambah stok keluar: Beras Premium 5kg (-25 unit) - Penjualan', 'model' => 'StockOut', 'record_id' => 301],
            ['action' => 'Menambah stok keluar: Minyak Goreng 2L (-10 unit) - Penjualan', 'model' => 'StockOut', 'record_id' => 302],
            ['action' => 'Menghapus stok keluar: Pembatalan retur', 'model' => 'StockOut', 'record_id' => 303],
            
            // Supplier
            ['action' => 'Menambah supplier baru: PT Beras Sejahtera', 'model' => 'Supplier', 'record_id' => 401],
            ['action' => 'Mengedit supplier: CV Minyak Nusantara - Update kontak', 'model' => 'Supplier', 'record_id' => 402],
            ['action' => 'Menambah supplier baru: UD Gula Manis', 'model' => 'Supplier', 'record_id' => 403],
            
            // Kategori
            ['action' => 'Menambah kategori baru: Sembako', 'model' => 'Kategori', 'record_id' => 501],
            ['action' => 'Mengedit kategori: Minuman - Ubah deskripsi', 'model' => 'Kategori', 'record_id' => 502],
            ['action' => 'Menambah kategori baru: Frozen Food', 'model' => 'Kategori', 'record_id' => 503],
            
            // Customer
            ['action' => 'Menambah pelanggan baru: Toko Makmur Jaya', 'model' => 'Customer', 'record_id' => 601],
            ['action' => 'Mengedit pelanggan: Warung Bu Siti - Update alamat', 'model' => 'Customer', 'record_id' => 602],
            ['action' => 'Menambah pelanggan baru: Minimarket Berkah', 'model' => 'Customer', 'record_id' => 603],
            
            // User Management
            ['action' => 'Membuat user baru: kasir@example.com', 'model' => 'User', 'record_id' => 701],
            ['action' => 'Mengubah role user: staff menjadi supervisor', 'model' => 'User', 'record_id' => 702],
            
            // Purchase Order
            ['action' => 'Menambah Purchase Order baru: PO-2024-001', 'model' => 'PurchaseOrder', 'record_id' => 801],
            ['action' => 'Memperbarui status PO: PO-2024-001 - Diterima', 'model' => 'PurchaseOrder', 'record_id' => 802],
        ];

        // Generate activities with random timestamps over the past 30 days
        foreach ($activities as $index => $activity) {
            $daysAgo = rand(0, 30);
            $hoursAgo = rand(0, 23);
            $minutesAgo = rand(0, 59);
            
            Activity::create([
                'user' => $users[array_rand($users)],
                'action' => $activity['action'],
                'model' => $activity['model'],
                'record_id' => $activity['record_id'],
                'created_at' => Carbon::now()->subDays($daysAgo)->subHours($hoursAgo)->subMinutes($minutesAgo),
                'updated_at' => Carbon::now()->subDays($daysAgo)->subHours($hoursAgo)->subMinutes($minutesAgo),
            ]);
        }

        // Add some activities for today
        $todayActivities = [
            ['action' => 'Transaksi POS berhasil - Total: Rp 175.000', 'model' => 'Transaksi', 'record_id' => 106],
            ['action' => 'Menambah stok masuk: Susu UHT 1L (+30 unit)', 'model' => 'StockIn', 'record_id' => 205],
            ['action' => 'Mengedit produk: Update harga Telur Ayam', 'model' => 'Produk', 'record_id' => 6],
        ];

        foreach ($todayActivities as $activity) {
            Activity::create([
                'user' => $users[array_rand($users)],
                'action' => $activity['action'],
                'model' => $activity['model'],
                'record_id' => $activity['record_id'],
                'created_at' => Carbon::now()->subHours(rand(0, 8))->subMinutes(rand(0, 59)),
                'updated_at' => Carbon::now()->subHours(rand(0, 8))->subMinutes(rand(0, 59)),
            ]);
        }

        $this->command->info('Activity seeder completed! ' . (count($activities) + count($todayActivities)) . ' activities created.');
    }
}
