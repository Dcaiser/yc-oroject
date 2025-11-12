<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
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
                'label' => $row['label'],
                'total' => $row['total'],
                'stock_in' => $row['stock_in'],
                'stock_out' => $row['stock_out'],
                'ending_stock' => $row['ending_stock'],
            ];
        }, $this->data, array_keys($this->data));
    }

    public function headings(): array
    {
        return [
            'No.',
            'Periode',
            'Total Aktivitas',
            'Stok Masuk',
            'Stok Keluar',
            'Stok Akhir',
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
            'B' => 20, // Periode
            'C' => 18, // Total Aktivitas
            'D' => 15, // Stok Masuk
            'E' => 15, // Stok Keluar
            'F' => 15, // Stok Akhir
        ];
    }

    public function title(): string
    {
        return 'Pergerakan Stok';
    }
}
