<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesTransactionExport;
use App\Exports\StockMovementExport;

class ReportController extends Controller
{
    /**
     * Display the unified reports dashboard.
     */
    public function index(Request $request)
    {
        $mode = $request->get('mode', 'range');
        $now = Carbon::now();

        $availableYears = Activity::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter(fn($year) => $year > 0) // Filter out invalid years
            ->map(fn ($year) => (int) $year)
            ->unique()
            ->values();

        if ($availableYears->isEmpty() || !$availableYears->contains($now->year)) {
            $availableYears = $availableYears->prepend($now->year)->unique()->values();
        }

        $groupFormat = 'Y-m-d';
        $labelFormatter = fn (Carbon $date) => $date->translatedFormat('d M');
        $periodDescription = '';
        $selectedYear = (int) $request->integer('year', $now->year);
        $selectedWeekYear = (int) ($request->integer('week_year') ?: $now->year);
        $selectedWeekMonth = (int) ($request->integer('week_month') ?: $now->month);

        // Ensure valid years
        if ($selectedYear <= 0) {
            $selectedYear = $now->year;
        }
        if ($selectedWeekYear <= 0) {
            $selectedWeekYear = $now->year;
        }

        if (! $availableYears->contains($selectedWeekYear)) {
            $availableYears = $availableYears->push($selectedWeekYear)->unique()->sortDesc()->values();
        }

        if ($selectedWeekMonth < 1 || $selectedWeekMonth > 12) {
            $selectedWeekMonth = $now->month;
        }

        $weekOptionsDetailed = $this->buildWeekOptionsForMonth($selectedWeekYear, $selectedWeekMonth, $now);
        $resolvedWeek = $this->pickWeekSelection($request->get('week'), $weekOptionsDetailed, $now);
        $weekValue = $resolvedWeek['value'];
        $dateFrom = null;
        $dateTo = null;
        $start = null;
        $end = null;

        switch ($mode) {
            case 'week':
                $start = $resolvedWeek['start']->copy();
                $end = $resolvedWeek['end']->copy();
                $periodDescription = 'Minggu ' . $resolvedWeek['label'];
                $weekValue = $resolvedWeek['value'];
                $dateFrom = $start->format('Y-m-d');
                $dateTo = $end->format('Y-m-d');
                $groupFormat = 'Y-m-d';
                $labelFormatter = fn (Carbon $date) => $date->translatedFormat('d M');
                $period = CarbonPeriod::create($start->copy(), '1 day', $end->copy());
                break;

            case 'year':
                if (!$availableYears->contains($selectedYear)) {
                    $selectedYear = $now->year;
                }

                $start = Carbon::create($selectedYear, 1, 1)->startOfYear();
                $end = $start->copy()->endOfYear();
                $periodDescription = 'Tahun ' . $selectedYear;
                $dateFrom = $start->format('Y-m-d');
                $dateTo = $end->format('Y-m-d');
                $groupFormat = 'Y-m';
                $labelFormatter = fn (Carbon $date) => $date->translatedFormat('M Y');
                $period = CarbonPeriod::create($start->copy(), '1 month', $end->copy());
                break;

            case 'range':
            default:
                $start = $this->safeParseDate($request->date_from ?? null, fn () => $now->copy()->subDays(6)->startOfDay());
                $end = $this->safeParseDate($request->date_to ?? null, fn () => $now->copy()->endOfDay(), endOfDay: true);

                if ($end->lt($start)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }

                $periodDescription = $start->translatedFormat('d M Y') . ' – ' . $end->translatedFormat('d M Y');
                $dateFrom = $start->format('Y-m-d');
                $dateTo = $end->format('Y-m-d');
                $groupFormat = 'Y-m-d';
                $labelFormatter = fn (Carbon $date) => $date->translatedFormat('d M');
                $period = CarbonPeriod::create($start->copy(), '1 day', $end->copy());
                break;
        }

        $weekOptions = $weekOptionsDetailed
            ->map(fn (array $option) => [
                'value' => $option['value'],
                'label' => $option['label'],
            ])
            ->values();

        $activities = Activity::query()
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $buckets = $this->initializeBuckets($period, $groupFormat, $labelFormatter);

        $summaryTotals = [
            'totalActivities' => 0,
            'stockIn' => 0,
            'stockOut' => 0,
            'endingStock' => 0,
        ];

        $cumulativeStock = 0;

        foreach ($activities as $activity) {
            $createdAt = $activity->created_at instanceof Carbon
                ? $activity->created_at
                : Carbon::parse($activity->created_at);

            $bucketKey = $createdAt->format($groupFormat);

            if (!$buckets->has($bucketKey)) {
                continue;
            }

            $bucket = $buckets->get($bucketKey);

            $actionText = Str::lower((string) $activity->action);
            $isStockIn = Str::contains($actionText, ['tambah', 'masuk', 'increase', 'restock']);
            $isStockOut = Str::contains($actionText, ['keluar', 'hapus', 'kurang', 'issue']);

            $bucket['total'] += 1;

            if ($isStockIn) {
                $bucket['stock_in'] += 1;
                $summaryTotals['stockIn'] += 1;
                $cumulativeStock += 1;
            }

            if ($isStockOut) {
                $bucket['stock_out'] += 1;
                $summaryTotals['stockOut'] += 1;
                $cumulativeStock -= 1;
            }

            $bucket['ending_stock'] = max(0, $cumulativeStock);
            $summaryTotals['totalActivities'] += 1;
            $buckets->put($bucketKey, $bucket);
        }

        $summaryTotals['endingStock'] = max(0, $cumulativeStock);

        $tableData = $buckets->values()->all();

        $chartData = [
            'labels' => array_column($tableData, 'label'),
            'datasets' => [
                'total' => array_column($tableData, 'total'),
                'stock_in' => array_column($tableData, 'stock_in'),
                'stock_out' => array_column($tableData, 'stock_out'),
                'ending_stock' => array_column($tableData, 'ending_stock'),
            ],
        ];

        $summary = array_merge($summaryTotals, [
            'uniqueUsers' => $activities->pluck('user')->filter()->unique()->count(),
        ]);

        $recentActivities = Activity::select(['id', 'action', 'model', 'created_at', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $salesTransactions = DB::table('stock_out')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'date' => Carbon::parse($row->created_at)->format('d M Y'),
                    'customer_name' => $row->customer_name,
                    'customer_type' => $row->customer_type,
                    'product_name' => $row->product_name,
                    'qty' => $row->stock_qty,
                    'satuan' => $row->satuan,
                    'price_per_unit' => $row->prices,
                    'total_price' => $row->total_price,
                    'shipping_cost' => $row->shipping_cost,
                    'grand_total' => $row->total_price + $row->shipping_cost,
                ];
            })
            ->values()
            ->all();

        $filters = [
            'mode' => $mode,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'week' => $weekValue,
            'week_month' => $selectedWeekMonth,
            'week_year' => $selectedWeekYear,
            'year' => $selectedYear,
        ];

        return view('reports.index', [
            'summary' => $summary,
            'chartData' => $chartData,
            'tableData' => $tableData,
            'salesTransactions' => $salesTransactions,
            'filters' => $filters,
            'availableYears' => $availableYears,
            'weekOptions' => $weekOptions->all(),
            'weekMonthOptions' => $this->buildWeekMonthOptions()->all(),
            'periodDescription' => $periodDescription,
            'recentActivities' => $recentActivities,
        ]);
    }

    /**
     * Prepare buckets for the given period.
     */
    private function initializeBuckets(CarbonPeriod $period, string $groupFormat, callable $labelFormatter): Collection
    {
        $buckets = collect();

        foreach ($period as $date) {
            $key = $date->format($groupFormat);

            $buckets->put($key, [
                'label' => $labelFormatter($date),
                'total' => 0,
                'stock_in' => 0,
                'stock_out' => 0,
                'ending_stock' => 0,
            ]);
        }

        return $buckets;
    }

    private function safeParseDate(?string $date, callable $fallback, bool $endOfDay = false): Carbon
    {
        if ($date === null) {
            $base = $fallback();
        } else {
            try {
                $base = Carbon::parse($date);
            } catch (\Throwable $e) {
                $base = $fallback();
            }
        }

        return $endOfDay ? $base->copy()->endOfDay() : $base->copy()->startOfDay();
    }

    private function buildWeekOptionsForMonth(int $year, int $month, Carbon $reference): Collection
    {
        $monthStart = Carbon::create($year, $month, 1, 0, 0, 0)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth();

        $cursor = $monthStart->copy()->startOfWeek();
        $options = collect();

        while ($cursor->lte($monthEnd)) {
            $weekStart = $cursor->copy();
            $weekEnd = $weekStart->copy()->endOfWeek();

            if ($weekEnd->lt($monthStart)) {
                $cursor->addWeek();
                continue;
            }

            if ($weekStart->gt($monthEnd)) {
                break;
            }

            $clampedStart = $weekStart->greaterThan($monthStart) ? $weekStart->copy() : $monthStart->copy();
            $clampedEnd = $weekEnd->lessThan($monthEnd) ? $weekEnd->copy() : $monthEnd->copy();

            $options->push([
                'value' => sprintf('%d-W%02d', $weekStart->isoWeekYear, $weekStart->isoWeek),
                'label' => $clampedStart->translatedFormat('d M') . ' – ' . $clampedEnd->translatedFormat('d M Y'),
                'start' => $clampedStart->copy()->startOfDay(),
                'end' => $clampedEnd->copy()->endOfDay(),
                'sort_key' => $weekStart->timestamp,
                'is_current' => $reference->between($weekStart, $weekEnd, true),
            ]);

            $cursor->addWeek();
        }

        if ($options->isEmpty()) {
            $options->push([
                'value' => sprintf('%d-W%02d', $monthStart->isoWeekYear, $monthStart->isoWeek),
                'label' => $monthStart->translatedFormat('d M') . ' – ' . $monthEnd->translatedFormat('d M Y'),
                'start' => $monthStart->copy()->startOfDay(),
                'end' => $monthEnd->copy()->endOfDay(),
                'sort_key' => $monthStart->timestamp,
                'is_current' => true,
            ]);
        }

        return $options->sortByDesc('sort_key')->values();
    }

    private function pickWeekSelection(?string $requestedWeek, Collection $options, Carbon $reference): array
    {
        if ($options->isEmpty()) {
            $start = $reference->copy()->startOfWeek();
            $end = $reference->copy()->endOfWeek();

            return [
                'value' => sprintf('%d-W%02d', $start->isoWeekYear, $start->isoWeek),
                'label' => $start->translatedFormat('d M Y') . ' – ' . $end->translatedFormat('d M Y'),
                'start' => $start,
                'end' => $end,
            ];
        }

        $selected = $options->firstWhere('value', $requestedWeek);

        if (! $selected) {
            $selected = $options->firstWhere('is_current', true) ?? $options->first();
        }

        return $selected;
    }

    private function buildWeekMonthOptions(): Collection
    {
        return collect(range(1, 12))->map(fn (int $month) => [
            'value' => $month,
            'label' => Carbon::create(2000, $month, 1)->translatedFormat('F'),
        ]);
    }

    /**
     * Export sales transactions report
     */
    public function exportSales(Request $request)
    {
        [$start, $end] = $this->getPeriodDates($request);
        
        $salesData = DB::table('stock_out')
            ->whereBetween('created_at', [$start, $end])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($row) {
                return [
                    'date' => Carbon::parse($row->created_at)->format('d M Y'),
                    'customer_name' => $row->customer_name,
                    'customer_type' => ucfirst($row->customer_type),
                    'product_name' => $row->product_name,
                    'qty' => $row->stock_qty . ' ' . $row->satuan,
                    'price_per_unit' => 'Rp ' . number_format($row->prices, 0, ',', '.'),
                    'total_price' => 'Rp ' . number_format($row->total_price, 0, ',', '.'),
                    'shipping_cost' => 'Rp ' . number_format($row->shipping_cost, 0, ',', '.'),
                    'grand_total' => 'Rp ' . number_format($row->total_price + $row->shipping_cost, 0, ',', '.'),
                ];
            })
            ->toArray();

        $format = $request->get('format', 'excel');
        $filename = 'laporan-transaksi-penjualan-' . $start->format('Y-m-d') . '-' . $end->format('Y-m-d');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.pdf.sales', [
                'data' => $salesData,
                'start' => $start->translatedFormat('d F Y'),
                'end' => $end->translatedFormat('d F Y'),
            ])->setPaper('a4', 'landscape');
            
            return $pdf->download($filename . '.pdf');
        }

        return Excel::download(new SalesTransactionExport($salesData, $start, $end), $filename . '.xlsx');
    }

    /**
     * Export stock movement report
     */
    public function exportStock(Request $request)
    {
        [$start, $end, $period, $groupFormat, $labelFormatter] = $this->getPeriodDataForExport($request);
        
        $activities = Activity::query()
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at')
            ->get();

        $buckets = $this->initializeBuckets($period, $groupFormat, $labelFormatter);
        $cumulativeStock = 0;

        foreach ($activities as $activity) {
            $createdAt = $activity->created_at instanceof Carbon
                ? $activity->created_at
                : Carbon::parse($activity->created_at);

            $bucketKey = $createdAt->format($groupFormat);

            if (!$buckets->has($bucketKey)) {
                continue;
            }

            $bucket = $buckets->get($bucketKey);
            $actionText = Str::lower((string) $activity->action);
            $isStockIn = Str::contains($actionText, ['tambah', 'masuk', 'increase', 'restock']);
            $isStockOut = Str::contains($actionText, ['keluar', 'hapus', 'kurang', 'issue']);

            $bucket['total'] += 1;

            if ($isStockIn) {
                $bucket['stock_in'] += 1;
                $cumulativeStock += 1;
            }

            if ($isStockOut) {
                $bucket['stock_out'] += 1;
                $cumulativeStock -= 1;
            }

            $bucket['ending_stock'] = max(0, $cumulativeStock);
            $buckets->put($bucketKey, $bucket);
        }

        $stockData = $buckets->values()->toArray();
        $format = $request->get('format', 'excel');
        $filename = 'laporan-pergerakan-stok-' . $start->format('Y-m-d') . '-' . $end->format('Y-m-d');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.pdf.stock', [
                'data' => $stockData,
                'start' => $start->translatedFormat('d F Y'),
                'end' => $end->translatedFormat('d F Y'),
            ]);
            
            return $pdf->download($filename . '.pdf');
        }

        return Excel::download(new StockMovementExport($stockData, $start, $end), $filename . '.xlsx');
    }

    /**
     * Get period dates from request
     */
    private function getPeriodDates(Request $request): array
    {
        $mode = $request->get('mode', 'range');
        $now = Carbon::now();

        switch ($mode) {
            case 'week':
                $weekYear = (int) ($request->integer('week_year') ?? $now->year);
                $weekMonth = (int) ($request->integer('week_month') ?? $now->month);
                $weekOptionsDetailed = $this->buildWeekOptionsForMonth($weekYear, $weekMonth, $now);
                $resolvedWeek = $this->pickWeekSelection($request->get('week'), $weekOptionsDetailed, $now);
                $start = $resolvedWeek['start']->copy();
                $end = $resolvedWeek['end']->copy();
                break;

            case 'year':
                $year = (int) $request->integer('year', $now->year);
                $start = Carbon::create($year, 1, 1)->startOfYear();
                $end = $start->copy()->endOfYear();
                break;

            case 'range':
            default:
                $start = $this->safeParseDate($request->date_from ?? null, fn () => $now->copy()->subDays(6)->startOfDay());
                $end = $this->safeParseDate($request->date_to ?? null, fn () => $now->copy()->endOfDay(), endOfDay: true);
                
                if ($end->lt($start)) {
                    [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
                }
                break;
        }

        return [$start, $end];
    }

    /**
     * Get period data for export
     */
    private function getPeriodDataForExport(Request $request): array
    {
        [$start, $end] = $this->getPeriodDates($request);
        $mode = $request->get('mode', 'range');

        if ($mode === 'year') {
            $groupFormat = 'Y-m';
            $labelFormatter = fn (Carbon $date) => $date->translatedFormat('M Y');
            $period = CarbonPeriod::create($start->copy(), '1 month', $end->copy());
        } else {
            $groupFormat = 'Y-m-d';
            $labelFormatter = fn (Carbon $date) => $date->translatedFormat('d M');
            $period = CarbonPeriod::create($start->copy(), '1 day', $end->copy());
        }

        return [$start, $end, $period, $groupFormat, $labelFormatter];
    }
}
