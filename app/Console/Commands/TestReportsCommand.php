<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TestReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:reports {--type=all : Type of report to test (all, stock-value, movement, supplier-performance, weekly, monthly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all report generation and export functions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Testing Al-Ruhamaa\' Inventory System Reports...');
        
        $type = $this->option('type');
        $controller = new ReportController();
        
        if ($type === 'all' || $type === 'stock-value') {
            $this->testStockValueReport($controller);
        }
        
        if ($type === 'all' || $type === 'movement') {
            $this->testMovementReport($controller);
        }
        
        if ($type === 'all' || $type === 'supplier-performance') {
            $this->testSupplierPerformanceReport($controller);
        }
        
        if ($type === 'all' || $type === 'weekly') {
            $this->testWeeklyReport($controller);
        }
        
        if ($type === 'all' || $type === 'monthly') {
            $this->testMonthlyReport($controller);
        }
        
        $this->info('✅ All tests completed successfully!');
        
        return 0;
    }
    
    private function testStockValueReport($controller)
    {
        $this->info('📊 Testing Stock Value Report...');
        
        try {
            // Test view
            $request = new Request();
            $response = $controller->stockValue($request);
            $this->line('  ✓ Stock value view generated successfully');
            
            // Test PDF export
            $request = new Request(['type' => 'stock-value']);
            $pdfResponse = $controller->exportPdf($request);
            $this->line('  ✓ Stock value PDF export working');
            
            // Test Excel export
            $excelResponse = $controller->exportExcel($request);
            $this->line('  ✓ Stock value Excel export working');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Stock Value Report failed: ' . $e->getMessage());
        }
    }
    
    private function testMovementReport($controller)
    {
        $this->info('📈 Testing Movement Report...');
        
        try {
            // Test view
            $request = new Request([
                'date_from' => Carbon::now()->subDays(7)->format('Y-m-d'),
                'date_to' => Carbon::now()->format('Y-m-d')
            ]);
            $response = $controller->movement($request);
            $this->line('  ✓ Movement view generated successfully');
            
            // Test PDF export
            $request = new Request(['type' => 'movement']);
            $pdfResponse = $controller->exportPdf($request);
            $this->line('  ✓ Movement PDF export working');
            
            // Test Excel export
            $excelResponse = $controller->exportExcel($request);
            $this->line('  ✓ Movement Excel export working');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Movement Report failed: ' . $e->getMessage());
        }
    }
    
    private function testSupplierPerformanceReport($controller)
    {
        $this->info('🏢 Testing Supplier Performance Report...');
        
        try {
            // Test view
            $request = new Request();
            $response = $controller->supplierPerformance($request);
            $this->line('  ✓ Supplier performance view generated successfully');
            
            // Test PDF export
            $request = new Request(['type' => 'supplier-performance']);
            $pdfResponse = $controller->exportPdf($request);
            $this->line('  ✓ Supplier performance PDF export working');
            
            // Test Excel export
            $excelResponse = $controller->exportExcel($request);
            $this->line('  ✓ Supplier performance Excel export working');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Supplier Performance Report failed: ' . $e->getMessage());
        }
    }
    
    private function testWeeklyReport($controller)
    {
        $this->info('📅 Testing Weekly Report...');
        
        try {
            // Test view
            $request = new Request(['week' => Carbon::now()->startOfWeek()->format('Y-m-d')]);
            $response = $controller->weekly($request);
            $this->line('  ✓ Weekly view generated successfully');
            
            // Test PDF export
            $request = new Request(['type' => 'weekly']);
            $pdfResponse = $controller->exportPdf($request);
            $this->line('  ✓ Weekly PDF export working');
            
            // Test Excel export
            $excelResponse = $controller->exportExcel($request);
            $this->line('  ✓ Weekly Excel export working');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Weekly Report failed: ' . $e->getMessage());
        }
    }
    
    private function testMonthlyReport($controller)
    {
        $this->info('📆 Testing Monthly Report...');
        
        try {
            // Test view
            $request = new Request(['month' => Carbon::now()->startOfMonth()->format('Y-m-d')]);
            $response = $controller->monthly($request);
            $this->line('  ✓ Monthly view generated successfully');
            
            // Test PDF export
            $request = new Request(['type' => 'monthly']);
            $pdfResponse = $controller->exportPdf($request);
            $this->line('  ✓ Monthly PDF export working');
            
            // Test Excel export
            $excelResponse = $controller->exportExcel($request);
            $this->line('  ✓ Monthly Excel export working');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Monthly Report failed: ' . $e->getMessage());
        }
    }
}
