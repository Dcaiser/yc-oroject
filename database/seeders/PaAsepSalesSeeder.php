<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaAsepSalesSeeder extends Seeder
{
    public function run(): void
    {
        $customerName = 'Pengantaran Pa Asep';
        $customerType = 'agent';
        $note = 'Penjualan distribusi melalui Pa Asep';

        $sales = [
            '2025-12-01' => [
                ['product' => 'Bisto Botol 220 ML (Isi 48)', 'qty' => 3, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 330 ML', 'qty' => 1, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Galon 19 L', 'qty' => 21, 'price' => 1500, 'unit' => 'Galon'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-02' => [
                ['product' => 'Bisto Botol 220 ML (Isi 48)', 'qty' => 1, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Galon 19 L', 'qty' => 11, 'price' => 1500, 'unit' => 'Galon'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-03' => [
                ['product' => 'Bisto Cup 200 ML', 'qty' => 35, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-04' => [
                ['product' => 'Bisto Cup 200 ML', 'qty' => 29, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 220 ML (Isi 48)', 'qty' => 6, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 330 ML', 'qty' => 7, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 600 ML', 'qty' => 3, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Galon 19 L', 'qty' => 23, 'price' => 1500, 'unit' => 'Galon'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-05' => [
                ['product' => 'Bisto Botol 600 ML', 'qty' => 20, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-06' => [
                ['product' => 'Bisto Botol 220 ML (Isi 48)', 'qty' => 1, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 600 ML', 'qty' => 2, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Galon 19 L', 'qty' => 7, 'price' => 1500, 'unit' => 'Galon'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-07' => [
                ['product' => 'Bisto Botol 600 ML', 'qty' => 1, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Galon 19 L', 'qty' => 7, 'price' => 1500, 'unit' => 'Galon'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-10' => [
                ['product' => 'Bisto Botol 220 ML (Isi 48)', 'qty' => 5, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 600 ML', 'qty' => 7, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
            '2025-12-11' => [
                ['product' => 'Bisto Botol 220 ML (Isi 48)', 'qty' => 5, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Botol 330 ML', 'qty' => 12, 'price' => 1000, 'unit' => 'Dus'],
                ['product' => 'Bisto Galon 19 L', 'qty' => 17, 'price' => 1500, 'unit' => 'Galon'],
                ['product' => 'Jasa Antar Pengantaran', 'qty' => 1, 'price' => 20000, 'unit' => 'Trip'],
            ],
        ];

        $startDate = '2025-12-01 00:00:00';
        $endDate = '2025-12-31 23:59:59';

        DB::transaction(function () use ($sales, $customerName, $customerType, $note, $startDate, $endDate) {
            DB::table('stock_out')
                ->where('customer_name', $customerName)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->delete();

            foreach ($sales as $date => $items) {
                $baseTimestamp = Carbon::parse($date)->setTime(9, 0, 0);

                foreach ($items as $offset => $item) {
                    $qty = (int) ($item['qty'] ?? 0);
                    if ($qty <= 0) {
                        continue;
                    }

                    $price = (int) ($item['price'] ?? 0);
                    $unit = $item['unit'] ?? 'Dus';
                    $timestamp = $baseTimestamp->copy()->addMinutes($offset);
                    $total = $qty * $price;

                    DB::table('stock_out')->updateOrInsert(
                        [
                            'product_name' => $item['product'],
                            'customer_name' => $customerName,
                            'customer_type' => $customerType,
                            'created_at' => $timestamp,
                        ],
                        [
                            'stock_qty' => $qty,
                            'satuan' => $unit,
                            'prices' => $price,
                            'shipping_cost' => 0,
                            'total_price' => $total,
                            'payment_received' => $total,
                            'note' => $note,
                            'updated_at' => $timestamp,
                        ]
                    );
                }
            }
        });
    }
}
