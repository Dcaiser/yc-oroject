<?php

namespace App\Exports;

use App\Models\Activity;
use App\Models\Produk;
use App\Models\Supplier;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class ReportExport extends StringValueBinder implements FromArray, WithHeadings, WithStyles, WithTitle, WithCustomValueBinder
{
    protected $reportType;
    protected $parameters;

    public function __construct($reportType, $parameters = [])
    {
        $this->reportType = $reportType;
        $this->parameters = $parameters;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        switch ($this->reportType) {
            case 'stock-value':
                return $this->getStockValueData();
            case 'movement':
                return $this->getMovementData();
            case 'supplier-performance':
                return $this->getSupplierPerformanceData();
            case 'weekly':
                return $this->getWeeklyData();
            case 'monthly':
                return $this->getMonthlyData();
            default:
                return [];
        }
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        switch ($this->reportType) {
            case 'stock-value':
                return [
                    'SKU',
                    'Nama Produk',
                    'Kategori',
                    'Stok',
                    'Satuan',
                    'Harga Satuan',
                    'Total Nilai',
                    'Status Stok',
                    'Supplier',
                    'Deskripsi'
                ];
            case 'movement':
                return [
                    'Tanggal',
                    'Waktu',
                    'User',
                    'Aktivitas',
                    'Model',
                    'Detail',
                    'IP Address'
                ];
            case 'supplier-performance':
                return [
                    'Nama Supplier',
                    'Contact Person',
                    'Telepon',
                    'Email',
                    'Alamat',
                    'Total Produk',
                    'Total Purchase Orders',
                    'Status',
                    'Rating'
                ];
            case 'weekly':
                return [
                    'Tanggal',
                    'Hari',
                    'User',
                    'Aktivitas',
                    'Model',
                    'Detail',
                    'Kategori Aktivitas'
                ];
            case 'monthly':
                return [
                    'Tanggal',
                    'Minggu',
                    'User',
                    'Aktivitas',
                    'Model',
                    'Detail',
                    'Kategori Aktivitas',
                    'Jam'
                ];
            default:
                return [];
        }
    }

    public function title(): string
    {
        $titles = [
            'stock-value' => 'Laporan Nilai Stok',
            'movement' => 'Laporan Pergerakan',
            'supplier-performance' => 'Laporan Performa Supplier',
            'weekly' => 'Laporan Mingguan',
            'monthly' => 'Laporan Bulanan'
        ];

        return $titles[$this->reportType] ?? 'Laporan';
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:Z1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1f7c4d']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Auto-size columns
        foreach (range('A', 'Z') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension('1')->setRowHeight(25);

        return [];
    }

    private function getStockValueData()
    {
        $query = Produk::with(['category', 'supplier']);
        
        if (isset($this->parameters['category_id']) && $this->parameters['category_id']) {
            $query->where('category_id', $this->parameters['category_id']);
        }
        
        $products = $query->get();
        
        return $products->map(function ($product) {
            return [
                $product->sku,
                $product->name,
                $product->category->name ?? 'Tidak ada kategori',
                $product->stock_quantity,
                $product->satuan ?? 'pcs',
                'Rp ' . number_format($product->getDefaultPrice(), 0, ',', '.'),
                'Rp ' . number_format($product->stock_quantity * $product->getDefaultPrice(), 0, ',', '.'),
                $product->stock_quantity < 10 ? 'Stok Rendah' : ($product->stock_quantity > 100 ? 'Stok Tinggi' : 'Normal'),
                $product->supplier->name ?? 'Tidak ada supplier',
                $product->description ?? ''
            ];
        })->toArray();
    }

    private function getMovementData()
    {
        $query = Activity::query();
        
        if (isset($this->parameters['date_from']) && isset($this->parameters['date_to'])) {
            $query->whereBetween('created_at', [
                $this->parameters['date_from'], 
                $this->parameters['date_to']
            ]);
        }
        
        $activities = $query->latest()->get();
        
        return $activities->map(function ($activity) {
            return [
                $activity->created_at->format('Y-m-d'),
                $activity->created_at->format('H:i:s'),
                $activity->user,
                $activity->action,
                $activity->model ?? '',
                $activity->details ?? '',
                $activity->ip_address ?? ''
            ];
        })->toArray();
    }

    private function getSupplierPerformanceData()
    {
        $suppliers = Supplier::withCount(['purchaseOrders'])->get();
        
        return $suppliers->map(function ($supplier) {
            return [
                $supplier->name,
                $supplier->contact_person ?? '',
                $supplier->phone ?? '',
                $supplier->email ?? '',
                $supplier->address ?? '',
                $supplier->produk()->count(),
                $supplier->purchase_orders_count,
                $supplier->status ?? 'Aktif',
                $supplier->rating ?? 'Belum dinilai'
            ];
        })->toArray();
    }

    private function getWeeklyData()
    {
        $startOfWeek = isset($this->parameters['week']) 
            ? Carbon::parse($this->parameters['week'])->startOfWeek()
            : Carbon::now()->startOfWeek();
        
        $endOfWeek = $startOfWeek->copy()->endOfWeek();
        
        $activities = Activity::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return $activities->map(function ($activity) {
            $category = 'Lainnya';
            $action = strtolower($activity->action);
            
            if (str_contains($action, 'tambah') || str_contains($action, 'create')) {
                $category = 'Penambahan';
            } elseif (str_contains($action, 'update') || str_contains($action, 'edit')) {
                $category = 'Perubahan';
            } elseif (str_contains($action, 'hapus') || str_contains($action, 'delete')) {
                $category = 'Penghapusan';
            } elseif (str_contains($action, 'login')) {
                $category = 'Login/Akses';
            }
            
            return [
                $activity->created_at->format('Y-m-d'),
                $activity->created_at->translatedFormat('l'),
                $activity->user,
                $activity->action,
                $activity->model ?? '',
                $activity->details ?? '',
                $category
            ];
        })->toArray();
    }

    private function getMonthlyData()
    {
        $month = isset($this->parameters['month']) 
            ? Carbon::parse($this->parameters['month'])
            : Carbon::now();
        
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();
        
        $activities = Activity::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return $activities->map(function ($activity) {
            $category = 'Lainnya';
            $action = strtolower($activity->action);
            
            if (str_contains($action, 'tambah') || str_contains($action, 'create')) {
                $category = 'Penambahan';
            } elseif (str_contains($action, 'update') || str_contains($action, 'edit')) {
                $category = 'Perubahan';
            } elseif (str_contains($action, 'hapus') || str_contains($action, 'delete')) {
                $category = 'Penghapusan';
            } elseif (str_contains($action, 'login')) {
                $category = 'Login/Akses';
            }
            
            return [
                $activity->created_at->format('Y-m-d'),
                'Minggu ' . $activity->created_at->format('W'),
                $activity->user,
                $activity->action,
                $activity->model ?? '',
                $activity->details ?? '',
                $category,
                $activity->created_at->format('H:i:s')
            ];
        })->toArray();
    }
}