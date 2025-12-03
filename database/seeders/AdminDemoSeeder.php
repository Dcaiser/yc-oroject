<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Kategori;
use App\Models\Price;
use App\Models\Produk;
use App\Models\Stockin;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminDemoSeeder extends Seeder
{
    /**
     * Seed demo data to showcase the admin experience.
     */
    public function run(): void
    {
        // Create units first
        $unitRecords = collect([
            ['name' => 'pcs', 'conversion_to_base' => 1],
            ['name' => 'unit', 'conversion_to_base' => 1],
            ['name' => 'box', 'conversion_to_base' => 12],
            ['name' => 'lusin', 'conversion_to_base' => 12],
            ['name' => 'kg', 'conversion_to_base' => 1],
        ])->mapWithKeys(fn ($unit) => [
            $unit['name'] => DB::table('units')->updateOrInsert(
                ['name' => $unit['name']],
                ['conversion_to_base' => $unit['conversion_to_base'], 'created_at' => now(), 'updated_at' => now()]
            ) ? DB::table('units')->where('name', $unit['name'])->first() : null
        ]);

        $users = collect([
            [
                'name' => 'Admin Example',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Manager Demo',
                'email' => 'manager@example.com',
                'role' => 'manager',
            ],
            [
                'name' => 'Staff Demo',
                'email' => 'staff@example.com',
                'role' => 'staff',
            ],
        ])->mapWithKeys(function (array $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => $userData['role'],
                ]
            );

            if (! $user->email_verified_at) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            return [$userData['role'] => $user];
        });

        $supplierRecords = collect([
            [
                'supplier_code' => 'SUP-DEMO1',
                'name' => 'PT Demo Sukses',
                'contact_person' => 'Andi Wijaya',
                'phone' => '021-8901122',
                'email' => 'sales@demosukses.id',
                'address' => 'Jl. Pusat Niaga No. 21, Jakarta Selatan',
                'npwp' => '10.234.567.8-999.000',
            ],
            [
                'supplier_code' => 'SUP-DEMO2',
                'name' => 'CV Prima Distribusi',
                'contact_person' => 'Rina Sari',
                'phone' => '021-6677123',
                'email' => 'halo@primadistribusi.co.id',
                'address' => 'Jl. Industri No. 12, Tangerang',
                'npwp' => '11.345.678.9-888.000',
            ],
        ])->mapWithKeys(fn ($supplier) => [
            $supplier['supplier_code'] => Supplier::updateOrCreate(
                ['supplier_code' => $supplier['supplier_code']],
                $supplier
            )
        ]);

        $categoryRecords = collect([
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan aksesoris kerja.'],
            ['name' => 'Perlengkapan Kantor', 'description' => 'Kebutuhan operasional harian kantor.'],
            ['name' => 'Logistik', 'description' => 'Barang pendukung aktivitas gudang.'],
        ])->mapWithKeys(fn ($category) => [
            $category['name'] => Kategori::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            )
        ]);

        $products = collect([
            [
                'sku' => 'PRD-001',
                'name' => 'Laptop Zenix 14',
                'price' => 14500000,
                'stock_quantity' => 24,
                'satuan' => 'unit',
                'description' => 'Ultrabook 14 inci untuk operasional harian tim.',
                'category' => 'Elektronik',
                'supplier_code' => 'SUP-DEMO1',
                'prices' => [
                    'agent' => 13750000,
                    'reseller' => 14000000,
                    'pelanggan' => 14500000,
                ],
            ],
            [
                'sku' => 'PRD-002',
                'name' => 'Printer Laser FastPrint',
                'price' => 4200000,
                'stock_quantity' => 18,
                'satuan' => 'unit',
                'description' => 'Printer laser monokrom untuk kebutuhan cetak dokumen.',
                'category' => 'Perlengkapan Kantor',
                'supplier_code' => 'SUP-DEMO1',
                'prices' => [
                    'agent' => 3950000,
                    'reseller' => 4050000,
                    'pelanggan' => 4200000,
                ],
            ],
            [
                'sku' => 'PRD-003',
                'name' => 'Hand Pallet 2.5T',
                'price' => 6850000,
                'stock_quantity' => 9,
                'satuan' => 'unit',
                'description' => 'Perangkat angkut manual kapasitas 2.5 ton untuk gudang.',
                'category' => 'Logistik',
                'supplier_code' => 'SUP-DEMO2',
                'prices' => [
                    'agent' => 6500000,
                    'reseller' => 6650000,
                    'pelanggan' => 6850000,
                ],
            ],
        ])->mapWithKeys(function (array $productData) use ($categoryRecords, $supplierRecords, $unitRecords) {
            $category = $categoryRecords[$productData['category']];
            $supplier = $supplierRecords[$productData['supplier_code']];
            $unit = $unitRecords[$productData['satuan']] ?? $unitRecords['pcs'];

            $product = Produk::updateOrCreate(
                ['sku' => $productData['sku']],
                [
                    'name' => $productData['name'],
                    'price' => $productData['price'],
                    'category_id' => $category->id,
                    'supplier_id' => $supplier->id,
                    'stock_quantity' => $productData['stock_quantity'],
                    'description' => trim($productData['description']),
                    'satuan' => $unit->id,
                ]
            );

            collect($productData['prices'])->each(function ($price, $type) use ($product) {
                Price::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'customer_type' => $type,
                    ],
                    ['price' => $price]
                );
            });

            return [$productData['sku'] => $product];
        });

        $stockAdjustments = [
            [
                'sku' => 'PRD-001',
                'supplier_code' => 'SUP-DEMO1',
                'stock_qty' => 12,
                'unit_price' => 13650000,
                'recorded_at' => Carbon::now()->subWeeks(2)->startOfWeek()->addDays(1),
            ],
            [
                'sku' => 'PRD-002',
                'supplier_code' => 'SUP-DEMO1',
                'stock_qty' => 8,
                'unit_price' => 3900000,
                'recorded_at' => Carbon::now()->subWeeks(4)->startOfWeek()->addDays(2),
            ],
            [
                'sku' => 'PRD-003',
                'supplier_code' => 'SUP-DEMO2',
                'stock_qty' => 5,
                'unit_price' => 6450000,
                'recorded_at' => Carbon::now()->subWeeks(6)->startOfWeek()->addDays(3),
            ],
        ];

        foreach ($stockAdjustments as $adjustment) {
            $product = $products[$adjustment['sku']];
            $supplier = $supplierRecords[$adjustment['supplier_code']];
            $totalPrice = $adjustment['unit_price'] * $adjustment['stock_qty'];
            
            // Get unit name from units table
            $unitName = DB::table('units')->where('id', $product->satuan)->value('name') ?? 'pcs';

            $stockIn = Stockin::updateOrCreate(
                [
                    'product_name' => $product->name,
                    'supplier_name' => $supplier->name,
                    'stock_qty' => $adjustment['stock_qty'],
                    'prices' => $adjustment['unit_price'],
                    'total_price' => $totalPrice,
                ],
                [
                    'satuan' => $unitName,
                ]
            );

            $stockIn->created_at = $adjustment['recorded_at'];
            $stockIn->updated_at = $adjustment['recorded_at'];
            $stockIn->saveQuietly();
        }

        $salesSamples = [
            [
                'sku' => 'PRD-001',
                'customer_name' => 'PT Nusantara Retail',
                'customer_type' => 'pelanggan',
                'quantity' => 3,
                'unit_price' => 125000,
                'shipping_cost' => 15000,
                'note' => 'Demo: Pesanan retail harian',
                'sold_at' => Carbon::now()->subDays(1)->setTime(10, 15),
            ],
            [
                'sku' => 'PRD-002',
                'customer_name' => 'CV Prima Office',
                'customer_type' => 'reseller',
                'quantity' => 2,
                'unit_price' => 85000,
                'shipping_cost' => 12000,
                'note' => 'Demo: Pengadaan printer cabang',
                'sold_at' => Carbon::now()->subDays(2)->setTime(14, 45),
            ],
            [
                'sku' => 'PRD-001',
                'customer_name' => 'Toko Digital Mega',
                'customer_type' => 'reseller',
                'quantity' => 1,
                'unit_price' => 78000,
                'shipping_cost' => 8000,
                'note' => 'Demo: Permintaan reseller cepat',
                'sold_at' => Carbon::now()->subDays(3)->setTime(16, 5),
            ],
            [
                'sku' => 'PRD-003',
                'customer_name' => 'PT Logistik Bersama',
                'customer_type' => 'agent',
                'quantity' => 1,
                'unit_price' => 99000,
                'shipping_cost' => 15000,
                'note' => 'Demo: Pengiriman alat gudang proyek barat',
                'sold_at' => Carbon::now()->subDays(4)->setTime(9, 30),
            ],
            [
                'sku' => 'PRD-002',
                'customer_name' => 'PT Sinar Supplies',
                'customer_type' => 'pelanggan',
                'quantity' => 4,
                'unit_price' => 62000,
                'shipping_cost' => 10000,
                'note' => 'Demo: Pengadaan printer bulanan',
                'sold_at' => Carbon::now()->subDays(6)->setTime(11, 50),
            ],
        ];

        $demoNotes = collect($salesSamples)->pluck('note')->filter()->all();

        if (! empty($demoNotes)) {
            DB::table('stock_out')->whereIn('note', $demoNotes)->delete();
        }

        foreach ($salesSamples as $sample) {
            $product = $products[$sample['sku']];
            $timestamp = $sample['sold_at'];
            $totalPrice = $sample['quantity'] * $sample['unit_price'];
            
            // Get unit name from units table
            $unitName = DB::table('units')->where('id', $product->satuan)->value('name') ?? 'pcs';

            DB::table('stock_out')->insert([
                'product_name' => $product->name,
                'customer_name' => $sample['customer_name'],
                'customer_type' => $sample['customer_type'],
                'stock_qty' => $sample['quantity'],
                'satuan' => $unitName,
                'prices' => $sample['unit_price'],
                'shipping_cost' => $sample['shipping_cost'],
                'total_price' => $totalPrice,
                'payment_received' => $totalPrice + $sample['shipping_cost'],
                'note' => $sample['note'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        $activitySeeds = [
            [
                'sku' => 'PRD-001',
                'action' => 'Menambah stok Laptop Zenix 14',
                'model' => 'Stockin',
                'offset_weeks' => 0,
                'offset_days' => 1,
                'user_role' => 'admin',
            ],
            [
                'sku' => 'PRD-001',
                'action' => 'Mengeluarkan stok Laptop Zenix 14 untuk pesanan retail',
                'model' => 'Stockout',
                'offset_weeks' => 1,
                'offset_days' => 2,
                'user_role' => 'manager',
            ],
            [
                'sku' => 'PRD-002',
                'action' => 'Menambah stok Printer Laser FastPrint',
                'model' => 'Stockin',
                'offset_weeks' => 3,
                'offset_days' => 4,
                'user_role' => 'staff',
            ],
            [
                'sku' => 'PRD-002',
                'action' => 'Mengeluarkan stok Printer Laser FastPrint untuk proyek tender',
                'model' => 'Stockout',
                'offset_weeks' => 4,
                'offset_days' => 1,
                'user_role' => 'admin',
            ],
            [
                'sku' => 'PRD-003',
                'action' => 'Menambah produk baru - Hand Pallet 2.5T',
                'model' => 'Produk',
                'offset_weeks' => 6,
                'offset_days' => 2,
                'user_role' => 'manager',
            ],
            [
                'sku' => 'PRD-003',
                'action' => 'Mengeluarkan stok Hand Pallet untuk proyek gudang',
                'model' => 'Stockout',
                'offset_weeks' => 2,
                'offset_days' => 5,
                'user_role' => 'staff',
            ],
            [
                'sku' => 'PRD-001',
                'action' => 'Menambah produk baru - Laptop Zenix 14',
                'model' => 'Produk',
                'offset_weeks' => 8,
                'offset_days' => 3,
                'user_role' => 'admin',
            ],
        ];

        foreach ($activitySeeds as $seed) {
            $product = $products[$seed['sku']];
            $actor = $users[$seed['user_role']];
            $timestamp = Carbon::now()
                ->subWeeks($seed['offset_weeks'])
                ->startOfWeek()
                ->addDays($seed['offset_days'])
                ->setTime(10, 15);

            DB::table('activities')->updateOrInsert(
                [
                    'action' => $seed['action'],
                    'model' => $seed['model'],
                    'record_id' => $product->id,
                    'created_at' => $timestamp,
                ],
                [
                    'user' => $actor->name,
                    'updated_at' => $timestamp,
                ]
            );
        }
    }
}
