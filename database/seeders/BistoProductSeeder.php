<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Price;
use App\Models\Produk;
use App\Models\Supplier;
use App\Models\Units;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BistoProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $category = Kategori::firstOrCreate(
                ['name' => 'Produk Bisto'],
                ['description' => 'Katalog varian produk Bisto']
            );

            $supplier = Supplier::firstOrCreate(
                ['supplier_code' => 'SUPBIS'],
                [
                    'name' => 'Distributor Bisto',
                    'contact_person' => 'Tim Penjualan',
                    'phone' => null,
                    'email' => null,
                    'address' => null,
                    'npwp' => null,
                ]
            );

            $dusUnit = Units::firstOrCreate(
                ['name' => 'Dus'],
                ['conversion_to_base' => 1]
            );

            $pcsUnit = Units::firstOrCreate(
                ['name' => 'PCS'],
                ['conversion_to_base' => 1]
            );

            $products = [
                [
                    'jenis' => 'Cup',
                    'ukuran' => '200 ML',
                    'qty' => '48 PCS',
                    'agent_price' => 16000,
                    'reseller_price' => 18000,
                    'unit_id' => $dusUnit->id,
                ],
                [
                    'jenis' => 'Botol',
                    'ukuran' => '220 ML',
                    'qty' => '24 PCS',
                    'agent_price' => 23000,
                    'reseller_price' => 25000,
                    'unit_id' => $dusUnit->id,
                ],
                [
                    'jenis' => 'Botol',
                    'ukuran' => '220 ML',
                    'qty' => '48 PCS',
                    'agent_price' => 40000,
                    'reseller_price' => 43000,
                    'unit_id' => $dusUnit->id,
                ],
                [
                    'jenis' => 'Botol',
                    'ukuran' => '330 ML',
                    'qty' => '24 PCS',
                    'agent_price' => 26000,
                    'reseller_price' => 30000,
                    'unit_id' => $dusUnit->id,
                ],
                [
                    'jenis' => 'Botol',
                    'ukuran' => '600 ML',
                    'qty' => '24 PCS',
                    'agent_price' => 32000,
                    'reseller_price' => 35000,
                    'unit_id' => $dusUnit->id,
                ],
                [
                    'jenis' => 'Botol',
                    'ukuran' => '1500 ML',
                    'qty' => '12 PCS',
                    'agent_price' => 30000,
                    'reseller_price' => 34000,
                    'unit_id' => $dusUnit->id,
                ],
                [
                    'jenis' => 'Galon',
                    'ukuran' => '19 L',
                    'qty' => '1 PC',
                    'agent_price' => 12000,
                    'reseller_price' => 14000,
                    'unit_id' => $pcsUnit->id,
                ],
            ];

            foreach ($products as $index => $item) {
                $normalizedJenis = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item['jenis']));
                $normalizedUkuran = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $item['ukuran']));
                $normalizedQty = preg_replace('/[^0-9]/', '', $item['qty']);

                $sku = sprintf(
                    'BIS-%s-%s-%s',
                    $normalizedJenis,
                    $normalizedUkuran,
                    str_pad($normalizedQty !== '' ? $normalizedQty : (string) ($index + 1), 2, '0', STR_PAD_LEFT)
                );

                $product = Produk::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'name' => sprintf('Bisto %s %s', $item['jenis'], $item['ukuran']),
                        'price' => $item['reseller_price'],
                        'stock_quantity' => 0,
                        'category_id' => $category->id,
                        'supplier_id' => $supplier->id,
                        'description' => sprintf(
                            'Kemasan %s ukuran %s. Isi per dus: %s.',
                            strtolower($item['jenis']),
                            $item['ukuran'],
                            $item['qty']
                        ),
                        'satuan' => $item['unit_id'],
                    ]
                );

                $priceMap = [
                    'agent' => $item['agent_price'],
                    'reseller' => $item['reseller_price'],
                    'pelanggan' => $item['reseller_price'],
                ];

                foreach ($priceMap as $customerType => $price) {
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
        });
    }
}
