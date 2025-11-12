<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesTransactionExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $start;
    protected $end;

    public function __construct(array $data, Carbon $start, Carbon $end)
    {
        $this->data = $data;
        $this->start = $start;
        $this->end = $end;
    }

    public function array(): array
    {
        return array_map(function ($row, $index) {
            return [
                'no' => $index + 1,
                'date' => $row['date'],
                'customer_name' => $row['customer_name'],
                'customer_type' => $row['customer_type'],
                'product_name' => $row['product_name'],
                'qty' => $row['qty'],
                'price_per_unit' => $row['price_per_unit'],
                'total_price' => $row['total_price'],
                'shipping_cost' => $row['shipping_cost'],
                'grand_total' => $row['grand_total'],
            ];
        }, $this->data, array_keys($this->data));
    }

    public function headings(): array
    {
        return [
            'No.',
            'Tanggal',
            'Customer',
            'Tipe Customer',
            'Produk',
            'Qty',
            'Harga Satuan',
            'Total Harga',
            'Ongkir',
            'Grand Total',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DBEAFE']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,  // No
            'B' => 15, // Tanggal
            'C' => 25, // Customer
            'D' => 15, // Tipe Customer
            'E' => 30, // Produk
            'F' => 12, // Qty
            'G' => 18, // Harga Satuan
            'H' => 18, // Total Harga
            'I' => 15, // Ongkir
            'J' => 18, // Grand Total
        ];
    }

    public function title(): string
    {
        return 'Transaksi Penjualan';
    }
}
